<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    testing_platform
 */

// Pass &debug=1 for extra checks that would not be expected to ever consistently pass

/**
 * Composr test case class (unit testing).
 */
class themewizard_colours_test_set extends cms_test_case
{
    public function testVariableCorrectness()
    {
        if (!addon_installed('themewizard')) {
            $this->assertTrue(false, 'Test only works with the themewizard addon.');
            return;
        }

        require_code('files2');

        $colours = [];
        $variables = [];
        $referenced_colours = [];

        $directories = [
             get_file_base() . '/themes/default/css_custom',
             get_file_base() . '/themes/default/css',
        ];

        $matches = [];

        // Some are just used for image generation
        $c = file_get_contents(get_file_base() . '/sources/themewizard.php');
        $needed = [];
        preg_match('#(\$needed = \[.*\];)#', $c, $matches);
        eval($matches[1]);
        foreach ($needed as $variable) {
            $referenced_colours[$variable] = true;
        }

        foreach ($directories as $dir) {
            $files = get_directory_contents($dir);
            foreach ($files as $e) {
                if (substr($e, -4) == '.css') {
                    $c = cms_file_get_contents_safe($dir . '/' . $e, FILE_READ_LOCK | FILE_READ_UNIXIFIED_TEXT);

                    $found = preg_match_all('#\{\$THEMEWIZARD_COLOR,\#\w+,([\w\.]+),#i', $c, $matches);
                    for ($i = 0; $i < $found; $i++) {
                        $colour = $matches[1][$i];
                        $this->assertTrue(!isset($colours[$colour]), 'Colour double defined: ' . $colour);
                        $colours[$colour] = true;
                    }

                    $found = preg_match_all('#\{\$SET,([\w\.]+),#i', $c, $matches);
                    for ($i = 0; $i < $found; $i++) {
                        $variable = $matches[1][$i];
                        $variables[$variable] = true;
                    }

                    $found = preg_match_all('#\{\$GET,([\w\.]+)\}#i', $c, $matches);
                    for ($i = 0; $i < $found; $i++) {
                        $referenced_colour = $matches[1][$i];
                        $referenced_colours[$referenced_colour] = true;
                    }
                }
            }
        }

        foreach (array_keys($referenced_colours) as $referenced_colour) {
            $this->assertTrue(isset($colours[$referenced_colour]) || isset($variables[$referenced_colour]), 'Colour not found: ' . $referenced_colour);
            unset($colours[$referenced_colour]);
        }

        foreach (array_keys($colours) as $colour) {
            // Exceptions
            if (in_array($colour, [
                'seed_contrast_low',
                'seed_contrast_medium',
            ])) {
                continue;
            }

            $this->assertTrue(false, 'Colour not used: ' . $colour);
        }
    }
}
