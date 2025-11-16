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

$page_title = $lang_module['staff_work_manage'];

// Xử lý tìm kiếm và lọc
$staff_id = $nv_Request->get_int('staff_id', 'get', 0);
$from_date = $nv_Request->get_title('from_date', 'get', '');
$to_date = $nv_Request->get_title('to_date', 'get', '');

// Phân trang
$page = $nv_Request->get_int('page', 'get', 1);
$per_page = 20;

// Xây dựng điều kiện tìm kiếm
$where = [];
if ($staff_id > 0) {
    $where[] = "staff_id=" . $staff_id;
}
if (!empty($from_date)) {
    $from_timestamp = strtotime($from_date . ' 00:00:00');
    $where[] = "work_date >= " . $from_timestamp;
}
if (!empty($to_date)) {
    $to_timestamp = strtotime($to_date . ' 23:59:59');
    $where[] = "work_date <= " . $to_timestamp;
}

$db_where = !empty($where) ? ' WHERE ' . implode(' AND ', $where) : '';

// Đếm tổng số bản ghi
$sql = "SELECT COUNT(*) FROM " . NV_PREFIXLANG . "_" . $module_data . "_staff_work" . $db_where;
$total_records = $db->query($sql)->fetchColumn();

// Lấy danh sách công việc
$sql = "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_staff_work" . $db_where . "
        ORDER BY work_date DESC, work_id DESC
        LIMIT " . (($page - 1) * $per_page) . ", " . $per_page;
$result = $db->query($sql);

$work_list = [];
while ($row = $result->fetch()) {
    $work_list[] = $row;
}

// Lấy danh sách nhân viên
$staff_list = nv_get_staff_list();

// Tạo base URL cho phân trang
$base_url = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op;
$params = [];
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
$xtpl = new XTemplate('staff_work.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('GLANG', $lang_global);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('OP', $op);
$xtpl->assign('FROM_DATE', $from_date);
$xtpl->assign('TO_DATE', $to_date);

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

// Danh sách công việc
if (!empty($work_list)) {
    $total_hours = 0;
    $total_orders = 0;
    $total_revenue = 0;

    foreach ($work_list as $work) {
        $staff_info = nv_get_staff_info($work['staff_id']);

        $total_hours += $work['work_hours'];
        $total_orders += $work['order_count'];
        $total_revenue += $work['total_revenue'];

        $xtpl->assign('WORK', [
            'work_id' => $work['work_id'],
            'staff_name' => !empty($staff_info) ? $staff_info['full_name'] : '',
            'work_date' => date('d/m/Y', $work['work_date']),
            'shift' => $work['shift'],
            'start_time' => $work['start_time'],
            'end_time' => $work['end_time'],
            'work_hours' => number_format($work['work_hours'], 2),
            'order_count' => $work['order_count'],
            'total_revenue' => nv_format_currency($work['total_revenue']),
            'edit_url' => NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=staff-work-content&amp;work_id=' . $work['work_id'],
            'delete_url' => NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=staff-work-del&amp;work_id=' . $work['work_id']
        ]);
        $xtpl->parse('main.work.loop');
    }

    $xtpl->assign('TOTAL_HOURS', number_format($total_hours, 2));
    $xtpl->assign('TOTAL_ORDERS', $total_orders);
    $xtpl->assign('TOTAL_REVENUE', nv_format_currency($total_revenue));

    $xtpl->parse('main.work.summary');
    $xtpl->parse('main.work');
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
