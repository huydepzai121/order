<?php

/**
 * NukeViet Content Management System
 * @version 5.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2025 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

if (!defined('NV_IS_FILE_ADMIN')) {
    exit('Stop!!!');
}

$page_title = nv_Lang::$lang_module['config'];

$config = nv_order_get_config();
$row = [
    'commission_rate' => isset($config['commission_rate']) ? $config['commission_rate'] : 10,
    'currency_symbol' => isset($config['currency_symbol']) ? $config['currency_symbol'] : 'đ',
    'tax_rate' => isset($config['tax_rate']) ? $config['tax_rate'] : 10,
    'allow_discount' => isset($config['allow_discount']) ? $config['allow_discount'] : 1
];

$error = [];

if ($nv_Request->get_title('save', 'post') === NV_CHECK_SESSION) {
    $row['commission_rate'] = $nv_Request->get_float('commission_rate', 'post', 10);
    $row['currency_symbol'] = $nv_Request->get_title('currency_symbol', 'post', 'đ');
    $row['tax_rate'] = $nv_Request->get_float('tax_rate', 'post', 10);
    $row['allow_discount'] = $nv_Request->get_int('allow_discount', 'post', 0);

    if (empty($row['currency_symbol'])) {
        $error[] = nv_Lang::$lang_module['error_required'];
    }

    if (empty($error)) {
        try {
            foreach ($row as $key => $value) {
                $sql = 'INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_config (config_name, config_value)
                    VALUES (:config_name, :config_value)
                    ON DUPLICATE KEY UPDATE config_value = :config_value2';

                $stmt = $db->prepare($sql);
                $stmt->bindParam(':config_name', $key, PDO::PARAM_STR);
                $stmt->bindParam(':config_value', $value, PDO::PARAM_STR);
                $stmt->bindParam(':config_value2', $value, PDO::PARAM_STR);
                $stmt->execute();
            }

            nv_insert_logs(NV_LANG_DATA, $module_name, 'Update config', '', $admin_info['userid']);
            nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op . '&saved=1');
        } catch (Exception $e) {
            $error[] = nv_Lang::$lang_module['save_error'];
        }
    }
}

$saved = $nv_Request->get_int('saved', 'get', 0);

// Template
$tpl = get_tpl_dir([$global_config['module_theme'], $global_config['admin_theme']], 'admin_default', '/modules/' . $module_file . '/config.tpl');
$smarty = new Smarty();
$smarty->setTemplateDir(NV_ROOTDIR . '/themes/' . $tpl);

$smarty->assign('LANG', nv_Lang::$lang_module);
$smarty->assign('GLANG', nv_Lang::$lang_global);
$smarty->assign('ROW', $row);
$smarty->assign('ERROR', implode('<br>', $error));
$smarty->assign('SAVED', $saved);
$smarty->assign('FORM_ACTION', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=' . $op);
$smarty->assign('NV_CHECK_SESSION', NV_CHECK_SESSION);

$contents = $smarty->fetch('config.tpl');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
