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

$page_title = $lang_module['staff_manage'];

// Xử lý tìm kiếm
$search = $nv_Request->get_title('search', 'get', '');

// Phân trang
$page = $nv_Request->get_int('page', 'get', 1);
$per_page = 20;

// Xây dựng điều kiện tìm kiếm
$where = ["active=1"];
if (!empty($search)) {
    $where[] = "(username LIKE '%" . $db->dblikeescape($search) . "%'
                OR first_name LIKE '%" . $db->dblikeescape($search) . "%'
                OR last_name LIKE '%" . $db->dblikeescape($search) . "%'
                OR email LIKE '%" . $db->dblikeescape($search) . "%')";
}

$db_where = !empty($where) ? ' WHERE ' . implode(' AND ', $where) : '';

// Đếm tổng số bản ghi
$sql = "SELECT COUNT(*) FROM " . NV_USERS_GLOBALTABLE . $db_where;
$total_records = $db->query($sql)->fetchColumn();

// Lấy danh sách nhân viên
$sql = "SELECT userid, username, first_name, last_name, email, gender, regdate, last_login
        FROM " . NV_USERS_GLOBALTABLE . $db_where . "
        ORDER BY userid DESC
        LIMIT " . (($page - 1) * $per_page) . ", " . $per_page;
$result = $db->query($sql);

$staff_list = [];
while ($row = $result->fetch()) {
    $row['full_name'] = trim($row['first_name'] . ' ' . $row['last_name']);
    if (empty($row['full_name'])) {
        $row['full_name'] = $row['username'];
    }

    // Thống kê đơn hàng của nhân viên
    $stats_sql = "SELECT COUNT(*) as order_count, SUM(total_amount) as total_revenue
                  FROM " . NV_PREFIXLANG . "_" . $module_data . "_orders
                  WHERE staff_id=" . $row['userid'];
    $stats_result = $db->query($stats_sql);
    if ($stats_result->rowCount()) {
        $stats = $stats_result->fetch();
        $row['order_count'] = $stats['order_count'];
        $row['total_revenue'] = $stats['total_revenue'];
    } else {
        $row['order_count'] = 0;
        $row['total_revenue'] = 0;
    }

    $staff_list[] = $row;
}

// Tạo base URL cho phân trang
$base_url = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op;
if (!empty($search)) {
    $base_url .= '&amp;search=' . urlencode($search);
}

$generate_page = nv_generate_page($base_url, $total_records, $per_page, $page);

// Include template
$xtpl = new XTemplate('staff.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('GLANG', $lang_global);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('OP', $op);
$xtpl->assign('SEARCH', $search);

// Danh sách nhân viên
if (!empty($staff_list)) {
    foreach ($staff_list as $staff) {
        $xtpl->assign('STAFF', [
            'userid' => $staff['userid'],
            'username' => $staff['username'],
            'full_name' => $staff['full_name'],
            'email' => $staff['email'],
            'regdate' => date('d/m/Y', $staff['regdate']),
            'order_count' => $staff['order_count'],
            'total_revenue' => nv_format_currency($staff['total_revenue']),
            'view_orders_url' => NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=main&amp;staff_id=' . $staff['userid'],
            'view_work_url' => NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=staff-work&amp;staff_id=' . $staff['userid']
        ]);
        $xtpl->parse('main.staff.loop');
    }
    $xtpl->parse('main.staff');
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
