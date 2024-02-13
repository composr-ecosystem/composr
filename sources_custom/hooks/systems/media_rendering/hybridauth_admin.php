<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    hybridauth
 */

/*
Notes...
 - The cache_age property is not supported. It would significantly complicate the API and hurt performance, and we don't know a use case for it. The spec says it is optional to support.
 - Link/semantic-webpage rendering will not use passed description parameter, etc. This is intentional: the normal flow of rendering through a standardised media template is not used.
*/

/**
 * Standard code module initialisation function.
 *
 * @ignore
 */
function init__hooks__systems__media_rendering__hybidauth_admin()
{
    global $ATOM_URL_RENDERING_CACHE;
    $ATOM_URL_RENDERING_CACHE = [];
}

/**
 * Hook class.
 */
class Hook_media_rendering_hybridauth_admin extends Media_renderer_with_fallback
{
    /**
     * Get the label for this media rendering type.
     *
     * @return string The label
     */
    public function get_type_label() : string
    {
        return 'Hybridauth';
    }

    /**
     * Find the media types this hook serves.
     *
     * @return integer The media type(s), as a bitmask
     */
    public function get_media_type() : int
    {
        return MEDIA_TYPE_OTHER;
    }

    /**
     * See if we can recognise this mime type.
     *
     * @param  ID_TEXT $mime_type The mime type
     * @param  ?array $meta_details The media signature, so we can go on this on top of the mime-type (null: not known)
     * @return integer Recognition precedence
     */
    public function recognises_mime_type(string $mime_type, ?array $meta_details = null) : int
    {
        return MEDIA_RECOG_PRECEDENCE_NONE;
    }

    /**
     * See if we can recognise this URL pattern.
     *
     * @param  URLPATH $url URL to pattern match
     * @return integer Recognition precedence
     */
    public function recognises_url(string $url) : int
    {
        if (!addon_installed('hybridauth')) {
            return MEDIA_RECOG_PRECEDENCE_NONE;
        }

        if (!function_exists('curl_init')) {
            return MEDIA_RECOG_PRECEDENCE_NONE;
        }

        if (strpos($url, '://') === false) {
            return MEDIA_RECOG_PRECEDENCE_NONE;
        }

        $this->hybridauth_scan($url);

        global $ATOM_URL_RENDERING_CACHE;
        if (isset($ATOM_URL_RENDERING_CACHE[$url])) {
            return MEDIA_RECOG_PRECEDENCE_MEDIUM;
        }

        return MEDIA_RECOG_PRECEDENCE_NONE;
    }

    /**
     * Scan Hybridauth for metadata of the URL.
     *
     * @param  mixed $url URL to render
     */
    protected function hybridauth_scan($url)
    {
        global $ATOM_URL_RENDERING_CACHE;
        if (isset($ATOM_URL_RENDERING_CACHE[$url])) {
            return;
        }

        require_code('hybridauth_admin');

        $before_type_strictness = ini_get('ocproducts.type_strictness');
        cms_ini_set('ocproducts.type_strictness', '0');
        $before_xss_detect = ini_get('ocproducts.xss_detect');
        cms_ini_set('ocproducts.xss_detect', '0');

        list($hybridauth, $admin_storage) = initiate_hybridauth_admin();

        $providers = find_all_hybridauth_admin_providers_matching(HYBRIDAUTH__ADVANCEDAPI_READ_ATOMS_FROM_URL);
        foreach ($providers as $provider => $info) {
            if (!$info['enabled']) {
                continue;
            }

            try {
                $adapter = $hybridauth->getAdapter($provider);
                $connected = $adapter->isConnected();
            } catch (Exception $e) {
                $connected = false;
            }

            if (!$connected) {
                continue;
            }

            try {
                $atom = $adapter->getAtomFullFromURL($url);
                if ($atom !== null) {
                    $ATOM_URL_RENDERING_CACHE[$url] = [$atom, $provider];
                    return;
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
     * Provide code to display what is at the URL, in the most appropriate way.
     *
     * @param  mixed $url URL to render
     * @param  mixed $url_safe URL to render (no sessions etc)
     * @param  array $attributes Attributes (e.g. width, height, length)
     * @param  boolean $as_admin Whether there are admin privileges, to render dangerous media types
     * @param  ?MEMBER $source_member Member to run as (null: current member)
     * @param  ?URLPATH $url_direct_filesystem Direct URL (not via a script) (null: just use the normal URL)
     * @param  ?string $original_filename Originally filename to display as a link caption where appropriate (null: use $url_safe)
     * @return Tempcode Rendered version
     */
    public function render($url, $url_safe, array $attributes, bool $as_admin = false, ?int $source_member = null, ?string $url_direct_filesystem = null, ?string $original_filename = null) : object
    {
        $this->hybridauth_scan($url);

        global $ATOM_URL_RENDERING_CACHE;
        if (!isset($ATOM_URL_RENDERING_CACHE[$url])) {
            require_code('comcode_renderer');
            if ($source_member === null) {
                $source_member = get_member();
            }
            $comcode = '';
            $url_tempcode = new Tempcode();
            $url_tempcode->attach($url);
            return _do_tags_comcode('url', [], $url_tempcode, false, '', 0, $source_member, false, $GLOBALS['SITE_DB'], $comcode, false, false);
        }

        list($atom) = $ATOM_URL_RENDERING_CACHE[$url];

        if (is_object($url)) {
            $url = $url->evaluate();
        }

        if (!empty($atom->summary)) {
            $description = $atom->summary;
        } elseif (!empty($atom->content)) {
            $description = $atom->content;
        } else {
            $description = '';
        }

        if ((!$as_admin) && (!has_privilege(($source_member === null) ? get_member() : $source_member, 'search_engine_links'))) {
            $rel = 'nofollow';
        } else {
            $rel = null;
        }

        $map = [
            'TITLE' => array_key_exists('title', $attributes) ? $attributes['title'] : '',
            'META_TITLE' => empty($atom->title) ? '' : $atom->title,
            'DESCRIPTION' => $description,
            'URL' => $url,
            'WIDTH' => ((array_key_exists('thumbnail_width', $attributes)) && ($attributes['thumbnail_width'] != '')) ? $attributes['thumbnail_width'] : get_option('oembed_max_size'),
            'HEIGHT' => ((array_key_exists('thumbnail_height', $attributes)) && ($attributes['thumbnail_height'] != '')) ? $attributes['thumbnail_height'] : get_option('oembed_max_size'),
            'REL' => $rel,
        ];

        if (count($atom->enclosures) == 1) {
            if ($atom->enclosures[0]->type == \Hybridauth\Atom\Enclosure::ENCLOSURE_IMAGE) {
                return do_template('MEDIA_WEBPAGE_SEMANTIC', $map + [
                    'IMAGE_URL' => $atom->enclosures[0]->url,
                ]);
            }

            if ($atom->enclosures[0]->type == \Hybridauth\Atom\Enclosure::ENCLOSURE_VIDEO) {
                return do_template('MEDIA_WEBPAGE_SEMANTIC', $map + [
                    'IMAGE_URL' => $atom->enclosures[0]->thumbnailUrl,
                ]);
            }
        } elseif (!empty($atom->author->photoURL)) {
            return do_template('MEDIA_WEBPAGE_SEMANTIC', $map + [
                'IMAGE_URL' => $atom->author->photoURL,
            ]);
        }

        return do_template('MEDIA_WEBPAGE_SEMANTIC', $map + [
            'IMAGE_URL' => '',
        ]);
    }
}
