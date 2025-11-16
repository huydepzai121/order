<?php

/**
 * NukeViet Content Management System
 * @version 5.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2025 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

if (!defined('NV_ADMIN')) {
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
    global $nv_Lang;

    return [
        0 => $nv_Lang->getModule('status_new'),
        1 => $nv_Lang->getModule('status_processing'),
        2 => $nv_Lang->getModule('status_completed'),
        3 => $nv_Lang->getModule('status_cancelled')
    ];
}

/**
 * Lấy danh sách trạng thái thanh toán
 */
function nv_payment_status_list()
{
    global $nv_Lang;

    return [
        0 => $nv_Lang->getModule('payment_unpaid'),
        1 => $nv_Lang->getModule('payment_paid')
    ];
}

/**
 * Lấy danh sách phương thức thanh toán
 */
function nv_payment_method_list()
{
    global $nv_Lang;

    return [
        'cash' => $nv_Lang->getModule('payment_cash'),
        'transfer' => $nv_Lang->getModule('payment_transfer'),
        'card' => $nv_Lang->getModule('payment_card')
    ];
}

/**
 * Tạo mã đơn hàng tự động
 */
function nv_generate_order_code()
{
    global $db, $db_config, $module_data;

    // Lấy prefix từ config
    $prefix = 'ORD';
    $sql = "SELECT config_value FROM " . NV_PREFIXLANG . "_" . $module_data . "_config WHERE config_name=:config_name";
    $stmt = $db->prepare($sql);
    $config_name = 'order_prefix';
    $stmt->bindParam(':config_name', $config_name, PDO::PARAM_STR);
    $stmt->execute();
    if ($stmt->rowCount()) {
        $row = $stmt->fetch();
        $prefix = $row['config_value'];
    }

    // Tạo mã đơn hàng
    $code = $prefix . date('Ymd') . sprintf('%04d', rand(1, 9999));

    // Kiểm tra trùng lặp
    $check_sql = "SELECT COUNT(*) FROM " . NV_PREFIXLANG . "_" . $module_data . "_orders WHERE order_code=:order_code";
    $check_stmt = $db->prepare($check_sql);
    $check_stmt->bindParam(':order_code', $code, PDO::PARAM_STR);
    $check_stmt->execute();
    while ($check_stmt->fetchColumn()) {
        $code = $prefix . date('Ymd') . sprintf('%04d', rand(1, 9999));
        $check_stmt->bindParam(':order_code', $code, PDO::PARAM_STR);
        $check_stmt->execute();
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
            WHERE userid=:userid";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':userid', $userid, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount()) {
        $row = $stmt->fetch();
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

    $sql = "SELECT userid, username, first_name, last_name, email
            FROM " . NV_USERS_GLOBALTABLE;

    if ($active_only) {
        $sql .= " WHERE active=:active";
    }

    $sql .= " ORDER BY first_name ASC, last_name ASC";

    $stmt = $db->prepare($sql);
    if ($active_only) {
        $active = 1;
        $stmt->bindParam(':active', $active, PDO::PARAM_INT);
    }
    $stmt->execute();

    $staff_list = [];
    while ($row = $stmt->fetch()) {
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

    $sql = "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_menu";

    if ($status >= 0) {
        $sql .= " WHERE status=:status";
    }

    $sql .= " ORDER BY weight ASC, menu_id ASC";

    $stmt = $db->prepare($sql);
    if ($status >= 0) {
        $stmt->bindParam(':status', $status, PDO::PARAM_INT);
    }
    $stmt->execute();

    $menu_list = [];
    while ($row = $stmt->fetch()) {
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

    $sql = "SELECT config_value FROM " . NV_PREFIXLANG . "_" . $module_data . "_config WHERE config_name=:config_name";
    $stmt = $db->prepare($sql);
    $config_name = 'work_shifts';
    $stmt->bindParam(':config_name', $config_name, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount()) {
        $row = $stmt->fetch();
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
            WHERE order_id=:order_id";

    $stmt = $db->prepare($sql);
    $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount()) {
        $row = $stmt->fetch();
        return floatval($row['total_amount']);
    }

    return 0;
}
