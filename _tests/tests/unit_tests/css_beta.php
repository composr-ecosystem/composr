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

// Pass &debug=1 for extra checks that would not be expected to ever consistently pass

/**
 * Composr test case class (unit testing).
 */
class css_beta_test_set extends cms_test_case
{
    public function testCorrectSetAsBeta()
    {
        require_code('themes2');
        require_code('files2');

        $themes = find_all_themes();
        foreach (array_keys($themes) as $theme) {
            // Exceptions
            if (in_array($theme, [
                '_unnamed_',
                '_testing_',
            ])) {
                continue;
            }

            if (($this->only !== null) && ($this->only != $theme)) {
                continue;
            }

            $directories = [
                 get_file_base() . '/themes/' . $theme . '/css_custom',
                 get_file_base() . '/themes/' . $theme . '/css',
            ];

            $in_beta = [
                'user-select:',
                'text-size-adjust:',
                'touch-action:',
                'text-decoration-',
                'font-kerning:',
                'hyphens:',

                // For specific properties
                'display: flex',
                'display: inline-flex',
            ];

            foreach ($directories as $dir) {
                $files = get_directory_contents($dir);
                foreach ($files as $e) {
                    if (in_array($e, [ // Exceptions
                        'confluence.css',
                        'mediaelementplayer.css',
                        'widget_select2.css',
                        'widget_color.css',
                        'jquery_ui.css',
                    ])) {
                        continue;
                    }

                    if (substr($e, -4) == '.css') {
                        $c = cms_file_get_contents_safe($dir . '/' . $e, FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT);

                        $matches = [];
                        $found = preg_match_all('#\{\$BETA_CSS_PROPERTY,(.*)\}#i', $c, $matches);
                        for ($i = 0; $i < $found; $i++) {
                            $property_line = $matches[1][$i];

                            $is_in_beta = false;
                            foreach ($in_beta as $_property) {
                                if (substr($property_line, 0, strlen($_property)) == $_property) {
                                    $is_in_beta = true;
                                }
                            }

                            $this->assertTrue($is_in_beta, 'Property ' . $property_line . ' should *not* be defined as beta in ' . $e . ' for theme ' . $theme);
                        }

                        foreach ($in_beta as $property) {
                            $is_not_as_beta = (strpos($c, "\t" . $property) !== false) || (strpos($c, ' ' . $property) !== false);
                            $this->assertTrue(!$is_not_as_beta, 'Property ' . $property . ' should be defined as beta in ' . $e . ' for theme ' . $theme);
                        }
                    }
                }
            }
        }
    }
}
