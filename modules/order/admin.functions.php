<?php

/**
 * NukeViet Content Management System
 * @version 5.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2025 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

if (!defined('NV_ADMIN') or !defined('NV_MAINFILE')) {
    exit('Stop!!!');
}

define('NV_IS_FILE_ADMIN', true);

/**
 * Lấy danh sách nhân viên từ bảng users
 */
function nv_order_get_employees()
{
    global $db;

    $sql = 'SELECT userid, username, first_name, last_name, email FROM ' . NV_USERS_GLOBALTABLE . ' WHERE active=1 ORDER BY username ASC';
    $result = $db->query($sql);

    $employees = [];
    while ($row = $result->fetch()) {
        $employees[$row['userid']] = [
            'userid' => $row['userid'],
            'username' => $row['username'],
            'full_name' => $row['first_name'] . ' ' . $row['last_name'],
            'email' => $row['email']
        ];
    }

    return $employees;
}

/**
 * Lấy thông tin cấu hình module
 */
function nv_order_get_config($config_name = '')
{
    global $db, $module_data;

    $config = [];
    $sql = 'SELECT config_name, config_value FROM ' . NV_PREFIXLANG . '_' . $module_data . '_config';
    $result = $db->query($sql);

    while ($row = $result->fetch()) {
        $config[$row['config_name']] = $row['config_value'];
    }

    if (!empty($config_name)) {
        return isset($config[$config_name]) ? $config[$config_name] : '';
    }

    return $config;
}

/**
 * Tạo mã đơn hàng tự động
 */
function nv_order_generate_code()
{
    return 'ORD' . date('Ymd') . rand(1000, 9999);
}

/**
 * Lấy trạng thái đơn hàng
 */
function nv_order_get_status_list()
{
    return [
        0 => nv_Lang::$lang_module['status_new'],
        1 => nv_Lang::$lang_module['status_processing'],
        2 => nv_Lang::$lang_module['status_completed'],
        3 => nv_Lang::$lang_module['status_cancelled']
    ];
}

/**
 * Lấy trạng thái thanh toán
 */
function nv_order_get_payment_status_list()
{
    return [
        0 => nv_Lang::$lang_module['payment_unpaid'],
        1 => nv_Lang::$lang_module['payment_paid'],
        2 => nv_Lang::$lang_module['payment_partial']
    ];
}

/**
 * Định dạng số tiền
 */
function nv_order_format_currency($amount)
{
    $config = nv_order_get_config();
    $symbol = isset($config['currency_symbol']) ? $config['currency_symbol'] : 'đ';
    return number_format($amount, 0, ',', '.') . ' ' . $symbol;
}
