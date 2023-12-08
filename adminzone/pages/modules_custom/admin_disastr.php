<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    disastr
 */

require_code('crud_module');

/**
 * Module page class.
 */
class Module_admin_disastr extends Standard_crud_module
{
    public $lang_type = 'DISEASE';
    public $select_name = 'NAME';
    public $possibly_some_kind_of_upload = true;
    public $output_of_action_is_confirmation = true;
    public $menu_label = 'DISASTR_TITLE';
    public $do_preview = null;
    public $view_entry_point = '_SEARCH:admin_disastr:view:_ID';

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
        $GLOBALS['SITE_DB']->drop_table_if_exists('diseases');
        $GLOBALS['SITE_DB']->drop_table_if_exists('members_diseases');

        //require_code('files');
        //deldir_contents(get_custom_file_base() . '/uploads/disastr_addon', true);
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
            $GLOBALS['SITE_DB']->create_table('diseases', array(
                'id' => '*AUTO',
                'name' => 'SHORT_TEXT',
                'image' => 'URLPATH',
                'cure' => 'SHORT_TEXT',
                'cure_price' => 'INTEGER',
                'immunisation' => 'SHORT_TEXT',
                'immunisation_price' => 'INTEGER',
                'spread_rate' => 'INTEGER',
                'points_per_spread' => 'INTEGER',
                'last_spread_time' => 'INTEGER',
                'enabled' => 'BINARY'
            ));

            $GLOBALS['SITE_DB']->create_table('members_diseases', array(
                'member_id' => '*MEMBER',
                'disease_id' => '*AUTO_LINK',
                'sick' => 'BINARY',
                'cure' => 'BINARY',
                'immunisation' => 'BINARY',
            ));

            $GLOBALS['SITE_DB']->query_insert('diseases', array('name' => 'Zombiism', 'image' => 'data_custom/images/disastr/hazard.png', 'cure' => 'Zombiism vaccine', 'cure_price' => 100, 'immunisation' => 'Immunise yourself from Zombiism', 'immunisation_price' => 50, 'spread_rate' => 12, 'points_per_spread' => 10, 'last_spread_time' => 0, 'enabled' => 1), true);
            $GLOBALS['SITE_DB']->query_insert('diseases', array('name' => 'A bad case of Hiccups', 'image' => 'data_custom/images/disastr/hazard.png', 'cure' => 'Hiccup vaccine', 'cure_price' => 100, 'immunisation' => 'Immunise yourself from the Hiccups', 'immunisation_price' => 50, 'spread_rate' => 12, 'points_per_spread' => 10, 'last_spread_time' => 0, 'enabled' => 1), true);
            $GLOBALS['SITE_DB']->query_insert('diseases', array('name' => 'Vampirism', 'image' => 'data_custom/images/disastr/hazard.png', 'cure' => 'Vampirism vaccine', 'cure_price' => 100, 'immunisation' => 'Immunise yourself against Vampirism', 'immunisation_price' => 50, 'spread_rate' => 12, 'points_per_spread' => 10, 'last_spread_time' => 0, 'enabled' => 1), true);
            $GLOBALS['SITE_DB']->query_insert('diseases', array('name' => 'The Flu', 'image' => 'data_custom/images/disastr/hazard.png', 'cure' => 'Flu vaccine', 'cure_price' => 100, 'immunisation' => 'Immunise yourself against the Flu', 'immunisation_price' => 50, 'spread_rate' => 12, 'points_per_spread' => 10, 'last_spread_time' => 0, 'enabled' => 1), true);
            $GLOBALS['SITE_DB']->query_insert('diseases', array('name' => 'Lice', 'image' => 'data_custom/images/disastr/hazard.png', 'cure' => 'Lice-Away Spray', 'cure_price' => 100, 'immunisation' => 'Lice repellant', 'immunisation_price' => 50, 'spread_rate' => 12, 'points_per_spread' => 10, 'last_spread_time' => 0, 'enabled' => 1), true);
            $GLOBALS['SITE_DB']->query_insert('diseases', array('name' => 'Fleas', 'image' => 'data_custom/images/disastr/hazard.png', 'cure' => 'Flea spray', 'cure_price' => 100, 'immunisation' => 'Flea repellant', 'immunisation_price' => 50, 'spread_rate' => 12, 'points_per_spread' => 10, 'last_spread_time' => 0, 'enabled' => 1), true);
            $GLOBALS['SITE_DB']->query_insert('diseases', array('name' => 'Man-Flu', 'image' => 'data_custom/images/disastr/hazard.png', 'cure' => 'Lots and lots of TLC', 'cure_price' => 1000, 'immunisation' => 'Anti Man-Flu Serum', 'immunisation_price' => 250, 'spread_rate' => 12, 'points_per_spread' => 100, 'last_spread_time' => 0, 'enabled' => 1), true);
        }

        if ((!is_null($upgrade_from)) && ($upgrade_from < 3)) {
            $GLOBALS['SITE_DB']->alter_table_field('members_diseases', 'desease_id', 'AUTO_LINK', 'disease_id');
            $GLOBALS['SITE_DB']->alter_table_field('members_diseases', 'user_id', '*MEMBER', 'member_id');
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
        if (get_forum_type() != 'cns') {
            return null;
        }

        return array(
            'browse' => array('MANAGE_DISEASES', 'menu/disastr_log'),
        ) + parent::get_entry_points();
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

        require_lang('disastr');

        set_helper_panel_tutorial('tut_subcom');

        if ($type == 'view') {
            breadcrumb_set_self(do_lang_tempcode('VIEW_DISEASE'));
        }

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
        if (get_forum_type() != 'cns') {
            warn_exit(do_lang_tempcode('NO_CNS'));
        } else {
            cns_require_all_forum_stuff();
        }

        require_code('cns_groups_action');
        require_code('cns_forums_action');
        require_code('cns_groups_action2');
        require_code('cns_forums_action2');

        if ($type == 'browse') {
            return $this->browse();
        }
        if ($type == 'view') {
            return $this->view();
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
        return do_next_manager(get_screen_title('DISASTR_TITLE'), comcode_lang_string('DOC_DISASTR'),
            array(
                array('menu/_generic_admin/add_one', array('_SELF', array('type' => 'add'), '_SELF'), do_lang('ADD_DISEASE')),
                array('menu/_generic_admin/edit_one', array('_SELF', array('type' => 'edit'), '_SELF'), do_lang('EDIT_DISEASE')),
            ),
            do_lang('DISASTR_TITLE')
        );
    }

    public function view()
    {
        $title = get_screen_title('VIEW_DISEASE');

        $id = get_param_integer('id');

        $rows = $GLOBALS['SITE_DB']->query_select('diseases', array('*'), array('id' => $id), '' , 1);
        if (!isset($rows[0])) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
        }

        $name = $rows[0]['name'];

        require_code('images');
        $image = do_image_thumb($rows[0]['image'], $name);

        $cure = $rows[0]['cure'];
        $cure_price = $rows[0]['cure_price'];
        $immunization = $rows[0]['immunisation'];
        $immunization_price = $rows[0]['immunisation_price'];

        $spread_rate = $rows[0]['spread_rate'];
        $points_per_spread = $rows[0]['points_per_spread'];

        $enabled = ($rows[0]['enabled'] == 1) ? do_lang_tempcode('YES') : do_lang_tempcode('NO');

        require_code('templates_map_table');
        return map_table_screen($title, array(
            'NAME' => $name,
            'IMAGE' => $image,
            'CURE' => $cure,
            'CURE_PRICE' => integer_format($cure_price),
            'IMMUNIZATION' => $immunization,
            'IMMUNIZATION_PRICE' => integer_format($immunization_price),
            'SPREAD_RATE' => integer_format($spread_rate),
            'POINTS_PER_SPREAD' => integer_format($points_per_spread),
            'ENABLED' => $enabled,
        ));
    }

    public function get_form_fields($id = null, $name = '', $image = '', $cure = '', $cure_price = 10, $immunization = '', $immunization_price = 5, $spread_rate = 12, $points_per_spread = 10, $enabled = 1)
    {
        $fields = new Tempcode();
        $hidden = new Tempcode();

        require_code('form_templates');

        $fields->attach(form_input_line(do_lang_tempcode('DISEASE'), do_lang_tempcode('DESCRIPTION_DISEASE'), 'name', $name, true));

        require_code('themes2');
        $fields->attach(form_input_upload_multi_source(do_lang_tempcode('IMAGE'), '', $hidden, 'image', null, true, $image));

        $fields->attach(form_input_line(do_lang_tempcode('CURE'), do_lang_tempcode('DESCRIPTION_CURE'), 'cure', $cure, true));
        $fields->attach(form_input_line(do_lang_tempcode('CURE_PRICE'), '', 'cure_price', strval($cure_price), true));
        $fields->attach(form_input_line(do_lang_tempcode('IMMUNIZATION'), do_lang_tempcode('DESCRIPTION_IMMUNIZATION'), 'immunization', $immunization, true));
        $fields->attach(form_input_line(do_lang_tempcode('IMMUNIZATION_PRICE'), '', 'immunization_price', strval($immunization_price), true));
        $fields->attach(form_input_line(do_lang_tempcode('SPREAD_RATE'), do_lang_tempcode('DESCRIPTION_SPREAD_RATE'), 'spread_rate', strval($spread_rate), true));
        $fields->attach(form_input_line(do_lang_tempcode('POINTS_PER_SPREAD'), do_lang_tempcode('DESCRIPTION_POINTS_PER_SPREAD'), 'points_per_spread', strval($points_per_spread), true));
        $fields->attach(form_input_tick(do_lang_tempcode('ENABLED'), do_lang_tempcode('DESCRIPTION_DISEASE_ENABLED'), 'enabled', $enabled == 1));

        return array($fields, $hidden);
    }

    /**
     * Standard crud_module list function.
     *
     * @return Tempcode The selection list
     */
    public function create_selection_list_entries()
    {
        $fields = new Tempcode();

        $rows = $GLOBALS['SITE_DB']->query_select('diseases', array('*'), null, 'ORDER BY name');

        foreach ($rows as $row) {
            $fields->attach(form_input_list_entry(strval($row['id']), false, $row['name']));
        }

        return $fields;
    }

    /**
     * Standard crud_module edit form filler.
     *
     * @param  ID_TEXT $id The entry being edited
     * @return array A pair: The input fields, Hidden fields
     */
    public function fill_in_edit_form($id)
    {
        $rows = $GLOBALS['SITE_DB']->query_select('diseases', array('*'), array('id' => intval($id)));
        if (!array_key_exists(0, $rows)) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
        }
        $myrow = $rows[0];

        $name = $myrow['name'];
        $image = $myrow['image'];
        $cure = $myrow['cure'];
        $cure_price = $myrow['cure_price'];
        $immunization = $myrow['immunisation'];
        $immunization_price = $myrow['immunisation_price'];
        $spread_rate = $myrow['spread_rate'];
        $points_per_spread = $myrow['points_per_spread'];
        $enabled = $myrow['enabled'];

        $ret = $this->get_form_fields($id, $name, $image, $cure, $cure_price, $immunization, $immunization_price, $spread_rate, $points_per_spread, $enabled);

        return $ret;
    }

    /**
     * Standard crud_module add actualiser.
     *
     * @return ID_TEXT The entry added
     */
    public function add_actualisation()
    {
        $name = post_param_string('name', '');
        $cure = post_param_string('cure', '');
        $cure_price = post_param_integer('cure_price', 0);
        $immunization = post_param_string('immunization', '');
        $immunization_price = post_param_integer('immunization_price', 0);
        $spread_rate = post_param_integer('spread_rate', 12);
        $points_per_spread = post_param_integer('points_per_spread', 10);
        $enabled = post_param_integer('enabled', 0);

        require_code('themes2');
        $url = post_param_image('image', 'uploads/disastr_addon', null, true);

        $id = $GLOBALS['SITE_DB']->query_insert('diseases', array('name' => $name, 'image' => $url, 'cure' => $cure, 'cure_price' => $cure_price, 'immunisation' => $immunization, 'immunisation_price' => $immunization_price, 'spread_rate' => $spread_rate, 'points_per_spread' => $points_per_spread, 'last_spread_time' => 0, 'enabled' => $enabled), true);

        return strval($id);
    }

    /**
     * Standard crud_module edit actualiser.
     *
     * @param  ID_TEXT $id The entry being edited
     * @return ?Tempcode Confirm message (null: continue)
     */
    public function edit_actualisation($_id)
    {
        $id = intval($_id);
        $name = post_param_string('name', '');
        $cure = post_param_string('cure', '');
        $cure_price = post_param_integer('cure_price', 0);
        $immunization = post_param_string('immunization', '');
        $immunization_price = post_param_integer('immunization_price', 0);
        $spread_rate = post_param_integer('spread_rate', 12);
        $points_per_spread = post_param_integer('points_per_spread', 10);
        $enabled = post_param_integer('enabled', 0);

        require_code('themes2');
        $url = post_param_image('image', 'uploads/disastr_addon', null, true, true);

        require_code('files2');
        delete_upload('uploads/disastr_addon', 'diseases', 'image', 'id', $id, $url);

        $map = array('name' => $name, 'cure' => $cure, 'cure_price' => $cure_price, 'immunisation' => $immunization, 'immunisation_price' => $immunization_price, 'spread_rate' => $spread_rate, 'points_per_spread' => $points_per_spread, 'enabled' => $enabled);
        if ($url !== null) {
            $map['image'] = $url;
        }
        $GLOBALS['SITE_DB']->query_update('diseases', $map, array('id' => $id), '', 1);

        return null;
    }

    /**
     * Standard crud_module delete actualiser.
     *
     * @param  ID_TEXT $id The entry being deleted
     */
    public function delete_actualisation($_id)
    {
        $id = intval($_id);

        require_code('files2');
        delete_upload('uploads/disastr_addon', 'diseases', 'image', 'id', $id);

        $GLOBALS['SITE_DB']->query_delete('diseases', array('id' => $id), '', 1);
    }
}
