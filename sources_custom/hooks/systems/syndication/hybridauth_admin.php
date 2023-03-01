<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    hybridauth
 */
// activity_feed addon's implementation of activity syndication. Which in turn implements syndication hooks for further syndication (i.e. it's a syndication API built on top of a syndication API, as we don't want to provide lots of implementation code in Composr itself when no default use case is shipped).

/**
 * Hook class.
 */
class Hook_syndication_hybridauth_admin
{
    /**
     * Syndicate a content object out, and also send out activities relating to the same content.
     *
     * @param  string $content_type Content type
     * @param  string $content_id Content ID
     * @param  string $title Title
     * @param  array $cma_info Content hook info
     * @param  array $content_row Content row
     * @param  Tempcode $url_safe URL
     * @param  object $content_ob Content object
     * @param  array $syndication_context A serialisable representation of data set via get_syndication_option_fields
     */
    public function syndicate_content($content_type, $content_id, $title, $submitter, $cma_info, $content_row, $url_safe, $content_ob, $syndication_context)
    {
        if (!addon_installed('hybridauth')) {
            return null;
        }

        if (!function_exists('curl_init')) {
            return null;
        }

        $syndicate_content_to = $syndication_context['syndicate_content_to'];
        if (empty($syndicate_content_to)) {
            return;
        }

        require_code('hybridauth_admin');
        require_lang('hybridauth');

        $atom = new \Hybridauth\Atom\Atom();
        $atom->author = $content_ob->get_author($content_row);
        $atom->published = new \DateTime('@' . strval($content_ob->get_add_time($content_row)));
        $atom->updated = new \DateTime('@' . strval(time()));
        $atom->title = $title;
        $atom->summary = $content_ob->get_description($content_row, FIELD_RENDER_HTML);
        $atom->url = $url_safe->evaluate();
        if ($cma_info['seo_type_code'] !== null) {
            list(, , $hash_tags) = seo_meta_get_for($cma_info['seo_type_code'], $content_id);
            if ($hash_tags != '') {
                $atom->hashTags = explode(',', $hash_tags);
            }
        }

        $image_url = $content_ob->get_image_url($content_row);
        if ($image_url != '') {
            $enclosure = $this->create_enclosure($image_url, \Hybridauth\Atom\Enclosure::ENCLOSURE_IMAGE);
            $atom->enclosures[] = $enclosure;
        }

        // FUDGE: We don't have a proper API to pull out videos, so we'll hard-code for gallery videos
        if ($content_type == 'video') {
            $video_url = $GLOBALS['SITE_DB']->query_select_value('videos', 'url', ['id' => intval($content_id)]);
            if (url_is_local($video_url)) {
                $video_url = get_custom_base_url() . '/' . $video_url;
            }
            $enclosure = $this->create_enclosure($video_url, \Hybridauth\Atom\Enclosure::ENCLOSURE_VIDEO);
            $atom->enclosures[] = $enclosure;
        }

        $before_type_strictness = ini_get('ocproducts.type_strictness');
        cms_ini_set('ocproducts.type_strictness', '0');
        $before_xss_detect = ini_get('ocproducts.xss_detect');
        cms_ini_set('ocproducts.xss_detect', '0');

        list($hybridauth) = initiate_hybridauth_admin();

        $providers = find_all_hybridauth_admin_providers_matching(HYBRIDAUTH__ADVANCEDAPI_INSERT_ATOMS);
        foreach ($providers as $provider => $info) {
            $syndicate_from = ($info['syndicate_from'] == '') ? [] : explode(',', $info['syndicate_from']);
            if ((!in_array($cma_info['addon_name'], $syndicate_from)) && (!in_array($content_type, $syndicate_from))) {
                continue;
            }

            try {
                $adapter = $hybridauth->getAdapter($provider);
                if (!$adapter->isConnected()) {
                    continue;
                }

                $key_map = [
                    'h_content_type' => $content_type,
                    'h_content_id' => $content_id,
                    'h_provider' => $provider,
                ];

                $test = $GLOBALS['SITE_DB']->query_select_value_if_there('hybridauth_content_map', 'h_provider_id', $key_map);
                $is_update = ($test !== null);
                if ($is_update) {
                    if (!in_array($provider, $syndicate_content_to)) {
                        $adapter->deleteAtom($test);

                        $GLOBALS['SITE_DB']->query_delete('hybridauth_content_map', $key_map, '', 1);

                        continue;
                    }

                    if (!hybridauth_provider_matches($provider, HYBRIDAUTH__ADVANCEDAPI_UPDATE_ATOMS)) {
                        continue;
                    }
                    $atom->identifier = $test;
                } else {
                    if (!in_array($provider, $syndicate_content_to)) {
                        continue;
                    }
                }

                $messages = [];
                $provider_id = $adapter->saveAtom($atom, $messages);

                foreach ($messages as $message) {
                    attach_message($message, 'notice', false, true);
                }

                // FUDGE: Honour remote_hosting for 'video' content type only
                if (($content_type == 'video') && ($info['remote_hosting'])) {
                    $remote_atom = $adapter->getAtomFull($provider_id);

                    $GLOBALS['SITE_DB']->query_update('videos', ['url' => $remote_atom->url], ['id' => intval($content_id)], '', 1);
                }

                if (!$is_update) {
                    $GLOBALS['SITE_DB']->query_insert('hybridauth_content_map', [
                        'h_provider_id' => $provider_id,
                        'h_sync_time' => time(),
                    ] + $key_map);
                } else {
                    $GLOBALS['SITE_DB']->query_update('hybridauth_content_map', [
                        'h_sync_time' => time(),
                    ], $key_map, '', 1);
                }
            } catch (Exception $e) {
                require_code('failure');
                cms_error_log($e->getMessage(), 'error_occurred_api');
            }
        }

        cms_ini_set('ocproducts.type_strictness', $before_type_strictness);
        cms_ini_set('ocproducts.xss_detect', $before_xss_detect);
    }

    /**
     * Create a Hybridauth enclosure.
     *
     * @param  string $url URL
     * @param  integer $type Enclosure type
     * @return object Enclosure
     */
    protected function create_enclosure(string $url, int $type) : object
    {
        $enclosure = new \Hybridauth\Atom\Enclosure();
        $enclosure->type = $type;
        $enclosure->url = $url;

        $prefix = get_custom_base_url() . '/uploads/';
        if (substr($url, 0, strlen($prefix)) == $prefix) {
            $path = get_custom_file_base() . '/uploads/' . rawurldecode(substr($url, strlen($prefix)));

            require_code('mime_types');
            $mime_type = get_mime_type(get_file_extension($url), true);
            if ($mime_type != 'application/octet-stream') {
                $enclosure->mimeType = $mime_type;
                $file_size = @filesize($path);
                if ($file_size !== false) {
                    $enclosure->contentLength = $file_size;
                }
            }
        }

        return $enclosure;
    }

    /**
     * Delete syndicated content, due to it being deleted locally.
     *
     * @param  string $content_type Content type
     * @param  string $content_id Content ID
     */
    public function unsyndicate_content(string $content_type, string $content_id)
    {
        if (!addon_installed('hybridauth')) {
            return null;
        }

        require_code('hybridauth_admin');
        require_lang('hybridauth');

        $before_type_strictness = ini_get('ocproducts.type_strictness');
        cms_ini_set('ocproducts.type_strictness', '0');
        $before_xss_detect = ini_get('ocproducts.xss_detect');
        cms_ini_set('ocproducts.xss_detect', '0');

        list($hybridauth) = initiate_hybridauth_admin();

        $providers = find_all_hybridauth_admin_providers_matching(HYBRIDAUTH__ADVANCEDAPI_DELETE_ATOMS);
        foreach ($providers as $provider => $info) {
            try {
                $adapter = $hybridauth->getAdapter($provider);
                if (!$adapter->isConnected()) {
                    continue;
                }

                $key_map = [
                    'h_content_type' => $content_type,
                    'h_content_id' => $content_id,
                    'h_provider' => $provider,
                ];

                $provider_id = $GLOBALS['SITE_DB']->query_select_value_if_there('hybridauth_content_map', 'h_provider_id', $key_map);

                if ($provider_id !== null) {
                    $adapter->deleteAtom($provider_id);

                    $GLOBALS['SITE_DB']->query_delete('hybridauth_content_map', $key_map, '', 1);
                }
            } catch (Exception $e) {
                require_code('failure');
                cms_error_log($e->getMessage(), 'error_occurred_api');
            }
        }

        cms_ini_set('ocproducts.type_strictness', $before_type_strictness);
        cms_ini_set('ocproducts.xss_detect', $before_xss_detect);
    }

    /**
     * Get syndication field UI.
     *
     * @param  string $content_type The content type this is for
     * @param  boolean $is_edit If these options are for an edit
     * @return Tempcode Syndication fields (or empty)
     */
    public function get_syndication_option_fields(string $content_type, bool $is_edit) : object
    {
        if (!addon_installed('hybridauth')) {
            return new Tempcode();
        }

        require_code('content');
        $addon_name = convert_composr_type_codes('content_type', $content_type, 'addon_name');

        $fields = new Tempcode();
        $list_options = new Tempcode();

        require_code('hybridauth_admin');

        //$match = $is_edit ? HYBRIDAUTH__ADVANCEDAPI_UPDATE_ATOMS : HYBRIDAUTH__ADVANCEDAPI_INSERT_ATOMS; Actually we must have all insert ones, as we use missing provider as a signal to delete
        $match = HYBRIDAUTH__ADVANCEDAPI_INSERT_ATOMS;
        $providers = find_all_hybridauth_admin_providers_matching($match);
        foreach ($providers as $provider => $info) {
            $syndicate_from = ($info['syndicate_from'] == '') ? [] : explode(',', $info['syndicate_from']);
            if ((in_array($addon_name, $syndicate_from)) || (in_array($content_type, $syndicate_from))) {
                $syndicate_from_by_default = ($info['syndicate_from_by_default'] == '') ? [] : explode(',', $info['syndicate_from_by_default']);
                $by_default = ((in_array($addon_name, $syndicate_from_by_default)) || (in_array($content_type, $syndicate_from_by_default)));

                $list_options->attach(form_input_list_entry($provider, $by_default));
            }
        }

        if ($list_options->is_empty()) {
            return $fields;
        }

        $content_type_ob = get_content_object($content_type);
        $cma_info = $content_type_ob->info();
        $content_type_label = do_lang($cma_info['content_type_label']);

        // There are no privileges for this, as it is done based on configuration; we can assume such content really is meant to be consistently syndicated if validated

        require_lang('hybridauth');

        $fields->attach(form_input_multi_list(do_lang_tempcode('SYNDICATE_TO_HYBRIDAUTH'), do_lang_tempcode('DESCRIPTION_SYNDICATE_TO_HYBRIDAUTH', $content_type_label), 'syndicate_content_to', $list_options));
        return $fields;
    }

    /**
     * Get syndication field settings, and other context we may need to serialise.
     *
     * @param  ?string $content_type The content type this is for (null: none)
     * @return array Syndication field context
     */
    public function read_get_syndication_option_fields(?string $content_type) : array
    {
        if (!addon_installed('hybridauth')) {
            return [];
        }

        $_syndicate_content_to = post_param_string('syndicate_content_to', '');
        $syndicate_content_to = ($_syndicate_content_to == '') ? [] : explode(',', $_syndicate_content_to);

        return [
            'syndicate_content_to' => $syndicate_content_to,
        ];
    }
}
