<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    disastr
 */

require_code('crud_module');

/**
 * Module page class.
 */
class Module_admin_disastr extends Standard_crud_module
{
    protected $lang_type = 'DISEASE';
    protected $select_name = 'NAME';
    protected $possibly_some_kind_of_upload = true;
    protected $output_of_action_is_confirmation = true;
    protected $menu_label = 'DISASTR_TITLE';
    protected $do_preview = null;
    protected $view_entry_point = '_SEARCH:admin_disastr:view:_ID';

    /**
     * Find details of the module.
     *
     * @return ?array Map of module info (null: module is disabled)
     */
    public function info() : ?array
    {
        $info = [];
        $info['author'] = 'Chris Graham';
        $info['organisation'] = 'Composr';
        $info['hacked_by'] = null;
        $info['hack_version'] = null;
        $info['version'] = 4;
        $info['update_require_upgrade'] = true;
        $info['locked'] = false;
        $info['min_cms_version'] = 11.0;
        $info['addon'] = 'disastr';
        return $info;
    }

    /**
     * Uninstall the module.
     */
    public function uninstall()
    {
        $tables = [
            'diseases',
            'members_diseases',
        ];
        $GLOBALS['SITE_DB']->drop_table_if_exists($tables);

        //require_code('files');
        //deldir_contents(get_custom_file_base() . '/uploads/disastr_addon', true);
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
            $GLOBALS['SITE_DB']->create_table('diseases', [
                'id' => '*AUTO',
                'name' => 'SHORT_TEXT',
                'image_url' => 'URLPATH',
                'cure' => 'SHORT_TEXT',
                'cure_price' => 'INTEGER',
                'immunisation' => 'SHORT_TEXT',
                'immunisation_price' => 'INTEGER',
                'spread_rate' => 'INTEGER',
                'points_per_spread' => 'INTEGER',
                'last_spread_time' => 'TIME',
                'enabled' => 'BINARY',
            ]);

            $GLOBALS['SITE_DB']->create_table('members_diseases', [
                'member_id' => '*MEMBER',
                'disease_id' => '*AUTO_LINK',
                'sick' => 'BINARY',
                'cure' => 'BINARY',
                'immunisation' => 'BINARY',
            ]);

            // TODO: Move to pre-defined content
            $GLOBALS['SITE_DB']->query_insert('diseases', ['name' => 'Zombiism', 'image_url' => 'data_custom/images/disastr/hazard.png', 'cure' => 'Zombiism vaccine', 'cure_price' => 100, 'immunisation' => 'Immunise yourself from Zombiism', 'immunisation_price' => 50, 'spread_rate' => 12, 'points_per_spread' => 10, 'last_spread_time' => 0, 'enabled' => 1], true);
            $GLOBALS['SITE_DB']->query_insert('diseases', ['name' => 'A bad case of Hiccups', 'image_url' => 'data_custom/images/disastr/hazard.png', 'cure' => 'Hiccup vaccine', 'cure_price' => 100, 'immunisation' => 'Immunise yourself from the Hiccups', 'immunisation_price' => 50, 'spread_rate' => 12, 'points_per_spread' => 10, 'last_spread_time' => 0, 'enabled' => 1], true);
            $GLOBALS['SITE_DB']->query_insert('diseases', ['name' => 'Vampirism', 'image_url' => 'data_custom/images/disastr/hazard.png', 'cure' => 'Vampirism vaccine', 'cure_price' => 100, 'immunisation' => 'Immunise yourself against Vampirism', 'immunisation_price' => 50, 'spread_rate' => 12, 'points_per_spread' => 10, 'last_spread_time' => 0, 'enabled' => 1], true);
            $GLOBALS['SITE_DB']->query_insert('diseases', ['name' => 'The Flu', 'image_url' => 'data_custom/images/disastr/hazard.png', 'cure' => 'Flu vaccine', 'cure_price' => 100, 'immunisation' => 'Immunise yourself against the Flu', 'immunisation_price' => 50, 'spread_rate' => 12, 'points_per_spread' => 10, 'last_spread_time' => 0, 'enabled' => 1], true);
            $GLOBALS['SITE_DB']->query_insert('diseases', ['name' => 'Lice', 'image_url' => 'data_custom/images/disastr/hazard.png', 'cure' => 'Lice-Away Spray', 'cure_price' => 100, 'immunisation' => 'Lice repellant', 'immunisation_price' => 50, 'spread_rate' => 12, 'points_per_spread' => 10, 'last_spread_time' => 0, 'enabled' => 1], true);
            $GLOBALS['SITE_DB']->query_insert('diseases', ['name' => 'Fleas', 'image_url' => 'data_custom/images/disastr/hazard.png', 'cure' => 'Flea spray', 'cure_price' => 100, 'immunisation' => 'Flea repellant', 'immunisation_price' => 50, 'spread_rate' => 12, 'points_per_spread' => 10, 'last_spread_time' => 0, 'enabled' => 1], true);
            $GLOBALS['SITE_DB']->query_insert('diseases', ['name' => 'Man-Flu', 'image_url' => 'data_custom/images/disastr/hazard.png', 'cure' => 'Lots and lots of TLC', 'cure_price' => 1000, 'immunisation' => 'Anti Man-Flu Serum', 'immunisation_price' => 250, 'spread_rate' => 12, 'points_per_spread' => 100, 'last_spread_time' => 0, 'enabled' => 1], true);
        }

        if (($upgrade_from !== null) && ($upgrade_from < 3)) { // LEGACY
            $GLOBALS['SITE_DB']->alter_table_field('members_diseases', 'desease_id', 'AUTO_LINK', 'disease_id');
            $GLOBALS['SITE_DB']->alter_table_field('members_diseases', 'user_id', '*MEMBER', 'member_id');
        }

        if (($upgrade_from !== null) && ($upgrade_from < 4)) { // LEGACY: 11.beta1
            // Database consistency fixes
            $GLOBALS['SITE_DB']->alter_table_field('diseases', 'image', 'URLPATH', 'image_url');
            $GLOBALS['SITE_DB']->alter_table_field('diseases', 'last_spread_time', 'TIME');
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
        if (!addon_installed('disastr')) {
            return null;
        }

        if (get_forum_type() != 'cns') {
            return null;
        }

        return [
            'browse' => ['MANAGE_DISEASES', 'spare/disaster'],
        ] + parent::get_entry_points();
    }

    public $title;

    /**
     * Module pre-run function. Allows us to know metadata for <head> before we start streaming output.
     *
     * @param  boolean $top_level Whether this is running at the top level, prior to having sub-objects called
     * @param  ?ID_TEXT $type The screen type to consider for metadata purposes (null: read from environment)
     * @return ?Tempcode Tempcode indicating some kind of exceptional output (null: none)
     */
    public function pre_run(bool $top_level = true, ?string $type = null) : ?object
    {
        i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

        $error_msg = new Tempcode();
        if (!addon_installed__messaged('disastr', $error_msg)) {
            return $error_msg;
        }

        if (!addon_installed('points')) {
            warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('points')));
        }

        if (get_forum_type() != 'cns') {
            warn_exit(do_lang_tempcode('NO_CNS'));
        }

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
    public function run_start(string $type) : object
    {
        cns_require_all_forum_stuff();

        $this->edit_this_label = do_lang_tempcode('EDIT_THIS_DISEASE');

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
    public function browse() : object
    {
        require_code('templates_donext');
        return do_next_manager(
            get_screen_title('DISASTR_TITLE'),
            comcode_lang_string('DOC_DISASTR'),
            [
                ['admin/add', ['_SELF', ['type' => 'add'], '_SELF'], do_lang('ADD_DISEASE')],
                ['admin/edit', ['_SELF', ['type' => 'edit'], '_SELF'], do_lang('EDIT_DISEASE')],
            ],
            do_lang('DISASTR_TITLE')
        );
    }

    public function view()
    {
        $title = get_screen_title('VIEW_DISEASE');

        $id = get_param_integer('id');

        $rows = $GLOBALS['SITE_DB']->query_select('diseases', ['*'], ['id' => $id], '', 1);
        if (!isset($rows[0])) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
        }

        $name = $rows[0]['name'];

        require_code('images');
        $image = do_image_thumb($rows[0]['image'], $name);

        $cure = $rows[0]['cure'];
        $cure_price = $rows[0]['cure_price'];
        $immunisation = $rows[0]['immunisation'];
        $immunisation_price = $rows[0]['immunisation_price'];

        $spread_rate = $rows[0]['spread_rate'];
        $points_per_spread = $rows[0]['points_per_spread'];

        $enabled = ($rows[0]['enabled'] == 1) ? do_lang_tempcode('YES') : do_lang_tempcode('NO');

        $fields = [
            'NAME' => $name,
            'image_url' => $image,
            'CURE' => $cure,
            'CURE_PRICE' => integer_format($cure_price, 0),
            'IMMUNISATION' => $immunisation,
            'IMMUNISATION_PRICE' => integer_format($immunisation_price, 0),
            'SPREAD_RATE' => integer_format($spread_rate),
            'POINTS_PER_SPREAD' => integer_format($points_per_spread, 0),
            'ENABLED' => $enabled,
        ];

        require_code('templates_map_table');
        return map_table_screen($title, $fields, true);
    }

    public function get_form_fields_for_add()
    {
        return $this->get_form_fields();
    }

    public function get_form_fields($id = null, $name = '', $image = '', $cure = '', $cure_price = 10, $immunisation = '', $immunisation_price = 5, $spread_rate = 12, $points_per_spread = 10, $enabled = 1)
    {
        $fields = new Tempcode();
        $hidden = new Tempcode();

        $fields->attach(form_input_line(do_lang_tempcode('DISEASE'), do_lang_tempcode('DESCRIPTION_DISEASE'), 'disease_name', $name, true));

        require_code('images');
        require_code('themes2');
        $fields->attach(form_input_upload_multi_source(do_lang_tempcode('IMAGE'), '', $hidden, 'image', null, true, $image, false, null, IMAGE_CRITERIA_WEBSAFE));

        $fields->attach(form_input_line(do_lang_tempcode('CURE'), do_lang_tempcode('DESCRIPTION_CURE'), 'cure', $cure, true));
        $fields->attach(form_input_line(do_lang_tempcode('CURE_PRICE'), '', 'cure_price', strval($cure_price), true));
        $fields->attach(form_input_line(do_lang_tempcode('IMMUNISATION'), do_lang_tempcode('DESCRIPTION_IMMUNISATION'), 'immunisation', $immunisation, true));
        $fields->attach(form_input_line(do_lang_tempcode('IMMUNISATION_PRICE'), '', 'immunisation_price', strval($immunisation_price), true));
        $fields->attach(form_input_line(do_lang_tempcode('SPREAD_RATE'), do_lang_tempcode('DESCRIPTION_SPREAD_RATE'), 'spread_rate', strval($spread_rate), true));
        $fields->attach(form_input_line(do_lang_tempcode('POINTS_PER_SPREAD'), do_lang_tempcode('DESCRIPTION_POINTS_PER_SPREAD'), 'points_per_spread', strval($points_per_spread), true));
        $fields->attach(form_input_tick(do_lang_tempcode('ENABLED'), do_lang_tempcode('DESCRIPTION_DISEASE_ENABLED'), 'enabled', $enabled == 1));

        return [$fields, $hidden];
    }

    /**
     * Standard crud_module list function.
     *
     * @return Tempcode The selection list
     */
    public function create_selection_list_entries() : object
    {
        $fields = new Tempcode();

        $rows = $GLOBALS['SITE_DB']->query_select('diseases', ['*'], [], 'ORDER BY name');

        foreach ($rows as $row) {
            $fields->attach(form_input_list_entry(strval($row['id']), false, $row['name']));
        }

        return $fields;
    }

    /**
     * Standard crud_module edit form filler.
     *
     * @param  ID_TEXT $id The entry being edited
     * @return mixed Either Tempcode; or a tuple of: (fields, hidden-fields[, delete-fields][, edit-text][, whether all delete fields are specified][, posting form text, more fields][, parsed WYSIWYG editable text])
     */
    public function fill_in_edit_form(string $id)
    {
        $rows = $GLOBALS['SITE_DB']->query_select('diseases', ['*'], ['id' => intval($id)]);
        if (!array_key_exists(0, $rows)) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
        }
        $myrow = $rows[0];

        $name = $myrow['name'];
        $image = $myrow['image_url'];
        $cure = $myrow['cure'];
        $cure_price = $myrow['cure_price'];
        $immunisation = $myrow['immunisation'];
        $immunisation_price = $myrow['immunisation_price'];
        $spread_rate = $myrow['spread_rate'];
        $points_per_spread = $myrow['points_per_spread'];
        $enabled = $myrow['enabled'];

        $ret = $this->get_form_fields($id, $name, $image, $cure, $cure_price, $immunisation, $immunisation_price, $spread_rate, $points_per_spread, $enabled);

        return $ret;
    }

    /**
     * Standard crud_module add actualiser.
     *
     * @return array A pair: The entry added, description about usage
     */
    public function add_actualisation() : array
    {
        $name = post_param_string('disease_name', '');
        $cure = post_param_string('cure', '');
        $cure_price = post_param_integer('cure_price', 0);
        $immunisation = post_param_string('immunisation', '');
        $immunisation_price = post_param_integer('immunisation_price', 0);
        $spread_rate = post_param_integer('spread_rate', 12);
        $points_per_spread = post_param_integer('points_per_spread', 10);
        $enabled = post_param_integer('enabled', 0);

        require_code('images2');
        $url = post_param_image('image', 'uploads/disastr_addon', null, true);

        $id = $GLOBALS['SITE_DB']->query_insert('diseases', ['name' => $name, 'image_url' => $url, 'cure' => $cure, 'cure_price' => $cure_price, 'immunisation' => $immunisation, 'immunisation_price' => $immunisation_price, 'spread_rate' => $spread_rate, 'points_per_spread' => $points_per_spread, 'last_spread_time' => 0, 'enabled' => $enabled], true);

        log_it('ADD_DISEASE', strval($id), $name);

        log_it('ADD_DISEASE', strval($id), $name);

        return [strval($id), null];
    }

    /**
     * Standard crud_module edit actualiser.
     *
     * @param  ID_TEXT $_id The entry being edited
     * @return ?Tempcode Description about usage (null: none)
     */
    public function edit_actualisation(string $_id) : ?object
    {
        $id = intval($_id);

        $name = post_param_string('disease_name', '');
        $cure = post_param_string('cure', '');
        $cure_price = post_param_integer('cure_price', 0);
        $immunisation = post_param_string('immunisation', '');
        $immunisation_price = post_param_integer('immunisation_price', 0);
        $spread_rate = post_param_integer('spread_rate', 12);
        $points_per_spread = post_param_integer('points_per_spread', 10);
        $enabled = post_param_integer('enabled', 0);

        require_code('images2');
        $url = post_param_image('image', 'uploads/disastr_addon', null, true, true);

        require_code('files2');
        delete_upload('uploads/disastr_addon', 'diseases', 'image', 'id', $id, $url);

        $map = ['name' => $name, 'cure' => $cure, 'cure_price' => $cure_price, 'immunisation' => $immunisation, 'immunisation_price' => $immunisation_price, 'spread_rate' => $spread_rate, 'points_per_spread' => $points_per_spread, 'enabled' => $enabled];
        if ($url !== null) {
            $map['image'] = $url;
        }
        $GLOBALS['SITE_DB']->query_update('diseases', $map, ['id' => $id], '', 1);

        log_it('EDIT_DISEASE', strval($id), $name);

        return null;
    }

    /**
     * Standard crud_module delete actualiser.
     *
     * @param  ID_TEXT $_id The entry being deleted
     */
    public function delete_actualisation(string $_id)
    {
        $id = intval($_id);

        $name = $GLOBALS['SITE_DB']->query_select_value_if_there('diseases', 'name', ['id' => $id]);

        if ($name === null) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
        }

        require_code('files2');
        delete_upload('uploads/disastr_addon', 'diseases', 'image', 'id', $id);

        $GLOBALS['SITE_DB']->query_delete('diseases', ['id' => $id], '', 1);

        log_it('DELETE_DISEASE', strval($id), $name);
    }
}
