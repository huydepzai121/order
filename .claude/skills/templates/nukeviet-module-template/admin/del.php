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

// Get and validate item ID
$item_id = $nv_Request->get_int('item_id', 'post', 0);

// Verify CSRF token
$checkss = $nv_Request->get_title('checkss', 'post', '');
if (!nv_validate_csrf($checkss)) {
    nv_jsonOutput([
        'status' => 'error',
        'message' => $lang_module['error_security']
    ]);
}

if ($item_id > 0) {
    // Get item info for logging
    $sql = "SELECT title FROM " . NV_PREFIXLANG . "_" . $module_data . "_items WHERE item_id=:item_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount()) {
        $row = $stmt->fetch();
        $title = $row['title'];

        try {
            // Delete item
            $sql = "DELETE FROM " . NV_PREFIXLANG . "_" . $module_data . "_items WHERE item_id=:item_id";
            $stmt = $db->prepare($sql);
            $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
            $stmt->execute();

            // Clear cache
            $nv_Cache->delMod($module_name);

            // Log action
            nv_insert_logs(NV_LANG_DATA, $module_name, 'Delete item', $title, $admin_info['userid']);

            nv_jsonOutput([
                'status' => 'OK',
                'message' => $lang_module['success_delete']
            ]);
        } catch (PDOException $e) {
            nv_insert_logs(NV_LANG_DATA, $module_name, 'ERROR Delete', $e->getMessage(), $admin_info['userid']);
            nv_jsonOutput([
                'status' => 'error',
                'message' => $lang_module['error_delete']
            ]);
        }
    }
}

nv_jsonOutput([
    'status' => 'error',
    'message' => $lang_module['error_delete']
]);
