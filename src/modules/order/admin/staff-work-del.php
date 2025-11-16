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

$work_id = $nv_Request->get_int('work_id', 'get,post', 0);

if ($work_id > 0) {
    // Xóa công việc
    $db->query("DELETE FROM " . NV_PREFIXLANG . "_" . $module_data . "_staff_work WHERE work_id=" . $work_id);

    nv_insert_logs(NV_LANG_DATA, $module_name, 'Delete staff work', '', $admin_info['userid']);

    nv_jsonOutput([
        'status' => 'OK',
        'message' => $lang_module['success_delete']
    ]);
}

nv_jsonOutput([
    'status' => 'error',
    'message' => $lang_module['error_delete']
]);
