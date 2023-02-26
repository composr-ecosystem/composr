<?php /*

 Composr
 Copyright (c) ocProducts, 2004-2023

 See docs/LICENSE.md for full licensing information.

*/

/**
 * @license    http://opensource.org/licenses/cpal_1.0 Common Public Attribution License
 * @copyright  ocProducts Ltd
 * @package    testing_platform
 */

/**
 * Composr test case class (unit testing).
 */
class shopping_order_management_test_set extends cms_test_case
{
    protected $admin_ecom;
    protected $item_id;
    protected $order_id;
    protected $access_mapping;
    protected $admin_shopping;
    protected $tasks_background;

    public function setUp()
    {
        parent::setUp();

        $this->tasks_background = get_option('tasks_background');
        set_option('tasks_background', '1');

        require_code('ecommerce');
        require_code('autosave');
        require_code('shopping');
        require_code('form_templates');

        require_lang('shopping');

        $txn_id = 'ddfsfdsdfsdfs';

        $this->order_id = $GLOBALS['SITE_DB']->query_insert('shopping_orders', [
            'member_id' => get_member(),
            'session_id' => get_session_id(),
            'add_date' => time(),
            'order_status' => 'NEW',
            'total_price' => 10.00,
            'total_tax_derivation' => '',
            'total_tax' => 1.00,
            'total_tax_tracking' => '',
            'total_shipping_cost' => 2.00,
            'total_shipping_tax' => 0.00,
            'total_product_weight' => 0.00,
            'total_product_length' => 0.00,
            'total_product_width' => 0.00,
            'total_product_height' => 0.00,
            'order_currency' => 'GBP',
            'notes' => '',
            'txn_id' => $txn_id,
            'purchase_through' => 'cart',
        ], true);

        $GLOBALS['SITE_DB']->query_delete('ecom_transactions', ['id' => $txn_id], '', 1);
        $GLOBALS['SITE_DB']->query_insert('ecom_transactions', [
            'id' => $txn_id,
            't_type_code' => 'CART_ORDER_1',
            't_purchase_id' => strval($this->order_id),
            't_status' => 'Completed',
            't_reason' => '',
            't_price' => 10.00,
            't_tax_derivation' => '',
            't_tax' => 1.00,
            't_tax_tracking' => '',
            't_shipping' => 2.00,
            't_transaction_fee' => 0.25,
            't_currency' => 'GBP',
            't_parent_txn_id' => '',
            't_time' => time(),
            't_pending_reason' => '',
            't_memo' => '',
            't_payment_gateway' => 'manual',
            't_invoicing_breakdown' => '',
            't_member_id' => get_member(),
            't_session_id' => get_session_id(),
        ]);

        $this->access_mapping = [db_get_first_id() => 4];

        require_code('adminzone/pages/modules/admin_ecommerce.php');
        $this->admin_ecom = new Module_admin_ecommerce();

        require_code('adminzone/pages/modules/admin_shopping.php');
        $this->admin_shopping = new Module_admin_shopping();
        if (method_exists($this->admin_shopping, 'pre_run')) {
            $this->admin_shopping->pre_run();
        }
        $this->admin_shopping->title = get_screen_title('ORDER_DETAILS');
        $this->admin_shopping->run();
    }

    public function testShowOrders()
    {
        $this->admin_shopping->browse();
    }

    public function testOrderDetails()
    {
        $order_id = $GLOBALS['SITE_DB']->query_select_value('shopping_orders', 'MAX(id)');
        $_GET['id'] = strval($order_id);
        $this->admin_shopping->order_details();
        @header_remove();
    }

    public function testAddNoteToOrderUI()
    {
        $order_id = $GLOBALS['SITE_DB']->query_select_value('shopping_orders', 'MAX(id)');
        $_GET['id'] = strval($order_id);
        $this->admin_shopping->add_note();
        @header_remove();
    }

    public function testAddNoteToOrderActualiser()
    {
        $order_id = $GLOBALS['SITE_DB']->query_select_value('shopping_orders', 'MAX(id)');
        $_POST['order_id'] = strval($order_id);
        $_POST['note'] = 'Test note';
        $this->admin_shopping->_add_note();
        @header_remove();
    }

    public function testOrderDispatch()
    {
        $order_id = $GLOBALS['SITE_DB']->query_select_value_if_there('shopping_orders', 'MAX(id)', ['order_status' => 'ORDER_STATUS_payment_received']);
        if ($order_id !== null) {
            $_GET['id'] = strval($order_id);
            $this->admin_shopping->dispatch();
            @header_remove();
        }
    }

    public function testOrderDispatchNotification()
    {
        $order_id = $GLOBALS['SITE_DB']->query_select_value('shopping_orders', 'MAX(id)');
        $this->admin_shopping->send_dispatch_notification($order_id);
        @header_remove();
    }

    public function testDeleteOrder()
    {
        $order_id = $GLOBALS['SITE_DB']->query_select_value('shopping_orders', 'MAX(id)');
        $_GET['id'] = strval($order_id);
        $this->admin_shopping->delete_order();
        @header_remove();
    }

    public function testReturnOrder()
    {
        $order_id = $GLOBALS['SITE_DB']->query_select_value('shopping_orders', 'MAX(id)');
        $_GET['id'] = strval($order_id);
        $this->admin_shopping->return_order();
        @header_remove();
    }

    public function testHoldOrder()
    {
        $order_id = $GLOBALS['SITE_DB']->query_select_value('shopping_orders', 'MAX(id)');
        $_GET['id'] = strval($order_id);
        $this->admin_shopping->hold_order();
        @header_remove();
    }

    public function testOrderExports()
    {
        $filter_id = null;
        $filter_username = '';
        $filter_txn_id = '';
        $filter_order_status = '';
        $filter_start = null;
        $filter_end = null;

        require_code('tasks');
        require_code('hooks/systems/tasks/export_shopping_orders');
        $ob_import = new Hook_task_export_shopping_orders();
        $ob_import->run($filter_id, $filter_username, $filter_txn_id, $filter_order_status, $filter_start, $filter_end);
    }

    public function tearDown()
    {
        $GLOBALS['SITE_DB']->query_delete('shopping_orders', ['id' => $this->order_id], '', 1);

        set_option('tasks_background', $this->tasks_background);

        parent::tearDown();
    }
}
