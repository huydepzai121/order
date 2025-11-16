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

$page_title = $lang_module['staff_list'];

// Xử lý thay đổi trạng thái
if ($nv_Request->isset_request('change_status', 'post')) {
    $id = $nv_Request->get_int('id', 'post', 0);
    $status = $nv_Request->get_int('status', 'post', 0);

    $sql = "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_staff SET status = " . $status . " WHERE id = " . $id;
    $db->query($sql);

    nv_jsonOutput([
        'status' => 'OK'
    ]);
}

// Tìm kiếm và lọc
$search = $nv_Request->get_title('search', 'get', '');
$status = $nv_Request->get_int('status', 'get', -1);
$department = $nv_Request->get_title('department', 'get', '');

$where = [];
if (!empty($search)) {
    $where[] = "(u.username LIKE '%" . $db->dblikeescape($search) . "%' OR u.first_name LIKE '%" . $db->dblikeescape($search) . "%' OR u.last_name LIKE '%" . $db->dblikeescape($search) . "%' OR s.position LIKE '%" . $db->dblikeescape($search) . "%')";
}
if ($status >= 0) {
    $where[] = "s.status = " . $status;
}
if (!empty($department)) {
    $where[] = "s.department = '" . $db->dblikeescape($department) . "'";
}

$where_sql = !empty($where) ? ' WHERE ' . implode(' AND ', $where) : '';

// Phân trang
$per_page = 20;
$page = $nv_Request->get_int('page', 'get', 1);

$sql = "SELECT COUNT(*) FROM " . NV_PREFIXLANG . "_" . $module_data . "_staff s
        INNER JOIN " . NV_USERS_GLOBALTABLE . " u ON s.userid = u.userid
        " . $where_sql;
$total = $db->query($sql)->fetchColumn();

// Lấy danh sách nhân viên
$staff_list = [];
$sql = "SELECT s.*, u.username, u.first_name, u.last_name, u.email
        FROM " . NV_PREFIXLANG . "_" . $module_data . "_staff s
        INNER JOIN " . NV_USERS_GLOBALTABLE . " u ON s.userid = u.userid
        " . $where_sql . "
        ORDER BY u.last_name ASC, u.first_name ASC
        LIMIT " . (($page - 1) * $per_page) . ", " . $per_page;

$result = $db->query($sql);
while ($row = $result->fetch()) {
    $staff_list[] = $row;
}

// Lấy danh sách phòng ban
$departments = [];
$sql = "SELECT DISTINCT department FROM " . NV_PREFIXLANG . "_" . $module_data . "_staff WHERE department != '' ORDER BY department ASC";
$result = $db->query($sql);
while ($row = $result->fetch()) {
    $departments[] = $row['department'];
}

$base_url = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=staff';
$generate_page = nv_generate_page($base_url, $total, $per_page, $page);

$xtpl = new XTemplate('staff.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('GLANG', $lang_global);
$xtpl->assign('SEARCH', $search);
$xtpl->assign('URL_ADD', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=staff-content');

// Filter status
$xtpl->assign('STATUS_SELECTED', $status == -1 ? ' selected="selected"' : '');
$xtpl->parse('main.status_all');

$xtpl->assign('STATUS_SELECTED', $status == 1 ? ' selected="selected"' : '');
$xtpl->parse('main.status_active');

$xtpl->assign('STATUS_SELECTED', $status == 0 ? ' selected="selected"' : '');
$xtpl->parse('main.status_inactive');

// Filter department
$xtpl->assign('DEPT_SELECTED', empty($department) ? ' selected="selected"' : '');
$xtpl->parse('main.dept_all');

foreach ($departments as $dept) {
    $xtpl->assign('DEPT_VALUE', $dept);
    $xtpl->assign('DEPT_SELECTED', $department == $dept ? ' selected="selected"' : '');
    $xtpl->parse('main.dept_loop');
}

// Danh sách nhân viên
$stt = ($page - 1) * $per_page;
foreach ($staff_list as $staff) {
    $stt++;
    $staff['stt'] = $stt;
    $staff['full_name'] = $staff['first_name'] . ' ' . $staff['last_name'];
    $staff['salary_format'] = nv_format_currency($staff['salary']);
    $staff['start_date_format'] = $staff['start_date'] ? date('d/m/Y', $staff['start_date']) : '';
    $staff['end_date_format'] = $staff['end_date'] ? date('d/m/Y', $staff['end_date']) : '';
    $staff['status_text'] = $staff['status'] ? $lang_module['status_active'] : $lang_module['status_inactive'];
    $staff['status_class'] = $staff['status'] ? 'success' : 'secondary';
    $staff['url_edit'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=staff-content&id=' . $staff['id'];
    $staff['url_delete'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=staff-del&id=' . $staff['id'];

    $xtpl->assign('STAFF', $staff);
    $xtpl->parse('main.loop');
}

if (empty($staff_list)) {
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
