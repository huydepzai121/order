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

$order_id = $nv_Request->get_int('order_id', 'post', 0);

if ($order_id > 0) {
    // Lấy thông tin đơn hàng
    $sql = "SELECT order_code FROM " . NV_PREFIXLANG . "_" . $module_data . "_orders WHERE order_id=:order_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount()) {
        $row = $stmt->fetch();
        $order_code = $row['order_code'];

        try {
            $db->query('BEGIN');

            // Xóa chi tiết đơn hàng
            $del_items_sql = "DELETE FROM " . NV_PREFIXLANG . "_" . $module_data . "_order_items WHERE order_id=:order_id";
            $del_items_stmt = $db->prepare($del_items_sql);
            $del_items_stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
            $del_items_stmt->execute();

            // Xóa đơn hàng
            $del_order_sql = "DELETE FROM " . NV_PREFIXLANG . "_" . $module_data . "_orders WHERE order_id=:order_id";
            $del_order_stmt = $db->prepare($del_order_sql);
            $del_order_stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
            $del_order_stmt->execute();

            $db->query('COMMIT');

            nv_insert_logs(NV_LANG_DATA, $module_name, 'Delete order', $order_code, $admin_info['userid']);

            nv_jsonOutput([
                'status' => 'OK',
                'message' => $nv_Lang->getModule('success_delete')
            ]);
        } catch (PDOException $e) {
            $db->query('ROLLBACK');
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
