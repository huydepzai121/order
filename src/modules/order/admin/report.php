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

$page_title = $lang_module['report'];

// Xử lý tìm kiếm và lọc
$from_date = $nv_Request->get_title('from_date', 'get', date('Y-m-01'));
$to_date = $nv_Request->get_title('to_date', 'get', date('Y-m-d'));
$report_type = $nv_Request->get_title('report_type', 'get', 'revenue');

// Convert dates to timestamp
$from_timestamp = strtotime($from_date . ' 00:00:00');
$to_timestamp = strtotime($to_date . ' 23:59:59');

// Báo cáo doanh thu
$revenue_data = [];
if ($report_type == 'revenue' || $report_type == 'all') {
    // Tổng quan doanh thu
    $sql = "SELECT
            COUNT(*) as total_orders,
            SUM(CASE WHEN status=2 THEN 1 ELSE 0 END) as completed_orders,
            SUM(CASE WHEN status=3 THEN 1 ELSE 0 END) as cancelled_orders,
            SUM(CASE WHEN status=2 THEN total_amount ELSE 0 END) as total_revenue,
            SUM(CASE WHEN status=2 AND payment_status=1 THEN total_amount ELSE 0 END) as paid_revenue,
            SUM(CASE WHEN status=2 AND payment_status=0 THEN total_amount ELSE 0 END) as unpaid_revenue
            FROM " . NV_PREFIXLANG . "_" . $module_data . "_orders
            WHERE order_date >= " . $from_timestamp . "
            AND order_date <= " . $to_timestamp;
    $result = $db->query($sql);
    $revenue_data = $result->fetch();

    // Doanh thu theo ngày
    $revenue_by_date = [];
    $sql = "SELECT
            DATE(FROM_UNIXTIME(order_date)) as date,
            COUNT(*) as order_count,
            SUM(CASE WHEN status=2 THEN total_amount ELSE 0 END) as revenue
            FROM " . NV_PREFIXLANG . "_" . $module_data . "_orders
            WHERE order_date >= " . $from_timestamp . "
            AND order_date <= " . $to_timestamp . "
            GROUP BY DATE(FROM_UNIXTIME(order_date))
            ORDER BY date ASC";
    $result = $db->query($sql);
    while ($row = $result->fetch()) {
        $revenue_by_date[] = $row;
    }
}

// Báo cáo thực đơn
$menu_data = [];
if ($report_type == 'menu' || $report_type == 'all') {
    $sql = "SELECT
            m.menu_id,
            m.menu_name,
            m.category,
            m.price,
            COUNT(oi.item_id) as order_count,
            SUM(oi.quantity) as total_quantity,
            SUM(oi.total) as total_revenue
            FROM " . NV_PREFIXLANG . "_" . $module_data . "_menu m
            LEFT JOIN " . NV_PREFIXLANG . "_" . $module_data . "_order_items oi ON m.menu_id = oi.menu_id
            LEFT JOIN " . NV_PREFIXLANG . "_" . $module_data . "_orders o ON oi.order_id = o.order_id
            WHERE o.order_date >= " . $from_timestamp . "
            AND o.order_date <= " . $to_timestamp . "
            AND o.status = 2
            GROUP BY m.menu_id
            ORDER BY total_revenue DESC";
    $result = $db->query($sql);
    while ($row = $result->fetch()) {
        $menu_data[] = $row;
    }
}

// Báo cáo nhân viên
$staff_data = [];
if ($report_type == 'staff' || $report_type == 'all') {
    $sql = "SELECT
            o.staff_id,
            COUNT(*) as order_count,
            SUM(CASE WHEN o.status=2 THEN o.total_amount ELSE 0 END) as total_revenue,
            SUM(sw.work_hours) as total_work_hours
            FROM " . NV_PREFIXLANG . "_" . $module_data . "_orders o
            LEFT JOIN " . NV_PREFIXLANG . "_" . $module_data . "_staff_work sw
                ON o.staff_id = sw.staff_id
                AND DATE(FROM_UNIXTIME(o.order_date)) = DATE(FROM_UNIXTIME(sw.work_date))
            WHERE o.order_date >= " . $from_timestamp . "
            AND o.order_date <= " . $to_timestamp . "
            GROUP BY o.staff_id
            ORDER BY total_revenue DESC";
    $result = $db->query($sql);
    while ($row = $result->fetch()) {
        $staff_info = nv_get_staff_info($row['staff_id']);
        $row['staff_name'] = !empty($staff_info) ? $staff_info['full_name'] : 'N/A';
        $staff_data[] = $row;
    }
}

// Include template
$xtpl = new XTemplate('report.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('GLANG', $lang_global);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('OP', $op);
$xtpl->assign('FROM_DATE', $from_date);
$xtpl->assign('TO_DATE', $to_date);

// Report type
$report_types = [
    'revenue' => 'Doanh thu',
    'menu' => 'Thực đơn',
    'staff' => 'Nhân viên'
];
foreach ($report_types as $key => $value) {
    $xtpl->assign('REPORT_TYPE', [
        'key' => $key,
        'value' => $value,
        'selected' => $key == $report_type ? 'selected="selected"' : ''
    ]);
    $xtpl->parse('main.report_type');
}

// Báo cáo doanh thu
if ($report_type == 'revenue' && !empty($revenue_data)) {
    $xtpl->assign('REVENUE', [
        'total_orders' => number_format($revenue_data['total_orders']),
        'completed_orders' => number_format($revenue_data['completed_orders']),
        'cancelled_orders' => number_format($revenue_data['cancelled_orders']),
        'total_revenue' => nv_format_currency($revenue_data['total_revenue']),
        'paid_revenue' => nv_format_currency($revenue_data['paid_revenue']),
        'unpaid_revenue' => nv_format_currency($revenue_data['unpaid_revenue'])
    ]);
    $xtpl->parse('main.revenue_report.summary');

    if (!empty($revenue_by_date)) {
        foreach ($revenue_by_date as $item) {
            $xtpl->assign('REVENUE_DATE', [
                'date' => date('d/m/Y', strtotime($item['date'])),
                'order_count' => number_format($item['order_count']),
                'revenue' => nv_format_currency($item['revenue'])
            ]);
            $xtpl->parse('main.revenue_report.by_date.loop');
        }
        $xtpl->parse('main.revenue_report.by_date');
    }

    $xtpl->parse('main.revenue_report');
}

// Báo cáo thực đơn
if ($report_type == 'menu' && !empty($menu_data)) {
    $total_quantity = 0;
    $total_revenue = 0;

    foreach ($menu_data as $item) {
        $total_quantity += $item['total_quantity'];
        $total_revenue += $item['total_revenue'];

        $xtpl->assign('MENU', [
            'menu_name' => $item['menu_name'],
            'category' => $item['category'],
            'price' => nv_format_currency($item['price']),
            'order_count' => number_format($item['order_count']),
            'total_quantity' => number_format($item['total_quantity']),
            'total_revenue' => nv_format_currency($item['total_revenue'])
        ]);
        $xtpl->parse('main.menu_report.loop');
    }

    $xtpl->assign('MENU_TOTAL_QUANTITY', number_format($total_quantity));
    $xtpl->assign('MENU_TOTAL_REVENUE', nv_format_currency($total_revenue));

    $xtpl->parse('main.menu_report');
}

// Báo cáo nhân viên
if ($report_type == 'staff' && !empty($staff_data)) {
    $total_orders = 0;
    $total_revenue = 0;
    $total_hours = 0;

    foreach ($staff_data as $item) {
        $total_orders += $item['order_count'];
        $total_revenue += $item['total_revenue'];
        $total_hours += $item['total_work_hours'];

        $xtpl->assign('STAFF_ITEM', [
            'staff_name' => $item['staff_name'],
            'order_count' => number_format($item['order_count']),
            'total_revenue' => nv_format_currency($item['total_revenue']),
            'total_work_hours' => number_format($item['total_work_hours'], 2)
        ]);
        $xtpl->parse('main.staff_report.loop');
    }

    $xtpl->assign('STAFF_TOTAL_ORDERS', number_format($total_orders));
    $xtpl->assign('STAFF_TOTAL_REVENUE', nv_format_currency($total_revenue));
    $xtpl->assign('STAFF_TOTAL_HOURS', number_format($total_hours, 2));

    $xtpl->parse('main.staff_report');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
