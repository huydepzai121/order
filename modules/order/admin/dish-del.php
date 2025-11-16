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

$sql = "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_dishes WHERE id = " . $id;
$row = $db->query($sql)->fetch();

if (empty($row)) {
    nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=dishes');
}

if ($nv_Request->isset_request('confirm', 'post')) {
    $checkss = $nv_Request->get_title('checkss', 'post', '');

    if ($checkss == md5($id . NV_CHECK_SESSION)) {
        // Kiểm tra món ăn có trong đơn hàng nào không
        $sql = "SELECT COUNT(*) FROM " . NV_PREFIXLANG . "_" . $module_data . "_order_details WHERE dish_id = " . $id;
        $count = $db->query($sql)->fetchColumn();

        if ($count > 0) {
            nv_jsonOutput([
                'status' => 'error',
                'message' => 'Không thể xóa món ăn này vì đã có trong đơn hàng'
            ]);
        }

        $db->query("DELETE FROM " . NV_PREFIXLANG . "_" . $module_data . "_dishes WHERE id = " . $id);

        nv_insert_logs(NV_LANG_DATA, $module_name, 'Delete Dish', $row['name'], $admin_info['userid']);

        nv_jsonOutput([
            'status' => 'OK',
            'message' => $lang_module['success_delete'],
            'redirect' => NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=dishes'
        ]);
    }
}

nv_jsonOutput([
    'status' => 'confirm',
    'message' => $lang_module['confirm_delete'] . '<br><strong>' . $row['name'] . '</strong>',
    'form' => '<input type="hidden" name="confirm" value="1"><input type="hidden" name="checkss" value="' . md5($id . NV_CHECK_SESSION) . '">'
]);
