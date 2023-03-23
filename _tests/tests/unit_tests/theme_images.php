<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

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

        cms_extend_time_limit(TIME_LIMIT_EXTEND__SLOW);

        require_code('themes2');
        require_code('images');
        require_code('files2');
    }

    public function testIconsSquare()
    {
        if (($this->only !== null) && ($this->only != 'testIconsSquare')) {
            return;
        }

        $themes = find_all_themes();
        foreach (array_keys($themes) as $theme) {
            // Exceptions
            if (in_array($theme, [
                '_unnamed_',
                '_testing_',
            ])) {
                continue;
            }

            $dirs = [
                get_file_base() . '/themes/' . $theme . '/images/icons',
                get_file_base() . '/themes/' . $theme . '/images/' . fallback_lang() . '/icons',
                get_file_base() . '/themes/' . $theme . '/images_custom/icons',
                get_file_base() . '/themes/' . $theme . '/images_custom/' . fallback_lang() . '/icons',
            ];
            $files = [];
            foreach ($dirs as $dir) {
                $files = array_merge($files, get_directory_contents($dir, $dir));
            }

            $files2 = [];
            foreach ($files as $path) {
                if ($path == get_file_base() . '/themes/default/images/icons/sprite.svg') {
                    continue;
                }

                if (substr($path, -4) == '.svg') {
                    $c = cms_file_get_contents_safe($path, FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT);
                    $matches = [];
                    $ok = (preg_match('#width="(\d+)px"\s+height="(\d+)px"#', $c, $matches) != 0);
                    $this->assertTrue($ok, 'Cannot find SVG width/height in ' . $path);
                    if (!$ok) {
                        continue;
                    }
                    $width = intval($matches[1]);
                    $height = intval($matches[2]);
                } elseif (is_image($path, IMAGE_CRITERIA_GD_READ, true)) {
                    list($width, $height) = cms_getimagesize($path);
                } else {
                    continue;
                }

                $this->assertTrue($width === $height, 'Non-square icon, ' . $path);
            }
        }
    }

    public function testSVGQuality()
    {
        if (($this->only !== null) && ($this->only != 'testSVGQuality')) {
            return;
        }

        require_code('files2');
        $files = get_directory_contents(get_file_base() . '/themes/default/', get_file_base() . '/themes/default', 0, true, true, ['svg']);

        foreach ($files as $path) {
            $c = cms_file_get_contents_safe($path, FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT | FILE_READ_BOM);
            $_c = $c;

            $this->assertTrue(strpos($c, '<image') === false, 'Raster data in ' . $path);

            $bad_patterns = [
                '<!-- Generator.*-->',
                '\s+xml:space="preserve"',
                '\s+enable-background="[^"]*"',
                '\s+xmlns:a="http://ns.adobe.com/AdobeSVGViewerExtensions/3.0/"',
                '<[^<>]* display="none"[^<>]*/>\n',
            ];
            foreach ($bad_patterns as $bad_pattern) {
                if (preg_match('#' . $bad_pattern . '#', $c) != 0) {
                    $this->assertTrue(false, 'Found: ' . $bad_pattern . ' in ' . $path . '; fix with &auto_fix=1');
                    $c = preg_replace('#' . $bad_pattern . '#', '', $c);
                }
            }
            if ($c != $_c) {
                if (get_param_integer('auto_fix', 0) == 1) {
                    cms_file_put_contents_safe($path, $c, FILE_WRITE_BOM);
                }
            }
        }
    }

    public function testDuplicateThemeImages()
    {
        if (($this->only !== null) && ($this->only != 'testDuplicateThemeImages')) {
            return;
        }

        $themes = find_all_themes();
        foreach (array_keys($themes) as $theme) {
            // Exceptions
            if (in_array($theme, [
                '_unnamed_',
                '_testing_',
            ])) {
                continue;
            }

            $dirs = [
                get_file_base() . '/themes/' . $theme . '/images_custom',
                get_file_base() . '/themes/' . $theme . '/images_custom/' . fallback_lang(),
            ];
            $files = [];
            foreach ($dirs as $dir) {
                $files = array_merge($files, get_directory_contents($dir));
            }

            $files2 = [];
            foreach ($files as $path) {
                $trimmed = preg_replace('#\.\w+$#', '', $path);
                $this->assertTrue(!isset($files2[$trimmed]), 'Duplicate theme image ' . $trimmed . ' in theme ' . $theme);
                $files2[$trimmed] = true;
            }
        }
    }

    public function testBrokenReferences()
    {
        if (($this->only !== null) && ($this->only != 'testBrokenReferences')) {
            return;
        }

        // Find default images
        $default_images_referenced = []; // true means referenced and exists, false means referenced and is not yet known to exist
        $default_images_there = $this->get_theme_images('default');

        // Get theme list, but 'default' must come first
        $themes = find_all_themes();
        unset($themes['default']);
        $themes = array_merge(['default' => null], $themes);

        // Go through each theme
        foreach (array_keys($themes) as $theme) {
            // Exceptions
            if (in_array($theme, [
                '_unnamed_',
                '_testing_',
            ])) {
                continue;
            }

            $non_css_contents = '';
            $images_referenced = $default_images_referenced;
            $images_there = $default_images_there;

            if ($theme == 'default') {
                $directories = [
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
                ];
            } else {
                $directories = array_merge($directories, [
                    get_file_base() . '/themes/' . $theme . '/css_custom',
                    get_file_base() . '/themes/' . $theme . '/css',
                    get_file_base() . '/themes/' . $theme . '/templates_custom',
                    get_file_base() . '/themes/' . $theme . '/templates',
                    get_file_base() . '/themes/' . $theme . '/javascript_custom',
                    get_file_base() . '/themes/' . $theme . '/javascript',
                    get_file_base() . '/themes/' . $theme . '/xml_custom',
                    get_file_base() . '/themes/' . $theme . '/xml',
                ]);

                $images_there = array_merge($images_there, $this->get_theme_images($theme));
            }

            $db_reference_sources = [
                'f_emoticons' => 'e_theme_img_code',
                'f_groups' => 'g_rank_image',
                'calendar_types' => 't_logo',
                'news_categories' => 'nc_img',
            ];
            foreach ($db_reference_sources as $table => $field) {
                $db = get_db_for($table);
                $_images_referenced = $db->query_select($table, ['DISTINCT ' . $field]);
                foreach ($_images_referenced as $_image_referenced) {
                    if (isset($images_there[$_image_referenced[$field]])) {
                        $images_referenced[$_image_referenced[$field]] = true;
                        $images_there[$_image_referenced[$field]] = true;
                    }
                }
            }

            foreach ($directories as $dir) {
                $dh = @opendir($dir);
                if ($dh !== false) {
                    while (($file = readdir($dh)) !== false) {
                        $is_css_file = (substr($file, -4) == '.css');
                        $is_tpl_file = (substr($file, -4) == '.tpl') || (substr($file, -4) == '.xml') || (substr($file, -3) == '.js');
                        $is_comcode_page = ((substr($file, -4) == '.txt') && ((count($themes) < 5) || (substr($file, 0, strlen($theme . '__')) == $theme . '__')));

                        if ($is_css_file || $is_tpl_file || $is_comcode_page) {
                            $c = cms_file_get_contents_safe($dir . '/' . $file, FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT);

                            if (strpos($c, '{$IMG') === false) {
                                continue;
                            }

                            // Find referenced images
                            $matches = [];
                            $num_matches = preg_match_all('#\{\$(IMG|IMG_INLINE)[^\w\{\},]*,([^{},]+)\}#', $c, $matches);
                            for ($i = 0; $i < $num_matches; $i++) {
                                $images_referenced[$matches[2][$i]] = isset($images_there[$matches[2][$i]]);
                            }

                            // See if any of the theme images were used
                            foreach ($images_there as $image => $is_there) {
                                if (!$is_there) {
                                    if (preg_match('#\{\$(IMG|IMG_INLINE)[^\w\{\},]*,' . preg_quote($image, '#') . '\}#', $c) != 0) {
                                        $images_there[$image] = true;
                                    }
                                }
                            }
                        }
                    }
                    closedir($dh);
                }
            }

            foreach ($images_there as $image => $is_used) {
                // Exceptions
                if (in_array($image, ['icons_sprite', 'icons_monochrome_sprite', 'cns_emoticons/none', 'audio_thumb', 'button1', 'button2', 'na', 'under_construction_animated', '-logo', 'standalone_logo', 'maps/star_highlight', 'google_translate'])) { // Used dynamically
                    continue;
                }
                if (in_array($image, ['icons/calendar/activity', 'icons/calendar/duty', 'icons/calendar/festival', 'icons/calendar/rss'])) { // Not used by default but useful
                    continue;
                }
                if (in_array($image, ['tracker/credit', 'icons/calendar/booking', 'youtube_channel_integration/youtube_channel_integration_icon'])) { // Addon files not used by default but useful
                    continue;
                }
                if (in_array($image, ['cns_rank_images/0', 'cns_rank_images/1', 'cns_rank_images/2', 'cns_rank_images/3', 'cns_rank_images/4'])) { // Rank settings may be changed
                    continue;
                }
                if (preg_match('#^([12]x/)?(chatcode_editor|results|cns_post_map|cns_general|progress_indicator|comcode_editor|realtime_rain|logo|cns_default_avatars|flags|flags_large|icons/spare|icons|icons_monochrome|twitter_feed)/#', $image) != 0) { // Dynamic choices / dynamic sets
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

            foreach ($images_referenced as $image => $is_existent) {
                $this->assertTrue($is_existent, 'Missing theme image in theme ' . $theme . ': ' . $image);
            }

            if ($theme == 'default') {
                $default_images_referenced = $images_referenced;
                $default_images_there = $images_there;
            }
        }
    }

    protected function get_theme_images($theme)
    {
        $dirs = [
            get_file_base() . '/themes/' . $theme . '/images',
            get_file_base() . '/themes/' . $theme . '/images_custom',
            get_file_base() . '/themes/' . $theme . '/images/EN',
        ];
        $images = [];
        foreach ($dirs as $dir) {
            $files = get_directory_contents($dir);
            foreach ($files as $path) {
                if (substr($path, -8) == '.gif.png') {
                    continue; // This is an APNG version of a gif, not a separate theme image
                }

                if (is_image($path, IMAGE_CRITERIA_WEBSAFE, true)) {
                    $img_code = substr($path, 0, strlen($path) - strlen('.' . get_file_extension($path)));
                    $images[$img_code] = false; // false means not yet found as used
                }
            }
        }
        $images['favicon'] = false; // Gets filtered by ignore rules

        return $images;
    }
}
