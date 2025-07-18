<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.


 NOTE TO PROGRAMMERS:
   Do not edit this file. If you need to make changes, save your changed file to the appropriate *_custom folder
   **** If you ignore this advice, then your website upgrades (e.g. for bug fixes) will likely kill your changes ****

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    core_configuration
 */

/**
 * Hook class.
 */
class Hook_config_imagemagick_path
{
    /**
     * Gets the details relating to the config option.
     *
     * @return ?array The details (null: disabled)
     */
    public function get_details()
    {
        return array(
            'human_name' => 'IMAGEMAGICK_PATH',
            'type' => 'line',
            'category' => 'SERVER',
            'group' => 'ADVANCED',
            'explanation' => 'CONFIG_OPTION_imagemagick_path',
            'shared_hosting_restricted' => '1',
            'list_options' => '',

            'addon' => 'core_configuration',
        );
    }

    /**
     * Gets the default value for the config option.
     *
     * @return ?string The default value (null: option is disabled)
     */
    public function get_default()
    {
        if (strpos(PHP_OS, 'WIN') !== false) {
            $search_dirs = array(
                'C:\\Program Files\\ImageMagick-*',
            );
            $filenames = array(
                'magick.exe', // New name since v7
                'convert.exe', // Dodgy, some other command could potentially be here and not even auto-exit
            );
        } else {
            $search_dirs = array(
                '/usr/local/bin',
                '/usr/bin',
                '/opt/local/bin',
                '/opt/cloudlinux/bin',
            );
            $filenames = array(
                'magick', // New name since v7
                'convert', // Dodgy, some other command could potentially be here and not even auto-exit
            );
        }

        foreach ($search_dirs as $dir) {
            foreach ($filenames as $file) {
                $path = $dir . '/' . $file;
                $_path = @glob($path);
                if (!@empty($_path)) {
                    if (($file != 'magick') || (@strpos(file_get_contents($_path[0]), 'Magick') !== false)) {
                        return $_path[0];
                    }
                }
            }
        }

        return '';
    }
}
