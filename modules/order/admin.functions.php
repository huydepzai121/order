<?php

/**
 * NukeViet Content Management System
 * @version 5.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2021 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

if (!defined('NV_ADMIN') or !defined('NV_MAINFILE') or !defined('NV_IS_MODADMIN')) {
    die('Stop!!!');
}

define('NV_IS_FILE_ADMIN', true);

$page_title = $lang_module['main'];

/**
 * nv_order_status_list()
 * Danh sách trạng thái đơn hàng
 *
 * @return array
 */
function nv_order_status_list()
{
    global $lang_module;

    return [
        0 => $lang_module['order_status_0'],
        1 => $lang_module['order_status_1'],
        2 => $lang_module['order_status_2'],
        3 => $lang_module['order_status_3']
    ];
}

/**
 * nv_payment_status_list()
 * Danh sách trạng thái thanh toán
 *
 * @return array
 */
function nv_payment_status_list()
{
    global $lang_module;

    return [
        0 => $lang_module['payment_status_0'],
        1 => $lang_module['payment_status_1'],
        2 => $lang_module['payment_status_2']
    ];
}

/**
 * nv_payment_method_list()
 * Danh sách phương thức thanh toán
 *
 * @return array
 */
function nv_payment_method_list()
{
    global $lang_module;

    return [
        'cash' => $lang_module['payment_method_cash'],
        'card' => $lang_module['payment_method_card'],
        'transfer' => $lang_module['payment_method_transfer']
    ];
}

/**
 * nv_generate_order_code()
 * Tạo mã đơn hàng tự động
 *
 * @return string
 */
function nv_generate_order_code()
{
    global $db, $module_data;

    $prefix = 'ORD';
    $date = date('Ymd');

    // Lấy số thứ tự cuối cùng trong ngày
    $sql = "SELECT order_code FROM " . NV_PREFIXLANG . "_" . $module_data . "_orders
            WHERE order_code LIKE '" . $prefix . $date . "%'
            ORDER BY id DESC LIMIT 1";
    $result = $db->query($sql);

    if ($result->rowCount()) {
        $row = $result->fetch();
        $last_code = $row['order_code'];
        $number = intval(substr($last_code, -4)) + 1;
    } else {
        $number = 1;
    }

    return $prefix . $date . str_pad($number, 4, '0', STR_PAD_LEFT);
}

/**
 * nv_get_staff_list()
 * Lấy danh sách nhân viên
 *
 * @return array
 */
function nv_get_staff_list()
{
    global $db, $module_data;

    $staff_list = [];

    $sql = "SELECT s.id, s.userid, u.username, u.first_name, u.last_name, s.position
            FROM " . NV_PREFIXLANG . "_" . $module_data . "_staff s
            INNER JOIN " . NV_USERS_GLOBALTABLE . " u ON s.userid = u.userid
            WHERE s.status = 1
            ORDER BY u.last_name ASC, u.first_name ASC";

    $result = $db->query($sql);
    while ($row = $result->fetch()) {
        $staff_list[$row['id']] = $row['first_name'] . ' ' . $row['last_name'] . ' (' . $row['position'] . ')';
    }

    return $staff_list;
}

/**
 * nv_format_currency()
 * Định dạng tiền tệ
 *
 * @param mixed $amount
 * @return string
 */
function nv_format_currency($amount)
{
    return number_format($amount, 0, ',', '.') . ' đ';
}
