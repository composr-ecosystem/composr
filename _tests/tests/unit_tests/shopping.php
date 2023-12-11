<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2016

 See text/EN/licence.txt for full licencing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class shopping_test_set extends cms_test_case
{
    public $product_id;

    public function setUp()
    {
        parent::setUp();

        require_code('ecommerce');
        require_code('catalogues');
        require_code('catalogues2');
        require_code('shopping');
        require_code('lorem');
        require_lang('catalogues');
        require_lang('shopping');
        require_lang('ecommerce');
        require_code('lang3');

        // Cleanup if needed...

        if (!is_null($GLOBALS['SITE_DB']->query_select_value_if_there('catalogues', 'c_name', array('c_name' => 'storetesting' . strval(get_member()))))) {
            actual_delete_catalogue('storetesting' . strval(get_member()));
        }

        $GLOBALS['SITE_DB']->query_delete('shopping_order');
        $GLOBALS['SITE_DB']->query_delete('shopping_order_details');
        $GLOBALS['SITE_DB']->query_delete('shopping_cart');

        // Create module...

        require_code('cms/pages/modules/cms_catalogues.php');
        $cms_module = new Module_cms_catalogues();

        // Create an eCommerce catalogue...

        $c_name = 'storetesting' . strval(get_member());
        actual_add_catalogue($c_name, insert_lang('c_title', do_lang('DEFAULT_CATALOGUE_PRODUCTS_TITLE'), 2), '', 0, 1, '', 0, 1);
        $category_id = $GLOBALS['SITE_DB']->query_select_value('catalogue_categories', 'id', array('c_name' => $c_name));

        $fields = array(
            //     Name  Description  Type  Defines order  Required  Visible  Searchable
            array('ECOM_CAT_product_title', 'DESCRIPTION_TITLE', 'short_trans', 1, 1, 1, 1),
            array('ECOM_CAT_sku', 'ECOM_CATD_sku', 'codename', 0, 1, 1, 1, 'RANDOM'),
            array('ECOM_CAT_price_pre_tax', 'ECOM_CATD_price_pre_tax', 'float', 0, 1, 1, 1, 'decimal_points_behaviour=price'),
            array('ECOM_CAT_stock_level', 'ECOM_CATD_stock_level', 'integer', 0, 0, 1, 0),
            array('ECOM_CAT_stock_level_warn_at', 'ECOM_CATD_stock_level_warn_at', 'integer', 0, 0, 0, 0),
            array('ECOM_CAT_stock_level_maintain', 'ECOM_CATD_stock_level_maintain', 'tick'/*will save as list*/, 0, 1, 0, 0),
            array('ECOM_CAT_tax_type', 'ECOM_CATD_tax_type', 'list', 0, 1, 0, 0, "0%|5%|17.5%"),
            array('ECOM_CAT_image', 'ECOM_CATD_image', 'picture', 0, 0, 1, 1),
            array('ECOM_CAT_weight', 'ECOM_CATD_weight', 'float', 0, 1, 0, 0),
            array('ECOM_CAT_description', 'DESCRIPTION_DESCRIPTION', 'long_trans', 0, 1, 1, 1)
        );

        foreach ($fields as $i => $field) {
            actual_add_catalogue_field('storetesting' . strval(get_member()), // $c_name
                lang_code_to_default_content('cf_name', $field[0], false, 3), // $name
                lang_code_to_default_content('cf_description', $field[1], false, 3), // $description
                ($field[2] == 'tick') ? 'list' : $field[2], // $type
                $i, // $order
                $field[3], // $defines_order
                $field[5], // $visible
                $field[6], // $searchable
                array_key_exists(7, $field) ? $field[7] : '', // $default
                $field[4], // $required
                array_key_exists(5, $field) ? $field[5] : 0, // $put_in_category
                array_key_exists(5, $field) ? $field[5] : 0 // $put_in_search
            );
        }

        $catalogue_name = 'storetesting' . strval(get_member());

        $fields = $GLOBALS['SITE_DB']->query_select('catalogue_fields', array('*'), array('c_name' => $catalogue_name));

        foreach ($fields as $val) {
            $type = $val['cf_type'];

            $id = $val['id'];

            switch ($type) {
                case 'integer':
                    $_POST['field_' . strval($id)] = '500'; // Stock
                    break;
                case 'short_trans':
                    $_POST['field_' . strval($id)] = lorem_phrase();
                    break;
                case 'long_trans':
                    $_POST['field_' . strval($id)] = lorem_paragraph();
                    break;
                case 'float':
                    $_POST['field_' . strval($id)] = '68.35'; // Price
                    break;
                case 'list':
                    if ($val['cf_order'] == 6) { // Tax
                        $_POST['field_' . strval($id)] = '5%';
                    } elseif ($val['cf_order'] == 5) { // Keep Stock
                        $_POST['field_' . strval($id)] = '1';
                    }
                    break;
            }
        }

        $map = $cms_module->get_set_field_map($catalogue_name, get_member());
        $this->product_id = actual_add_catalogue_entry($category_id, 0, 'test note', 1, 1, 1, $map);

        require_code('site/pages/modules/shopping.php');
        $shopping_module = new Module_shopping();

        $shopping_module->empty_cart();

        $_POST['product_id'] = $this->product_id;
        $_GET['hook'] = 'catalogue_items';
        $shopping_module->add_item_to_cart();

        $_GET['page'] = 'shopping'; // Static setting to identify the module in payment form
        render_cart_payment_form();

        $_purchase_id = $GLOBALS['SITE_DB']->query_select_value('shopping_order', 'MAX(id)', array());
        if ($_purchase_id === null) {
            $this->assertTrue(false, 'Order did not save');
            return;
        }
        $purchase_id = strval($_purchase_id);
        $item_name = lorem_phrase();
        $payment_status = 'Completed';
        $reason_code = '';
        $pending_reason = 'bar';
        $memo = 'foo';
        $mc_gross = (get_db_type() == 'xml'/*rounding difference*/) ? '71.40' : '71.77';
        $mc_currency = get_option('currency');
        $txn_id = '0';
        $parent_txn_id = '0';
        $period = '';
        $via = 'paypal';

        handle_confirmed_transaction($purchase_id, $item_name, $payment_status, $reason_code, $pending_reason, $memo, $mc_gross, $mc_currency, $txn_id, $parent_txn_id, $period, $via);
    }

    public function tearDown()
    {
        actual_delete_catalogue('storetesting' . strval(get_member()));
        parent::tearDown();
    }
}
