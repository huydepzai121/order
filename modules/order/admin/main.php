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

$page_title = $lang_module['main'];

// Thống kê tổng quan
$sql = "SELECT COUNT(*) FROM " . NV_PREFIXLANG . "_" . $module_data . "_orders";
$total_orders = $db->query($sql)->fetchColumn();

$sql = "SELECT COUNT(*) FROM " . NV_PREFIXLANG . "_" . $module_data . "_orders WHERE order_status = 2";
$completed_orders = $db->query($sql)->fetchColumn();

$sql = "SELECT COUNT(*) FROM " . NV_PREFIXLANG . "_" . $module_data . "_orders WHERE order_status = 0";
$pending_orders = $db->query($sql)->fetchColumn();

$sql = "SELECT SUM(final_amount) FROM " . NV_PREFIXLANG . "_" . $module_data . "_orders WHERE payment_status = 1";
$total_revenue = $db->query($sql)->fetchColumn();
$total_revenue = $total_revenue ? $total_revenue : 0;

$sql = "SELECT COUNT(*) FROM " . NV_PREFIXLANG . "_" . $module_data . "_dishes WHERE status = 1";
$total_dishes = $db->query($sql)->fetchColumn();

$sql = "SELECT COUNT(*) FROM " . NV_PREFIXLANG . "_" . $module_data . "_staff WHERE status = 1";
$total_staff = $db->query($sql)->fetchColumn();

// Đơn hàng gần đây
$recent_orders = [];
$sql = "SELECT o.*, s.userid, u.first_name, u.last_name
        FROM " . NV_PREFIXLANG . "_" . $module_data . "_orders o
        LEFT JOIN " . NV_PREFIXLANG . "_" . $module_data . "_staff s ON o.staff_id = s.id
        LEFT JOIN " . NV_USERS_GLOBALTABLE . " u ON s.userid = u.userid
        ORDER BY o.order_time DESC
        LIMIT 10";

$result = $db->query($sql);
while ($row = $result->fetch()) {
    $row['staff_name'] = !empty($row['first_name']) ? $row['first_name'] . ' ' . $row['last_name'] : '';
    $recent_orders[] = $row;
}

$order_status_list = nv_order_status_list();
$payment_status_list = nv_payment_status_list();

$xtpl = new XTemplate('main.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('GLANG', $lang_global);

// Thống kê
$xtpl->assign('TOTAL_ORDERS', number_format($total_orders));
$xtpl->assign('COMPLETED_ORDERS', number_format($completed_orders));
$xtpl->assign('PENDING_ORDERS', number_format($pending_orders));
$xtpl->assign('TOTAL_REVENUE', nv_format_currency($total_revenue));
$xtpl->assign('TOTAL_DISHES', number_format($total_dishes));
$xtpl->assign('TOTAL_STAFF', number_format($total_staff));

// Đơn hàng gần đây
foreach ($recent_orders as $order) {
    $order['order_time_format'] = date('d/m/Y H:i', $order['order_time']);
    $order['final_amount_format'] = nv_format_currency($order['final_amount']);
    $order['order_status_text'] = $order_status_list[$order['order_status']];
    $order['payment_status_text'] = $payment_status_list[$order['payment_status']];
    $order['url_edit'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=order-content&id=' . $order['id'];

    $xtpl->assign('ORDER', $order);
    $xtpl->parse('main.recent_orders.loop');
}

if (!empty($recent_orders)) {
    $xtpl->parse('main.recent_orders');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
