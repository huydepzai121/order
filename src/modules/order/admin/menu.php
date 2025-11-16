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

$page_title = $lang_module['menu_manage'];

// Xử lý AJAX cập nhật trạng thái
if ($nv_Request->isset_request('ajax_action', 'post')) {
    $menu_id = $nv_Request->get_int('menu_id', 'post', 0);
    $field = $nv_Request->get_title('field', 'post', '');
    $value = $nv_Request->get_int('value', 'post', 0);

    if ($menu_id > 0 && in_array($field, ['status'])) {
        $sql = "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_menu SET " . $field . "=" . $value . " WHERE menu_id=" . $menu_id;
        $db->query($sql);

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
$sql = "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_menu" . $db_where . "
        ORDER BY weight ASC, menu_id DESC
        LIMIT " . (($page - 1) * $per_page) . ", " . $per_page;
$result = $db->query($sql);

$menu_items = [];
while ($row = $result->fetch()) {
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

// Include template
$xtpl = new XTemplate('menu.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('GLANG', $lang_global);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('OP', $op);
$xtpl->assign('SEARCH', $search);

// Danh sách danh mục
$xtpl->assign('CATEGORY_SELECTED', $category);
foreach ($categories as $cat) {
    $xtpl->assign('CATEGORY', [
        'value' => $cat,
        'selected' => $cat == $category ? 'selected="selected"' : ''
    ]);
    $xtpl->parse('main.category');
}

// Danh sách trạng thái
$xtpl->assign('STATUS_SELECTED', $status);
$status_options = [
    1 => $lang_module['active'],
    0 => $lang_module['inactive']
];
foreach ($status_options as $key => $value) {
    $xtpl->assign('STATUS', [
        'key' => $key,
        'value' => $value,
        'selected' => $key == $status ? 'selected="selected"' : ''
    ]);
    $xtpl->parse('main.status_filter');
}

// Danh sách thực đơn
if (!empty($menu_items)) {
    foreach ($menu_items as $item) {
        $xtpl->assign('ITEM', [
            'menu_id' => $item['menu_id'],
            'menu_code' => $item['menu_code'],
            'menu_name' => $item['menu_name'],
            'category' => $item['category'],
            'price' => nv_format_currency($item['price']),
            'status' => $item['status'],
            'status_text' => $item['status'] ? $lang_module['active'] : $lang_module['inactive'],
            'status_class' => $item['status'] ? 'success' : 'secondary',
            'weight' => $item['weight'],
            'edit_url' => NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=menu-content&amp;menu_id=' . $item['menu_id'],
            'delete_url' => NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=menu-del&amp;menu_id=' . $item['menu_id']
        ]);
        $xtpl->parse('main.items.loop');
    }
    $xtpl->parse('main.items');
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
