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

$page_title = $lang_module['reports'];

// Lấy tham số tìm kiếm
$date_from = $nv_Request->get_title('date_from', 'get', date('Y-m-01'));
$date_to = $nv_Request->get_title('date_to', 'get', date('Y-m-d'));

$time_from = strtotime($date_from . ' 00:00:00');
$time_to = strtotime($date_to . ' 23:59:59');

// Báo cáo doanh thu
$revenue_data = [
    'total_orders' => 0,
    'completed_orders' => 0,
    'cancelled_orders' => 0,
    'total_revenue' => 0,
    'paid_revenue' => 0,
    'unpaid_revenue' => 0,
    'avg_order_value' => 0
];

$sql = "SELECT
        COUNT(*) as total_orders,
        SUM(CASE WHEN order_status = 2 THEN 1 ELSE 0 END) as completed_orders,
        SUM(CASE WHEN order_status = 3 THEN 1 ELSE 0 END) as cancelled_orders,
        SUM(final_amount) as total_revenue,
        SUM(CASE WHEN payment_status = 1 THEN final_amount ELSE 0 END) as paid_revenue,
        SUM(CASE WHEN payment_status = 0 THEN final_amount ELSE 0 END) as unpaid_revenue
        FROM " . NV_PREFIXLANG . "_" . $module_data . "_orders
        WHERE order_time >= " . $time_from . " AND order_time <= " . $time_to;

$result = $db->query($sql);
if ($row = $result->fetch()) {
    $revenue_data = array_merge($revenue_data, $row);
    if ($revenue_data['total_orders'] > 0) {
        $revenue_data['avg_order_value'] = $revenue_data['total_revenue'] / $revenue_data['total_orders'];
    }
}

// Báo cáo món ăn bán chạy
$top_dishes = [];
$sql = "SELECT d.dish_name, SUM(d.quantity) as total_qty, SUM(d.total_price) as total_amount
        FROM " . NV_PREFIXLANG . "_" . $module_data . "_order_details d
        INNER JOIN " . NV_PREFIXLANG . "_" . $module_data . "_orders o ON d.order_id = o.id
        WHERE o.order_time >= " . $time_from . " AND o.order_time <= " . $time_to . "
        AND o.order_status != 3
        GROUP BY d.dish_id, d.dish_name
        ORDER BY total_qty DESC
        LIMIT 10";

$result = $db->query($sql);
while ($row = $result->fetch()) {
    $top_dishes[] = $row;
}

// Báo cáo nhân viên
$staff_report = [];
$sql = "SELECT s.id, u.first_name, u.last_name,
        COUNT(o.id) as total_orders,
        SUM(o.final_amount) as total_amount,
        SUM(CASE WHEN o.payment_status = 1 THEN o.final_amount ELSE 0 END) as paid_amount
        FROM " . NV_PREFIXLANG . "_" . $module_data . "_staff s
        INNER JOIN " . NV_USERS_GLOBALTABLE . " u ON s.userid = u.userid
        LEFT JOIN " . NV_PREFIXLANG . "_" . $module_data . "_orders o ON s.id = o.staff_id
            AND o.order_time >= " . $time_from . " AND o.order_time <= " . $time_to . "
        WHERE s.status = 1
        GROUP BY s.id, u.first_name, u.last_name
        ORDER BY total_amount DESC";

$result = $db->query($sql);
while ($row = $result->fetch()) {
    $staff_report[] = $row;
}

// Doanh thu theo ngày (cho biểu đồ)
$daily_revenue = [];
$sql = "SELECT DATE(FROM_UNIXTIME(order_time)) as order_date,
        COUNT(*) as total_orders,
        SUM(final_amount) as total_amount
        FROM " . NV_PREFIXLANG . "_" . $module_data . "_orders
        WHERE order_time >= " . $time_from . " AND order_time <= " . $time_to . "
        AND order_status != 3
        GROUP BY order_date
        ORDER BY order_date ASC";

$result = $db->query($sql);
while ($row = $result->fetch()) {
    $daily_revenue[] = $row;
}

$xtpl = new XTemplate('reports.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('GLANG', $lang_global);
$xtpl->assign('DATE_FROM', $date_from);
$xtpl->assign('DATE_TO', $date_to);

// Revenue summary
$xtpl->assign('TOTAL_ORDERS', number_format($revenue_data['total_orders']));
$xtpl->assign('COMPLETED_ORDERS', number_format($revenue_data['completed_orders']));
$xtpl->assign('CANCELLED_ORDERS', number_format($revenue_data['cancelled_orders']));
$xtpl->assign('TOTAL_REVENUE', nv_format_currency($revenue_data['total_revenue']));
$xtpl->assign('PAID_REVENUE', nv_format_currency($revenue_data['paid_revenue']));
$xtpl->assign('UNPAID_REVENUE', nv_format_currency($revenue_data['unpaid_revenue']));
$xtpl->assign('AVG_ORDER_VALUE', nv_format_currency($revenue_data['avg_order_value']));

// Top dishes
foreach ($top_dishes as $key => $dish) {
    $dish['stt'] = $key + 1;
    $dish['total_amount_format'] = nv_format_currency($dish['total_amount']);
    $xtpl->assign('DISH', $dish);
    $xtpl->parse('main.top_dish');
}

if (empty($top_dishes)) {
    $xtpl->parse('main.no_dishes');
}

// Staff report
foreach ($staff_report as $staff) {
    $staff['full_name'] = $staff['first_name'] . ' ' . $staff['last_name'];
    $staff['total_amount_format'] = nv_format_currency($staff['total_amount']);
    $staff['paid_amount_format'] = nv_format_currency($staff['paid_amount']);
    $xtpl->assign('STAFF', $staff);
    $xtpl->parse('main.staff_report');
}

if (empty($staff_report)) {
    $xtpl->parse('main.no_staff');
}

// Daily revenue for chart
$chart_labels = [];
$chart_data = [];
foreach ($daily_revenue as $day) {
    $chart_labels[] = date('d/m', strtotime($day['order_date']));
    $chart_data[] = $day['total_amount'];
}

$xtpl->assign('CHART_LABELS', json_encode($chart_labels));
$xtpl->assign('CHART_DATA', json_encode($chart_data));

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
