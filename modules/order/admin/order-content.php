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

$id = $nv_Request->get_int('id', 'get', 0);
$row = [];
$error = [];

if ($id > 0) {
    $sql = "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_orders WHERE id = " . $id;
    $row = $db->query($sql)->fetch();

    if (empty($row)) {
        nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=orders');
    }

    $page_title = $lang_module['order_edit'];

    // Lấy chi tiết đơn hàng
    $sql = "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_order_details WHERE order_id = " . $id;
    $result = $db->query($sql);
    $order_details = [];
    while ($detail = $result->fetch()) {
        $order_details[] = $detail;
    }
} else {
    $page_title = $lang_module['order_add'];
    $row = [
        'id' => 0,
        'order_code' => nv_generate_order_code(),
        'customer_name' => '',
        'customer_phone' => '',
        'customer_address' => '',
        'staff_id' => 0,
        'total_amount' => 0,
        'discount_amount' => 0,
        'final_amount' => 0,
        'payment_method' => 'cash',
        'payment_status' => 0,
        'order_status' => 0,
        'notes' => '',
        'order_time' => NV_CURRENTTIME
    ];
    $order_details = [];
}

// Xử lý POST
if ($nv_Request->isset_request('submit', 'post')) {
    $row['order_code'] = $nv_Request->get_title('order_code', 'post', '');
    $row['customer_name'] = $nv_Request->get_title('customer_name', 'post', '');
    $row['customer_phone'] = $nv_Request->get_title('customer_phone', 'post', '');
    $row['customer_address'] = $nv_Request->get_textarea('customer_address', 'post', '');
    $row['staff_id'] = $nv_Request->get_int('staff_id', 'post', 0);
    $row['discount_amount'] = $nv_Request->get_float('discount_amount', 'post', 0);
    $row['payment_method'] = $nv_Request->get_title('payment_method', 'post', 'cash');
    $row['payment_status'] = $nv_Request->get_int('payment_status', 'post', 0);
    $row['order_status'] = $nv_Request->get_int('order_status', 'post', 0);
    $row['notes'] = $nv_Request->get_textarea('notes', 'post', '');

    // Lấy chi tiết món ăn
    $dish_ids = $nv_Request->get_array('dish_id', 'post', []);
    $quantities = $nv_Request->get_array('quantity', 'post', []);

    // Validate
    if (empty($row['order_code'])) {
        $error[] = $lang_module['error_required_fields'];
    }
    if (empty($row['customer_name'])) {
        $error[] = $lang_module['error_required_fields'];
    }
    if (empty($dish_ids)) {
        $error[] = 'Vui lòng chọn ít nhất một món ăn';
    }

    if (empty($error)) {
        // Tính tổng tiền
        $total_amount = 0;
        $valid_details = [];

        foreach ($dish_ids as $key => $dish_id) {
            if ($dish_id > 0 && isset($quantities[$key]) && $quantities[$key] > 0) {
                $sql = "SELECT id, name, price FROM " . NV_PREFIXLANG . "_" . $module_data . "_dishes WHERE id = " . $dish_id;
                $dish = $db->query($sql)->fetch();

                if ($dish) {
                    $quantity = intval($quantities[$key]);
                    $unit_price = $dish['price'];
                    $total_price = $unit_price * $quantity;
                    $total_amount += $total_price;

                    $valid_details[] = [
                        'dish_id' => $dish_id,
                        'dish_name' => $dish['name'],
                        'quantity' => $quantity,
                        'unit_price' => $unit_price,
                        'total_price' => $total_price
                    ];
                }
            }
        }

        $row['total_amount'] = $total_amount;
        $row['final_amount'] = $total_amount - $row['discount_amount'];

        if ($row['final_amount'] < 0) {
            $row['final_amount'] = 0;
        }

        try {
            if ($id > 0) {
                // Update
                $sql = "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_orders SET
                    order_code = :order_code,
                    customer_name = :customer_name,
                    customer_phone = :customer_phone,
                    customer_address = :customer_address,
                    staff_id = :staff_id,
                    total_amount = :total_amount,
                    discount_amount = :discount_amount,
                    final_amount = :final_amount,
                    payment_method = :payment_method,
                    payment_status = :payment_status,
                    order_status = :order_status,
                    notes = :notes,
                    edit_time = " . NV_CURRENTTIME . "
                    WHERE id = " . $id;

                $sth = $db->prepare($sql);
                $sth->bindParam(':order_code', $row['order_code'], PDO::PARAM_STR);
                $sth->bindParam(':customer_name', $row['customer_name'], PDO::PARAM_STR);
                $sth->bindParam(':customer_phone', $row['customer_phone'], PDO::PARAM_STR);
                $sth->bindParam(':customer_address', $row['customer_address'], PDO::PARAM_STR);
                $sth->bindParam(':staff_id', $row['staff_id'], PDO::PARAM_INT);
                $sth->bindParam(':total_amount', $row['total_amount'], PDO::PARAM_STR);
                $sth->bindParam(':discount_amount', $row['discount_amount'], PDO::PARAM_STR);
                $sth->bindParam(':final_amount', $row['final_amount'], PDO::PARAM_STR);
                $sth->bindParam(':payment_method', $row['payment_method'], PDO::PARAM_STR);
                $sth->bindParam(':payment_status', $row['payment_status'], PDO::PARAM_INT);
                $sth->bindParam(':order_status', $row['order_status'], PDO::PARAM_INT);
                $sth->bindParam(':notes', $row['notes'], PDO::PARAM_STR);
                $sth->execute();

                // Xóa chi tiết cũ
                $db->query("DELETE FROM " . NV_PREFIXLANG . "_" . $module_data . "_order_details WHERE order_id = " . $id);
            } else {
                // Insert
                $sql = "INSERT INTO " . NV_PREFIXLANG . "_" . $module_data . "_orders (
                    order_code, customer_name, customer_phone, customer_address, staff_id,
                    total_amount, discount_amount, final_amount, payment_method, payment_status,
                    order_status, notes, order_time, admin_id
                ) VALUES (
                    :order_code, :customer_name, :customer_phone, :customer_address, :staff_id,
                    :total_amount, :discount_amount, :final_amount, :payment_method, :payment_status,
                    :order_status, :notes, " . NV_CURRENTTIME . ", " . $admin_info['admin_id'] . "
                )";

                $sth = $db->prepare($sql);
                $sth->bindParam(':order_code', $row['order_code'], PDO::PARAM_STR);
                $sth->bindParam(':customer_name', $row['customer_name'], PDO::PARAM_STR);
                $sth->bindParam(':customer_phone', $row['customer_phone'], PDO::PARAM_STR);
                $sth->bindParam(':customer_address', $row['customer_address'], PDO::PARAM_STR);
                $sth->bindParam(':staff_id', $row['staff_id'], PDO::PARAM_INT);
                $sth->bindParam(':total_amount', $row['total_amount'], PDO::PARAM_STR);
                $sth->bindParam(':discount_amount', $row['discount_amount'], PDO::PARAM_STR);
                $sth->bindParam(':final_amount', $row['final_amount'], PDO::PARAM_STR);
                $sth->bindParam(':payment_method', $row['payment_method'], PDO::PARAM_STR);
                $sth->bindParam(':payment_status', $row['payment_status'], PDO::PARAM_INT);
                $sth->bindParam(':order_status', $row['order_status'], PDO::PARAM_INT);
                $sth->bindParam(':notes', $row['notes'], PDO::PARAM_STR);
                $sth->execute();

                $id = $db->lastInsertId();
            }

            // Insert chi tiết đơn hàng
            foreach ($valid_details as $detail) {
                $sql = "INSERT INTO " . NV_PREFIXLANG . "_" . $module_data . "_order_details (
                    order_id, dish_id, dish_name, quantity, unit_price, total_price
                ) VALUES (
                    " . $id . ", :dish_id, :dish_name, :quantity, :unit_price, :total_price
                )";

                $sth = $db->prepare($sql);
                $sth->bindParam(':dish_id', $detail['dish_id'], PDO::PARAM_INT);
                $sth->bindParam(':dish_name', $detail['dish_name'], PDO::PARAM_STR);
                $sth->bindParam(':quantity', $detail['quantity'], PDO::PARAM_INT);
                $sth->bindParam(':unit_price', $detail['unit_price'], PDO::PARAM_STR);
                $sth->bindParam(':total_price', $detail['total_price'], PDO::PARAM_STR);
                $sth->execute();
            }

            nv_insert_logs(NV_LANG_DATA, $module_name, $id > 0 ? 'Edit Order' : 'Add Order', 'ID: ' . $id, $admin_info['userid']);
            nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=orders');
        } catch (PDOException $e) {
            $error[] = $e->getMessage();
        }
    }
}

// Lấy danh sách món ăn
$dishes_list = [];
$sql = "SELECT id, name, price FROM " . NV_PREFIXLANG . "_" . $module_data . "_dishes WHERE status = 1 ORDER BY weight ASC, name ASC";
$result = $db->query($sql);
while ($dish = $result->fetch()) {
    $dishes_list[$dish['id']] = $dish;
}

$staff_list = nv_get_staff_list();
$order_status_list = nv_order_status_list();
$payment_status_list = nv_payment_status_list();
$payment_method_list = nv_payment_method_list();

$xtpl = new XTemplate('order-content.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('GLANG', $lang_global);
$xtpl->assign('DATA', $row);
$xtpl->assign('URL_BACK', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=orders');

// Errors
if (!empty($error)) {
    $xtpl->assign('ERROR', implode('<br>', $error));
    $xtpl->parse('main.error');
}

// Staff list
foreach ($staff_list as $key => $value) {
    $xtpl->assign('STAFF_ID', $key);
    $xtpl->assign('STAFF_NAME', $value);
    $xtpl->assign('STAFF_SELECTED', $row['staff_id'] == $key ? ' selected="selected"' : '');
    $xtpl->parse('main.staff_loop');
}

// Order status
foreach ($order_status_list as $key => $value) {
    $xtpl->assign('STATUS_ID', $key);
    $xtpl->assign('STATUS_NAME', $value);
    $xtpl->assign('STATUS_SELECTED', $row['order_status'] == $key ? ' selected="selected"' : '');
    $xtpl->parse('main.order_status_loop');
}

// Payment status
foreach ($payment_status_list as $key => $value) {
    $xtpl->assign('STATUS_ID', $key);
    $xtpl->assign('STATUS_NAME', $value);
    $xtpl->assign('STATUS_SELECTED', $row['payment_status'] == $key ? ' selected="selected"' : '');
    $xtpl->parse('main.payment_status_loop');
}

// Payment method
foreach ($payment_method_list as $key => $value) {
    $xtpl->assign('METHOD_ID', $key);
    $xtpl->assign('METHOD_NAME', $value);
    $xtpl->assign('METHOD_SELECTED', $row['payment_method'] == $key ? ' selected="selected"' : '');
    $xtpl->parse('main.payment_method_loop');
}

// Dishes list cho select
foreach ($dishes_list as $dish) {
    $xtpl->assign('DISH_ID', $dish['id']);
    $xtpl->assign('DISH_NAME', $dish['name']);
    $xtpl->assign('DISH_PRICE', $dish['price']);
    $xtpl->parse('main.dish_option');
}

// Order details
if (!empty($order_details)) {
    foreach ($order_details as $detail) {
        $detail['total_price_format'] = nv_format_currency($detail['total_price']);
        $xtpl->assign('DETAIL', $detail);

        foreach ($dishes_list as $dish) {
            $xtpl->assign('DISH_ID', $dish['id']);
            $xtpl->assign('DISH_NAME', $dish['name']);
            $xtpl->assign('DISH_SELECTED', $detail['dish_id'] == $dish['id'] ? ' selected="selected"' : '');
            $xtpl->parse('main.order_detail.dish_option');
        }

        $xtpl->parse('main.order_detail');
    }
} else {
    // Template trống cho add mới
    $xtpl->parse('main.empty_detail');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
