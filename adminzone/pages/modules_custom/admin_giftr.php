<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

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
    public $lang_type = 'GIFT';
    public $select_name = 'NAME';
    public $possibly_some_kind_of_upload = true;
    public $output_of_action_is_confirmation = true;
    public $menu_label = 'GIFTR_TITLE';
    public $do_preview = null;
    public $view_entry_point = '_SEARCH:admin_giftr:view:_ID';

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
        $info['version'] = 5;
        $info['update_require_upgrade'] = true;
        $info['locked'] = false;
        return $info;
    }

    /**
     * Uninstall the module.
     */
    public function uninstall()
    {
        $GLOBALS['SITE_DB']->drop_table_if_exists('giftr');
        $GLOBALS['SITE_DB']->drop_table_if_exists('members_gifts');

        //require_code('files');
        //deldir_contents(get_custom_file_base() . '/uploads/giftr_addon', true);
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
            $GLOBALS['SITE_DB']->create_table('giftr', array(
                'id' => '*AUTO',
                'name' => 'SHORT_TEXT',
                'image' => 'SHORT_TEXT',
                'price' => 'INTEGER',
                'enabled' => 'BINARY',
                'category' => 'SHORT_TEXT',
            ));

            $default_gifts = array();
            $default_gifts[] = array('name' => 'A Bouquet of Flowers', 'image' => 'data_custom/images/giftr/bouquet_of_flowers.gif', 'price' => 10, 'enabled' => 1);
            $default_gifts[] = array('name' => 'A bag of Money!', 'image' => 'data_custom/images/giftr/Money_Bag_Icon.gif', 'price' => 10, 'enabled' => 1);
            $default_gifts[] = array('name' => 'A glass of Beer', 'image' => 'data_custom/images/giftr/glass_of_beer.gif', 'price' => 10, 'enabled' => 1);
            $default_gifts[] = array('name' => 'A Birthday Cake, Happy Birthday!!', 'image' => 'data_custom/images/giftr/Birthday_cake.gif', 'price' => 10, 'enabled' => 1);
            $default_gifts[] = array('name' => 'A Football', 'image' => 'data_custom/images/giftr/3Football_%28soccer%29.gif', 'price' => 10, 'enabled' => 1);
            $default_gifts[] = array('name' => 'Some Party Streamers, Lets Celebrate!!', 'image' => 'data_custom/images/giftr/ghirlande_festa.gif', 'price' => 10, 'enabled' => 1);
            $default_gifts[] = array('name' => 'A Love Heart', 'image' => 'data_custom/images/giftr/love_heart.gif', 'price' => 10, 'enabled' => 1);
            $default_gifts[] = array('name' => 'A Cocktail', 'image' => 'data_custom/images/giftr/hrum_cocktail.gif', 'price' => 10, 'enabled' => 1);
            $default_gifts[] = array('name' => 'Some Balloons', 'image' => 'data_custom/images/giftr/jean_victor_balin_balloons.gif', 'price' => 10, 'enabled' => 1);
            $default_gifts[] = array('name' => 'A four leaf Clover, Good Luck!', 'image' => 'data_custom/images/giftr/liftarn_Four_leaf_clover.gif', 'price' => 10, 'enabled' => 1);
            $default_gifts[] = array('name' => 'A Green hat', 'image' => 'data_custom/images/giftr/liftarn_Green_hat.gif', 'price' => 10, 'enabled' => 1);
            $default_gifts[] = array('name' => 'A Glass of Wine', 'image' => 'data_custom/images/giftr/2Muga_Glass_of_red_wine.png', 'price' => 10, 'enabled' => 1);
            $default_gifts[] = array('name' => 'A Valentine\'s day Kiss', 'image' => 'data_custom/images/giftr/reporter_Happy_Valentine.gif', 'price' => 10, 'enabled' => 1);
            $default_gifts[] = array('name' => 'A Drum kit', 'image' => 'data_custom/images/giftr/Drum_Kit_3.jpg', 'price' => 10, 'enabled' => 1);
            $default_gifts[] = array('name' => 'An Electric Guitar', 'image' => 'data_custom/images/giftr/electric_guitar.jpg', 'price' => 10, 'enabled' => 1);
            $default_gifts[] = array('name' => 'A Piano', 'image' => 'data_custom/images/giftr/piano.jpg', 'price' => 10, 'enabled' => 1);
            $default_gifts[] = array('name' => 'A single red Rose', 'image' => 'data_custom/images/giftr/red-rose.jpg', 'price' => 10, 'enabled' => 1);
            $default_gifts[] = array('name' => 'Some Champagne', 'image' => 'data_custom/images/giftr/Champagne.jpg', 'price' => 10, 'enabled' => 1);
            $default_gifts[] = array('name' => 'A Kiss', 'image' => 'data_custom/images/giftr/2a%20kiss.jpg', 'price' => 10, 'enabled' => 1);
            $default_gifts[] = array('name' => 'A Love note', 'image' => 'data_custom/images/giftr/love%20note.jpg', 'price' => 10, 'enabled' => 1);
            $default_gifts[] = array('name' => 'A Santa hat', 'image' => 'data_custom/images/giftr/Santa_Hat.jpg', 'price' => 10, 'enabled' => 1);
            $GLOBALS['SITE_DB']->query_insert('giftr', array(
                'name' => collapse_1d_complexity('name', $default_gifts),
                'image' => collapse_1d_complexity('image', $default_gifts),
                'price' => collapse_1d_complexity('price', $default_gifts),
                'enabled' => collapse_1d_complexity('enabled', $default_gifts),
                'category' => array_fill(0, count($default_gifts), do_lang('DEFAULT')),
            ));

            $GLOBALS['SITE_DB']->create_table('members_gifts', array(
                'id' => '*AUTO',
                'to_member_id' => 'MEMBER',
                'from_member_id' => 'MEMBER',
                'gift_id' => 'AUTO_LINK',
                'add_time' => 'TIME',
                'is_anonymous' => 'BINARY',
                'gift_message' => 'LONG_TEXT',
            ));
        }

        if ((!is_null($upgrade_from)) && ($upgrade_from < 3)) {
            $GLOBALS['SITE_DB']->add_table_field('giftr', 'category', 'SHORT_TEXT', do_lang('GENERAL'));

            $GLOBALS['SITE_DB']->alter_table_field('members_gifts', 'annonymous', 'BINARY', 'is_anonymous');
            $GLOBALS['SITE_DB']->alter_table_field('members_gifts', 'topic_id', '?AUTO_LINK');
        }

        if ((!is_null($upgrade_from)) && ($upgrade_from < 4)) {
            $GLOBALS['SITE_DB']->delete_table_field('members_gifts', 'topic_id');
            $GLOBALS['SITE_DB']->alter_table_field('members_gifts', 'from_user_id', 'MEMBER', 'from_member_id');
            $GLOBALS['SITE_DB']->alter_table_field('members_gifts', 'to_user_id', 'MEMBER', 'to_member_id');
        }

        if ((!is_null($upgrade_from)) && ($upgrade_from < 5)) {
            $GLOBALS['SITE_DB']->rename_table('ocgifts', 'giftr');
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
            'browse' => array('MANAGE_GIFTS', 'menu/giftr'),
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

        return do_next_manager(get_screen_title('GIFTR_TITLE'), comcode_lang_string('DOC_GIFTR'),
            array(
                array('menu/_generic_admin/add_one', array('_SELF', array('type' => 'add'), '_SELF'), do_lang('ADD_GIFT')),
                array('menu/_generic_admin/edit_one', array('_SELF', array('type' => 'edit'), '_SELF'), do_lang('EDIT_GIFT')),
            ),
            do_lang('GIFTR_TITLE')
        );
    }

    /**
     * The UI to view details of a gift.
     *
     * @return Tempcode The UI
     */
    public function view()
    {
        $id = null;
        $name = '';
        $image = '';
        $price = 0;
        $category = '';
        $enabled = do_lang_tempcode('GIFT_DISABLED');

        $id = get_param_integer('id', null);
        if ($id !== null) {
            $rows = $GLOBALS['SITE_DB']->query_select('giftr', array('*'), array('id' => $id), '', 1);

            if (isset($rows[0]['id']) && $rows[0]['id'] > 0) {
                $id = $rows[0]['id'];
                $name = $rows[0]['name'];
                $image = $rows[0]['image'];
                $price = $rows[0]['price'];
                $category = $rows[0]['category'];
                $enabled = ($rows[0]['enabled'] == 1) ? do_lang_tempcode('ENABLED') : do_lang_tempcode('GIFT_DISABLED');
            }
        }

        require_code('templates_map_table');
        return map_table_screen(get_screen_title('VIEW_GIFT'), array('NAME' => $name, 'IMAGE' => $image, 'PRICE' => integer_format($price), 'CATEGORY' => $category, 'ENABLED' => $enabled));
    }

    public function get_form_fields($id = null, $name = '', $category = '', $image = '', $price = 10, $enabled = 1)
    {
        $fields = new Tempcode();
        $hidden = new Tempcode();

        require_code('form_templates');

        $fields->attach(form_input_line(do_lang_tempcode('GIFT'), do_lang_tempcode('DESCRIPTION_GIFT'), 'name', $name, true));

        $fields->attach(form_input_line(do_lang_tempcode('CATEGORY'), do_lang_tempcode('DESCRIPTION_GIFT_CATEGORY'), 'category', $category, true));

        $fields->attach(form_input_upload_multi_source(do_lang_tempcode('IMAGE'), '', $hidden, 'image', null, true, $image));

        $fields->attach(form_input_integer(do_lang_tempcode('PRICE'), '', 'price', $price, true));

        $fields->attach(form_input_tick(do_lang_tempcode('ENABLED'), do_lang_tempcode('DESCRIPTION_GIFT_ENABLED'), 'enabled', $enabled == 1));

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

        $rows = $GLOBALS['SITE_DB']->query_select('giftr', array('*'), null);

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
        $rows = $GLOBALS['SITE_DB']->query_select('giftr', array('*'), array('id' => intval($id)));
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
     * @return ID_TEXT The entry added
     */
    public function add_actualisation()
    {
        $name = post_param_string('name');
        $category = post_param_string('category');
        $price = post_param_integer('price');
        $enabled = post_param_integer('enabled', 0);

        require_code('themes2');
        $url = post_param_image('image', 'uploads/giftr_addon', null, true);

        $id = $GLOBALS['SITE_DB']->query_insert('giftr', array('name' => $name, 'image' => $url, 'price' => $price, 'enabled' => $enabled, 'category' => $category), true);

        return strval($id);
    }

    /**
     * Standard crud_module edit actualiser.
     *
     * @param  ID_TEXT $id The entry being edited
     * @return ?Tempcode Confirm message (null: continue)
     */
    public function edit_actualisation($id)
    {
        $name = post_param_string('name');
        $category = post_param_string('category');
        $price = post_param_integer('price');
        $enabled = post_param_integer('enabled', 0);

        require_code('themes2');
        $url = post_param_image('image', 'uploads/giftr_addon', null, true, true);

        require_code('files2');
        delete_upload('uploads/giftr_addon', 'giftr', 'image', 'id', intval($id), $url);

        $map = array('name' => $name, 'price' => $price, 'enabled' => $enabled, 'category' => $category);
        if ($url !== null) {
            $map['image'] = $url;
        }
        $GLOBALS['SITE_DB']->query_update('giftr', $map, array('id' => intval($id)), '', 1);

        return null;
    }

    /**
     * Standard crud_module delete actualiser.
     *
     * @param  ID_TEXT $id The entry being deleted
     */
    public function delete_actualisation($id)
    {
        require_code('files2');
        delete_upload('uploads/giftr_addon', 'giftr', 'image', 'id', intval($id));

        $GLOBALS['SITE_DB']->query_delete('giftr', array('id' => intval($id)), '', 1);
    }
}
