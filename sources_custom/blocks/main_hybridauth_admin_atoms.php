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

/**
 * Block class.
 */
class Block_main_hybridauth_admin_atoms
{
    /**
     * Find details of the block.
     *
     * @return ?array Map of block info (null: block is disabled)
     */
    public function info() : ?array
    {
        $info = [];
        $info['author'] = 'Chris Graham';
        $info['organisation'] = 'ocProducts';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 1;
        $info['locked'] = false;
        $info['parameters'] = ['param', 'max', 'category_filter', 'require_images', 'require_videos', 'require_audios', 'require_binaries', 'include_contributed_content', 'include_private', 'shuffle'];
        return $info;
    }

    /**
     * Find caching details for the block.
     *
     * @return ?array Map of cache details (cache_on and ttl) (null: block is disabled)
     */
    public function caching_environment() : ?array
    {
        $info = [];
        $info['cache_on'] = <<<'PHP'
        [
            array_key_exists('param', $map) ? $map['param'] : '',
            array_key_exists('max', $map) ? intval($map['max']) : 5,

            array_key_exists('category_filter', $map) ? $map['category_filter'] : '',
            array_key_exists('require_images', $map) ? ($map['require_images'] == '1') : false,
            array_key_exists('require_videos', $map) ? ($map['require_videos'] == '1') : false,
            array_key_exists('require_audios', $map) ? ($map['require_audios'] == '1') : false,
            array_key_exists('require_binaries', $map) ? ($map['require_binaries'] == '1') : false,
            array_key_exists('include_contributed_content', $map) ? ($map['include_contributed_content'] == '1') : false,
            array_key_exists('include_private', $map) ? ($map['include_private'] == '1') : false,
            array_key_exists('shuffle', $map) ? ($map['shuffle'] == '1') : false,
        ]
PHP;
        $info['ttl'] = 30;
        return $info;
    }

    /**
     * Execute the block.
     *
     * @param  array $map A map of parameters
     * @return Tempcode The result of execution
     */
    public function run(array $map) : object
    {
        i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

        $block_id = get_block_id($map);

        if (!addon_installed('hybridauth')) {
            return do_template('RED_ALERT', ['_GUID' => '1002b1292dfbc985f873480eb60407c6', 'TEXT' => do_lang_tempcode('MISSING_ADDON', escape_html('hybridauth'))]);
        }

        require_code('hybridauth_admin');
        require_lang('hybridauth');
        require_code('character_sets');

        if (addon_installed('news_shared')) {
            require_css('news');
        }

        $before_type_strictness = ini_get('ocproducts.type_strictness');
        cms_ini_set('ocproducts.type_strictness', '0');
        $before_xss_detect = ini_get('ocproducts.xss_detect');
        cms_ini_set('ocproducts.xss_detect', '0');

        list($hybridauth, $admin_storage, $providers) = initiate_hybridauth_admin();

        if (empty($map['param'])) {
            return do_template('RED_ALERT', ['_GUID' => '98e14965302d40e596c712058d7563f1', 'TEXT' => '\'param\' parameter is needed.']);
        }

        $provider = $map['param'];
        $max = empty($map['max']) ? 5 : intval($map['max']);
        $category_filter = array_key_exists('category_filter', $map) ? $map['category_filter'] : '';
        if ($category_filter == '') {
            $category_filter = null;
        }
        $require_images = array_key_exists('require_images', $map) ? ($map['require_images'] == '1') : false;
        $require_videos = array_key_exists('require_videos', $map) ? ($map['require_videos'] == '1') : false;
        $require_audios = array_key_exists('require_audios', $map) ? ($map['require_audios'] == '1') : false;
        $require_binaries = array_key_exists('require_binaries', $map) ? ($map['require_binaries'] == '1') : false;
        $include_contributed_content = array_key_exists('include_contributed_content', $map) ? ($map['include_contributed_content'] == '1') : false;
        $include_private = array_key_exists('include_private', $map) ? ($map['include_private'] == '1') : false;
        $shuffle = array_key_exists('shuffle', $map) ? ($map['shuffle'] == '1') : false;

        if (!isset($providers[$provider])) {
            return paragraph($provider . ' is not a provider.', 'red_alert');
        }

        if (!$providers[$provider]['enabled']) {
            return paragraph($provider . ' is not configured.', 'red_alert');
        }

        $filter = new Hybridauth\Atom\Filter();
        $filter->categoryFilter = $category_filter;
        $enclosure_type_filter = null;
        if (($require_images) || ($require_videos) || ($require_audios) || ($require_binaries)) {
            $enclosure_type_filter = 0;
            if ($require_images) {
                $enclosure_type_filter += Hybridauth\Atom\Enclosure::ENCLOSURE_IMAGE;
            }
            if ($require_videos) {
                $enclosure_type_filter += Hybridauth\Atom\Enclosure::ENCLOSURE_VIDEO;
            }
            if ($require_audios) {
                $enclosure_type_filter += Hybridauth\Atom\Enclosure::ENCLOSURE_AUDIO;
            }
            if ($require_binaries) {
                $enclosure_type_filter += Hybridauth\Atom\Enclosure::ENCLOSURE_BINARY;
            }
        }
        $filter->enclosureTypeFilter = $enclosure_type_filter;
        $filter->includeContributedContent = $include_contributed_content;
        $filter->includePrivate = $include_private;
        $filter->limit = $max;
        $filter->deepProbe = true;

        try {
            $adapter = $hybridauth->getAdapter($provider);
        } catch (Exception $e) {
            return do_template('RED_ALERT', ['_GUID' => '8f0a85e70e775ef2400bb69349f745fe', 'TEXT' => $e->getMessage()]);
        }

        if (!$adapter->isConnected()) {
            return do_template('RED_ALERT', ['_GUID' => '164607cb0ff8f3ccc43e415cf2f770c5', 'TEXT' => 'Connection to ' . $provider . ' is lost.']);
        }

        if (!$adapter instanceof Hybridauth\Adapter\AtomInterface) {
            return do_template('RED_ALERT', ['_GUID' => 'ac3ea00f5bbd4d85ce1dad74c889d69b', 'TEXT' => 'Atom interface not implemented by ' . $provider]);
        }

        $feed = [];

        try {
            $user_profile = $adapter->getUserProfile();

            $all_categories = $adapter->getCategories();

            list($atoms, $has_results) = $adapter->getAtoms($filter);

            if ($shuffle) {
                shuffle($atoms);
            }

            foreach ($atoms as $atom) {
                if (count($feed) >= $max) {
                    break;
                }

                if ($atom->isIncomplete) {
                    $atom = $adapter->getAtomFull();
                }

                if ($atom->published !== null) {
                    $_published = $atom->published->getTimestamp();
                    $published = get_timezoned_date_time_tempcode($_published);
                } else {
                    $_published = null;
                    $published = null;
                }

                if ($atom->updated !== null) {
                    $_updated = $atom->updated->getTimestamp();
                    $updated = get_timezoned_date_time_tempcode($_updated);
                } else {
                    $_updated = null;
                    $updated = null;
                }

                $enclosures = [];
                $image_url = null;
                foreach ($atom->enclosures as $enclosure) {
                    switch ($enclosure->type) {
                        case Hybridauth\Atom\Enclosure::ENCLOSURE_IMAGE:
                            $type = 'image';
                            if ($image_url === null) {
                                $image_url = $enclosure->url;
                            }
                            break;
                        case Hybridauth\Atom\Enclosure::ENCLOSURE_VIDEO:
                            $type = 'video';
                            break;
                        case Hybridauth\Atom\Enclosure::ENCLOSURE_AUDIO:
                            $type = 'audio';
                            break;
                        case Hybridauth\Atom\Enclosure::ENCLOSURE_BINARY:
                            $type = 'binary';
                            break;
                        default:
                            $type = '';
                            break;
                    }

                    $enclosures[] = [
                        'TYPE' => $type,
                        'MIME_TYPE' => $enclosure->mimeType,
                        'URL' => $enclosure->url,
                        'THUMBNAIL_URL' => $enclosure->thumbnailUrl,
                    ];
                }

                $message = null;
                foreach ([[$atom->title, false], [$atom->summary, true], [$atom->content, true]] as $_field) {
                    list($field, $is_html) = $_field;
                    if (!empty($field)) {
                        $message = $is_html ? $field : Hybridauth\Atom\AtomHelper::plainTextToHtml($field);
                        break;
                    }
                }

                $_category = null;
                $category = do_lang('UNKNOWN');
                $categories = [];
                foreach ($atom->categories as $i => $category) {
                    $categories[] = [
                        'IDENTIFIER' => $this->convert_charset($category->identifier),
                        'LABEL' => $this->convert_charset($category->label),
                    ];
                    if ($i == 0) {
                        $_category = $this->convert_charset($category->identifier);
                        $category = $this->convert_charset($category->label);
                    }
                }

                $points_home = (substr($atom->url, 0, strlen(get_base_url()) + 1) == get_base_url() . '/');

                $feed[] = [
                    'IDENTIFIER' => $this->convert_charset($atom->identifier),
                    'AUTHOR_IDENTIFIER' => ($atom->author === null) ? null : $this->convert_charset($atom->author->identifier),
                    'AUTHOR_DISPLAY_NAME' => ($atom->author === null) ? null : $this->convert_charset($atom->author->displayName),
                    'AUTHOR_PROFILE_URL' => ($atom->author === null) ? null : $atom->author->profileURL,
                    'AUTHOR_PHOTO_URL' => ($atom->author === null) ? null : $atom->author->photoURL,
                    'CATEGORIES' => $categories,
                    '_CATEGORY' => $_category,
                    'CATEGORY' => $category,
                    'PUBLISHED' => $published,
                    '_PUBLISHED' => ($_published === null) ? null : strval($_published),
                    'UPDATED' => $updated,
                    '_UPDATED' => ($_updated === null) ? null : strval($_updated),
                    'TITLE' => $this->convert_charset($atom->title),
                    'SUMMARY' => $this->convert_charset($atom->summary),
                    'CONTENT' => $this->convert_charset($atom->content),
                    'MESSAGE' => $this->convert_charset($message),
                    'ENCLOSURES' => $atom->enclosures,
                    'URL' => $atom->url,
                    'BEST_URL' => $points_home ? null : $atom->url,
                    'POINTS_HOME' => $points_home,
                    'IMAGE_URL' => $image_url,
                ];
            }
        } catch (Exception $e) {
            return do_template('RED_ALERT', ['_GUID' => '9fcebd74147f4724a0cf20642d58a109', 'TEXT' => $e->getMessage()]);
        }

        cms_ini_set('ocproducts.type_strictness', $before_type_strictness);
        cms_ini_set('ocproducts.xss_detect', $before_xss_detect);

        return do_template('BLOCK_MAIN_HYBRIDAUTH_ADMIN_ATOMS', [
            '_GUID' => 'd231298079cbdf9662a366e8479d872e',
            'PROVIDER' => $provider,

            'FEED_IDENTIFIER' => $this->convert_charset($user_profile->identifier),
            'FEED_PROFILE_URL' => $user_profile->profileURL,
            'FEED_PHOTO_URL' => $user_profile->photoURL,
            'FEED_DISPLAY_NAME' => $this->convert_charset($user_profile->displayName),
            'FEED_DESCRIPTION' => $this->convert_charset($user_profile->description),
            'FEED_FIRST_NAME' => $this->convert_charset($user_profile->firstName),
            'FEED_LAST_NAME' => $this->convert_charset($user_profile->lastName),

            'MAX' => strval($max),
            'BLOCK_ID' => $block_id,

            'FEED' => $feed,
        ]);
    }

    protected function convert_charset($str)
    {
        return convert_to_internal_encoding($str, 'utf-8');
    }
}
