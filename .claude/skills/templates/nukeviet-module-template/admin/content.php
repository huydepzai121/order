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

$page_title = $nv_Lang->getModule('item_add');
$item_id = $nv_Request->get_int('item_id', 'get', 0);

// Load existing item if editing
$item = [];
if ($item_id > 0) {
    $sql = "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_items WHERE item_id=:item_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount()) {
        $item = $stmt->fetch();
        $page_title = $nv_Lang->getModule('item_edit');
    } else {
        nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=main');
    }
}

$error = [];

// Handle POST submission
if ($nv_Request->isset_request('submit', 'post')) {
    // Verify CSRF token
    $checkss = $nv_Request->get_title('checkss', 'post', '');
    if ($checkss != md5($client_info['session_id'] . $global_config['sitekey'])) {
        $error[] = $nv_Lang->getModule('error_security');
    }

    // Get and validate input
    $title = $nv_Request->get_title('title', 'post', '');
    $alias = $nv_Request->get_title('alias', 'post', '');
    $content = $nv_Request->get_editor('content', '', 'post');
    $status = $nv_Request->get_int('status', 'post', 1);
    $weight = $nv_Request->get_int('weight', 'post', 0);

    // Validation
    if (empty($title)) {
        $error[] = $nv_Lang->getModule('error_required_title');
    }

    // Generate alias if empty
    if (empty($alias)) {
        $alias = change_alias($title);
    }

    // Check unique alias
    if (!empty($alias)) {
        $sql = "SELECT COUNT(*) FROM " . NV_PREFIXLANG . "_" . $module_data . "_items
                WHERE alias=:alias AND item_id!=:item_id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':alias', $alias, PDO::PARAM_STR);
        $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->fetchColumn()) {
            $error[] = $nv_Lang->getModule('error_alias_exists');
        }
    }

    if (empty($error)) {
        try {
            $db->query('BEGIN');

            if ($item_id > 0) {
                // Update
                $sql = "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_items SET
                        title=:title,
                        alias=:alias,
                        content=:content,
                        status=:status,
                        weight=:weight,
                        update_time=:update_time
                        WHERE item_id=:item_id";

                $stmt = $db->prepare($sql);
                $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
            } else {
                // Insert
                $sql = "INSERT INTO " . NV_PREFIXLANG . "_" . $module_data . "_items (
                        title, alias, content, status, weight, admin_id, add_time, update_time
                    ) VALUES (
                        :title, :alias, :content, :status, :weight, :admin_id, :add_time, :update_time
                    )";

                $stmt = $db->prepare($sql);
                $stmt->bindValue(':admin_id', $admin_info['userid'], PDO::PARAM_INT);
                $stmt->bindValue(':add_time', NV_CURRENTTIME, PDO::PARAM_INT);
            }

            // Bind common parameters
            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt->bindParam(':alias', $alias, PDO::PARAM_STR);
            $stmt->bindParam(':content', $content, PDO::PARAM_STR, strlen($content));
            $stmt->bindParam(':status', $status, PDO::PARAM_INT);
            $stmt->bindParam(':weight', $weight, PDO::PARAM_INT);
            $stmt->bindValue(':update_time', NV_CURRENTTIME, PDO::PARAM_INT);

            $stmt->execute();

            if ($item_id == 0) {
                $item_id = $db->lastInsertId();
            }

            $db->query('COMMIT');

            // Clear cache
            $nv_Cache->delMod($module_name);

            // Log
            nv_insert_logs(NV_LANG_DATA, $module_name, $item_id > 0 ? 'Edit item' : 'Add item', $title, $admin_info['userid']);

            nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=main');
        } catch (PDOException $e) {
            $db->query('ROLLBACK');
            nv_insert_logs(NV_LANG_DATA, $module_name, 'ERROR', $e->getMessage(), $admin_info['userid']);
            $error[] = $nv_Lang->getModule('error_save');
        }
    }
}

// Prepare data for template
$data = [
    'item_id' => $item_id,
    'title' => !empty($item) ? $item['title'] : '',
    'alias' => !empty($item) ? $item['alias'] : '',
    'content' => !empty($item) ? $item['content'] : '',
    'status' => !empty($item) ? $item['status'] : 1,
    'weight' => !empty($item) ? $item['weight'] : 0
];

// Initialize Smarty template
$tpl = new \NukeViet\Template\NVSmarty();
$tpl->setTemplateDir(get_module_tpl_dir('content.tpl'));
$tpl->assign('LANG', $nv_Lang);
$tpl->assign('MODULE_NAME', $module_name);
$tpl->assign('OP', $op);
$tpl->assign('DATA', $data);
$tpl->assign('ERROR', $error);
$tpl->assign('NV_CHECK', md5($client_info['session_id'] . $global_config['sitekey']));

$contents = $tpl->fetch('content.tpl');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
