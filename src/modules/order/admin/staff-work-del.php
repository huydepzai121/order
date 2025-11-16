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

$work_id = $nv_Request->get_int('work_id', 'post', 0);

if ($work_id > 0) {
    try {
        // Xóa công việc
        $del_sql = "DELETE FROM " . NV_PREFIXLANG . "_" . $module_data . "_staff_work WHERE work_id=:work_id";
        $del_stmt = $db->prepare($del_sql);
        $del_stmt->bindParam(':work_id', $work_id, PDO::PARAM_INT);
        $del_stmt->execute();

        nv_insert_logs(NV_LANG_DATA, $module_name, 'Delete staff work', '', $admin_info['userid']);

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

nv_jsonOutput([
    'status' => 'error',
    'message' => $nv_Lang->getModule('error_delete')
]);
