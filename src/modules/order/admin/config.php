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

$page_title = $lang_module['config'];

$error = [];
$success = '';

// Lấy cấu hình hiện tại
$config = [];
$sql = "SELECT config_name, config_value FROM " . NV_PREFIXLANG . "_" . $module_data . "_config";
$result = $db->query($sql);
while ($row = $result->fetch()) {
    $config[$row['config_name']] = $row['config_value'];
}

// Xử lý POST
if ($nv_Request->isset_request('submit', 'post')) {
    $order_prefix = $nv_Request->get_title('order_prefix', 'post', 'ORD');
    $auto_order_code = $nv_Request->get_int('auto_order_code', 'post', 1);
    $default_payment_method = $nv_Request->get_title('default_payment_method', 'post', 'cash');
    $work_shifts = $nv_Request->get_title('work_shifts', 'post', '');

    // Validate
    if (empty($order_prefix)) {
        $error[] = 'Tiền tố mã đơn hàng không được để trống';
    }
    if (empty($work_shifts)) {
        $error[] = 'Danh sách ca làm việc không được để trống';
    }

    if (empty($error)) {
        try {
            // Cập nhật cấu hình
            $configs = [
                'order_prefix' => $order_prefix,
                'auto_order_code' => $auto_order_code,
                'default_payment_method' => $default_payment_method,
                'work_shifts' => $work_shifts
            ];

            foreach ($configs as $name => $value) {
                $sql = "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_config
                        SET config_value=:value WHERE config_name=:name";
                $stmt = $db->prepare($sql);
                $stmt->bindParam(':value', $value, PDO::PARAM_STR);
                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                $stmt->execute();
            }

            nv_insert_logs(NV_LANG_DATA, $module_name, 'Update config', '', $admin_info['userid']);
            $success = $lang_module['success_save'];

            // Reload config
            $config = $configs;
        } catch (PDOException $e) {
            $error[] = $lang_module['error_save'];
        }
    }
}

// Giá trị mặc định
$order_prefix = isset($config['order_prefix']) ? $config['order_prefix'] : 'ORD';
$auto_order_code = isset($config['auto_order_code']) ? intval($config['auto_order_code']) : 1;
$default_payment_method = isset($config['default_payment_method']) ? $config['default_payment_method'] : 'cash';
$work_shifts = isset($config['work_shifts']) ? $config['work_shifts'] : 'Sáng,Chiều,Tối';

// Include template
$xtpl = new XTemplate('config.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('GLANG', $lang_global);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('OP', $op);
$xtpl->assign('ORDER_PREFIX', $order_prefix);
$xtpl->assign('AUTO_ORDER_CODE', $auto_order_code);
$xtpl->assign('WORK_SHIFTS', $work_shifts);

// Success message
if (!empty($success)) {
    $xtpl->assign('SUCCESS', $success);
    $xtpl->parse('main.success');
}

// Errors
if (!empty($error)) {
    foreach ($error as $e) {
        $xtpl->assign('ERROR', $e);
        $xtpl->parse('main.error.loop');
    }
    $xtpl->parse('main.error');
}

// Payment methods
$payment_methods = nv_payment_method_list();
foreach ($payment_methods as $key => $value) {
    $xtpl->assign('PAYMENT_METHOD', [
        'key' => $key,
        'value' => $value,
        'selected' => $key == $default_payment_method ? 'selected="selected"' : ''
    ]);
    $xtpl->parse('main.payment_method');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
