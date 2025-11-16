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

$menu_id = $nv_Request->get_int('menu_id', 'get,post', 0);

if ($menu_id > 0) {
    // Lấy thông tin món ăn
    $sql = "SELECT menu_name FROM " . NV_PREFIXLANG . "_" . $module_data . "_menu WHERE menu_id=" . $menu_id;
    $result = $db->query($sql);

    if ($result->rowCount()) {
        $row = $result->fetch();
        $menu_name = $row['menu_name'];

        // Xóa món ăn
        $db->query("DELETE FROM " . NV_PREFIXLANG . "_" . $module_data . "_menu WHERE menu_id=" . $menu_id);

        nv_insert_logs(NV_LANG_DATA, $module_name, 'Delete menu', $menu_name, $admin_info['userid']);

        nv_jsonOutput([
            'status' => 'OK',
            'message' => $lang_module['success_delete']
        ]);
    }
}

nv_jsonOutput([
    'status' => 'error',
    'message' => $lang_module['error_delete']
]);
