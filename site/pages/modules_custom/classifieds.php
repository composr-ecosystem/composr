<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

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
        $info['min_cms_version'] = 11.0;
        $info['addon'] = 'classified_ads';
        return $info;
    }

    /**
     * Uninstall the module.
     */
    public function uninstall()
    {
        $GLOBALS['SITE_DB']->drop_table_if_exists('ecom_classifieds_prices');
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
            $GLOBALS['SITE_DB']->create_table('ecom_classifieds_prices', [
                'id' => '*AUTO',
                'c_catalogue_name' => 'ID_TEXT',
                //'c_category_id' => '?AUTO_LINK',
                'c_days' => 'INTEGER',
                'c_label' => 'SHORT_TRANS',
                'c_price' => 'REAL',
            ]);
            $GLOBALS['SITE_DB']->create_index('ecom_classifieds_prices', 'c_catalogue_name', ['c_catalogue_name']);

            require_lang('classifieds');

            $prices = [
                'ONE_WEEK' => [0.0, 7],
                'ONE_MONTH' => [5.0, 30],
                'THREE_MONTHS' => [12.0, 90],
                'SIX_MONTHS' => [20.0, 180],
                'ONE_YEAR' => [32.0, 365],
            ];
            foreach ($prices as $level => $bits) {
                list($price, $days) = $bits;
                $map = [
                    'c_catalogue_name' => 'classifieds',
                    'c_days' => $days,
                    'c_price' => $price,
                ];
                $map += insert_lang('c_label', do_lang('CLASSIFIEDS_DEFAULT_PRICE_LEVEL_' . $level), 2);
                $GLOBALS['SITE_DB']->query_insert('ecom_classifieds_prices', $map);
            }
        }

        if (($upgrade_from !== null) && ($upgrade_from < 3)) {
            $GLOBALS['SITE_DB']->rename_table('classifieds_prices', 'ecom_classifieds_prices');
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
        if (!addon_installed('classified_ads')) {
            return null;
        }

        if ($member_id === null) {
            $member_id = get_member();
        }

        $ret = [];
        if ((!$check_perms || !is_guest($member_id)) && (get_forum_type() != 'cns') && ($GLOBALS['SITE_DB']->query_select_value('catalogue_entries e JOIN ' . get_table_prefix() . 'ecom_classifieds_prices c ON c.c_catalogue_name=e.c_name', 'COUNT(*)', ['ce_submitter' => $member_id]) > 0)) {
            $ret['browse'] = ['CLASSIFIED_ADVERTS', 'spare/classifieds'];
        }
        return $ret;
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
        if (!addon_installed__messaged('classified_ads', $error_msg)) {
            return $error_msg;
        }

        if (!addon_installed('catalogues')) {
            warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('catalogues')));
        }
        if (!addon_installed('ecommerce')) {
            warn_exit(do_lang_tempcode('MISSING_ADDON', escape_html('ecommerce')));
        }

        require_lang('classifieds');

        $member_id = get_param_integer('member_id', get_member());
        $this->title = get_screen_title(($member_id == get_member()) ? 'CLASSIFIED_ADVERTS' : '_CLASSIFIED_ADVERTS', true, [$GLOBALS['FORUM_DRIVER']->get_username($member_id, true)]);

        return null;
    }

    /**
     * Standard module run function.
     *
     * @return Tempcode The output of the run
     */
    public function run() : object
    {
        return $this->adverts();
    }

    /**
     * View an overview of the member's adverts on the system.
     *
     * @return Tempcode The UI
     */
    public function adverts() : object
    {
        require_code('catalogues');
        require_code('ecommerce');

        $member_id = get_param_integer('member_id', get_member());

        if (is_guest()) {
            access_denied('NOT_AS_GUEST');
        }

        enforce_personal_access($member_id);

        $start = get_param_integer('classifieds_start', 0);
        $max = get_param_integer('classifieds_max', intval(get_option('max_classified_listings_per_page')));

        require_code('templates_pagination');

        $max_rows = $GLOBALS['SITE_DB']->query_select_value('catalogue_entries e JOIN ' . get_table_prefix() . 'ecom_classifieds_prices c ON c.c_catalogue_name=e.c_name', 'COUNT(*)', ['ce_submitter' => $member_id]);

        $rows = $GLOBALS['SITE_DB']->query_select('catalogue_entries e JOIN ' . get_table_prefix() . 'ecom_classifieds_prices c ON c.c_catalogue_name=e.c_name', ['DISTINCT e.*'], ['ce_submitter' => $member_id], 'ORDER BY ce_add_date DESC'); // Join so we only find catalogues with classified prices defined
        if (empty($rows)) {
            inform_exit(do_lang_tempcode('NO_ENTRIES', 'catalogue_entry'));
        }

        $ads = [];
        foreach ($rows as $row) {
            $data_map = get_catalogue_entry_map($row, null, 'CATEGORY', 'DEFAULT', get_param_integer('keep_catalogue_' . $row['c_name'] . '_root', null), null, [0]);
            $ad_title = $data_map['FIELD_0'];

            $purchase_url = build_url(['page' => 'purchase', 'type' => 'browse', 'category' => 'classifieds', 'id' => $row['id']], get_module_zone('purchase'));

            // We'll show all transactions against this ad
            $transaction_details = $GLOBALS['SITE_DB']->query('SELECT * FROM ' . get_table_prefix() . 'ecom_transactions WHERE t_purchase_id=' . strval($row['id']) . ' AND t_type_code LIKE \'' . db_encode_like('CLASSIFIEDS\_ADVERT\_%') . '\'');
            $_transaction_details = [];
            foreach ($transaction_details as $t) {
                list($details) = find_product_details($t['t_type_code']);
                if ($details !== null) {
                    $item_title = $details['item_name'];
                } else {
                    $item_title = $t['t_type_code'];
                }

                $_transaction_details[] = [
                    'T_ID' => strval($t['id']),
                    'T_PURCHASE_ID' => strval($t['t_purchase_id']),
                    'T_STATUS' => get_transaction_status_string($t['t_status']),
                    'T_REASON' => $t['t_reason'],
                    'T_PRICE' => float_format($t['t_price']),
                    'T_TAX' => float_format($t['t_tax']),
                    'T_SHIPPING' => float_format($t['t_shipping']),
                    'T_CURRENCY' => $t['t_currency'],
                    'T_PARENT_TXN_ID' => $t['t_parent_txn_id'],
                    'T_TIME' => strval($t['t_time']),
                    'T_TYPE_CODE' => $t['t_type_code'],
                    'T_ITEM_TITLE' => $item_title,
                    'T_PENDING_REASON' => $t['t_pending_reason'],
                    'T_MEMO' => $t['t_memo'],
                    'T_PAYMENT_GATEWAY' => $t['t_payment_gateway'],
                ];
            }
            $url_map = ['page' => 'catalogues', 'type' => 'entry', 'id' => $row['id']];
            $url = build_url($url_map, '_SELF');

            // No known expiry status: put on free, or let expire
            if ($row['ce_last_moved'] == $row['ce_add_date']) {
                require_code('classifieds');
                initialise_classified_listing($row);
            }

            $ads[] = [
                'AD_TITLE' => $ad_title,
                'TRANSACTION_DETAILS' => $_transaction_details,
                'DATE' => get_timezoned_date_time($row['ce_add_date']),
                'DATE_RAW' => strval($row['ce_add_date']),
                'EXPIRES_DATE' => get_timezoned_date_time($row['ce_last_moved']),
                'EXPIRES_DATE_RAW' => strval($row['ce_last_moved']),
                'ACTIVE' => $row['ce_validated'] == 1,
                'PURCHASE_URL' => $purchase_url,
                'ID' => strval($row['id']),
                'URL' => $url,
                '_NUM_VIEWS' => strval($row['ce_views']),
                'NUM_VIEWS' => integer_format($row['ce_views'], 0),
            ];
        }

        $pagination = pagination(do_lang_tempcode('CLASSIFIEDS'), $start, 'classifieds_start', $max, 'classifieds_max', $max_rows);

        $tpl = do_template('CLASSIFIED_ADVERTS_SCREEN', ['_GUID' => 'b25659c245a738b4f161dc87869d9edc', 'TITLE' => $this->title, 'PAGINATION' => $pagination, 'ADS' => $ads]);

        require_code('templates_internalise_screen');
        return internalise_own_screen($tpl);
    }
}
