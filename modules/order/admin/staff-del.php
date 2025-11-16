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

$id = $nv_Request->get_int('id', 'get,post', 0);

$sql = "SELECT s.*, u.username, u.first_name, u.last_name
        FROM " . NV_PREFIXLANG . "_" . $module_data . "_staff s
        INNER JOIN " . NV_USERS_GLOBALTABLE . " u ON s.userid = u.userid
        WHERE s.id = " . $id;
$row = $db->query($sql)->fetch();

if (empty($row)) {
    nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=staff');
}

if ($nv_Request->isset_request('confirm', 'post')) {
    $checkss = $nv_Request->get_title('checkss', 'post', '');

    if ($checkss == md5($id . NV_CHECK_SESSION)) {
        // Kiểm tra nhân viên có trong đơn hàng nào không
        $sql = "SELECT COUNT(*) FROM " . NV_PREFIXLANG . "_" . $module_data . "_orders WHERE staff_id = " . $id;
        $count = $db->query($sql)->fetchColumn();

        if ($count > 0) {
            nv_jsonOutput([
                'status' => 'error',
                'message' => 'Không thể xóa nhân viên này vì đã có trong đơn hàng'
            ]);
        }

        $db->query("DELETE FROM " . NV_PREFIXLANG . "_" . $module_data . "_staff WHERE id = " . $id);

        nv_insert_logs(NV_LANG_DATA, $module_name, 'Delete Staff', $row['first_name'] . ' ' . $row['last_name'], $admin_info['userid']);

        nv_jsonOutput([
            'status' => 'OK',
            'message' => $lang_module['success_delete'],
            'redirect' => NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=staff'
        ]);
    }
}

nv_jsonOutput([
    'status' => 'confirm',
    'message' => $lang_module['confirm_delete'] . '<br><strong>' . $row['first_name'] . ' ' . $row['last_name'] . '</strong>',
    'form' => '<input type="hidden" name="confirm" value="1"><input type="hidden" name="checkss" value="' . md5($id . NV_CHECK_SESSION) . '">'
]);
