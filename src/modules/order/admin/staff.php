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

$page_title = $nv_Lang->getModule('staff_manage');

// Xử lý tìm kiếm
$search = $nv_Request->get_title('search', 'get', '');

// Phân trang
$page = $nv_Request->get_int('page', 'get', 1);
$per_page = 20;

// Xây dựng điều kiện tìm kiếm
$where = ["active=1"];
$bind_params = [];

if (!empty($search)) {
    $where[] = "(username LIKE :search1 OR first_name LIKE :search2 OR last_name LIKE :search3 OR email LIKE :search4)";
    $search_param = '%' . $db->dblikeescape($search) . '%';
    $bind_params[':search1'] = $search_param;
    $bind_params[':search2'] = $search_param;
    $bind_params[':search3'] = $search_param;
    $bind_params[':search4'] = $search_param;
}

$db_where = !empty($where) ? ' WHERE ' . implode(' AND ', $where) : '';

// Đếm tổng số bản ghi
$sql = "SELECT COUNT(*) FROM " . NV_USERS_GLOBALTABLE . $db_where;
$stmt = $db->prepare($sql);
foreach ($bind_params as $key => $value) {
    $stmt->bindValue($key, $value, PDO::PARAM_STR);
}
$stmt->execute();
$total_records = $stmt->fetchColumn();

// Lấy danh sách nhân viên
$sql = "SELECT userid, username, first_name, last_name, email, gender, regdate, last_login
        FROM " . NV_USERS_GLOBALTABLE . $db_where . "
        ORDER BY userid DESC
        LIMIT :offset, :limit";
$stmt = $db->prepare($sql);
foreach ($bind_params as $key => $value) {
    $stmt->bindValue($key, $value, PDO::PARAM_STR);
}
$stmt->bindValue(':offset', ($page - 1) * $per_page, PDO::PARAM_INT);
$stmt->bindValue(':limit', $per_page, PDO::PARAM_INT);
$stmt->execute();

$staff_list = [];
while ($row = $stmt->fetch()) {
    $row['full_name'] = trim($row['first_name'] . ' ' . $row['last_name']);
    if (empty($row['full_name'])) {
        $row['full_name'] = $row['username'];
    }

    // Thống kê đơn hàng của nhân viên
    $stats_sql = "SELECT COUNT(*) as order_count, SUM(total_amount) as total_revenue
                  FROM " . NV_PREFIXLANG . "_" . $module_data . "_orders
                  WHERE staff_id=:staff_id";
    $stats_stmt = $db->prepare($stats_sql);
    $stats_stmt->bindParam(':staff_id', $row['userid'], PDO::PARAM_INT);
    $stats_stmt->execute();

    if ($stats_stmt->rowCount()) {
        $stats = $stats_stmt->fetch();
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

// Prepare data for Smarty template
$staff_list_data = [];
foreach ($staff_list as $staff) {
    $staff_list_data[] = [
        'userid' => $staff['userid'],
        'username' => $staff['username'],
        'full_name' => $staff['full_name'],
        'email' => $staff['email'],
        'regdate' => date('d/m/Y', $staff['regdate']),
        'order_count' => $staff['order_count'],
        'total_revenue' => nv_format_currency($staff['total_revenue']),
        'view_orders_url' => NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=main&amp;staff_id=' . $staff['userid'],
        'view_work_url' => NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=staff-work&amp;staff_id=' . $staff['userid']
    ];
}

// Initialize Smarty template
$tpl = new \NukeViet\Template\NVSmarty();
$tpl->setTemplateDir(get_module_tpl_dir('staff.tpl'));
$tpl->assign('LANG', $nv_Lang);
$tpl->assign('MODULE_NAME', $module_name);
$tpl->assign('OP', $op);
$tpl->assign('SEARCH', $search);
$tpl->assign('STAFF_LIST', $staff_list_data);
$tpl->assign('GENERATE_PAGE', $generate_page);

$contents = $tpl->fetch('staff.tpl');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
