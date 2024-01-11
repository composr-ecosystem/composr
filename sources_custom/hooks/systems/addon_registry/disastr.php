<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
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
     * Get the version of Composr this addon is for (used in generating the TAR filename).
     *
     * @return float Version number
     */
    public function get_version() : float
    {
        return cms_version_number();
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
        return null;
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
        return 'Licensed on the same terms as Composr';
    }

    /**
     * Get the description of the addon.
     *
     * @return string Description of the addon
     */
    public function get_description() : string
    {
        return 'Encourage your website users to interact more and increase their activity. You can release a number of diseases all at once or one at a time. Disastr comes configured with a number of pre-created viruses and you can add more. There are also Cures and Immunisations for the diseases which can be purchased through the eCommerce. Each disease will cause a member\'s points total to become sick and start going down unless they buy the cure. The cure is usually twice the price of the immunisation. If the user cannot afford the cure they will have to interact more with the site to rebuild up their points total to be able to afford to buy it. All the pre-configured diseases come unreleased and you have the opportunity to choose when they are released and how virulent they are. Users which have been infected will be sent a notification with a link to the cure. Once cured, members can still be re-infected if they have not purchased an Immunisation. The diseases are spread via the friend lists in Composr.

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
            'sources_custom/hooks/systems/addon_registry/disastr.php',
            'sources_custom/hooks/systems/privacy/disastr.php',
            'sources_custom/hooks/systems/notifications/got_disease.php',
            'adminzone/pages/modules_custom/admin_disastr.php',
            'lang_custom/EN/disastr.ini',
            'sources_custom/hooks/systems/ecommerce/disastr.php',
            'sources_custom/hooks/systems/cron/disastr.php',
            'sources_custom/hooks/systems/page_groupings/disastr.php',
            'uploads/disastr_addon/index.html',
            'data_custom/images/disastr/hazard.jpg', // LEGACY
            'data_custom/images/disastr/hazard.png',
            'data_custom/images/disastr/index.html',
            'sources_custom/hooks/systems/actionlog/disastr.php',
        ];
    }
}
