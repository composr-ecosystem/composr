<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2020

 See text/EN/licence.txt for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    bankr
 */

/**
 * Hook class.
 */
class Hook_addon_registry_bankr
{
    /**
     * Get a list of file permissions to set.
     *
     * @param  boolean $runtime Whether to include wildcards represented runtime-created chmoddable files
     * @return array File permissions to set
     */
    public function get_chmod_array($runtime = false)
    {
        return [];
    }

    /**
     * Get the version of Composr this addon is for.
     *
     * @return float Version number
     */
    public function get_version()
    {
        return cms_version_number();
    }

    /**
     * Get the addon category.
     *
     * @return string The category
     */
    public function get_category()
    {
        return 'Fun and Games';
    }

    /**
     * Get the addon author.
     *
     * @return string The author
     */
    public function get_author()
    {
        return 'Kamen Blaginov';
    }

    /**
     * Find other authors.
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution()
    {
        return [];
    }

    /**
     * Get the addon licence (one-line summary only).
     *
     * @return string The licence
     */
    public function get_licence()
    {
        return 'Licensed on the same terms as Composr';
    }

    /**
     * Get the description of the addon.
     *
     * @return string Description of the addon
     */
    public function get_description()
    {
        return 'Members can deposit some of their hard earned points (but not the gift points) into a "bank account" and extract them again only after a month. The administrator can set the interest rate, that users will get with their points back at the end of the account term. To set the Interest rate go to Admin Zone > Setup > Manage eCommerce inventory and click "edit your eCommerce configuration" and change the interest rate to the level you would like. To deposit points go to the purchasing module and click on the bank option and choose how much you would like to deposit for a month. The interest paid out will be at the level it was at when the points were deposited.';
    }

    /**
     * Get a list of tutorials that apply to this addon.
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials()
    {
        return [];
    }

    /**
     * Get a mapping of dependency types.
     *
     * @return array File permissions to set
     */
    public function get_dependencies()
    {
        return [
            'requires' => [
                'System scheduler',
                'points',
                'ecommerce',
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
    public function get_default_icon()
    {
        return 'themes/default/images/icons/admin/component.svg';
    }

    /**
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return [
            'sources_custom/hooks/systems/addon_registry/bankr.php',
            'sources_custom/hooks/systems/privacy/bankr.php',
            'sources_custom/hooks/systems/ecommerce/bank.php',
            'lang_custom/EN/bank.ini',
            'sources_custom/hooks/systems/cron/bank.php',
            'sources_custom/hooks/systems/config/bank_dividend.php',
        ];
    }

    /**
     * Uninstall the addon.
     */
    public function uninstall()
    {
        $GLOBALS['SITE_DB']->drop_table_if_exists('bank');
    }

    /**
     * Install the addon.
     *
     * @param  ?integer $upgrade_from What version we're upgrading from (null: new install)
     */
    public function install($upgrade_from = null)
    {
        if ($upgrade_from === null) {
            $GLOBALS['SITE_DB']->create_table('bank', [
                'id' => '*AUTO',
                'member_id' => 'MEMBER',
                'amount' => 'INTEGER',
                'dividend' => 'INTEGER',
                'add_time' => '?TIME',
            ]);
        }
    }
}
