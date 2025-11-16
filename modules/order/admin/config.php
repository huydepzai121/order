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

$page_title = $lang_module['config'];

// Load config
$config_data = [];
$sql = "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_config";
$result = $db->query($sql);
while ($row = $result->fetch()) {
    $config_data[$row['config_name']] = $row['config_value'];
}

// Default values
$defaults = [
    'order_prefix' => 'ORD',
    'tax_rate' => '10',
    'currency' => 'VND',
    'auto_order_code' => '1'
];

$config_data = array_merge($defaults, $config_data);

$error = [];

// Xử lý POST
if ($nv_Request->isset_request('submit', 'post')) {
    $config_data['order_prefix'] = $nv_Request->get_title('order_prefix', 'post', 'ORD');
    $config_data['tax_rate'] = $nv_Request->get_float('tax_rate', 'post', 10);
    $config_data['currency'] = $nv_Request->get_title('currency', 'post', 'VND');
    $config_data['auto_order_code'] = $nv_Request->get_int('auto_order_code', 'post', 1);

    if (empty($error)) {
        try {
            // Xóa config cũ
            $db->query("DELETE FROM " . NV_PREFIXLANG . "_" . $module_data . "_config");

            // Insert config mới
            foreach ($config_data as $name => $value) {
                $sql = "INSERT INTO " . NV_PREFIXLANG . "_" . $module_data . "_config (config_name, config_value) VALUES (:name, :value)";
                $sth = $db->prepare($sql);
                $sth->bindParam(':name', $name, PDO::PARAM_STR);
                $sth->bindParam(':value', $value, PDO::PARAM_STR);
                $sth->execute();
            }

            nv_insert_logs(NV_LANG_DATA, $module_name, 'Update Config', '', $admin_info['userid']);

            nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=config&save=1');
        } catch (PDOException $e) {
            $error[] = $e->getMessage();
        }
    }
}

// Show success message
if ($nv_Request->isset_request('save', 'get')) {
    $success = $lang_module['success_save'];
}

$xtpl = new XTemplate('config.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('GLANG', $lang_global);
$xtpl->assign('DATA', $config_data);

// Errors
if (!empty($error)) {
    $xtpl->assign('ERROR', implode('<br>', $error));
    $xtpl->parse('main.error');
}

// Success
if (isset($success)) {
    $xtpl->assign('SUCCESS', $success);
    $xtpl->parse('main.success');
}

// Auto order code
$xtpl->assign('AUTO_ORDER_CODE_CHECKED', $config_data['auto_order_code'] == 1 ? ' checked="checked"' : '');

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
