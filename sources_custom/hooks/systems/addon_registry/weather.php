<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    weather
 */

/**
 * Hook class.
 */
class Hook_addon_registry_weather
{
    /**
     * Get a list of file permissions to set.
     *
     * @param  boolean $runtime Whether to include wildcards represented runtime-created chmoddable files
     * @return array File permissions to set
     */
    public function get_chmod_array(bool $runtime = false) : array
    {
        return [];
    }

    /**
     * Get the current version of this addon (usually software major, software minor, addon build).
     * Put the comment "// addon_version_auto_update" to the right of the return if you want release tools to automatically update this according to software version and find_addon_effective_mtime.
     *
     * @return SHORT_TEXT Version number
     */
    public function get_version() : string
    {
        return '11'; // addon_version_auto_update bacc253359bcdeeb87ab3b944fe7407a
    }

    /**
     * Get the minimum required version of the website software needed to use this addon.
     *
     * @return float Minimum required website software version
     */
    public function get_min_cms_version() : float
    {
        return 11.0;
    }

    /**
     * Get the maximum compatible version of the website software to use this addon.
     *
     * @return ?float Maximum compatible website software version (null: no maximum version currently)
     */
    public function get_max_cms_version() : ?float
    {
        return 11.9;
    }

    /**
     * Get the addon category.
     *
     * @return string The category
     */
    public function get_category() : string
    {
        return 'Information Display';
    }

    /**
     * Get the addon author.
     *
     * @return string The author
     */
    public function get_author() : string
    {
        return 'Chris Graham';
    }

    /**
     * Find other authors.
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution() : array
    {
        return [];
    }

    /**
     * Get the addon licence (one-line summary only).
     *
     * @return string The licence
     */
    public function get_licence() : string
    {
        return 'Licensed on the same terms as ' . brand_name();
    }

    /**
     * Get the description of the addon.
     *
     * @return string Description of the addon
     */
    public function get_description() : string
    {
        return 'A weather block. Different backends are supported, with a default implementation of OpenWeatherMap provided.

You will need to set up an OpenWeatherMap API key at Admin Zone > Setup > Configuration > Composr API options > Weather report.';
    }

    /**
     * Get a list of tutorials that apply to this addon.
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials() : array
    {
        return [];
    }

    /**
     * Get a mapping of dependency types.
     *
     * @return array A structure specifying dependency information
     */
    public function get_dependencies() : array
    {
        return [
            'requires' => [],
            'recommends' => [],
            'conflicts_with' => [],
        ];
    }

    /**
     * Explicitly say which icon should be used.
     *
     * @return URLPATH Icon
     */
    public function get_default_icon() : string
    {
        return 'themes/default/images/icons/admin/component.svg';
    }

    /**
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list() : array
    {
        return [
            'lang_custom/EN/weather.ini',
            'sources_custom/blocks/side_weather.php',
            'sources_custom/hooks/systems/addon_registry/weather.php',
            'sources_custom/hooks/systems/config/openweathermap_api_key.php',
            'sources_custom/hooks/systems/health_checks/weather.php',
            'sources_custom/hooks/systems/weather/.htaccess',
            'sources_custom/hooks/systems/weather/index.html',
            'sources_custom/hooks/systems/weather/openweathermap.php',
            'sources_custom/weather.php',
            'themes/default/templates_custom/BLOCK_SIDE_WEATHER.tpl',
        ];
    }

    /**
     * Uninstall the addon.
     */
    public function uninstall()
    {
        $GLOBALS['SITE_DB']->drop_table_if_exists('cached_weather_codes');
    }

    /**
     * Install the addon.
     *
     * @param  ?float $upgrade_major_minor From what major/minor version we are upgrading (null: new install)
     * @param  ?integer $upgrade_patch From what patch version of $upgrade_major_minor we are upgrading (null: new install)
     */
    public function install(?float $upgrade_major_minor = null, ?int $upgrade_patch = null)
    {
        if ($upgrade_major_minor === null) {
            $GLOBALS['SITE_DB']->create_table('cached_weather_codes', [
                'id' => '*AUTO',
                'w_string' => 'SHORT_TEXT',
                'w_code' => 'INTEGER',
            ]);
        }
    }
}
