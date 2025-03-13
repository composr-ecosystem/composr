<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    disastr
 */

/**
 * Hook class.
 */
class Hook_addon_registry_disastr
{
    /**
     * Get a list of file permissions to set.
     *
     * @param  boolean $runtime Whether to include wildcards represented runtime-created chmoddable files
     * @return array File permissions to set
     */
    public function get_chmod_array(bool $runtime = false) : array
    {
        $ret = [
            'uploads/disastr_addon',
        ];
        if ($runtime) {
            $ret = array_merge($ret, [
                'uploads/disastr_addon/*',
            ]);
        }
        return $ret;
    }

    /**
     * Get the current version of this addon (usually software major, software minor, addon build).
     * Put the comment "// addon_version_auto_update" to the right of the return if you want release tools to automatically update this according to software version and find_addon_effective_md5.
     *
     * @return SHORT_TEXT Version number
     */
    public function get_version() : string
    {
        return '11.0.1'; // addon_version_auto_update 0dbead524e214d19743b7c4fb5112fba
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
        return 'Fun and Games';
    }

    /**
     * Get the addon author.
     *
     * @return string The author
     */
    public function get_author() : string
    {
        return 'Kamen Blaginov';
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
        return 'Discourage your website users from going periods of time without interacting on your site. You can release a number of diseases all at once or one at a time. Disastr comes configured with a number of pre-created viruses and you can add more. There are also Cures and Immunisations for the diseases which can be purchased through the eCommerce. Each disease will cause a member\'s points total to become sick and start going down unless they buy the cure. The cure is usually twice the price of the immunisation. If the user cannot afford the cure they will have to interact more with the site to rebuild up their points total to be able to afford to buy it. All the pre-configured diseases come unreleased and you have the opportunity to choose when they are released and how virulent they are. Users which have been infected will be sent a notification with a link to the cure. Once cured, members can still be re-infected if they have not purchased an Immunisation.

To configure the diseases go to Admin Zone > Setup > Manage Diseases.';
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
            'requires' => [
                'System scheduler',
                'Conversr',
                'points',
                'ecommerce',
                //'core_all_icons',
            ],
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
        return 'themes/default/images/icons/spare/disaster.svg';
    }

    /**
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list() : array
    {
        return [
            'adminzone/pages/modules_custom/admin_disastr.php',
            'data_custom/images/disastr/hazard.jpg',
            'data_custom/images/disastr/hazard.png',
            'data_custom/images/disastr/index.html',
            'lang_custom/EN/disastr.ini',
            'sources_custom/hooks/systems/actionlog/disastr.php',
            'sources_custom/hooks/systems/addon_registry/disastr.php',
            'sources_custom/hooks/systems/cron/disastr.php',
            'sources_custom/hooks/systems/ecommerce/disastr.php',
            'sources_custom/hooks/systems/notifications/got_disease.php',
            'sources_custom/hooks/systems/page_groupings/disastr.php',
            'sources_custom/hooks/systems/privacy/disastr.php',
            'uploads/disastr_addon/index.html',
        ];
    }

    /**
     * Get an array of maps containing predefined diseases.
     *
     * @return array Predefined diseases as database rows
     */
    protected function _predefined_content() : array
    {
        $ret = [];
        $ret[] = ['name' => 'Zombiism', 'image_url' => 'data_custom/images/disastr/hazard.png', 'cure' => 'Zombiism vaccine', 'cure_price' => 100, 'immunisation' => 'Immunise yourself from Zombiism', 'immunisation_price' => 50, 'spread_rate' => 12, 'points_per_spread' => 10, 'last_spread_time' => 0, 'enabled' => 1];
        $ret[] = ['name' => 'A bad case of Hiccups', 'image_url' => 'data_custom/images/disastr/hazard.png', 'cure' => 'Hiccup vaccine', 'cure_price' => 100, 'immunisation' => 'Immunise yourself from the Hiccups', 'immunisation_price' => 50, 'spread_rate' => 12, 'points_per_spread' => 10, 'last_spread_time' => 0, 'enabled' => 1];
        $ret[] = ['name' => 'Vampirism', 'image_url' => 'data_custom/images/disastr/hazard.png', 'cure' => 'Vampirism vaccine', 'cure_price' => 100, 'immunisation' => 'Immunise yourself against Vampirism', 'immunisation_price' => 50, 'spread_rate' => 12, 'points_per_spread' => 10, 'last_spread_time' => 0, 'enabled' => 1];
        $ret[] = ['name' => 'The Flu', 'image_url' => 'data_custom/images/disastr/hazard.png', 'cure' => 'Flu vaccine', 'cure_price' => 100, 'immunisation' => 'Immunise yourself against the Flu', 'immunisation_price' => 50, 'spread_rate' => 12, 'points_per_spread' => 10, 'last_spread_time' => 0, 'enabled' => 1];
        $ret[] = ['name' => 'Lice', 'image_url' => 'data_custom/images/disastr/hazard.png', 'cure' => 'Lice-Away Spray', 'cure_price' => 100, 'immunisation' => 'Lice repellant', 'immunisation_price' => 50, 'spread_rate' => 12, 'points_per_spread' => 10, 'last_spread_time' => 0, 'enabled' => 1];
        $ret[] = ['name' => 'Fleas', 'image_url' => 'data_custom/images/disastr/hazard.png', 'cure' => 'Flea spray', 'cure_price' => 100, 'immunisation' => 'Flea repellant', 'immunisation_price' => 50, 'spread_rate' => 12, 'points_per_spread' => 10, 'last_spread_time' => 0, 'enabled' => 1];
        $ret[] = ['name' => 'Man-Flu', 'image_url' => 'data_custom/images/disastr/hazard.png', 'cure' => 'Lots and lots of TLC', 'cure_price' => 1000, 'immunisation' => 'Anti Man-Flu Serum', 'immunisation_price' => 250, 'spread_rate' => 12, 'points_per_spread' => 100, 'last_spread_time' => 0, 'enabled' => 1];
        return $ret;
    }

    /**
     * Find available predefined content, and what is installed.
     *
     * @return array A map of available predefined content codenames, and details (if installed, and title)
     */
    public function enumerate_predefined_content() : array
    {
        $ret = [];

        $diseases = $this->_predefined_content();
        foreach ($diseases as $disease) {
            $installed = ($GLOBALS['SITE_DB']->query_select_value_if_there('diseases', 'id', ['name' => $disease['name']]) !== null);

            $ret[md5($disease['name'])] = [
                'title' => $disease['name'],
                'description' => new Tempcode(),
                'installed' => $installed,
            ];
        }

        return $ret;
    }

    /**
     * Install predefined content.
     *
     * @param  ?array $content A list of predefined content labels to install (null: all)
     */
    public function install_predefined_content(?array $content = null)
    {
        $diseases = $this->_predefined_content();
        foreach ($diseases as $disease) {
            if ((($content === null) || (in_array(md5($disease['name']), $content))) && (!has_predefined_content('disastr', md5($disease['name'])))) {
                $GLOBALS['SITE_DB']->query_insert('diseases', $disease);
            }
        }
    }

    /**
     * Uninstall predefined content.
     *
     * @param  ?array $content A list of predefined content labels to uninstall (null: all)
     */
    public function uninstall_predefined_content(?array $content = null)
    {
        $diseases = $this->_predefined_content();
        foreach ($diseases as $disease) {
            if ((($content === null) || (in_array(md5($disease['name']), $content))) && (has_predefined_content('disastr', md5($disease['name'])))) {
                $GLOBALS['SITE_DB']->query_delete('diseases', ['name' => $disease['name']]);
            }
        }
    }
}
