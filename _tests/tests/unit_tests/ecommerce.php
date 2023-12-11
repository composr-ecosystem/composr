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
class ecommerce_test_set extends cms_test_case
{
    public $admin_ecom;
    public $item_id;
    public $order_id;
    public $access_mapping;
    public $admin_orders;

    public function setUp()
    {
        parent::setUp();

        require_code('ecommerce');
        require_code('autosave');
        require_code('shopping');
        require_code('form_templates');

        require_lang('ecommerce');
        require_lang('shopping');

        $this->order_id = $GLOBALS['SITE_DB']->query_insert('shopping_order', array(
            'c_member' => get_member(),
            'session_id' => get_session_id(),
            'add_date' => time(),
            'tot_price' => 0.0,
            'order_status' => 'NEW',
            'notes' => '',
            'transaction_id' => 'ddfsfdsdfsdfs',
            'purchase_through' => 'cart',
            'tax_opted_out' => 0,
        ), true);

        $this->access_mapping = array(db_get_first_id() => 4);

        require_code('adminzone/pages/modules/admin_ecommerce.php');
        $this->admin_ecom = new Module_admin_ecommerce();

        require_code('adminzone/pages/modules/admin_orders.php');
        $this->admin_orders = new Module_admin_orders();
        if (method_exists($this->admin_orders, 'pre_run')) {
            $this->admin_orders->pre_run();
        }
        $this->admin_orders->run();
    }

    public function testShowOrders()
    {
        return $this->admin_orders->show_orders();
    }

    public function testOrderDetails()
    {
        $order_id = $GLOBALS['SITE_DB']->query_select_value('shopping_order', 'MAX(id)');
        $_GET['id'] = strval($order_id);
        return $this->admin_orders->order_details();
    }

    public function testAddNoteToOrderUI()
    {
        $order_id = $GLOBALS['SITE_DB']->query_select_value('shopping_order', 'MAX(id)');
        $_GET['id'] = strval($order_id);
        $this->admin_orders->add_note();
    }

    public function testAddNoteToOrderActualiser()
    {
        $order_id = $GLOBALS['SITE_DB']->query_select_value('shopping_order', 'MAX(id)');
        $_POST['order_id'] = $order_id;
        $_POST['note'] = 'Test note';
        $this->admin_orders->_add_note();
    }

    public function testOrderDispatch()
    {
        $order_id = $GLOBALS['SITE_DB']->query_select_value_if_there('shopping_order', 'MAX(id)', array('order_status' => 'ORDER_STATUS_payment_received'));
        if (!is_null($order_id)) {
            $_GET['id'] = $order_id;
            $this->admin_orders->dispatch();
        }
    }

    public function testOrderDispatchNotification()
    {
        $order_id = $GLOBALS['SITE_DB']->query_select_value('shopping_order', 'MAX(id)');
        $this->admin_orders->send_dispatch_notification($order_id);
    }

    public function testDeleteOrder()
    {
        $order_id = $GLOBALS['SITE_DB']->query_select_value('shopping_order', 'MAX(id)');
        $_GET['id'] = $order_id;
        $this->admin_orders->delete_order();
    }

    public function testReturnOrder()
    {
        $order_id = $GLOBALS['SITE_DB']->query_select_value('shopping_order', 'MAX(id)');
        $_GET['id'] = $order_id;
        $this->admin_orders->return_order();
    }

    public function testHoldOrder()
    {
        $order_id = $GLOBALS['SITE_DB']->query_select_value('shopping_order', 'MAX(id)');
        $_GET['id'] = $order_id;
        $this->admin_orders->hold_order();
    }

    public function testOrderExportUI()
    {
        $this->admin_orders->order_export();
    }

    public function testOrderExportActualiser()
    {
        $_POST = array(
            'order_status' => 'ORDER_STATUS_awaiting_payment',
            'require__order_status' => 0,
            'start_date_day' => 10,
            'start_date_month' => 12,
            'start_date_year' => 2008,
            'start_date_hour' => 7,
            'start_date_minute' => 0,
            'require__start_date' => 1,
            'end_date_day' => 10,
            'end_date_month' => 12,
            'end_date_year' => 2009,
            'end_date_hour' => 7,
            'end_date_minute' => 0,
            'require__end_date' => 1,
            'is_from_unit_test' => 1
        );

        $this->admin_orders->_order_export(true);
    }

    public function tearDown()
    {
        $GLOBALS['SITE_DB']->query_delete('shopping_order', array('id' => $this->order_id), '', 1);
        parent::tearDown();
    }
}
