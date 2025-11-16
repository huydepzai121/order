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

$page_title = $nv_Lang->getModule('config');

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
    // Verify CSRF token
    $checkss = $nv_Request->get_title('checkss', 'post', '');
    if ($checkss != md5($client_info['session_id'] . $global_config['sitekey'])) {
        $error[] = $nv_Lang->getModule('error_security');
    } else {
        $order_prefix = $nv_Request->get_title('order_prefix', 'post', 'ORD');
        $auto_order_code = $nv_Request->get_int('auto_order_code', 'post', 1);
        $default_payment_method = $nv_Request->get_title('default_payment_method', 'post', 'cash');
        $work_shifts = $nv_Request->get_title('work_shifts', 'post', '');

        // Validate
        if (empty($order_prefix)) {
            $error[] = $nv_Lang->getModule('error_order_prefix_required');
        }
        if (empty($work_shifts)) {
            $error[] = $nv_Lang->getModule('error_work_shifts_required');
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
                $success = $nv_Lang->getModule('success_save');

                // Reload config
                $config = $configs;
            } catch (PDOException $e) {
                $error[] = $nv_Lang->getModule('error_save');
            }
        }
    }
}

// Giá trị mặc định
$order_prefix = isset($config['order_prefix']) ? $config['order_prefix'] : 'ORD';
$auto_order_code = isset($config['auto_order_code']) ? intval($config['auto_order_code']) : 1;
$default_payment_method = isset($config['default_payment_method']) ? $config['default_payment_method'] : 'cash';
$work_shifts = isset($config['work_shifts']) ? $config['work_shifts'] : $nv_Lang->getModule('default_work_shifts');

// Prepare payment methods data
$payment_methods = nv_payment_method_list();
$payment_methods_data = [];
foreach ($payment_methods as $key => $value) {
    $payment_methods_data[] = [
        'key' => $key,
        'value' => $value,
        'selected' => $key == $default_payment_method
    ];
}

// Initialize Smarty template
$tpl = new \NukeViet\Template\NVSmarty();
$tpl->setTemplateDir(get_module_tpl_dir('config.tpl'));
$tpl->assign('LANG', $nv_Lang);
$tpl->assign('MODULE_NAME', $module_name);
$tpl->assign('OP', $op);
$tpl->assign('ORDER_PREFIX', $order_prefix);
$tpl->assign('AUTO_ORDER_CODE_CHECKED', $auto_order_code == 1);
$tpl->assign('WORK_SHIFTS', $work_shifts);
$tpl->assign('SUCCESS', $success);
$tpl->assign('ERROR', $error);
$tpl->assign('PAYMENT_METHODS', $payment_methods_data);
$tpl->assign('NV_CHECK', md5($client_info['session_id'] . $global_config['sitekey']));

$contents = $tpl->fetch('config.tpl');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
