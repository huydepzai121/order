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

$order_id = $nv_Request->get_int('order_id', 'get,post', 0);

if ($order_id > 0) {
    // Lấy thông tin đơn hàng
    $sql = "SELECT order_code FROM " . NV_PREFIXLANG . "_" . $module_data . "_orders WHERE order_id=" . $order_id;
    $result = $db->query($sql);

    if ($result->rowCount()) {
        $row = $result->fetch();
        $order_code = $row['order_code'];

        try {
            $db->query('BEGIN');

            // Xóa chi tiết đơn hàng
            $db->query("DELETE FROM " . NV_PREFIXLANG . "_" . $module_data . "_order_items WHERE order_id=" . $order_id);

            // Xóa đơn hàng
            $db->query("DELETE FROM " . NV_PREFIXLANG . "_" . $module_data . "_orders WHERE order_id=" . $order_id);

            $db->query('COMMIT');

            nv_insert_logs(NV_LANG_DATA, $module_name, 'Delete order', $order_code, $admin_info['userid']);

            nv_jsonOutput([
                'status' => 'OK',
                'message' => $lang_module['success_delete']
            ]);
        } catch (PDOException $e) {
            $db->query('ROLLBACK');
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
