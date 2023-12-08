<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    classified_ads
 */

/**
 * Module page class.
 */
class Module_classifieds
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
        $info['version'] = 2;
        $info['update_require_upgrade'] = true;
        $info['locked'] = false;
        return $info;
    }

    /**
     * Uninstall the module.
     */
    public function uninstall()
    {
        $GLOBALS['SITE_DB']->drop_table_if_exists('classifieds_prices');
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
            $GLOBALS['SITE_DB']->create_table('classifieds_prices', array(
                'id' => '*AUTO',
                'c_catalogue_name' => 'ID_TEXT',
                //'c_category_id' => '?AUTO_LINK',
                'c_days' => 'INTEGER',
                'c_label' => 'SHORT_TRANS',
                'c_price' => 'REAL',
            ));
            $GLOBALS['SITE_DB']->create_index('classifieds_prices', 'c_catalogue_name', array('c_catalogue_name'));

            require_lang('classifieds');

            $prices = array(
                'ONE_WEEK' => array(0.0, 7),
                'ONE_MONTH' => array(5.0, 30),
                'THREE_MONTHS' => array(12.0, 90),
                'SIX_MONTHS' => array(20.0, 180),
                'ONE_YEAR' => array(32.0, 365),
            );
            foreach ($prices as $level => $bits) {
                list($price, $days) = $bits;
                $map = array(
                    'c_catalogue_name' => 'classifieds',
                    'c_days' => $days,
                    'c_price' => $price,
                );
                $map += insert_lang('c_label', do_lang('CLASSIFIEDS_DEFAULT_PRICE_LEVEL_' . $level), 2);
                $GLOBALS['SITE_DB']->query_insert('classifieds_prices', $map);
            }
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
        $ret = array();
        if (!$check_perms || !is_guest($member_id)) {
            $ret['adverts'] = array('CLASSIFIED_ADVERTS', 'menu/classifieds');
        }
        return $ret;
    }

    public $title;

    /**
     * Module pre-run function. Allows us to know metadata for <head> before we start streaming output.
     *
     * @return ?Tempcode Tempcode indicating some kind of exceptional output (null: none).
     */
    public function pre_run()
    {
        i_solemnly_declare(I_UNDERSTAND_SQL_INJECTION | I_UNDERSTAND_XSS | I_UNDERSTAND_PATH_INJECTION);

        $type = get_param_string('type', 'adverts');

        require_lang('classifieds');

        $member_id = get_param_integer('member_id', get_member());
        $this->title = get_screen_title(($member_id == get_member()) ? 'CLASSIFIED_ADVERTS' : '_CLASSIFIED_ADVERTS', true, array($GLOBALS['FORUM_DRIVER']->get_username($member_id, true)));

        return null;
    }

    /**
     * Standard module run function.
     *
     * @return Tempcode The output of the run
     */
    public function run()
    {
        $type = get_param_string('type', 'adverts');

        if ($type == 'adverts') {
            return $this->adverts();
        }

        return new Tempcode();
    }

    /**
     * View an overview of the members adverts on the system.
     *
     * @return Tempcode The UI
     */
    public function adverts()
    {
        require_code('catalogues');
        require_code('ecommerce');

        $member_id = get_param_integer('member_id', get_member());

        if (is_guest()) {
            access_denied('NOT_AS_GUEST');
        }

        enforce_personal_access($member_id);

        $start = get_param_integer('classifieds_start', 0);
        $max = get_param_integer('classifieds_max', 30);

        require_code('templates_pagination');

        $max_rows = $GLOBALS['SITE_DB']->query_select_value('catalogue_entries e JOIN ' . get_table_prefix() . 'classifieds_prices c ON c.c_catalogue_name=e.c_name', 'COUNT(*)', array('ce_submitter' => $member_id));

        $rows = $GLOBALS['SITE_DB']->query_select('catalogue_entries e JOIN ' . get_table_prefix() . 'classifieds_prices c ON c.c_catalogue_name=e.c_name', array('e.*'), array('ce_submitter' => $member_id), 'ORDER BY ce_add_date DESC');
        $rows = remove_duplicate_rows($rows, 'e_id');
        if (count($rows) == 0) {
            inform_exit(do_lang_tempcode('NO_ENTRIES', 'catalogue_entry'));
        }

        $ads = array();
        foreach ($rows as $row) {
            $data_map = get_catalogue_entry_map($row, null, 'CATEGORY', 'DEFAULT', get_param_integer('keep_catalogue_' . $row['c_name'] . '_root', null), null, array(0));
            $ad_title = $data_map['FIELD_0'];

            $purchase_url = build_url(array('page' => 'purchase', 'type' => 'browse', 'filter' => 'CLASSIFIEDS_ADVERT', 'id' => $row['id']), get_module_zone('purchase'));

            // We'll show all transactions against this ad
            $transaction_details = $GLOBALS['SITE_DB']->query('SELECT * FROM ' . get_table_prefix() . 'transactions WHERE t_purchase_id=' . strval($row['id']) . ' AND t_type_code LIKE \'' . db_encode_like('CLASSIFIEDS\_ADVERT\_%') . '\'');
            $_transaction_details = array();
            foreach ($transaction_details as $t) {
                list($found,) = find_product_row($t['t_type_code']);
                if (!is_null($found)) {
                    $item_title = $found[4];
                } else {
                    $item_title = do_lang('UNKNOWN');
                }

                $_transaction_details[] = array(
                    'T_ID' => strval($t['id']),
                    'T_PURCHASE_ID' => strval($t['t_purchase_id']),
                    'T_STATUS' => $t['t_status'],
                    'T_REASON' => $t['t_reason'],
                    'T_AMOUNT' => float_format($t['t_amount']),
                    'T_CURRENCY' => $t['t_currency'],
                    'T_PARENT_TXN_ID' => $t['t_parent_txn_id'],
                    'T_TIME' => strval($t['t_time']),
                    'T_TYPE_CODE' => $t['t_type_code'],
                    'T_ITEM_TITLE' => $item_title,
                    'T_PENDING_REASON' => $t['t_pending_reason'],
                    'T_MEMO' => $t['t_memo'],
                    'T_VIA' => $t['t_via']
                );
            }
            $url_map = array('page' => 'catalogues', 'type' => 'entry', 'id' => $row['id']);
            $url = build_url($url_map, '_SELF');

            // No known expiry status: put on free, or let expire
            if ($row['ce_last_moved'] == $row['ce_add_date']) {
                require_code('classifieds');
                initialise_classified_listing($row);
            }

            $ads[] = array(
                'AD_TITLE' => $ad_title,
                'TRANSACTION_DETAILS' => $_transaction_details,
                'DATE' => get_timezoned_date($row['ce_add_date']),
                'DATE_RAW' => strval($row['ce_add_date']),
                'EXPIRES_DATE' => get_timezoned_date($row['ce_last_moved']),
                'EXPIRES_DATE_RAW' => strval($row['ce_last_moved']),
                'ACTIVE' => $row['ce_validated'] == 1,
                'PURCHASE_URL' => $purchase_url,
                'ID' => strval($row['id']),
                'URL' => $url,
                'NUM_VIEWS' => integer_format($row['ce_views']),
            );
        }

        $pagination = pagination(do_lang('CLASSIFIEDS'), $start, 'classifieds_start', $max, 'classifieds_max', $max_rows);

        $tpl = do_template('CLASSIFIED_ADVERTS_SCREEN', array('_GUID' => 'b25659c245a738b4f161dc87869d9edc', 'TITLE' => $this->title, 'PAGINATION' => $pagination, 'ADS' => $ads));

        require_code('templates_internalise_screen');
        return internalise_own_screen($tpl);
    }
}
