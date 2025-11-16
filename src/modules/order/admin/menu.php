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

$page_title = $nv_Lang->getModule('menu_manage');

// Xử lý AJAX cập nhật trạng thái
if ($nv_Request->isset_request('ajax_action', 'post')) {
    // Verify CSRF token
    $checkss = $nv_Request->get_title('checkss', 'post', '');
    if ($checkss != md5($client_info['session_id'] . $global_config['sitekey'])) {
        nv_jsonOutput([
            'status' => 'error',
            'message' => $nv_Lang->getModule('error_security')
        ]);
    }

    $menu_id = $nv_Request->get_int('menu_id', 'post', 0);
    $field = $nv_Request->get_title('field', 'post', '');
    $value = $nv_Request->get_int('value', 'post', 0);

    if ($menu_id > 0 && in_array($field, ['status'])) {
        $sql = "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_menu SET " . $field . "=:value WHERE menu_id=:menu_id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':value', $value, PDO::PARAM_INT);
        $stmt->bindParam(':menu_id', $menu_id, PDO::PARAM_INT);
        $stmt->execute();

        nv_jsonOutput([
            'status' => 'OK'
        ]);
    }

    nv_jsonOutput([
        'status' => 'error'
    ]);
}

// Xử lý tìm kiếm và lọc
$search = $nv_Request->get_title('search', 'get', '');
$category = $nv_Request->get_title('category', 'get', '');
$status = $nv_Request->get_int('status', 'get', -1);

// Phân trang
$page = $nv_Request->get_int('page', 'get', 1);
$per_page = 20;

// Xây dựng điều kiện tìm kiếm
$where = [];
if (!empty($search)) {
    $where[] = "(menu_name LIKE '%" . $db->dblikeescape($search) . "%' OR menu_code LIKE '%" . $db->dblikeescape($search) . "%')";
}
if (!empty($category)) {
    $where[] = "category='" . $db->dblikeescape($category) . "'";
}
if ($status >= 0) {
    $where[] = "status=" . $status;
}

$db_where = !empty($where) ? ' WHERE ' . implode(' AND ', $where) : '';

// Đếm tổng số bản ghi
$sql = "SELECT COUNT(*) FROM " . NV_PREFIXLANG . "_" . $module_data . "_menu" . $db_where;
$total_records = $db->query($sql)->fetchColumn();

// Lấy danh sách thực đơn
$offset = ($page - 1) * $per_page;
$sql = "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_menu" . $db_where . "
        ORDER BY weight ASC, menu_id DESC
        LIMIT :offset, :per_page";
$stmt = $db->prepare($sql);
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':per_page', $per_page, PDO::PARAM_INT);
$stmt->execute();

$menu_items = [];
while ($row = $stmt->fetch()) {
    $menu_items[] = $row;
}

// Lấy danh sách danh mục
$sql = "SELECT DISTINCT category FROM " . NV_PREFIXLANG . "_" . $module_data . "_menu ORDER BY category ASC";
$result = $db->query($sql);
$categories = [];
while ($row = $result->fetch()) {
    $categories[] = $row['category'];
}

// Tạo base URL cho phân trang
$base_url = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op;
$params = [];
if (!empty($search)) {
    $params['search'] = $search;
}
if (!empty($category)) {
    $params['category'] = $category;
}
if ($status >= 0) {
    $params['status'] = $status;
}
if (!empty($params)) {
    $base_url .= '&amp;' . http_build_query($params);
}

$generate_page = nv_generate_page($base_url, $total_records, $per_page, $page);

// Danh sách trạng thái
$status_options = [
    1 => $nv_Lang->getModule('active'),
    0 => $nv_Lang->getModule('inactive')
];

// Prepare data for menu items
$menu_items_data = [];
foreach ($menu_items as $item) {
    $menu_items_data[] = [
        'menu_id' => $item['menu_id'],
        'menu_code' => $item['menu_code'],
        'menu_name' => $item['menu_name'],
        'category' => $item['category'],
        'price' => nv_format_currency($item['price']),
        'status' => $item['status'],
        'status_text' => $item['status'] ? $nv_Lang->getModule('active') : $nv_Lang->getModule('inactive'),
        'status_class' => $item['status'] ? 'success' : 'secondary',
        'weight' => $item['weight']
    ];
}

// Initialize Smarty template
$tpl = new \NukeViet\Template\NVSmarty();
$tpl->setTemplateDir(get_module_tpl_dir('menu.tpl'));
$tpl->assign('LANG', $nv_Lang);
$tpl->assign('MODULE_NAME', $module_name);
$tpl->assign('OP', $op);
$tpl->assign('SEARCH', $search);
$tpl->assign('CATEGORIES', $categories);
$tpl->assign('CATEGORY_SELECTED', $category);
$tpl->assign('STATUS_OPTIONS', $status_options);
$tpl->assign('STATUS_SELECTED', $status);
$tpl->assign('MENU_ITEMS', $menu_items_data);
$tpl->assign('GENERATE_PAGE', $generate_page);
$tpl->assign('NV_CHECK', md5($client_info['session_id'] . $global_config['sitekey']));

$contents = $tpl->fetch('menu.tpl');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
