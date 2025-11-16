<?php

/**
 * NukeViet Content Management System
 * @version 5.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2025 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

if (!defined('NV_ADMIN') or !defined('NV_MAINFILE') or !defined('NV_IS_MODADMIN')) {
    exit('Stop!!!');
}

define('NV_IS_FILE_ADMIN', true);

$allow_func = [
    'main',
    'content',
    'del',
    'menu',
    'menu-content',
    'menu-del',
    'staff',
    'staff-work',
    'staff-work-content',
    'staff-work-del',
    'report',
    'config'
];

/**
 * Lấy danh sách trạng thái đơn hàng
 */
function nv_order_status_list()
{
    global $lang_module;

    return [
        0 => $lang_module['status_new'],
        1 => $lang_module['status_processing'],
        2 => $lang_module['status_completed'],
        3 => $lang_module['status_cancelled']
    ];
}

/**
 * Lấy danh sách trạng thái thanh toán
 */
function nv_payment_status_list()
{
    global $lang_module;

    return [
        0 => $lang_module['payment_unpaid'],
        1 => $lang_module['payment_paid']
    ];
}

/**
 * Lấy danh sách phương thức thanh toán
 */
function nv_payment_method_list()
{
    global $lang_module;

    return [
        'cash' => $lang_module['payment_cash'],
        'transfer' => $lang_module['payment_transfer'],
        'card' => $lang_module['payment_card']
    ];
}

/**
 * Tạo mã đơn hàng tự động
 */
function nv_generate_order_code()
{
    global $db, $db_config, $module_data, $lang;

    // Lấy prefix từ config
    $prefix = 'ORD';
    $sql = "SELECT config_value FROM " . NV_PREFIXLANG . "_" . $module_data . "_config WHERE config_name='order_prefix'";
    $result = $db->query($sql);
    if ($result->rowCount()) {
        $row = $result->fetch();
        $prefix = $row['config_value'];
    }

    // Tạo mã đơn hàng
    $code = $prefix . date('Ymd') . sprintf('%04d', rand(1, 9999));

    // Kiểm tra trùng lặp
    $check_sql = "SELECT COUNT(*) FROM " . NV_PREFIXLANG . "_" . $module_data . "_orders WHERE order_code='" . $code . "'";
    while ($db->query($check_sql)->fetchColumn()) {
        $code = $prefix . date('Ymd') . sprintf('%04d', rand(1, 9999));
    }

    return $code;
}

/**
 * Lấy thông tin nhân viên từ bảng users
 */
function nv_get_staff_info($userid)
{
    global $db;

    if (empty($userid)) {
        return [];
    }

    $sql = "SELECT userid, username, first_name, last_name, email, gender
            FROM " . NV_USERS_GLOBALTABLE . "
            WHERE userid=" . intval($userid);
    $result = $db->query($sql);

    if ($result->rowCount()) {
        $row = $result->fetch();
        $row['full_name'] = trim($row['first_name'] . ' ' . $row['last_name']);
        if (empty($row['full_name'])) {
            $row['full_name'] = $row['username'];
        }
        return $row;
    }

    return [];
}

/**
 * Lấy danh sách nhân viên
 */
function nv_get_staff_list($active_only = true)
{
    global $db;

    $where = [];
    if ($active_only) {
        $where[] = "active=1";
    }

    $sql = "SELECT userid, username, first_name, last_name, email
            FROM " . NV_USERS_GLOBALTABLE;

    if (!empty($where)) {
        $sql .= " WHERE " . implode(' AND ', $where);
    }

    $sql .= " ORDER BY first_name ASC, last_name ASC";

    $result = $db->query($sql);
    $staff_list = [];

    while ($row = $result->fetch()) {
        $row['full_name'] = trim($row['first_name'] . ' ' . $row['last_name']);
        if (empty($row['full_name'])) {
            $row['full_name'] = $row['username'];
        }
        $staff_list[$row['userid']] = $row;
    }

    return $staff_list;
}

/**
 * Lấy danh sách thực đơn
 */
function nv_get_menu_list($status = -1)
{
    global $db, $module_data;

    $where = [];
    if ($status >= 0) {
        $where[] = "status=" . intval($status);
    }

    $sql = "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_menu";

    if (!empty($where)) {
        $sql .= " WHERE " . implode(' AND ', $where);
    }

    $sql .= " ORDER BY weight ASC, menu_id ASC";

    $result = $db->query($sql);
    $menu_list = [];

    while ($row = $result->fetch()) {
        $menu_list[$row['menu_id']] = $row;
    }

    return $menu_list;
}

/**
 * Lấy danh sách ca làm việc
 */
function nv_get_work_shifts()
{
    global $db, $module_data;

    $sql = "SELECT config_value FROM " . NV_PREFIXLANG . "_" . $module_data . "_config WHERE config_name='work_shifts'";
    $result = $db->query($sql);

    if ($result->rowCount()) {
        $row = $result->fetch();
        $shifts = explode(',', $row['config_value']);
        return array_map('trim', $shifts);
    }

    return ['Sáng', 'Chiều', 'Tối'];
}

/**
 * Format tiền tệ
 */
function nv_format_currency($amount)
{
    return number_format($amount, 0, ',', '.') . ' đ';
}

/**
 * Tính tổng tiền đơn hàng
 */
function nv_calculate_order_total($order_id)
{
    global $db, $module_data;

    $sql = "SELECT SUM(total) as total_amount
            FROM " . NV_PREFIXLANG . "_" . $module_data . "_order_items
            WHERE order_id=" . intval($order_id);

    $result = $db->query($sql);
    if ($result->rowCount()) {
        $row = $result->fetch();
        return floatval($row['total_amount']);
    }

    return 0;
}
