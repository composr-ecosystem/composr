<?php /*

 Composr
 Copyright (c) Christopher Graham, 2004-2024

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  Christopher Graham
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class shopping_test_set extends cms_test_case
{
    protected $entry_id;

    public function setUp()
    {
        parent::setUp();

        require_code('ecommerce');

        set_option('ecommerce_test_mode', '1');

        set_option('shipping_density', '5000.0');
        set_option('shipping_weight_units', 'Kg');
        set_option('shipping_distance_units', 'Cm');
        set_option('shipping_tax_code', '0%');
        set_option('shipping_cost_base', '10.00');
        set_option('shipping_cost_factor', '1.20');

        set_option('shipping_shippo_api_test', '');
        set_option('shipping_shippo_api_live', '');

        set_option('business_street_address', '1234 Scope');
        set_option('business_city', 'Hope');
        set_option('business_county', '');
        set_option('business_state', '');
        set_option('business_post_code', 'HO1 234');
        set_option('business_country', 'GB');

        require_code('ecommerce');
        require_code('catalogues');
        require_code('catalogues2');
        require_code('shopping');
        require_code('lorem');
        require_lang('catalogues');
        require_lang('shopping');
        require_code('lang3');

        $catalogue_name = 'storetesting' . strval(get_member());

        // Cleanup if needed...

        if ($GLOBALS['SITE_DB']->query_select_value_if_there('catalogues', 'c_name', ['c_name' => $catalogue_name]) !== null) {
            actual_delete_catalogue($catalogue_name);
        }

        $GLOBALS['SITE_DB']->query_delete('shopping_orders');
        $GLOBALS['SITE_DB']->query_delete('shopping_order_details');
        $GLOBALS['SITE_DB']->query_delete('shopping_cart');

        // Create module...

        require_code('cms/pages/modules/cms_catalogues.php');
        $cms_module = new Module_cms_catalogues();

        $category_id = create_ecommerce_catalogue($catalogue_name);

        $fields = $GLOBALS['SITE_DB']->query_select('catalogue_fields', ['*'], ['c_name' => $catalogue_name], 'ORDER BY cf_order');

        $fields_map = find_shopping_catalogue_fields($catalogue_name);
        foreach ($fields_map as $key => $i) {
            if (!isset($fields[$i])) {
                continue;
            }

            $id = $fields[$i]['id'];

            switch ($key) {
                case 'product_title':
                    $_POST['field_' . strval($id)] = lorem_phrase();
                    break;

                case 'sku':
                    $_POST['field_' . strval($id)] = placeholder_codename();
                    break;

                case 'price':
                    $_POST['field_' . strval($id)] = float_format(60.00);
                    break;

                case 'stock_level':
                    $_POST['field_' . strval($id)] = '500';
                    break;

                case 'stock_level_warn_at':
                    $_POST['field_' . strval($id)] = '0';
                    break;

                case 'stock_level_maintain':
                    $_POST['field_' . strval($id)] = '1';
                    break;

                case 'tax_code':
                    $_POST['field_' . strval($id) . '_flat'] = '5.0';
                    break;

                case 'image':
                    $_POST['field_' . strval($id)] = '';
                    break;

                case 'weight':
                    $_POST['field_' . strval($id)] = float_format(2.0);
                    break;

                case 'length':
                    $_POST['field_' . strval($id)] = float_format(10.0);
                    break;

                case 'width':
                    $_POST['field_' . strval($id)] = float_format(10.0);
                    break;

                case 'height':
                    $_POST['field_' . strval($id)] = float_format(10.0);
                    break;

                case 'description':
                    $_POST['field_' . strval($id)] = lorem_paragraph();
                    break;
            }
        }

        $map = $cms_module->get_set_field_map($catalogue_name, get_member());
        $entry_id = actual_add_catalogue_entry($category_id, 0, 'test note', 1, 1, 1, $map);

        // Add item to cart...

        require_code('site/pages/modules/shopping.php');

        $shopping_module = new Module_shopping();
        $shopping_module->empty_cart();
        @header_remove();

        $_POST['type_code'] = strval($entry_id);
        $shopping_module->add_item();
        @header_remove();
    }

    public function testViewCart()
    {
        $shopping_module = new Module_shopping();
        $shopping_module->view_shopping_cart();
        @header_remove();
    }

    public function testHandleTransaction()
    {
        $order_id = copy_shopping_cart_to_order();
        $type_code = 'CART_ORDER_' . strval($order_id);
        $item_name = do_lang('CART_ORDER', strval($order_id));
        $purchase_id = '';
        $status = 'Completed';
        $reason = '';
        $pending_reason = 'bar';
        $memo = 'foo';
        $price = 60.00;
        $tax = 5.00;
        $shipping = 12.40;
        $transaction_fee = 0.25;
        $currency = get_option('currency');
        $txn_id = strval(mt_rand(1, 1000000));
        $parent_txn_id = '';
        $period = '';
        $payment_gateway = 'manual';
        $is_subscription = false;

        handle_confirmed_transaction(null, $txn_id, $type_code, $item_name, $purchase_id, $is_subscription, $status, $reason, $price, $tax, $shipping, $transaction_fee, $currency, true, $parent_txn_id, $pending_reason, $memo, $period, get_member(), $payment_gateway, false, true);
    }

    public function tearDown()
    {
        $catalogue_name = 'storetesting' . strval(get_member());
        actual_delete_catalogue($catalogue_name);

        parent::tearDown();
    }
}
