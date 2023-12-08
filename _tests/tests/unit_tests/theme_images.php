<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class theme_images_test_set extends cms_test_case
{
    public function setUp()
    {
        parent::setUp();

        require_code('themes2');
        require_code('images');
        require_code('files2');
    }

    public function testDuplicateThemeImages()
    {
        $themes = find_all_themes();
        foreach ($themes as $theme) {
            $dirs = array(
                get_file_base() . '/themes/' . $theme . '/images_custom',
                get_file_base() . '/themes/' . $theme . '/images_custom/' . fallback_lang(),
            );
            $files = array();
            foreach ($dirs as $dir) {
                $files = array_merge($files, get_directory_contents($dir));
            }

            $files2 = array();
            foreach ($files as $file) {
                $trimmed = preg_replace('#\.\w+$#', '', $file);
                $this->assertTrue(!isset($files2[$trimmed]), 'Duplicate theme image ' . $trimmed . ' in theme ' . $theme);
                $files2[$trimmed] = true;
            }
        }
    }

    public function testBrokenReferences()
    {
        // Find default images
        $default_images = $this->getThemeImages('default');

        // Go through each theme
        $themes = find_all_themes();
        foreach (array_keys($themes) as $theme) {
            if ($theme == '_unnamed_') {
                continue;
            }

            $directories = array(
                 get_file_base() . '/themes/default/css_custom',
                 get_file_base() . '/themes/default/css',
                 get_file_base() . '/themes/default/templates_custom',
                 get_file_base() . '/themes/default/templates',
                 get_file_base() . '/themes/default/javascript_custom',
                 get_file_base() . '/themes/default/javascript',
                 get_file_base() . '/themes/default/xml_custom',
                 get_file_base() . '/themes/default/xml',
                 get_file_base() . '/site/pages/comcode_custom/EN',
                 get_file_base() . '/pages/comcode_custom/EN',
            );
            if ($theme != 'default') {
                $directories = array_merge($directories, array(
                    get_file_base() . '/themes/' . $theme . '/css_custom',
                    get_file_base() . '/themes/' . $theme . '/css',
                    get_file_base() . '/themes/' . $theme . '/templates_custom',
                    get_file_base() . '/themes/' . $theme . '/templates',
                    get_file_base() . '/themes/' . $theme . '/javascript_custom',
                    get_file_base() . '/themes/' . $theme . '/javascript',
                    get_file_base() . '/themes/' . $theme . '/xml_custom',
                    get_file_base() . '/themes/' . $theme . '/xml',
                ));
            }

            $images_there = array_merge($default_images, $this->getThemeImages($theme));

            $images_referenced = array(); // true means referenced and exists, false means referenced and is not yet known to exist
            $non_css_contents = '';

            $db_reference_sources = array(
                'f_emoticons' => 'e_theme_img_code',
                'f_groups' => 'g_rank_image',
                'calendar_types' => 't_logo',
                'news_categories' => 'nc_img',
            );
            foreach ($db_reference_sources as $table => $field) {
                if (substr($table, 0, 2) == 'f_') {
                    $db = $GLOBALS['FORUM_DB'];
                } else {
                    $db = $GLOBALS['SITE_DB'];
                }
                $_images_referenced = $db->query_select($table, array('DISTINCT ' . $field));
                foreach ($_images_referenced as $_image_referenced) {
                    if (isset($images_there[$_image_referenced[$field]])) {
                        $images_referenced[$_image_referenced[$field]] = true;
                        $images_there[$_image_referenced[$field]] = true;
                    }
                }
            }

            foreach ($directories as $dir) {
                $dh = opendir($dir);
                while (($f = readdir($dh)) !== false) {
                    $is_css_file = (substr($f, -4) == '.css');
                    $is_tpl_file = (substr($f, -4) == '.tpl') || (substr($f, -3) == '.js');
                    $is_comcode_page = ((substr($f, -4) == '.txt') && ((count($themes) < 5) || (substr($f, 0, strlen($theme . '__')) == $theme . '__')));

                    if ($is_css_file || $is_tpl_file || $is_comcode_page) {
                        $contents = file_get_contents($dir . '/' . $f);

                        // Find referenced images
                        $matches = array();
                        $num_matches = preg_match_all('#\{\$(IMG|IMG_INLINE)[^\w,]*,([^{},]+)\}#', $contents, $matches);
                        for ($i = 0; $i < $num_matches; $i++) {
                            $images_referenced[$matches[2][$i]] = isset($images_there[$matches[2][$i]]);
                        }

                        // See if any of the theme images were used
                        foreach ($images_there as $image => $is_there) {
                            if (!$is_there) {
                                if (preg_match('#\{\$(IMG|IMG_INLINE)[^\w,]*,' . preg_quote($image, '#') . '\}#', $contents) != 0) {
                                    $images_there[$image] = true;
                                }
                            }
                        }
                    }
                }
                closedir($dh);
            }

            foreach ($images_there as $image => $is_used) {
                // Exceptions
                if (in_array($image, array('cns_emoticons/none', 'audio_thumb', 'button1', 'button2', 'na', 'under_construction_animated', '-logo', 'help_small', 'standalone_logo', 'star_highlight'))) { // Used dynamically
                    continue;
                }
                if (in_array($image, array('calendar/activity', 'calendar/duty', 'calendar/festival', 'calendar/rss'))) { // Not used by default but useful
                    continue;
                }
                if (in_array($image, array('tracker/credit', 'calendar/booking', 'youtube_channel_integration/youtube_channel_integration_icon'))) { // Addon files not used by default but useful
                    continue;
                }
                if (preg_match('#^([12]x/)?(chatcodeeditor|results|cns_post_map|cns_general|progress_indicator|comcodeeditor|realtime_rain|logo|cns_default_avatars|flags|tutorial_icons|icons|twitter_feed)/#', $image) != 0) { // Dynamic choices / dynamic sets
                    continue;
                }
                if (preg_match('#^calendar/priority_#', $image) != 0) { // Dynamic set
                    continue;
                }
                if (preg_match('#^[A-Z]{2,4}/#', $image) != 0) { // Language-overrides
                    continue;
                }

                $this->assertTrue($is_used || (strpos(find_theme_image($image), '_custom') !== false), 'Unused theme image in theme ' . $theme . ': ' . $image);
            }

            foreach ($images_referenced as $image => $is_used) {
                $this->assertTrue($is_used, 'Missing theme image in theme ' . $theme . ': ' . $image);
            }
        }
    }

    protected function getThemeImages($theme)
    {
        $dirs = array(
            get_file_base() . '/themes/' . $theme . '/images',
            get_file_base() . '/themes/' . $theme . '/images_custom',
            get_file_base() . '/themes/' . $theme . '/images/EN',
        );
        $images = array();
        foreach ($dirs as $dir) {
            $files = get_directory_contents($dir);
            foreach ($files as $f) {
                if (is_image($f) || substr($f, -4) == '.svg' || substr($f, -4) == '.ico') {
                    $img_code = substr($f, 0, strlen($f) - strlen('.' . get_file_extension($f)));
                    $images[$img_code] = false; // false means not yet found as used
                }
            }
        }
        $images['favicon'] = false; // Gets filtered by ignore rules

        return $images;
    }
}
