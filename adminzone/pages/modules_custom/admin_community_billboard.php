<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    community_billboard
 */

require_code('crud_module');

/**
 * Module page class.
 */
class Module_admin_community_billboard extends Standard_crud_module
{
    public $lang_type = 'COMMUNITY_BILLBOARD';
    public $special_edit_frontend = true;
    public $redirect_type = 'edit';
    public $menu_label = 'COMMUNITY_BILLBOARD';
    public $select_name = 'MESSAGE';
    public $table = 'community_billboard';
    public $orderer = 'the_message';
    public $title_is_multi_lang = true;

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
        $info['update_require_upgrade'] = true;
        $info['version'] = 4;
        $info['locked'] = false;
        return $info;
    }

    /**
     * Uninstall the module.
     */
    public function uninstall()
    {
        $GLOBALS['SITE_DB']->drop_table_if_exists('community_billboard');
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
            $GLOBALS['SITE_DB']->create_table('community_billboard', array(
                'id' => '*AUTO',
                'member_id' => 'MEMBER',
                'the_message' => 'SHORT_TRANS__COMCODE',
                'days' => 'INTEGER',
                'order_time' => 'TIME',
                'activation_time' => '?TIME',
                'active_now' => 'BINARY',
                'notes' => 'LONG_TEXT'
            ));

            $GLOBALS['SITE_DB']->create_index('community_billboard', 'find_active_billboard_msg', array('active_now'));
        }

        if ((!is_null($upgrade_from)) && ($upgrade_from < 4)) {
            rename_config_option('system_flagrant', 'system_community_billboard');

            $GLOBALS['SITE_DB']->rename_table('text', 'community_billboard');

            $GLOBALS['SITE_DB']->alter_table_field('community_billboard', 'user_id', 'MEMBER', 'member_id');
        }
    }

    public $title;

    /**
     * Module pre-run function. Allows us to know metadata for <head> before we start streaming output.
     *
     * @param  boolean $top_level Whether this is running at the top level, prior to having sub-objects called.
     * @param  ?ID_TEXT $type The screen type to consider for metadata purposes (null: read from environment).
     * @return ?Tempcode Tempcode indicating some kind of exceptional output (null: none).
     */
    public function pre_run($top_level = true, $type = null)
    {
        i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

        $type = get_param_string('type', 'browse');

        require_lang('community_billboard');

        set_helper_panel_tutorial('tut_points');

        return parent::pre_run($top_level);
    }

    /**
     * Standard crud_module run_start.
     *
     * @param  ID_TEXT $type The type of module execution
     * @return Tempcode The output of the run
     */
    public function run_start($type)
    {
        require_code('community_billboard');

        $this->add_one_label = do_lang_tempcode('ADD_COMMUNITY_BILLBOARD');
        $this->edit_this_label = do_lang_tempcode('EDIT_THIS_COMMUNITY_BILLBOARD');
        $this->edit_one_label = do_lang_tempcode('EDIT_COMMUNITY_BILLBOARD');

        if ($type == 'browse') {
            return $this->browse();
        }

        return new Tempcode();
    }

    /**
     * The do-next manager for before content management.
     *
     * @return Tempcode The UI
     */
    public function browse()
    {
        require_code('templates_donext');
        return do_next_manager(get_screen_title('COMMUNITY_BILLBOARD'), comcode_lang_string('DOC_COMMUNITY_BILLBOARD'),
            array(
                array('menu/_generic_admin/add_one', array('_SELF', array('type' => 'add'), '_SELF'), do_lang('ADD_COMMUNITY_BILLBOARD')),
                array('menu/_generic_admin/edit_one', array('_SELF', array('type' => 'edit'), '_SELF'), do_lang('EDIT_COMMUNITY_BILLBOARD')),
            ),
            do_lang('COMMUNITY_BILLBOARD')
        );
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
            'browse' => array('COMMUNITY_BILLBOARD_MANAGE', 'menu/adminzone/audit/community_billboard'),
        );
    }

    /**
     * Standard crud_module table function.
     *
     * @param  array $url_map Details to go to build_url for link to the next screen.
     * @return array A pair: The choose table, Whether reordering is supported from this screen.
     */
    public function create_selection_list_choose_table($url_map)
    {
        require_code('templates_results_table');

        $current_ordering = get_param_string('sort', 'the_message ASC');
        if (strpos($current_ordering, ' ') === false) {
            warn_exit(do_lang_tempcode('INTERNAL_ERROR'));
        }
        list($sortable, $sort_order) = explode(' ', $current_ordering, 2);
        $sortables = array(
            'the_message' => do_lang_tempcode('MESSAGE'),
            'days' => do_lang_tempcode('NUMBER_DAYS'),
            'order_time' => do_lang_tempcode('ORDER_DATE'),
            'member_id' => do_lang_tempcode('metadata:OWNER'),
        );
        if (((strtoupper($sort_order) != 'ASC') && (strtoupper($sort_order) != 'DESC')) || (!array_key_exists($sortable, $sortables))) {
            log_hack_attack_and_exit('ORDERBY_HACK');
        }

        $header_row = results_field_title(array(
            do_lang_tempcode('MESSAGE'),
            do_lang_tempcode('NUMBER_DAYS'),
            do_lang_tempcode('ORDER_DATE'),
            do_lang_tempcode('_UP_FOR'),
            do_lang_tempcode('metadata:OWNER'),
            do_lang_tempcode('ACTIONS'),
        ), $sortables, 'sort', $sortable . ' ' . $sort_order);

        $fields = new Tempcode();

        require_code('form_templates');
        list($rows, $max_rows) = $this->get_entry_rows(false, $current_ordering);
        foreach ($rows as $row) {
            $edit_link = build_url($url_map + array('id' => $row['id']), '_SELF');

            $username = protect_from_escaping($GLOBALS['FORUM_DRIVER']->member_profile_hyperlink($row['member_id']));

            $activation_time = $row['activation_time'];
            $days = is_null($activation_time) ? '' : float_format(floatval(time() - $activation_time) / 60.0 / 60.0 / 24.0, 3);

            $fields->attach(results_entry(array(protect_from_escaping(get_translated_tempcode('community_billboard', $row, 'the_message')), integer_format($row['days']), get_timezoned_date($row['order_time']), ($row['active_now'] == 1) ? $days : do_lang_tempcode('NA_EM'), $username, protect_from_escaping(hyperlink($edit_link, do_lang_tempcode('EDIT'), false, true, do_lang('EDIT') . ' #' . strval($row['id'])))), true));
        }

        return array(results_table(do_lang($this->menu_label), either_param_integer('start', 0), 'start', either_param_integer('max', 20), 'max', $max_rows, $header_row, $fields, $sortables, $sortable, $sort_order), false);
    }

    /**
     * Get Tempcode for a community billboard message adding/editing form.
     *
     * @param  SHORT_TEXT $message The message
     * @param  integer $days The number of days to display for
     * @param  LONG_TEXT $notes Notes
     * @param  BINARY $validated Whether the message is for immediate use
     * @return array A pair: The input fields, Hidden fields
     */
    public function get_form_fields($message = '', $days = 1, $notes = '', $validated = 1)
    {
        $fields = new Tempcode();
        require_code('form_templates');
        $fields->attach(form_input_line_comcode(do_lang_tempcode('MESSAGE'), do_lang_tempcode('DESCRIPTION_MESSAGE'), 'message', $message, true));
        $fields->attach(form_input_integer(do_lang_tempcode('NUMBER_DAYS'), do_lang_tempcode('NUMBER_DAYS_DESCRIPTION'), 'days', $days, true));
        if (get_option('enable_staff_notes') == '1') {
            $fields->attach(form_input_text(do_lang_tempcode('NOTES'), do_lang_tempcode('DESCRIPTION_NOTES'), 'notes', $notes, false));
        }
        $fields->attach(form_input_tick(do_lang_tempcode('IMMEDIATE_USE'), do_lang_tempcode(($message == '') ? 'DESCRIPTION_IMMEDIATE_USE_ADD' : 'DESCRIPTION_IMMEDIATE_USE'), 'validated', $validated == 1));

        return array($fields, new Tempcode());
    }

    /**
     * Standard crud_module edit form filler.
     *
     * @param  ID_TEXT $id The entry being edited
     * @return array A quartet: fields, hidden, delete-fields, text
     */
    public function fill_in_edit_form($id)
    {
        $rows = $GLOBALS['SITE_DB']->query_select('community_billboard', array('*'), array('id' => intval($id)));
        if (!array_key_exists(0, $rows)) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
        }
        $myrow = $rows[0];
        $date = get_timezoned_date($myrow['order_time']);
        $date_raw = $myrow['order_time'];
        list($fields, $hidden) = $this->get_form_fields(get_translated_text($myrow['the_message']), $myrow['days'], $myrow['notes'], $myrow['active_now']);

        $username = $GLOBALS['FORUM_DRIVER']->member_profile_hyperlink($myrow['member_id']);

        $text = do_template('COMMUNITY_BILLBOARD_DETAILS', array('_GUID' => 'dcc7a8b027d450a3c17c79b23b39cd87', 'USERNAME' => $username, 'DAYS_ORDERED' => integer_format($myrow['days']), 'DATE_RAW' => strval($date_raw), 'DATE' => $date));

        return array($fields, $hidden, new Tempcode(), $text);
    }

    /**
     * Standard crud_module add actualiser.
     *
     * @return ID_TEXT The entry added
     */
    public function add_actualisation()
    {
        $message = post_param_string('message');
        $notes = post_param_string('notes', '');
        $validated = post_param_integer('validated', 0);

        return strval(add_community_billboard_message($message, post_param_integer('days'), $notes, $validated));
    }

    /**
     * Standard crud_module edit actualiser.
     *
     * @param  ID_TEXT $id The entry being edited
     */
    public function edit_actualisation($id)
    {
        $message = post_param_string('message');
        $notes = post_param_string('notes', '');
        $validated = post_param_integer('validated', 0);
        edit_community_billboard_message(intval($id), $message, $notes, $validated);
    }

    /**
     * Standard crud_module delete actualiser.
     *
     * @param  ID_TEXT $id The entry being deleted
     */
    public function delete_actualisation($id)
    {
        delete_community_billboard_message(intval($id));
    }
}
