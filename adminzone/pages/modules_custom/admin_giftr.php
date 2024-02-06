<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    giftr
 */

require_code('crud_module');

/**
 * Module page class.
 */
class Module_admin_giftr extends Standard_crud_module
{
    protected $lang_type = 'GIFT';
    protected $select_name = 'NAME';
    protected $possibly_some_kind_of_upload = true;
    protected $output_of_action_is_confirmation = true;
    protected $menu_label = 'GIFTR_TITLE';
    protected $do_preview = null;
    protected $view_entry_point = '_SEARCH:admin_giftr:view:_ID';

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
        $info['version'] = 6;
        $info['update_require_upgrade'] = true;
        $info['locked'] = false;
        $info['min_cms_version'] = 11.0;
        $info['addon'] = 'giftr';
        return $info;
    }

    /**
     * Uninstall the module.
     */
    public function uninstall()
    {
        $tables = [
            'giftr',
            'members_gifts',
        ];
        $GLOBALS['SITE_DB']->drop_table_if_exists($tables);

        //require_code('files');
        //deldir_contents(get_custom_file_base() . '/uploads/giftr_addon', true);
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
            $GLOBALS['SITE_DB']->create_table('giftr', [
                'id' => '*AUTO',
                'name' => 'SHORT_TEXT',
                'image' => 'URLPATH',
                'price' => 'INTEGER',
                'enabled' => 'BINARY',
                'category' => 'SHORT_TEXT',
            ]);

            $default_gifts = [];
            $default_gifts[] = ['name' => 'A Bouquet of Flowers', 'image' => 'data_custom/images/giftr/bouquet_of_flowers.gif', 'price' => 10, 'enabled' => 1];
            $default_gifts[] = ['name' => 'A bag of Money!', 'image' => 'data_custom/images/giftr/money_bag_icon.gif', 'price' => 10, 'enabled' => 1];
            $default_gifts[] = ['name' => 'A glass of Beer', 'image' => 'data_custom/images/giftr/glass_of_beer.gif', 'price' => 10, 'enabled' => 1];
            $default_gifts[] = ['name' => 'A Birthday Cake, Happy Birthday!!', 'image' => 'data_custom/images/giftr/birthday_cake.gif', 'price' => 10, 'enabled' => 1];
            $default_gifts[] = ['name' => 'A Football', 'image' => 'data_custom/images/giftr/football.gif', 'price' => 10, 'enabled' => 1];
            $default_gifts[] = ['name' => 'Some Party Streamers, Lets Celebrate!!', 'image' => 'data_custom/images/giftr/ghirlande_festa.gif', 'price' => 10, 'enabled' => 1];
            $default_gifts[] = ['name' => 'A Love Heart', 'image' => 'data_custom/images/giftr/love_heart.gif', 'price' => 10, 'enabled' => 1];
            $default_gifts[] = ['name' => 'A Cocktail', 'image' => 'data_custom/images/giftr/hrum_cocktail.gif', 'price' => 10, 'enabled' => 1];
            $default_gifts[] = ['name' => 'Some Balloons', 'image' => 'data_custom/images/giftr/jean_victor_balin_balloons.gif', 'price' => 10, 'enabled' => 1];
            $default_gifts[] = ['name' => 'A four leaf Clover, Good Luck!', 'image' => 'data_custom/images/giftr/liftarn_four_leaf_clover.gif', 'price' => 10, 'enabled' => 1];
            $default_gifts[] = ['name' => 'A Green hat', 'image' => 'data_custom/images/giftr/liftarn_green_hat.gif', 'price' => 10, 'enabled' => 1];
            $default_gifts[] = ['name' => 'A Glass of Wine', 'image' => 'data_custom/images/giftr/muga_glass_of_red_wine.png', 'price' => 10, 'enabled' => 1];
            $default_gifts[] = ['name' => 'A Valentine\'s day Kiss', 'image' => 'data_custom/images/giftr/reporter_happy_valentine.gif', 'price' => 10, 'enabled' => 1];
            $default_gifts[] = ['name' => 'A Drum kit', 'image' => 'data_custom/images/giftr/drum_kit_3.jpg', 'price' => 10, 'enabled' => 1];
            $default_gifts[] = ['name' => 'An Electric Guitar', 'image' => 'data_custom/images/giftr/electric_guitar.jpg', 'price' => 10, 'enabled' => 1];
            $default_gifts[] = ['name' => 'A Piano', 'image' => 'data_custom/images/giftr/piano.jpg', 'price' => 10, 'enabled' => 1];
            $default_gifts[] = ['name' => 'A single red Rose', 'image' => 'data_custom/images/giftr/red-rose.jpg', 'price' => 10, 'enabled' => 1];
            $default_gifts[] = ['name' => 'Some Champagne', 'image' => 'data_custom/images/giftr/champagne.jpg', 'price' => 10, 'enabled' => 1];
            $default_gifts[] = ['name' => 'A Kiss', 'image' => 'data_custom/images/giftr/kiss.jpg', 'price' => 10, 'enabled' => 1];
            $default_gifts[] = ['name' => 'A Love note', 'image' => 'data_custom/images/giftr/love_note.jpg', 'price' => 10, 'enabled' => 1];
            $default_gifts[] = ['name' => 'A Santa hat', 'image' => 'data_custom/images/giftr/santa_hat.jpg', 'price' => 10, 'enabled' => 1];
            $GLOBALS['SITE_DB']->query_insert('giftr', [
                'name' => collapse_1d_complexity('name', $default_gifts),
                'image' => collapse_1d_complexity('image', $default_gifts),
                'price' => collapse_1d_complexity('price', $default_gifts),
                'enabled' => collapse_1d_complexity('enabled', $default_gifts),
                'category' => array_fill(0, count($default_gifts), do_lang('DEFAULT')),
            ]);

            $GLOBALS['SITE_DB']->create_table('members_gifts', [
                'id' => '*AUTO',
                'to_member_id' => 'MEMBER',
                'from_member_id' => 'MEMBER',
                'gift_id' => 'AUTO_LINK',
                'add_time' => 'TIME',
                'is_anonymous' => 'BINARY',
                'gift_message' => 'LONG_TEXT',
            ]);
        }

        if (($upgrade_from !== null) && ($upgrade_from < 3)) { // LEGACY
            $GLOBALS['SITE_DB']->add_table_field('giftr', 'category', 'SHORT_TEXT', do_lang('GENERAL'));

            $GLOBALS['SITE_DB']->alter_table_field('members_gifts', 'annonymous', 'BINARY', 'is_anonymous');
            $GLOBALS['SITE_DB']->alter_table_field('members_gifts', 'topic_id', '?AUTO_LINK');
        }

        if (($upgrade_from !== null) && ($upgrade_from < 4)) { // LEGACY
            $GLOBALS['SITE_DB']->delete_table_field('members_gifts', 'topic_id');
            $GLOBALS['SITE_DB']->alter_table_field('members_gifts', 'from_user_id', 'MEMBER', 'from_member_id');
            $GLOBALS['SITE_DB']->alter_table_field('members_gifts', 'to_user_id', 'MEMBER', 'to_member_id');
        }

        if (($upgrade_from !== null) && ($upgrade_from < 5)) { // LEGACY
            $GLOBALS['SITE_DB']->rename_table('ocgifts', 'giftr');
        }

        if (($upgrade_from !== null) && ($upgrade_from < 6)) { // LEGACY
            $GLOBALS['SITE_DB']->alter_table_field('giftr', 'image', 'URLPATH');
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
        if (!addon_installed('giftr')) {
            return null;
        }

        if (get_forum_type() != 'cns') {
            return null;
        }

        return [
            'browse' => ['MANAGE_GIFTS', 'spare/gifts'],
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
        if (!addon_installed__messaged('giftr', $error_msg)) {
            return $error_msg;
        }

        if (!addon_installed('ecommerce')) {
            warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('ecommerce')));
        }
        if (!addon_installed('points')) {
            warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('points')));
        }

        if (get_forum_type() != 'cns') {
            warn_exit(do_lang_tempcode('NO_CNS'));
        }

        $type = get_param_string('type', 'browse');

        require_lang('giftr');

        set_helper_panel_tutorial('tut_subcom');

        if ($type == 'view') {
            breadcrumb_set_self(do_lang_tempcode('VIEW_GIFT'));
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
        $this->edit_this_label = do_lang_tempcode('EDIT_THIS_GIFT');

        cns_require_all_forum_stuff();

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
            get_screen_title('GIFTR_TITLE'),
            comcode_lang_string('DOC_GIFTR'),
            [
                ['admin/add', ['_SELF', ['type' => 'add'], '_SELF'], do_lang('ADD_GIFT')],
                ['admin/edit', ['_SELF', ['type' => 'edit'], '_SELF'], do_lang('EDIT_GIFT')],
            ],
            do_lang('GIFTR_TITLE')
        );
    }

    /**
     * The UI to view details of a gift.
     *
     * @return Tempcode The UI
     */
    public function view() : object
    {
        $id = null;
        $name = '';
        $image = '';
        $price = 0;
        $category = '';
        $enabled = do_lang_tempcode('GIFT_DISABLED');

        $id = get_param_integer('id', null);
        if ($id !== null) {
            $rows = $GLOBALS['SITE_DB']->query_select('giftr', ['*'], ['id' => $id], '', 1);

            if (isset($rows[0]['id']) && $rows[0]['id'] > 0) {
                $id = $rows[0]['id'];
                $name = $rows[0]['name'];
                $image = $rows[0]['image'];
                $price = $rows[0]['price'];
                $category = $rows[0]['category'];
                $enabled = ($rows[0]['enabled'] == 1) ? do_lang_tempcode('ENABLED') : do_lang_tempcode('GIFT_DISABLED');
            }
        }

        $fields = [
            'NAME' => $name,
            'IMAGE' => $image,
            'PRICE' => integer_format($price, 0),
            'CATEGORY' => $category,
            'ENABLED' => $enabled,
        ];

        require_code('templates_map_table');
        return map_table_screen(get_screen_title('VIEW_GIFT'), $fields, true);
    }

    public function get_form_fields_for_add()
    {
        return $this->get_form_fields();
    }

    public function get_form_fields($id = null, $name = '', $category = '', $image = '', $price = 10, $enabled = 1)
    {
        $fields = new Tempcode();
        $hidden = new Tempcode();

        $fields->attach(form_input_line(do_lang_tempcode('GIFT'), do_lang_tempcode('DESCRIPTION_GIFT'), 'gift_name', $name, true));

        $fields->attach(form_input_line(do_lang_tempcode('CATEGORY'), do_lang_tempcode('DESCRIPTION_GIFT_CATEGORY'), 'category', $category, true));

        require_code('images');
        $fields->attach(form_input_upload_multi_source(do_lang_tempcode('IMAGE'), '', $hidden, 'image', null, true, $image, false, null, IMAGE_CRITERIA_WEBSAFE));

        $fields->attach(form_input_integer(do_lang_tempcode('PRICE'), '', 'price', $price, true));

        $fields->attach(form_input_tick(do_lang_tempcode('ENABLED'), do_lang_tempcode('DESCRIPTION_GIFT_ENABLED'), 'enabled', $enabled == 1));

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

        $rows = $GLOBALS['SITE_DB']->query_select('giftr', ['*'], []);

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
        $rows = $GLOBALS['SITE_DB']->query_select('giftr', ['*'], ['id' => intval($id)]);
        if (!array_key_exists(0, $rows)) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
        }
        $myrow = $rows[0];

        $name = $myrow['name'];
        $image = $myrow['image'];
        $price = $myrow['price'];
        $enabled = $myrow['enabled'];
        $category = $myrow['category'];

        $ret = $this->get_form_fields($id, $name, $category, $image, $price, $enabled);

        return $ret;
    }

    /**
     * Standard crud_module add actualiser.
     *
     * @return array A pair: The entry added, description about usage
     */
    public function add_actualisation() : array
    {
        $name = post_param_string('gift_name');
        $category = post_param_string('category');
        $price = post_param_integer('price');
        $enabled = post_param_integer('enabled', 0);

        require_code('images2');
        $url = post_param_image('image', 'uploads/giftr_addon', null, true);

        $id = $GLOBALS['SITE_DB']->query_insert('giftr', ['name' => $name, 'image' => $url, 'price' => $price, 'enabled' => $enabled, 'category' => $category], true);

        log_it('ADD_GIFT', strval($id), $name);

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

        $name = post_param_string('gift_name');
        $category = post_param_string('category');
        $price = post_param_integer('price');
        $enabled = post_param_integer('enabled', 0);

        require_code('images2');
        $url = post_param_image('image', 'uploads/giftr_addon', null, true, true);

        require_code('files2');
        delete_upload('uploads/giftr_addon', 'giftr', 'image', 'id', $id, $url);

        $map = ['name' => $name, 'price' => $price, 'enabled' => $enabled, 'category' => $category];
        if ($url !== null) {
            $map['image'] = $url;
        }
        $GLOBALS['SITE_DB']->query_update('giftr', $map, ['id' => $id], '', 1);

        log_it('EDIT_GIFT', strval($id), $name);

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

        $name = $GLOBALS['SITE_DB']->query_select_value_if_there('giftr', 'name', ['id' => $id]);
        if ($name === null) {
            warn_exit(do_lang_tempcode('MISSING_RESOURCE'));
        }

        require_code('files2');
        delete_upload('uploads/giftr_addon', 'giftr', 'image', 'id', $id);

        $GLOBALS['SITE_DB']->query_delete('giftr', ['id' => $id], '', 1);

        log_it('DELETE_GIFT', strval($id), $name);
    }
}
