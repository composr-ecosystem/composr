<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2021

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    patreon
 */

/**
 * Hook class.
 */
class Hook_addon_registry_patreon
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
     * Get the version of Composr this addon is for.
     *
     * @return float Version number
     */
    public function get_version() : float
    {
        return cms_version_number();
    }

    /**
     * Get the addon category.
     *
     * @return string The category
     */
    public function get_category() : string
    {
        return 'Third Party Integration';
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
        return 'Licensed on the same terms as Composr';
    }

    /**
     * Get the description of the addon.
     *
     * @return string Description of the addon
     */
    public function get_description() : string
    {
        return 'Put all Patreon patrons into a particular usergroup (as a secondary usergroup membership).
Integration is done via the [tt]hybridauth[/tt] addon, which must be installed.

Basic setup instructions:
1) Set up [url="https://www.patreon.com/portal/registration/register-clients"]Patreon API client[/url]
2) Set up *1 [url="https://www.patreon.com/portal/registration/register-webhooks"]Patreon API webhook[/url] and enable the 3 "members:pledge:*" events
3) Configure Hybridauth Patreon from Admin Zone > Setup > Hybridauth configuration (*1)
4) Connect Hybridauth Patreon from Admin Zone > Setup > Setup API access
5) Create new patron usergroup, unless you intend to use an existing usergroup
6) Configure this addon from Admin Zone > Setup > Configuration > Third Party Integration > Patreon

*1 You can skip webhook configuration if daily sync is good enough

*2 The Hybridauth XML configuration will look like this:
[code]
<hybridauth>
    <Patreon>
        <hybridauth-config scope="identity campaigns w:campaigns.webhook campaigns.members campaigns.members[email]" />
        <composr-config allow_signups="false" />
        <keys-config id="FILLME" secret="FILLME" />
    </Patreon>
</hybridauth>
[/code]
';
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
                'hybridauth',
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
        return 'themes/default/images/icons/menu/social/members.svg';
    }

    /**
     * Get a list of files that belong to this addon.
     *
     * @return array List of files
     */
    public function get_file_list() : array
    {
        return [
            'sources_custom/hooks/systems/addon_registry/patreon.php',
            'sources_custom/hooks/systems/cns_implicit_usergroups/patreon.php',
            'sources_custom/hooks/systems/config/patreon_group.php',
            'sources_custom/hooks/systems/config/patreon_webhook_secret.php',
            'sources_custom/hooks/systems/config/patreon_tiers.php',
            'sources_custom/hooks/systems/cron/patreon.php',
            'lang_custom/EN/patreon.ini',
            'data_custom/patreon_webhook.php',
            'themes/default/templates_custom/BLOCK_MAIN_PATREON_PATRONS.tpl',
            'sources_custom/patreon.php',
            'sources_custom/miniblocks/main_patreon_patrons.php',
            'data_custom/patreon_patrons.php',
            'sources_custom/hooks/systems/privacy/patreon.php',
        ];
    }

    /**
     * Uninstall the addon.
     */
    public function uninstall()
    {
        $GLOBALS['SITE_DB']->drop_table_if_exists('patreon_patrons');
    }

    /**
     * Install the addon.
     *
     * @param  ?integer $upgrade_from What version we're upgrading from (null: new install)
     */
    public function install(?int $upgrade_from = null)
    {
        $GLOBALS['SITE_DB']->create_table('patreon_patrons', [
            'p_member_id' => '*MEMBER',
            'p_tier' => '*ID_TEXT',
            'p_id' => 'ID_TEXT',
            'p_monthly' => 'INTEGER',
            'p_name' => 'SHORT_TEXT',
        ]);
    }
}
