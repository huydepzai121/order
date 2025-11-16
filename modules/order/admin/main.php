<?php

/**
 * NukeViet Content Management System
 * @version 5.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2025 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

if (!defined('NV_IS_FILE_ADMIN')) {
    exit('Stop!!!');
}

$page_title = nv_Lang::$lang_module['order_list'];

// Xử lý xóa đơn hàng
if ($nv_Request->isset_request('delete', 'post')) {
    $id = $nv_Request->get_int('id', 'post', 0);
    if ($id > 0) {
        try {
            $db->query('DELETE FROM ' . NV_PREFIXLANG . '_' . $module_data . '_orders WHERE id=' . $id);
            $db->query('DELETE FROM ' . NV_PREFIXLANG . '_' . $module_data . '_order_details WHERE order_id=' . $id);
            nv_insert_logs(NV_LANG_DATA, $module_name, 'Delete order', 'ID: ' . $id, $admin_info['userid']);
            nv_jsonOutput([
                'status' => 'success',
                'message' => nv_Lang::$lang_module['delete_success']
            ]);
        } catch (Exception $e) {
            nv_jsonOutput([
                'status' => 'error',
                'message' => nv_Lang::$lang_module['delete_error']
            ]);
        }
    }
}

// Lấy tham số tìm kiếm và phân trang
$per_page = 20;
$page = $nv_Request->get_int('page', 'get', 1);
$base_url = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name;

$search_keyword = $nv_Request->get_title('q', 'get', '');
$search_status = $nv_Request->get_int('status', 'get', -1);
$search_payment = $nv_Request->get_int('payment', 'get', -1);
$search_employee = $nv_Request->get_int('employee', 'get', 0);

// Điều kiện tìm kiếm
$where = [];
if (!empty($search_keyword)) {
    $where[] = "(order_code LIKE '%" . $db->dblikeescape($search_keyword) . "%' OR customer_name LIKE '%" . $db->dblikeescape($search_keyword) . "%' OR customer_phone LIKE '%" . $db->dblikeescape($search_keyword) . "%')";
}
if ($search_status >= 0) {
    $where[] = 'order_status=' . $search_status;
}
if ($search_payment >= 0) {
    $where[] = 'payment_status=' . $search_payment;
}
if ($search_employee > 0) {
    $where[] = 'employee_id=' . $search_employee;
}

$where_sql = !empty($where) ? ' WHERE ' . implode(' AND ', $where) : '';

// Đếm tổng số bản ghi
$sql = 'SELECT COUNT(*) FROM ' . NV_PREFIXLANG . '_' . $module_data . '_orders' . $where_sql;
$total = $db->query($sql)->fetchColumn();

// Lấy danh sách đơn hàng
$sql = 'SELECT * FROM ' . NV_PREFIXLANG . '_' . $module_data . '_orders' . $where_sql . ' ORDER BY id DESC LIMIT ' . (($page - 1) * $per_page) . ',' . $per_page;
$result = $db->query($sql);

$array_orders = [];
while ($row = $result->fetch()) {
    $array_orders[] = $row;
}

// Lấy danh sách nhân viên
$employees = nv_order_get_employees();
$status_list = nv_order_get_status_list();
$payment_status_list = nv_order_get_payment_status_list();

// Chuẩn bị dữ liệu cho template
$tpl = get_tpl_dir([$global_config['module_theme'], $global_config['admin_theme']], 'admin_default', '/modules/' . $module_file . '/main.tpl');
$smarty = new Smarty();
$smarty->setTemplateDir(NV_ROOTDIR . '/themes/' . $tpl);

$smarty->assign('LANG', nv_Lang::$lang_module);
$smarty->assign('GLANG', nv_Lang::$lang_global);
$smarty->assign('MODULE_NAME', $module_name);
$smarty->assign('OP', $op);
$smarty->assign('SEARCH', [
    'keyword' => $search_keyword,
    'status' => $search_status,
    'payment' => $search_payment,
    'employee' => $search_employee
]);
$smarty->assign('BASE_URL', $base_url);
$smarty->assign('NV_BASE_ADMINURL', NV_BASE_ADMINURL);
$smarty->assign('NV_LANG_VARIABLE', NV_LANG_VARIABLE);
$smarty->assign('NV_LANG_DATA', NV_LANG_DATA);
$smarty->assign('NV_NAME_VARIABLE', NV_NAME_VARIABLE);
$smarty->assign('NV_OP_VARIABLE', NV_OP_VARIABLE);

// Trạng thái đơn hàng
$status_options = [];
foreach ($status_list as $key => $value) {
    $status_options[] = [
        'key' => $key,
        'value' => $value,
        'selected' => ($key == $search_status)
    ];
}
$smarty->assign('STATUS_OPTIONS', $status_options);

// Trạng thái thanh toán
$payment_options = [];
foreach ($payment_status_list as $key => $value) {
    $payment_options[] = [
        'key' => $key,
        'value' => $value,
        'selected' => ($key == $search_payment)
    ];
}
$smarty->assign('PAYMENT_OPTIONS', $payment_options);

// Nhân viên
$employee_options = [[
    'key' => 0,
    'value' => nv_Lang::$lang_module['all_employees'],
    'selected' => ($search_employee == 0)
]];

foreach ($employees as $emp) {
    $employee_options[] = [
        'key' => $emp['userid'],
        'value' => $emp['full_name'] . ' (' . $emp['username'] . ')',
        'selected' => ($emp['userid'] == $search_employee)
    ];
}
$smarty->assign('EMPLOYEE_OPTIONS', $employee_options);

// Danh sách đơn hàng
$orders_data = [];
if (!empty($array_orders)) {
    $i = 0;
    foreach ($array_orders as $row) {
        $row['order_date_format'] = date('d/m/Y H:i', $row['order_date']);
        $row['employee_name'] = isset($employees[$row['employee_id']]) ? $employees[$row['employee_id']]['full_name'] : '';
        $row['order_status_text'] = $status_list[$row['order_status']];
        $row['payment_status_text'] = $payment_status_list[$row['payment_status']];
        $row['final_amount_format'] = nv_order_format_currency($row['final_amount']);

        // CSS class cho trạng thái
        $status_class = [0 => 'info', 1 => 'warning', 2 => 'success', 3 => 'danger'];
        $row['status_class'] = $status_class[$row['order_status']];

        $payment_class = [0 => 'danger', 1 => 'success', 2 => 'warning'];
        $row['payment_class'] = $payment_class[$row['payment_status']];

        $row['stt'] = $i + 1 + (($page - 1) * $per_page);
        $row['edit_url'] = $base_url . '&' . NV_OP_VARIABLE . '=order-add&id=' . $row['id'];

        $orders_data[] = $row;
        $i++;
    }
}
$smarty->assign('ORDERS', $orders_data);

// Phân trang
if ($total > $per_page) {
    $generate_page = nv_generate_page($base_url, $total, $per_page, $page);
    $smarty->assign('GENERATE_PAGE', $generate_page);
}

$contents = $smarty->fetch('main.tpl');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
