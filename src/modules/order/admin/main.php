<?php

/**
 * NukeViet Content Management System
 * @version 5.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2025 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

if (!defined('NV_IS_FILE_ADMIN')) {
    exit('Stop!!!');
}

$page_title = $lang_module['order_manage'];

// Xử lý tìm kiếm và lọc
$search = $nv_Request->get_title('search', 'get', '');
$status = $nv_Request->get_int('status', 'get', -1);
$payment_status = $nv_Request->get_int('payment_status', 'get', -1);
$staff_id = $nv_Request->get_int('staff_id', 'get', 0);
$from_date = $nv_Request->get_title('from_date', 'get', '');
$to_date = $nv_Request->get_title('to_date', 'get', '');

// Phân trang
$page = $nv_Request->get_int('page', 'get', 1);
$per_page = 20;

// Xây dựng điều kiện tìm kiếm
$where = [];
if (!empty($search)) {
    $where[] = "(order_code LIKE '%" . $db->dblikeescape($search) . "%'
                OR customer_name LIKE '%" . $db->dblikeescape($search) . "%'
                OR customer_phone LIKE '%" . $db->dblikeescape($search) . "%')";
}
if ($status >= 0) {
    $where[] = "status=" . $status;
}
if ($payment_status >= 0) {
    $where[] = "payment_status=" . $payment_status;
}
if ($staff_id > 0) {
    $where[] = "staff_id=" . $staff_id;
}
if (!empty($from_date)) {
    $from_timestamp = strtotime($from_date . ' 00:00:00');
    $where[] = "order_date >= " . $from_timestamp;
}
if (!empty($to_date)) {
    $to_timestamp = strtotime($to_date . ' 23:59:59');
    $where[] = "order_date <= " . $to_timestamp;
}

$db_where = !empty($where) ? ' WHERE ' . implode(' AND ', $where) : '';

// Đếm tổng số bản ghi
$sql = "SELECT COUNT(*) FROM " . NV_PREFIXLANG . "_" . $module_data . "_orders" . $db_where;
$total_records = $db->query($sql)->fetchColumn();

// Lấy danh sách đơn hàng
$sql = "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_orders" . $db_where . "
        ORDER BY order_id DESC
        LIMIT " . (($page - 1) * $per_page) . ", " . $per_page;
$result = $db->query($sql);

$orders = [];
while ($row = $result->fetch()) {
    $orders[] = $row;
}

// Lấy danh sách nhân viên
$staff_list = nv_get_staff_list();

// Lấy danh sách trạng thái
$status_list = nv_order_status_list();
$payment_status_list = nv_payment_status_list();

// Tạo base URL cho phân trang
$base_url = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op;
$params = [];
if (!empty($search)) {
    $params['search'] = $search;
}
if ($status >= 0) {
    $params['status'] = $status;
}
if ($payment_status >= 0) {
    $params['payment_status'] = $payment_status;
}
if ($staff_id > 0) {
    $params['staff_id'] = $staff_id;
}
if (!empty($from_date)) {
    $params['from_date'] = $from_date;
}
if (!empty($to_date)) {
    $params['to_date'] = $to_date;
}
if (!empty($params)) {
    $base_url .= '&amp;' . http_build_query($params);
}

$generate_page = nv_generate_page($base_url, $total_records, $per_page, $page);

// Include template
$xtpl = new XTemplate('main.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('GLANG', $lang_global);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('OP', $op);
$xtpl->assign('SEARCH', $search);
$xtpl->assign('FROM_DATE', $from_date);
$xtpl->assign('TO_DATE', $to_date);

// Danh sách trạng thái đơn hàng
$xtpl->assign('STATUS_SELECTED', $status);
foreach ($status_list as $key => $value) {
    $xtpl->assign('STATUS', [
        'key' => $key,
        'value' => $value,
        'selected' => $key == $status ? 'selected="selected"' : ''
    ]);
    $xtpl->parse('main.status');
}

// Danh sách trạng thái thanh toán
$xtpl->assign('PAYMENT_STATUS_SELECTED', $payment_status);
foreach ($payment_status_list as $key => $value) {
    $xtpl->assign('PAYMENT_STATUS_ITEM', [
        'key' => $key,
        'value' => $value,
        'selected' => $key == $payment_status ? 'selected="selected"' : ''
    ]);
    $xtpl->parse('main.payment_status');
}

// Danh sách nhân viên
$xtpl->assign('STAFF_ID_SELECTED', $staff_id);
foreach ($staff_list as $staff) {
    $xtpl->assign('STAFF', [
        'userid' => $staff['userid'],
        'full_name' => $staff['full_name'],
        'selected' => $staff['userid'] == $staff_id ? 'selected="selected"' : ''
    ]);
    $xtpl->parse('main.staff');
}

// Danh sách đơn hàng
if (!empty($orders)) {
    $i = 0;
    foreach ($orders as $order) {
        $staff_info = nv_get_staff_info($order['staff_id']);

        $xtpl->assign('ORDER', [
            'order_id' => $order['order_id'],
            'order_code' => $order['order_code'],
            'customer_name' => $order['customer_name'],
            'customer_phone' => $order['customer_phone'],
            'order_date' => date('d/m/Y H:i', $order['order_date']),
            'delivery_date' => $order['delivery_date'] ? date('d/m/Y H:i', $order['delivery_date']) : '',
            'total_amount' => nv_format_currency($order['total_amount']),
            'status' => $status_list[$order['status']],
            'status_class' => $order['status'] == 2 ? 'success' : ($order['status'] == 3 ? 'danger' : ($order['status'] == 1 ? 'warning' : 'info')),
            'payment_status' => $payment_status_list[$order['payment_status']],
            'payment_status_class' => $order['payment_status'] == 1 ? 'success' : 'danger',
            'staff_name' => !empty($staff_info) ? $staff_info['full_name'] : '',
            'edit_url' => NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=content&amp;order_id=' . $order['order_id'],
            'delete_url' => NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=del&amp;order_id=' . $order['order_id']
        ]);
        $xtpl->parse('main.orders.loop');
        $i++;
    }
    $xtpl->parse('main.orders');
} else {
    $xtpl->parse('main.no_data');
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
