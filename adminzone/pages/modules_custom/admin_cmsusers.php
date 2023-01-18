<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2022

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    composr_homesite
 */

/**
 * Module page class.
 */
class Module_admin_cmsusers
{
    /**
     * Find details of the module.
     *
     * @return ?array Map of module info (null: module is disabled)
     */
    public function info() : ?array
    {
        $info = [];
        $info['author'] = 'Chris Graham';
        $info['organisation'] = 'ocProducts';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 3;
        $info['update_require_upgrade'] = true;
        $info['locked'] = false;
        return $info;
    }

    /**
     * Uninstall the module.
     */
    public function uninstall()
    {
        $tables = [
            'may_feature',
            'logged',
        ];
        $GLOBALS['SITE_DB']->drop_table_if_exists($tables);
    }

    /**
     * Install the module.
     *
     * @param  ?integer $upgrade_from What version we're upgrading from (null: new install)
     * @param  ?integer $upgrade_from_hack What hack version we're upgrading from (null: new-install/not-upgrading-from-a-hacked-version)
     */
    public function install(?int $upgrade_from = null, ?int $upgrade_from_hack = null)
    {
        if ($upgrade_from === null) {
            $GLOBALS['SITE_DB']->create_table('may_feature', [
                'id' => '*AUTO',
                'url' => 'URLPATH',
            ]);

            $GLOBALS['SITE_DB']->create_table('logged', [
                'id' => '*AUTO',
                'website_url' => 'URLPATH',
                'website_name' => 'SHORT_TEXT',
                'l_version' => 'ID_TEXT',
                'hittime' => 'TIME',
                'num_members' => 'INTEGER',
                'num_hits_per_day' => 'INTEGER',
            ]);
        }

        if (($upgrade_from !== null) && ($upgrade_from < 2)) { // LEGACY
            $GLOBALS['SITE_DB']->rename_table('mayfeature', 'may_feature');
        }

        if (($upgrade_from !== null) && ($upgrade_from < 3)) { // LEGACY
            $GLOBALS['SITE_DB']->add_table_field('logged', 'num_members', 'INTEGER');
            $GLOBALS['SITE_DB']->add_table_field('logged', 'num_hits_per_day', 'INTEGER');
            $GLOBALS['SITE_DB']->delete_table_field('logged', 'is_registered');
            $GLOBALS['SITE_DB']->delete_table_field('logged', 'log_key');
            $GLOBALS['SITE_DB']->delete_table_field('logged', 'expire');
        }
    }

    /**
     * Find entry-points available within this module.
     *
     * @param  boolean $check_perms Whether to check permissions
     * @param  ?MEMBER $member_id The member to check permissions as (null: current user)
     * @param  boolean $support_crosslinks Whether to allow cross links to other modules (identifiable via a full-page-link rather than a screen-name)
     * @param  boolean $be_deferential Whether to avoid any entry-point (or even return null to disable the page in the Sitemap) if we know another module, or page_group, is going to link to that entry-point. Note that "!" and "browse" entry points are automatically merged with container page nodes (likely called by page-groupings) as appropriate.
     * @return ?array A map of entry points (screen-name=>language-code/string or screen-name=>[language-code/string, icon-theme-image]) (null: disabled)
     */
    public function get_entry_points(bool $check_perms = true, ?int $member_id = null, bool $support_crosslinks = true, bool $be_deferential = false) : ?array
    {
        if (!addon_installed('composr_homesite')) {
            return null;
        }

        return [
            'browse' => ['CMS_SITES_INSTALLED', 'admin/tool'],
        ];
    }

    public $title;

    /**
     * Module pre-run function. Allows us to know metadata for <head> before we start streaming output.
     *
     * @return ?Tempcode Tempcode indicating some kind of exceptional output (null: none)
     */
    public function pre_run() : ?object
    {
        i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

        $error_msg = new Tempcode();
        if (!addon_installed__messaged('composr_homesite', $error_msg)) {
            return $error_msg;
        }

        $type = get_param_string('type', 'browse');

        require_lang('composr_homesite');

        $this->title = get_screen_title('CMS_SITES_INSTALLED');

        return null;
    }

    /**
     * Execute the module.
     *
     * @return Tempcode The result of execution
     */
    public function run() : object
    {
        require_code('composr_homesite');
        require_code('form_templates');

        $type = get_param_string('type', 'browse');
        if ($type == 'browse') {
            return $this->users();
        }
        return new Tempcode();
    }

    /**
     * List of sites that have installed Composr.
     *
     * @return Tempcode The result of execution
     */
    public function users() : object
    {
        $_sort = get_param_string('sort', 'num_hits_per_day DESC');
        list($sort, $dir) = explode(' ', $_sort);
        $anti_dir = (($dir == 'ASC') ? 'DESC' : 'ASC');

        $order_by = '';
        switch ($sort) {
            case 'website_name':
                $order_by = ' ORDER BY website_name';
                break;

            case 'hittime':
                $order_by = ' ORDER BY hittime';
                break;

            case 'l_version':
                $order_by = ' ORDER BY l_version';
                break;

            case 'num_members':
                $order_by = ' ORDER BY num_members';
                break;

            case 'num_hits_per_day':
                $order_by = ' ORDER BY num_hits_per_day';
                break;

            default:
                warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
        }
        $order_by .= ' ' . $dir;

        $max = 500;

        $select = 'website_url,website_name,MAX(l_version) AS l_version,MAX(hittime) AS hittime,MAX(num_members) AS num_members,MAX(num_hits_per_day) AS num_hits_per_day';
        $where = 'website_url NOT LIKE \'%.composr.info%\'';
        if (!$GLOBALS['DEV_MODE']) {
            $where .= ' AND ' . db_string_not_equal_to('website_name', '') . ' AND ' . db_string_not_equal_to('website_name', '(unnamed)');
        }
        $sql = 'SELECT ' . $select . ' FROM ' . get_table_prefix() . 'logged WHERE ' . $where . ' GROUP BY website_url,website_name ' . $order_by;
        $rows = $GLOBALS['SITE_DB']->query($sql, $max);

        $seen_before = [];

        $_rows = [];
        foreach ($rows as $i => $r) {
            // Test that they give feature permission
            $url_parts = cms_parse_url_safe($r['website_url']);
            if (!array_key_exists('host', $url_parts)) {
                continue;
            }
            $perm = $GLOBALS['SITE_DB']->query_select_value_if_there('may_feature', 'id', ['url' => $url_parts['scheme'] . '://' . $url_parts['host']]);
            if (($perm === null) && (get_param_integer('no_feature', 0) == 1)) {
                continue;
            }

            $rt = [];

            $rt['L_VERSION'] = $r['l_version'];

            $rt['WEBSITE_URL'] = $r['website_url'];

            $rt['WEBSITE_NAME'] = $r['website_name'];

            $rt['HITTIME'] = integer_format(intval(round((time() - $r['hittime']) / 60 / 60)));
            $rt['HITTIME_2'] = integer_format(intval(round((time() - $r['hittime']) / 60 / 60 / 24)));

            if ($i < 100) {
                $active = get_value_newer_than('testing__' . $r['website_url'] . '/_config.php', time() - 60 * 60 * 10, true);
                if ($active === null) {
                    $test = cms_http_request($r['website_url'] . '/_config.php', ['convert_to_internal_encoding' => true, 'trigger_error' => false, 'byte_limit' => 0, 'ua' => 'Simple install stats', 'timeout' => 2.0]);
                    if ($test->data !== null) {
                        $active = do_lang('YES');
                    } else {
                        $active = @strval($test->message);
                        if ($active == '') {
                            $active = do_lang('NO');
                        } else {
                            $active .= do_lang('CMS_WHEN_CHECKING');
                        }
                    }
                    set_value('testing__' . $r['website_url'] . '/_config.php', $active, true);
                }
                $rt['CMS_ACTIVE'] = $active;
            } else {
                $rt['CMS_ACTIVE'] = do_lang('CMS_CHECK_LIMIT');
            }

            $rt['NOTE'] = $perm ? do_lang('CMS_MAY_FEATURE') : do_lang('CMS_KEEP_PRIVATE');

            $rt['NUM_MEMBERS'] = integer_format($r['num_members']);

            $rt['NUM_HITS_PER_DAY'] = integer_format($r['num_hits_per_day']);

            $_rows[] = $rt;
        }

        return do_template('CMS_SITES_SCREEN', [
            '_GUID' => '7f4b56c730f2b613994a3fe6f00ed525',
            'TITLE' => $this->title,
            'ROWS' => $_rows,

            'WEBSITE_NAME_DIR' => ($sort == 'website_name') ? $anti_dir : 'ASC',
            'HITTIME_DIR' => ($sort == 'hittime') ? $anti_dir : 'DESC',
            'L_VERSION_DIR' => ($sort == 'l_version') ? $anti_dir : 'ASC',
            'NUM_MEMBERS_DIR' => ($sort == 'num_members') ? $anti_dir : 'DESC',
            'NUM_HITS_PER_DAY_DIR' => ($sort == 'num_hits_per_day') ? $anti_dir : 'DESC',
        ]);
    }
}
