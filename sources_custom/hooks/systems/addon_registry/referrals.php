<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    referrals
 */

/**
 * Hook class.
 */
class Hook_addon_registry_referrals
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
        return null;
    }

    /**
     * Get the addon category.
     *
     * @return string The category
     */
    public function get_category() : string
    {
        return 'eCommerce';
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
        return 'A referrals package.

Allows people to specify who referred them when they join your site or other configurable triggers in the system, and defines award levels people can reach. Note that tracking of referrals and award of points is a default part of Composr, but referrals are only picked up if made via the recommend module or if the new member uses the same address they were recommended to. This addon will allow referrals to be specified explicitly on the join form.

1) Edit the settings in text_custom/referrals.txt (there is an editing link for this on the setup menu)

2) Edit the messages in the referrals.ini language file as required.

3) Probably set up a page on your site explaining the awards you give.';
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
                'Conversr',
                //'core_all_icons',
                'stats',
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
        return 'themes/default/images/icons/spare/referrals.svg';
    }

    /**
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list() : array
    {
        return [
            'adminzone/pages/comcode_custom/EN/referrals.txt',
            'adminzone/pages/modules_custom/admin_referrals.php',
            'data_custom/referrer_report.php',
            'lang_custom/EN/referrals.ini',
            'sources_custom/cns_join.php',
            'sources_custom/hooks/modules/members/referrals.php',
            'sources_custom/hooks/systems/actionlog/referrals.php',
            'sources_custom/hooks/systems/addon_registry/referrals.php',
            'sources_custom/hooks/systems/ecommerce/cart_orders.php',
            'sources_custom/hooks/systems/ecommerce/usergroup.php',
            'sources_custom/hooks/systems/notifications/referral.php',
            'sources_custom/hooks/systems/notifications/referral_staff.php',
            'sources_custom/hooks/systems/page_groupings/referrals.php',
            'sources_custom/hooks/systems/privacy/referrals.php',
            'sources_custom/hooks/systems/referrals/.htaccess',
            'sources_custom/hooks/systems/referrals/index.html',
            'sources_custom/referrals.php',
            'text_custom/referrals.txt',
        ];
    }

    /**
     * Uninstall the addon.
     */
    public function uninstall()
    {
        $tables = [
            'referrer_override',
            'referees_qualified_for',
        ];
        $GLOBALS['SITE_DB']->drop_table_if_exists($tables);
    }

    /**
     * Install the addon.
     *
     * @param  ?integer $upgrade_from What version we're upgrading from (null: new install)
     */
    public function install(?int $upgrade_from = null)
    {
        if ($upgrade_from === null) {
            $GLOBALS['SITE_DB']->create_table('referrer_override', [
                'o_referrer' => '*MEMBER',
                'o_scheme_name' => '*ID_TEXT',
                'o_referrals_dif' => 'INTEGER',
                'o_is_qualified' => '?BINARY',
            ]);

            $GLOBALS['SITE_DB']->create_table('referees_qualified_for', [
                'id' => '*AUTO',
                'q_referee' => 'MEMBER',
                'q_referrer' => 'MEMBER',
                'q_scheme_name' => 'ID_TEXT',
                'q_email_address' => 'SHORT_TEXT',
                'q_time' => 'TIME',
                'q_action' => 'ID_TEXT',
            ]);

            if (get_forum_type() == 'cns') {
                // Populate from current invites
                $rows = $GLOBALS['FORUM_DB']->query_select('f_invites', ['i_email_address', 'i_time', 'i_inviter'], ['i_taken' => 1]);
                foreach ($rows as $row) {
                    $member_id = $GLOBALS['FORUM_DB']->query_select_value_if_there('f_members', 'id', ['m_email_address' => $row['i_email_address']]);
                    if ($member_id !== null) {
                        require_code('files');

                        $path = get_custom_file_base() . '/text_custom/referrals.txt';
                        if (!is_file($path)) {
                            $path = get_file_base() . '/text_custom/referrals.txt';
                        }
                        $ini_file = cms_parse_ini_file_safe($path, true);

                        foreach (array_keys($ini_file) as $scheme_name) {
                            $GLOBALS['SITE_DB']->query_insert('referees_qualified_for', [
                                'q_referee' => $member_id,
                                'q_referrer' => $row['i_inviter'],
                                'q_scheme_name' => $scheme_name,
                                'q_email_address' => $row['i_email_address'],
                                'q_time' => $row['i_time'],
                                'q_action' => '',
                            ]);
                        }
                    }
                }
            }
        }
    }
}
