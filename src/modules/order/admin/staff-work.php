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

$page_title = $nv_Lang->getModule('staff_work_manage');

// Xử lý tìm kiếm và lọc
$staff_id = $nv_Request->get_int('staff_id', 'get', 0);
$from_date = $nv_Request->get_title('from_date', 'get', '');
$to_date = $nv_Request->get_title('to_date', 'get', '');

// Phân trang
$page = $nv_Request->get_int('page', 'get', 1);
$per_page = 20;

// Xây dựng điều kiện tìm kiếm
$where = [];
$bind_params = [];

if ($staff_id > 0) {
    $where[] = "staff_id=:staff_id";
    $bind_params[':staff_id'] = $staff_id;
}
if (!empty($from_date)) {
    $from_timestamp = strtotime($from_date . ' 00:00:00');
    $where[] = "work_date >= :from_date";
    $bind_params[':from_date'] = $from_timestamp;
}
if (!empty($to_date)) {
    $to_timestamp = strtotime($to_date . ' 23:59:59');
    $where[] = "work_date <= :to_date";
    $bind_params[':to_date'] = $to_timestamp;
}

$db_where = !empty($where) ? ' WHERE ' . implode(' AND ', $where) : '';

// Đếm tổng số bản ghi
$sql = "SELECT COUNT(*) FROM " . NV_PREFIXLANG . "_" . $module_data . "_staff_work" . $db_where;
$stmt = $db->prepare($sql);
foreach ($bind_params as $key => $value) {
    $stmt->bindValue($key, $value, PDO::PARAM_INT);
}
$stmt->execute();
$total_records = $stmt->fetchColumn();

// Lấy danh sách công việc
$sql = "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_staff_work" . $db_where . "
        ORDER BY work_date DESC, work_id DESC
        LIMIT :offset, :limit";
$stmt = $db->prepare($sql);
foreach ($bind_params as $key => $value) {
    $stmt->bindValue($key, $value, PDO::PARAM_INT);
}
$stmt->bindValue(':offset', ($page - 1) * $per_page, PDO::PARAM_INT);
$stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
$stmt->execute();

$work_list = [];
while ($row = $stmt->fetch()) {
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

// Prepare staff list data
$staff_list_data = [];
foreach ($staff_list as $staff) {
    $staff_list_data[] = [
        'userid' => $staff['userid'],
        'full_name' => $staff['full_name'],
        'selected' => $staff['userid'] == $staff_id
    ];
}

// Prepare work list data
$work_list_data = [];
$total_hours = 0;
$total_orders = 0;
$total_revenue = 0;

foreach ($work_list as $work) {
    $staff_info = nv_get_staff_info($work['staff_id']);

    $total_hours += $work['work_hours'];
    $total_orders += $work['order_count'];
    $total_revenue += $work['total_revenue'];

    $work_list_data[] = [
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
    ];
}

// Initialize Smarty template
$tpl = new \NukeViet\Template\NVSmarty();
$tpl->setTemplateDir(get_module_tpl_dir('staff_work.tpl'));
$tpl->assign('LANG', $nv_Lang);
$tpl->assign('MODULE_NAME', $module_name);
$tpl->assign('OP', $op);
$tpl->assign('FROM_DATE', $from_date);
$tpl->assign('TO_DATE', $to_date);
$tpl->assign('STAFF_ID_SELECTED', $staff_id);
$tpl->assign('STAFF_LIST', $staff_list_data);
$tpl->assign('WORK_LIST', $work_list_data);
$tpl->assign('TOTAL_HOURS', number_format($total_hours, 2));
$tpl->assign('TOTAL_ORDERS', $total_orders);
$tpl->assign('TOTAL_REVENUE', nv_format_currency($total_revenue));
$tpl->assign('GENERATE_PAGE', $generate_page);

$contents = $tpl->fetch('staff_work.tpl');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
