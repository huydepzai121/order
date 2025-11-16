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

// Verify CSRF token
$checkss = $nv_Request->get_title('checkss', 'post', '');
if ($checkss != md5($client_info['session_id'] . $global_config['sitekey'])) {
    nv_jsonOutput([
        'status' => 'error',
        'message' => $nv_Lang->getModule('error_security')
    ]);
}

$menu_id = $nv_Request->get_int('menu_id', 'post', 0);

if ($menu_id > 0) {
    // Lấy thông tin món ăn
    $sql = "SELECT menu_name FROM " . NV_PREFIXLANG . "_" . $module_data . "_menu WHERE menu_id=:menu_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':menu_id', $menu_id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount()) {
        $row = $stmt->fetch();
        $menu_name = $row['menu_name'];

        try {
            // Xóa món ăn
            $del_sql = "DELETE FROM " . NV_PREFIXLANG . "_" . $module_data . "_menu WHERE menu_id=:menu_id";
            $del_stmt = $db->prepare($del_sql);
            $del_stmt->bindParam(':menu_id', $menu_id, PDO::PARAM_INT);
            $del_stmt->execute();

            nv_insert_logs(NV_LANG_DATA, $module_name, 'Delete menu', $menu_name, $admin_info['userid']);

            nv_jsonOutput([
                'status' => 'OK',
                'message' => $nv_Lang->getModule('success_delete')
            ]);
        } catch (PDOException $e) {
            nv_insert_logs(NV_LANG_DATA, $module_name, 'ERROR', $e->getMessage(), $admin_info['userid']);
            nv_jsonOutput([
                'status' => 'error',
                'message' => $nv_Lang->getModule('error_delete')
            ]);
        }
    }
}

nv_jsonOutput([
    'status' => 'error',
    'message' => $nv_Lang->getModule('error_delete')
]);
