<?php

/**
 * NukeViet Content Management System
 * @version 5.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2021 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

if (!defined('NV_IS_FILE_ADMIN')) {
    die('Stop!!!');
}

$page_title = $lang_module['order_list'];

// Xử lý tìm kiếm và lọc
$search = $nv_Request->get_title('search', 'get', '');
$order_status = $nv_Request->get_int('order_status', 'get', -1);
$payment_status = $nv_Request->get_int('payment_status', 'get', -1);
$staff_id = $nv_Request->get_int('staff_id', 'get', 0);

$where = [];
if (!empty($search)) {
    $where[] = "(order_code LIKE '%" . $db->dblikeescape($search) . "%' OR customer_name LIKE '%" . $db->dblikeescape($search) . "%' OR customer_phone LIKE '%" . $db->dblikeescape($search) . "%')";
}
if ($order_status >= 0) {
    $where[] = "o.order_status = " . $order_status;
}
if ($payment_status >= 0) {
    $where[] = "o.payment_status = " . $payment_status;
}
if ($staff_id > 0) {
    $where[] = "o.staff_id = " . $staff_id;
}

$where_sql = !empty($where) ? ' WHERE ' . implode(' AND ', $where) : '';

// Phân trang
$per_page = 20;
$page = $nv_Request->get_int('page', 'get', 1);
$db->sqlreset()
    ->select('COUNT(*)')
    ->from(NV_PREFIXLANG . '_' . $module_data . '_orders o');

if (!empty($where)) {
    $db->where(implode(' AND ', $where));
}

$total = $db->query($db->sql())->fetchColumn();

// Lấy danh sách đơn hàng
$orders = [];
$sql = "SELECT o.*, s.userid, u.first_name, u.last_name
        FROM " . NV_PREFIXLANG . "_" . $module_data . "_orders o
        LEFT JOIN " . NV_PREFIXLANG . "_" . $module_data . "_staff s ON o.staff_id = s.id
        LEFT JOIN " . NV_USERS_GLOBALTABLE . " u ON s.userid = u.userid
        " . $where_sql . "
        ORDER BY o.order_time DESC
        LIMIT " . (($page - 1) * $per_page) . ", " . $per_page;

$result = $db->query($sql);
while ($row = $result->fetch()) {
    $row['staff_name'] = !empty($row['first_name']) ? $row['first_name'] . ' ' . $row['last_name'] : '';
    $orders[] = $row;
}

$order_status_list = nv_order_status_list();
$payment_status_list = nv_payment_status_list();
$staff_list = nv_get_staff_list();

$base_url = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=orders';
$generate_page = nv_generate_page($base_url, $total, $per_page, $page);

$xtpl = new XTemplate('orders.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('GLANG', $lang_global);
$xtpl->assign('SEARCH', $search);
$xtpl->assign('URL_ADD', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=order-content');

// Filter - Order Status
$xtpl->assign('ORDER_STATUS_SELECTED', $order_status == -1 ? ' selected="selected"' : '');
$xtpl->parse('main.order_status_all');
foreach ($order_status_list as $key => $value) {
    $xtpl->assign('STATUS_KEY', $key);
    $xtpl->assign('STATUS_VALUE', $value);
    $xtpl->assign('STATUS_SELECTED', $order_status == $key ? ' selected="selected"' : '');
    $xtpl->parse('main.order_status_loop');
}

// Filter - Payment Status
$xtpl->assign('PAYMENT_STATUS_SELECTED', $payment_status == -1 ? ' selected="selected"' : '');
$xtpl->parse('main.payment_status_all');
foreach ($payment_status_list as $key => $value) {
    $xtpl->assign('STATUS_KEY', $key);
    $xtpl->assign('STATUS_VALUE', $value);
    $xtpl->assign('STATUS_SELECTED', $payment_status == $key ? ' selected="selected"' : '');
    $xtpl->parse('main.payment_status_loop');
}

// Filter - Staff
$xtpl->assign('STAFF_SELECTED', $staff_id == 0 ? ' selected="selected"' : '');
$xtpl->parse('main.staff_all');
foreach ($staff_list as $key => $value) {
    $xtpl->assign('STAFF_KEY', $key);
    $xtpl->assign('STAFF_VALUE', $value);
    $xtpl->assign('STAFF_SELECTED', $staff_id == $key ? ' selected="selected"' : '');
    $xtpl->parse('main.staff_loop');
}

// Danh sách đơn hàng
foreach ($orders as $order) {
    $order['order_time_format'] = date('d/m/Y H:i', $order['order_time']);
    $order['final_amount_format'] = nv_format_currency($order['final_amount']);
    $order['order_status_text'] = $order_status_list[$order['order_status']];
    $order['payment_status_text'] = $payment_status_list[$order['payment_status']];
    $order['url_edit'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=order-content&id=' . $order['id'];
    $order['url_delete'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=order-del&id=' . $order['id'];

    // Order status badge
    $order['status_class'] = 'secondary';
    if ($order['order_status'] == 1) $order['status_class'] = 'info';
    if ($order['order_status'] == 2) $order['status_class'] = 'success';
    if ($order['order_status'] == 3) $order['status_class'] = 'danger';

    // Payment status badge
    $order['payment_class'] = 'warning';
    if ($order['payment_status'] == 1) $order['payment_class'] = 'success';
    if ($order['payment_status'] == 2) $order['payment_class'] = 'info';

    $xtpl->assign('ORDER', $order);
    $xtpl->parse('main.loop');
}

if (empty($orders)) {
    $xtpl->parse('main.empty');
}

if (!empty($generate_page)) {
    $xtpl->assign('GENERATE_PAGE', $generate_page);
    $xtpl->parse('main.generate_page');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
