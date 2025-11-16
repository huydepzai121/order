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

$page_title = $lang_module['dish_list'];

// Xử lý thay đổi trạng thái
if ($nv_Request->isset_request('change_status', 'post')) {
    $id = $nv_Request->get_int('id', 'post', 0);
    $status = $nv_Request->get_int('status', 'post', 0);

    $sql = "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_dishes SET status = " . $status . " WHERE id = " . $id;
    $db->query($sql);

    nv_jsonOutput([
        'status' => 'OK'
    ]);
}

// Xử lý thay đổi weight (sắp xếp)
if ($nv_Request->isset_request('change_weight', 'post')) {
    $id = $nv_Request->get_int('id', 'post', 0);
    $weight = $nv_Request->get_int('weight', 'post', 0);

    $sql = "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_dishes SET weight = " . $weight . " WHERE id = " . $id;
    $db->query($sql);

    nv_jsonOutput([
        'status' => 'OK'
    ]);
}

// Tìm kiếm và lọc
$search = $nv_Request->get_title('search', 'get', '');
$status = $nv_Request->get_int('status', 'get', -1);

$where = [];
if (!empty($search)) {
    $where[] = "(name LIKE '%" . $db->dblikeescape($search) . "%' OR alias LIKE '%" . $db->dblikeescape($search) . "%')";
}
if ($status >= 0) {
    $where[] = "status = " . $status;
}

$where_sql = !empty($where) ? ' WHERE ' . implode(' AND ', $where) : '';

// Phân trang
$per_page = 20;
$page = $nv_Request->get_int('page', 'get', 1);

$sql = "SELECT COUNT(*) FROM " . NV_PREFIXLANG . "_" . $module_data . "_dishes" . $where_sql;
$total = $db->query($sql)->fetchColumn();

// Lấy danh sách món ăn
$dishes = [];
$sql = "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_dishes
        " . $where_sql . "
        ORDER BY weight ASC, name ASC
        LIMIT " . (($page - 1) * $per_page) . ", " . $per_page;

$result = $db->query($sql);
while ($row = $result->fetch()) {
    $dishes[] = $row;
}

$base_url = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=dishes';
$generate_page = nv_generate_page($base_url, $total, $per_page, $page);

$xtpl = new XTemplate('dishes.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('GLANG', $lang_global);
$xtpl->assign('SEARCH', $search);
$xtpl->assign('URL_ADD', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=dish-content');

// Filter status
$xtpl->assign('STATUS_SELECTED', $status == -1 ? ' selected="selected"' : '');
$xtpl->parse('main.status_all');

$xtpl->assign('STATUS_SELECTED', $status == 1 ? ' selected="selected"' : '');
$xtpl->parse('main.status_active');

$xtpl->assign('STATUS_SELECTED', $status == 0 ? ' selected="selected"' : '');
$xtpl->parse('main.status_inactive');

// Danh sách món ăn
$stt = ($page - 1) * $per_page;
foreach ($dishes as $dish) {
    $stt++;
    $dish['stt'] = $stt;
    $dish['price_format'] = nv_format_currency($dish['price']);
    $dish['add_time_format'] = date('d/m/Y H:i', $dish['add_time']);
    $dish['status_text'] = $dish['status'] ? $lang_module['status_active'] : $lang_module['status_inactive'];
    $dish['status_class'] = $dish['status'] ? 'success' : 'secondary';
    $dish['url_edit'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=dish-content&id=' . $dish['id'];
    $dish['url_delete'] = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=dish-del&id=' . $dish['id'];

    $xtpl->assign('DISH', $dish);
    $xtpl->parse('main.loop');
}

if (empty($dishes)) {
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
