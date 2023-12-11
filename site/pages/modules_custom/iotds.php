<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    iotds
 */

/**
 * Module page class.
 */
class Module_iotds
{
    /**
     * Find details of the module.
     *
     * @return ?array Map of module info (null: module is disabled).
     */
    public function info()
    {
        $info = array();
        $info['author'] = 'Chris Graham';
        $info['organisation'] = 'ocProducts';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 4;
        $info['update_require_upgrade'] = true;
        $info['locked'] = false;
        return $info;
    }

    /**
     * Uninstall the module.
     */
    public function uninstall()
    {
        $GLOBALS['SITE_DB']->drop_table_if_exists('iotd');

        delete_privilege('choose_iotd');

        $GLOBALS['SITE_DB']->query_delete('trackbacks', array('trackback_for_type' => 'iotds'));

        require_code('files');
        if (!$GLOBALS['DEV_MODE']) {
            deldir_contents(get_custom_file_base() . '/uploads/iotds_addon', true);
        }
    }

    /**
     * Install the module.
     *
     * @param  ?integer $upgrade_from What version we're upgrading from (null: new install)
     * @param  ?integer $upgrade_from_hack What hack version we're upgrading from (null: new-install/not-upgrading-from-a-hacked-version)
     */
    public function install($upgrade_from = null, $upgrade_from_hack = null)
    {
        if (is_null($upgrade_from)) {
            $GLOBALS['SITE_DB']->create_table('iotd', array(
                'id' => '*AUTO',
                'url' => 'URLPATH',
                'i_title' => 'SHORT_TRANS__COMCODE',
                'caption' => 'LONG_TRANS__COMCODE',
                'thumb_url' => 'URLPATH',
                'is_current' => 'BINARY',
                'allow_rating' => 'BINARY',
                'allow_comments' => 'SHORT_INTEGER',
                'allow_trackbacks' => 'BINARY',
                'notes' => 'LONG_TEXT',
                'used' => 'BINARY',
                'date_and_time' => '?TIME',
                'iotd_views' => 'INTEGER',
                'submitter' => 'MEMBER',
                'add_date' => 'TIME',
                'edit_date' => '?TIME'
            ));

            $GLOBALS['SITE_DB']->create_index('iotd', 'iotd_views', array('iotd_views'));
            $GLOBALS['SITE_DB']->create_index('iotd', 'get_current', array('is_current'));
            $GLOBALS['SITE_DB']->create_index('iotd', 'ios', array('submitter'));
            $GLOBALS['SITE_DB']->create_index('iotd', 'iadd_date', array('add_date'));
            $GLOBALS['SITE_DB']->create_index('iotd', 'date_and_time', array('date_and_time'));

            add_privilege('IOTDS', 'choose_iotd', false);

            $GLOBALS['SITE_DB']->create_index('iotd', 'ftjoin_icap', array('caption'));
        }
    }

    /**
     * Find entry-points available within this module.
     *
     * @param  boolean $check_perms Whether to check permissions.
     * @param  ?MEMBER $member_id The member to check permissions as (null: current user).
     * @param  boolean $support_crosslinks Whether to allow cross links to other modules (identifiable via a full-page-link rather than a screen-name).
     * @param  boolean $be_deferential Whether to avoid any entry-point (or even return null to disable the page in the Sitemap) if we know another module, or page_group, is going to link to that entry-point. Note that "!" and "browse" entry points are automatically merged with container page nodes (likely called by page-groupings) as appropriate.
     * @return ?array A map of entry points (screen-name=>language-code/string or screen-name=>[language-code/string, icon-theme-image]) (null: disabled).
     */
    public function get_entry_points($check_perms = true, $member_id = null, $support_crosslinks = true, $be_deferential = false)
    {
        return array(
            'browse' => array('IOTDS', 'menu/rich_content/iotds'),
        );
    }

    public $title;
    public $id;
    public $myrow;
    public $url;

    /**
     * Module pre-run function. Allows us to know metadata for <head> before we start streaming output.
     *
     * @return ?Tempcode Tempcode indicating some kind of exceptional output (null: none).
     */
    public function pre_run()
    {
        i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

        $type = get_param_string('type', 'browse');

        require_lang('iotds');

        if ($type == 'browse') {
            $this->title = get_screen_title('IOTD_ARCHIVE');
        }

        if ($type == 'view') {
            set_feed_url('?mode=iotds&select=');

            $id = get_param_integer('id');

            // Breadcrumbs
            breadcrumb_set_parents(array(array('_SELF:_SELF:browse', do_lang_tempcode('IOTD_ARCHIVE'))));

            // Fetch details
            $rows = $GLOBALS['SITE_DB']->query_select('iotd', array('*'), array('id' => $id), '', 1);
            if (!array_key_exists(0, $rows)) {
                return warn_screen($this->title, do_lang_tempcode('MISSING_RESOURCE', 'iotd'));
            }
            $myrow = $rows[0];
            $url = $myrow['url'];
            if (url_is_local($url)) {
                $url = get_custom_base_url() . '/' . $url;
            }

            // Metadata
            set_extra_request_metadata(array(
                'identifier' => '_SEARCH:iotds:view:' . strval($id),
            ), $myrow, 'iotd', strval($id));

            $this->title = get_screen_title('IOTD');

            $this->id = $id;
            $this->myrow = $myrow;
            $this->url = $url;
        }

        return null;
    }

    /**
     * Execute the module.
     *
     * @return Tempcode The result of execution.
     */
    public function run()
    {
        require_code('feedback');
        require_code('iotds');
        require_css('iotds');

        // What action are we going to do?
        $type = get_param_string('type', 'browse');

        if ($type == 'browse') {
            return $this->iotd_browse();
        }
        if ($type == 'view') {
            return $this->view();
        }

        return new Tempcode();
    }

    /**
     * The UI to browse IOTDs.
     *
     * @return Tempcode The UI
     */
    public function iotd_browse()
    {
        $content = do_block('main_multi_content', array('param' => 'iotd', 'efficient' => '0', 'zone' => get_zone_name(), 'sort' => 'recent', 'max' => '10', 'no_links' => '1', 'pagination' => '1', 'give_context' => '0', 'include_breadcrumbs' => '0', 'block_id' => 'module', 'guid' => 'module'));

        return do_template('PAGINATION_SCREEN', array('_GUID' => 'd8a493c2b007d98074f104ea433c8091', 'TITLE' => $this->title, 'CONTENT' => $content));
    }

    /**
     * The UI to view an IOTD.
     *
     * @return Tempcode The UI
     */
    public function view()
    {
        $id = $this->id;
        $myrow = $this->myrow;
        $url = $this->url;

        // Feedback
        list($rating_details, $comment_details, $trackback_details) = embed_feedback_systems(
            get_page_name(),
            strval($id),
            $myrow['allow_rating'],
            $myrow['allow_comments'],
            $myrow['allow_trackbacks'],
            ((is_null($myrow['date_and_time'])) && ($myrow['used'] == 0)) ? 0 : 1,
            $myrow['submitter'],
            build_url(array('page' => '_SELF', 'type' => 'view', 'id' => $id), '_SELF', null, false, false, true),
            get_translated_text($myrow['i_title']),
            find_overridden_comment_forum('iotds'),
            $myrow['add_date']
        );

        $date = get_timezoned_date($myrow['date_and_time']);
        $date_raw = strval($myrow['date_and_time']);
        $add_date = get_timezoned_date($myrow['add_date']);
        $add_date_raw = strval($myrow['add_date']);
        $edit_date = get_timezoned_date($myrow['edit_date']);
        $edit_date_raw = is_null($myrow['edit_date']) ? '' : strval($myrow['edit_date']);

        // Views
        if ((get_db_type() != 'xml') && (get_value('no_view_counts') !== '1') && (is_null(get_bot_type()))) {
            $myrow['iotd_views']++;
            if (!$GLOBALS['SITE_DB']->table_is_locked('iotd')) {
                $GLOBALS['SITE_DB']->query_update('iotd', array('iotd_views' => $myrow['iotd_views']), array('id' => $id), '', 1, null, false, true);
            }
        }

        // Management links
        if ((has_actual_page_access(null, 'cms_iotds', null, null)) && (has_edit_permission('high', get_member(), $myrow['submitter'], 'cms_iotds'))) {
            $edit_url = build_url(array('page' => 'cms_iotds', 'type' => '_edit', 'id' => $id), get_module_zone('cms_iotds'));
        } else {
            $edit_url = new Tempcode();
        }

        return do_template('IOTD_ENTRY_SCREEN', array(
            '_GUID' => 'f508d483459b88fab44cd8b9f4db780b',
            'TITLE' => $this->title,
            'SUBMITTER' => strval($myrow['submitter']),
            'I_TITLE' => get_translated_tempcode('iotd', $myrow, 'i_title'),
            'CAPTION' => get_translated_tempcode('iotd', $myrow, 'caption'),
            'DATE' => $date,
            'DATE_RAW' => $date_raw,
            'ADD_DATE' => $add_date,
            'ADD_DATE_RAW' => $add_date_raw,
            'EDIT_DATE' => $edit_date,
            'EDIT_DATE_RAW' => $edit_date_raw,
            'VIEWS' => integer_format($myrow['iotd_views']),
            'TRACKBACK_DETAILS' => $trackback_details,
            'RATING_DETAILS' => $rating_details,
            'COMMENT_DETAILS' => $comment_details,
            'EDIT_URL' => $edit_url,
            'URL' => $url,
        ));
    }
}
