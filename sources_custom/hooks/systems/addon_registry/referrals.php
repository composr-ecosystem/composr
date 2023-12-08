<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    referrals
 */

/**
 * Hook class.
 */
class Hook_addon_registry_referrals
{
    /**
     * Get a list of file permissions to set
     *
     * @param  boolean $runtime Whether to include wildcards represented runtime-created chmoddable files
     * @return array File permissions to set
     */
    public function get_chmod_array($runtime = false)
    {
        return array();
    }

    /**
     * Get the version of Composr this addon is for
     *
     * @return float Version number
     */
    public function get_version()
    {
        return cms_version_number();
    }

    /**
     * Get the addon category
     *
     * @return string The category
     */
    public function get_category()
    {
        return 'New Features';
    }

    /**
     * Get the addon author
     *
     * @return string The author
     */
    public function get_author()
    {
        return 'Chris Graham';
    }

    /**
     * Find other authors
     *
     * @return array A list of co-authors that should be attributed
     */
    public function get_copyright_attribution()
    {
        return array(
            'Icon by Titan Creations',
        );
    }

    /**
     * Get the addon licence (one-line summary only)
     *
     * @return string The licence
     */
    public function get_licence()
    {
        return 'Licensed on the same terms as Composr';
    }

    /**
     * Get the description of the addon
     *
     * @return string Description of the addon
     */
    public function get_description()
    {
        return 'A referrals package.

Allows people to specify who referred them when they join your site or other configurable triggers in the system, and defines award levels people can reach. Note that tracking of referrals and award of point is a default part of Composr, but referrals are only picked up if made via the recommend module and the new member uses the same address they were recommended to. This addon will allow referrals to be specified explicitly via the URL or typed in manually.

1) Edit the settings in text_custom/referrals.txt (there is an editing link for this on the setup menu)

2) Edit the messages in the referrals.ini language file as required.

3) Probably set up a page on your site explaining the awards you give.';
    }

    /**
     * Get a list of tutorials that apply to this addon
     *
     * @return array List of tutorials
     */
    public function get_applicable_tutorials()
    {
        return array();
    }

    /**
     * Get a mapping of dependency types
     *
     * @return array File permissions to set
     */
    public function get_dependencies()
    {
        return array(
            'requires' => array('cns'),
            'recommends' => array(),
            'conflicts_with' => array()
        );
    }

    /**
     * Explicitly say which icon should be used
     *
     * @return URLPATH Icon
     */
    public function get_default_icon()
    {
        return 'themes/default/images_custom/icons/48x48/menu/referrals.png';
    }

    /**
     * Get a list of files that belong to this addon
     *
     * @return array List of files
     */
    public function get_file_list()
    {
        return array(
            'themes/default/images_custom/icons/24x24/menu/referrals.png',
            'themes/default/images_custom/icons/48x48/menu/referrals.png',
            'sources_custom/hooks/systems/addon_registry/referrals.php',
            'sources_custom/hooks/systems/notifications/referral.php',
            'sources_custom/hooks/systems/notifications/referral_staff.php',
            'text_custom/referrals.txt',
            'data_custom/referrer_report.php',
            'lang_custom/EN/referrals.ini',
            'sources_custom/cns_join.php',
            'sources_custom/referrals.php',
            'sources_custom/hooks/systems/ecommerce/usergroup.php',
            'sources_custom/hooks/systems/ecommerce/cart_orders.php',
            'sources_custom/hooks/systems/page_groupings/referrals.php',
            'sources_custom/hooks/modules/members/referrals.php',
            'adminzone/pages/comcode_custom/EN/referrals.txt',
            'adminzone/pages/modules_custom/admin_referrals.php',
            'sources_custom/hooks/systems/startup/referrals.php',
        );
    }

    /**
     * Uninstall the addon.
     */
    public function uninstall()
    {
        $GLOBALS['SITE_DB']->drop_table_if_exists('referrer_override');
        $GLOBALS['SITE_DB']->drop_table_if_exists('referees_qualified_for');
    }

    /**
     * Install the addon.
     *
     * @param  ?integer $upgrade_from What version we're upgrading from (null: new install)
     */
    public function install($upgrade_from = null)
    {
        if (is_null($upgrade_from)) {
            $GLOBALS['SITE_DB']->create_table('referrer_override', array(
                'o_referrer' => '*MEMBER',
                'o_scheme_name' => '*ID_TEXT',
                'o_referrals_dif' => 'INTEGER',
                'o_is_qualified' => '?BINARY',
            ));

            $GLOBALS['SITE_DB']->create_table('referees_qualified_for', array(
                'id' => '*AUTO',
                'q_referee' => 'MEMBER',
                'q_referrer' => 'MEMBER',
                'q_scheme_name' => 'ID_TEXT',
                'q_email_address' => 'SHORT_TEXT',
                'q_time' => 'TIME',
                'q_action' => 'ID_TEXT',
            ));

            if (get_forum_type() == 'cns') {
                // Populate from current invites
                $rows = $GLOBALS['FORUM_DB']->query_select('f_invites', array('i_email_address', 'i_time', 'i_inviter'), array('i_taken' => 1));
                foreach ($rows as $row) {
                    $member_id = $GLOBALS['FORUM_DB']->query_select_value_if_there('f_members', 'id', array('m_email_address' => $row['i_email_address']));
                    if (!is_null($member_id)) {
                        $ini_file = parse_ini_file(get_custom_file_base() . '/text_custom/referrals.txt', true);

                        foreach (array_keys($ini_file) as $scheme_name) {
                            $GLOBALS['SITE_DB']->query_insert('referees_qualified_for', array(
                                'q_referee' => $member_id,
                                'q_referrer' => $row['i_inviter'],
                                'q_scheme_name' => $scheme_name,
                                'q_email_address' => $row['i_email_address'],
                                'q_time' => $row['i_time'],
                                'q_action' => '',
                            ));
                        }
                    }
                }
            }
        }
    }
}
