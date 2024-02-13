<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    composr_mobile_sdk
 */

// Fixup SCRIPT_FILENAME potentially being missing
$_SERVER['SCRIPT_FILENAME'] = __FILE__;

// Find Composr base directory, and chdir into it
global $FILE_BASE, $RELATIVE_PATH;
$FILE_BASE = (strpos(__FILE__, './') === false) ? __FILE__ : realpath(__FILE__);
if (substr($FILE_BASE, -4) == '.php') {
    $a = strrpos($FILE_BASE, '/');
    if ($a === false) {
        $a = 0;
    }
    $b = strrpos($FILE_BASE, '\\');
    if ($b === false) {
        $b = 0;
    }
    $FILE_BASE = dirname($FILE_BASE);
}
if (!is_file($FILE_BASE . '/sources/global.php')) {
    $a = strrpos($FILE_BASE, '/');
    if ($a === false) {
        $a = 0;
    }
    $b = strrpos($FILE_BASE, '\\');
    if ($b === false) {
        $b = 0;
    }
    $RELATIVE_PATH = basename($FILE_BASE);
    $FILE_BASE = dirname($FILE_BASE);
} else {
    $RELATIVE_PATH = '';
}
@chdir($FILE_BASE);

global $FORCE_INVISIBLE_GUEST;
$FORCE_INVISIBLE_GUEST = false;
global $EXTERNAL_CALL;
$EXTERNAL_CALL = false;
if (!is_file($FILE_BASE . '/sources/global.php')) {
    exit('<!DOCTYPE html>' . "\n" . '<html lang="EN"><head><title>Critical startup error</title></head><body><h1>Composr startup error</h1><p>The second most basic Composr startup file, sources/global.php, could not be located. This is almost always due to an incomplete upload of the Composr system, so please check all files are uploaded correctly.</p><p>Once all Composr files are in place, Composr must actually be installed by running the installer. You must be seeing this message either because your system has become corrupt since installation, or because you have uploaded some but not all files from our manual installer package: the quick installer is easier, so you might consider using that instead.</p><p>The core developers maintain full documentation for all procedures and tools, especially those for installation. These may be found on the <a href="https://compo.sr">Composr website</a>. If you are unable to easily solve this problem, we may be contacted from our website and can help resolve it for you.</p><hr /><p style="font-size: 0.8em">Composr is a website engine created by Christopher Graham.</p></body></html>');
}
require($FILE_BASE . '/sources/global.php');

if (!addon_installed('composr_mobile_sdk')) {
    warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('composr_mobile_sdk')));
}

header('X-Robots-Tag: noindex');

// Bootstrap tools
$ob = new Composr_mobile_sdk_tools();
$ob->run();

/**
 * Composr Mobile SDK tools.
 */
class Composr_mobile_sdk_tools
{
    /**
     * Composr Mobile SDK build tools script.
     */
    public function run()
    {
        // Check running on the command line
        if (!is_cli()) {
            header('Content-Type: text/plain; charset=' . get_charset());
            exit('This script must be run from the command line, php data_custom/composr_mobile_sdk_build.php <toolname> <params...>' . "\n");
        }

        if (!isset($_SERVER['argv'][1])) {
            exit('Missing toolname parameter' . "\n");
        }

        $toolname = $_SERVER['argv'][1];
        switch ($toolname) {
            case 'images':
                $this->images();
                break;

            case 'language':
                $this->language();
                break;

            default:
                exit('Tool name not recognised, must be images or language' . "\n");
        }
    }

    /**
     * Images tool.
     */
    private function images()
    {
        $theme = isset($_SERVER['argv'][2]) ? $_SERVER['argv'][2] : $GLOBALS['FORUM_DRIVER']->get_theme('');
        $lang = isset($_SERVER['argv'][3]) ? $_SERVER['argv'][3] : get_site_default_lang();

        $out_dir = get_file_base() . '/exports/composr_mobile_sdk/image_assets';

        if (!file_exists(get_file_base() . '/themes/' . $theme)) {
            exit('Theme ' . $theme . ' does not exist' . "\n");
        }

        $theme_images = $GLOBALS['SITE_DB']->query_select('theme_images', ['id', 'url'], ['theme' => $theme, 'lang' => $lang]);
        foreach ($theme_images as $theme_image) {
            $ext = get_file_extension($theme_image['url']);
            $file = basename($theme_image['id']) . '.' . $ext;
            @mkdir($out_dir . '/' . dirname($theme_image['id']), 0777, true);
            @copy(get_file_base() . '/' . $theme_image['url'], $out_dir . '/' . dirname($theme_image['id']) . '/' . $file);
        }

        echo 'FINISHED' . "\n";
    }

    /**
     * Language tool.
     */
    private function language()
    {
        if (!isset($_SERVER['argv'][2])) {
            exit('At least one language file must be specified' . "\n");
        }

        $lang = end($_SERVER['argv']);
        if (!file_exists(get_file_base() . '/lang_custom/' . $lang)) {
            $lang = get_site_default_lang();
        }

        $out_dir = get_file_base() . '/exports/composr_mobile_sdk';

        for ($i = 2; $i < count($_SERVER['argv']) - 1; $i++) {
            $file = $_SERVER['argv'][$i];

            $ini_path = get_file_base() . '/lang_custom/' . $lang . '/' . $file . '.ini';
            if (file_exists($ini_path)) {
                echo 'Processing ' . $ini_path . "\n";
            } else {
                $ini_path = get_file_base() . '/lang/' . $lang . '/' . $file . '.ini';
                if (file_exists($ini_path)) {
                    echo 'Processing ' . $ini_path . "\n";
                } else {
                    echo $file . ' language file not found for ' . $lang . ' pack' . "\n";
                    continue;
                }
            }

            $ios_path = $out_dir . '/' . $file . '.strings';
            $ios_file = fopen($ios_path, 'wb');
            flock($ios_file, LOCK_EX);
            $android_path = $out_dir . '/' . $file . '.xml';
            $android_file = fopen($android_path, 'wb');
            flock($android_file, LOCK_EX);

            fwrite($android_file, '<resources>' . "\n");

            $processing = false;
            $lines = cms_file_safe($ini_path);
            foreach ($lines as $line) {
                if (rtrim($line) == '[strings]') {
                    $processing = true;
                } elseif (substr($line, 0, 1) == '[') {
                    $processing = false;
                }
                if ($processing) {
                    $parts = explode('=', rtrim($line), 2);
                    if (count($parts) == 2) {
                        $k = $parts[0];
                        $s = $parts[1];

                        $x = preg_replace('#\{\d+\}#', '$@', $s);
                        $out_line = '"' . $k . '" = "' . $x . '";' . "\n";
                        fwrite($ios_file, $out_line);

                        $x = preg_replace('#\{(\d+)\}#', '%${1}$', $s);
                        $out_line = '<string name="' . $k . '">' . htmlentities($x) . '</string>' . "\n";
                        fwrite($android_file, $out_line);
                    }
                }
            }

            fwrite($android_file, '</resources>' . "\n");

            flock(($ios_file), LOCK_UN);
            fclose($ios_file);
            flock($android_file, LOCK_UN);
            fclose($android_file);
        }

        echo 'FINISHED' . "\n";
    }
}
