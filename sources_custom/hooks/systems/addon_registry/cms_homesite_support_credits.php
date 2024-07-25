<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    cms_homesite_support_credits
 */

/**
 * Hook class.
 */
class Hook_addon_registry_cms_homesite_support_credits
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
     * Get the version of the software this addon is for (used in generating the TAR filename).
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
        return 11.9;
    }

    /**
     * Get the addon category.
     *
     * @return string The category
     */
    public function get_category() : string
    {
        return 'Development';
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
        return 'This addon enables the support for purchasing support credits for professional development services or sponsoring tracker issues.';
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
                'tickets',
                'ecommerce',
                'points',
                'Conversr',
                //'core_all_icons',
                'cms_homesite',
                'cms_homesite_tracker',
            ],
            'recommends' => [
                'stats',
                'MySQL',
            ],
            'conflicts_with' => ['composr_homesite_support_credits'/*LEGACY*/],
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
            'adminzone/pages/minimodules_custom/outstanding_credits.php',
            'adminzone/pages/modules_custom/admin_customers.php',
            'lang_custom/EN/customers.ini',
            'sources_custom/customers.php',
            'sources_custom/hooks/modules/members/customers.php',
            'sources_custom/hooks/systems/actionlog/cms_homesite_support_credits.php',
            'sources_custom/hooks/systems/addon_registry/cms_homesite_support_credits.php',
            'sources_custom/hooks/systems/cns_cpf_filter/customers.php',
            'sources_custom/hooks/systems/config/points_support_credits.php',
            'sources_custom/hooks/systems/config/support_credit_price.php',
            'sources_custom/hooks/systems/config/support_credit_tax_code.php',
            'sources_custom/hooks/systems/config/support_priority_backburner_minutes.php',
            'sources_custom/hooks/systems/config/support_priority_regular_minutes.php',
            'sources_custom/hooks/systems/ecommerce/support_credits.php',
            'sources_custom/hooks/systems/page_groupings/support_credits.php',
            'sources_custom/hooks/systems/points/support_credits__purchase.php',
            'sources_custom/hooks/systems/privacy/cms_homesite_support_credits.php',
            'sources_custom/hooks/systems/symbols/MANTIS_SPONSOR_WEEK_ACTIVITY.php',
            'sources_custom/miniblocks/credit_exps.php',
            'sources_custom/miniblocks/show_credits.php',
            'sources_custom/tickets.php',
            'text_custom/EN/support_credits_terms.txt',
            'themes/default/templates_custom/BLOCK_CREDIT_EXPS_INNER.tpl',
            'themes/default/templates_custom/SHOW_CREDITS_BAR.tpl',
            'themes/default/templates_custom/SUPPORT_CREDITS_OUTSTANDING_SCREEN.tpl',
        ];
    }
}
