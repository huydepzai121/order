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

$page_title = $nv_Lang->getModule('item_list');

// Handle search and filters
$search = $nv_Request->get_title('search', 'get', '');
$status = $nv_Request->get_int('status', 'get', -1);

// Pagination
$page = $nv_Request->get_int('page', 'get', 1);
$per_page = 20;

// Build WHERE conditions
$where = [];
if (!empty($search)) {
    $where[] = "(title LIKE :search OR alias LIKE :search)";
}
if ($status >= 0) {
    $where[] = "status=" . intval($status);
}

$db_where = !empty($where) ? ' WHERE ' . implode(' AND ', $where) : '';

// Count total records
$sql = "SELECT COUNT(*) FROM " . NV_PREFIXLANG . "_" . $module_data . "_items" . $db_where;
if (!empty($search)) {
    $stmt = $db->prepare($sql);
    $search_param = '%' . $db->dblikeescape($search) . '%';
    $stmt->bindParam(':search', $search_param, PDO::PARAM_STR);
    $stmt->execute();
    $total_records = $stmt->fetchColumn();
} else {
    $total_records = $db->query($sql)->fetchColumn();
}

// Get item list
$sql = "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_items" . $db_where . "
        ORDER BY weight ASC, item_id DESC
        LIMIT " . (($page - 1) * $per_page) . ", " . $per_page;

if (!empty($search)) {
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':search', $search_param, PDO::PARAM_STR);
    $stmt->execute();
    $result = $stmt;
} else {
    $result = $db->query($sql);
}

$items = [];
while ($row = $result->fetch()) {
    $items[] = $row;
}

// Generate pagination
$base_url = NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&amp;' . NV_NAME_VARIABLE . '=' . $module_name . '&amp;' . NV_OP_VARIABLE . '=' . $op;
$params = [];
if (!empty($search)) {
    $params['search'] = $search;
}
if ($status >= 0) {
    $params['status'] = $status;
}
if (!empty($params)) {
    $base_url .= '&amp;' . http_build_query($params);
}

$generate_page = nv_generate_page($base_url, $total_records, $per_page, $page);

// Initialize Smarty template
$tpl = new \NukeViet\Template\NVSmarty();
$tpl->setTemplateDir(get_module_tpl_dir('main.tpl'));
$tpl->assign('LANG', $nv_Lang);
$tpl->assign('MODULE_NAME', $module_name);
$tpl->assign('OP', $op);
$tpl->assign('SEARCH', $search);
$tpl->assign('ITEMS', $items);
$tpl->assign('GENERATE_PAGE', $generate_page);
$tpl->assign('NV_CHECK', md5($client_info['session_id'] . $global_config['sitekey']));

$contents = $tpl->fetch('main.tpl');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
