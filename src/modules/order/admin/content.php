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

$page_title = $lang_module['order_add'];
$order_id = $nv_Request->get_int('order_id', 'get', 0);

// Lấy thông tin đơn hàng nếu đang sửa
$order = [];
$order_items = [];
if ($order_id > 0) {
    $sql = "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_orders WHERE order_id=" . $order_id;
    $result = $db->query($sql);
    if ($result->rowCount()) {
        $order = $result->fetch();
        $page_title = $lang_module['edit'] . ': ' . $order['order_code'];

        // Lấy chi tiết đơn hàng
        $sql = "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_order_items WHERE order_id=" . $order_id;
        $result = $db->query($sql);
        while ($row = $result->fetch()) {
            $order_items[] = $row;
        }
    } else {
        nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=main');
    }
}

$error = [];

// Xử lý POST
if ($nv_Request->isset_request('submit', 'post')) {
    $order_code = $nv_Request->get_title('order_code', 'post', '');
    $staff_id = $nv_Request->get_int('staff_id', 'post', 0);
    $customer_name = $nv_Request->get_title('customer_name', 'post', '');
    $customer_phone = $nv_Request->get_title('customer_phone', 'post', '');
    $customer_address = $nv_Request->get_title('customer_address', 'post', '');
    $order_date = $nv_Request->get_title('order_date', 'post', '');
    $delivery_date = $nv_Request->get_title('delivery_date', 'post', '');
    $status = $nv_Request->get_int('status', 'post', 0);
    $payment_status = $nv_Request->get_int('payment_status', 'post', 0);
    $payment_method = $nv_Request->get_title('payment_method', 'post', 'cash');
    $note = $nv_Request->get_textarea('note', '', 'post');

    // Lấy chi tiết đơn hàng
    $items_menu_id = $nv_Request->get_array('items_menu_id', 'post', []);
    $items_quantity = $nv_Request->get_array('items_quantity', 'post', []);
    $items_price = $nv_Request->get_array('items_price', 'post', []);
    $items_note = $nv_Request->get_array('items_note', 'post', []);

    // Validate
    if (empty($customer_name)) {
        $error[] = $lang_module['customer_name'] . ': ' . $lang_module['error_required'];
    }
    if (empty($customer_phone)) {
        $error[] = $lang_module['customer_phone'] . ': ' . $lang_module['error_required'];
    }
    if (empty($order_date)) {
        $error[] = $lang_module['order_date'] . ': ' . $lang_module['error_required'];
    }

    // Tạo mã đơn hàng nếu chưa có
    if (empty($order_code)) {
        $order_code = nv_generate_order_code();
    }

    // Convert dates to timestamp
    $order_date_timestamp = strtotime($order_date);
    $delivery_date_timestamp = !empty($delivery_date) ? strtotime($delivery_date) : 0;

    // Tính tổng tiền
    $total_amount = 0;
    foreach ($items_menu_id as $key => $menu_id) {
        if (!empty($menu_id) && !empty($items_quantity[$key]) && !empty($items_price[$key])) {
            $quantity = intval($items_quantity[$key]);
            $price = floatval(str_replace(',', '', $items_price[$key]));
            $total_amount += $quantity * $price;
        }
    }

    if (empty($error)) {
        try {
            $db->query('BEGIN');

            if ($order_id > 0) {
                // Cập nhật đơn hàng
                $sql = "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_orders SET
                        order_code=:order_code,
                        staff_id=:staff_id,
                        customer_name=:customer_name,
                        customer_phone=:customer_phone,
                        customer_address=:customer_address,
                        order_date=:order_date,
                        delivery_date=:delivery_date,
                        total_amount=:total_amount,
                        status=:status,
                        payment_status=:payment_status,
                        payment_method=:payment_method,
                        note=:note,
                        update_time=:update_time
                        WHERE order_id=" . $order_id;
            } else {
                // Thêm mới đơn hàng
                $sql = "INSERT INTO " . NV_PREFIXLANG . "_" . $module_data . "_orders (
                        order_code, staff_id, customer_name, customer_phone, customer_address,
                        order_date, delivery_date, total_amount, status, payment_status,
                        payment_method, note, admin_id, add_time, update_time
                    ) VALUES (
                        :order_code, :staff_id, :customer_name, :customer_phone, :customer_address,
                        :order_date, :delivery_date, :total_amount, :status, :payment_status,
                        :payment_method, :note, :admin_id, :add_time, :update_time
                    )";
            }

            $stmt = $db->prepare($sql);
            $stmt->bindParam(':order_code', $order_code, PDO::PARAM_STR);
            $stmt->bindParam(':staff_id', $staff_id, PDO::PARAM_INT);
            $stmt->bindParam(':customer_name', $customer_name, PDO::PARAM_STR);
            $stmt->bindParam(':customer_phone', $customer_phone, PDO::PARAM_STR);
            $stmt->bindParam(':customer_address', $customer_address, PDO::PARAM_STR);
            $stmt->bindParam(':order_date', $order_date_timestamp, PDO::PARAM_INT);
            $stmt->bindParam(':delivery_date', $delivery_date_timestamp, PDO::PARAM_INT);
            $stmt->bindParam(':total_amount', $total_amount, PDO::PARAM_STR);
            $stmt->bindParam(':status', $status, PDO::PARAM_INT);
            $stmt->bindParam(':payment_status', $payment_status, PDO::PARAM_INT);
            $stmt->bindParam(':payment_method', $payment_method, PDO::PARAM_STR);
            $stmt->bindParam(':note', $note, PDO::PARAM_STR);
            $stmt->bindValue(':update_time', NV_CURRENTTIME, PDO::PARAM_INT);

            if ($order_id == 0) {
                $stmt->bindValue(':admin_id', $admin_info['userid'], PDO::PARAM_INT);
                $stmt->bindValue(':add_time', NV_CURRENTTIME, PDO::PARAM_INT);
            }

            $stmt->execute();

            if ($order_id == 0) {
                $order_id = $db->lastInsertId();
            }

            // Xóa chi tiết đơn hàng cũ
            $db->query("DELETE FROM " . NV_PREFIXLANG . "_" . $module_data . "_order_items WHERE order_id=" . $order_id);

            // Thêm chi tiết đơn hàng mới
            foreach ($items_menu_id as $key => $menu_id) {
                if (!empty($menu_id) && !empty($items_quantity[$key]) && !empty($items_price[$key])) {
                    $quantity = intval($items_quantity[$key]);
                    $price = floatval(str_replace(',', '', $items_price[$key]));
                    $item_total = $quantity * $price;
                    $item_note = isset($items_note[$key]) ? $items_note[$key] : '';

                    // Lấy tên món
                    $menu_name = '';
                    $menu_sql = "SELECT menu_name FROM " . NV_PREFIXLANG . "_" . $module_data . "_menu WHERE menu_id=" . $menu_id;
                    $menu_result = $db->query($menu_sql);
                    if ($menu_result->rowCount()) {
                        $menu_row = $menu_result->fetch();
                        $menu_name = $menu_row['menu_name'];
                    }

                    $item_sql = "INSERT INTO " . NV_PREFIXLANG . "_" . $module_data . "_order_items (
                                order_id, menu_id, menu_name, quantity, price, total, note
                            ) VALUES (
                                " . $order_id . ", " . $menu_id . ", :menu_name, " . $quantity . ",
                                " . $price . ", " . $item_total . ", :note
                            )";

                    $item_stmt = $db->prepare($item_sql);
                    $item_stmt->bindParam(':menu_name', $menu_name, PDO::PARAM_STR);
                    $item_stmt->bindParam(':note', $item_note, PDO::PARAM_STR);
                    $item_stmt->execute();
                }
            }

            $db->query('COMMIT');

            nv_insert_logs(NV_LANG_DATA, $module_name, $order_id > 0 ? 'Edit order' : 'Add order', $order_code, $admin_info['userid']);
            nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=main');
        } catch (PDOException $e) {
            $db->query('ROLLBACK');
            $error[] = $lang_module['error_save'];
        }
    }
} else {
    if (!empty($order)) {
        $order_code = $order['order_code'];
        $staff_id = $order['staff_id'];
        $customer_name = $order['customer_name'];
        $customer_phone = $order['customer_phone'];
        $customer_address = $order['customer_address'];
        $order_date = date('Y-m-d H:i', $order['order_date']);
        $delivery_date = $order['delivery_date'] ? date('Y-m-d H:i', $order['delivery_date']) : '';
        $status = $order['status'];
        $payment_status = $order['payment_status'];
        $payment_method = $order['payment_method'];
        $note = $order['note'];
    } else {
        $order_code = '';
        $staff_id = 0;
        $customer_name = '';
        $customer_phone = '';
        $customer_address = '';
        $order_date = date('Y-m-d H:i');
        $delivery_date = '';
        $status = 0;
        $payment_status = 0;
        $payment_method = 'cash';
        $note = '';
    }
}

// Lấy danh sách nhân viên
$staff_list = nv_get_staff_list();

// Lấy danh sách thực đơn
$menu_list = nv_get_menu_list(1);

// Lấy danh sách trạng thái
$status_list = nv_order_status_list();
$payment_status_list = nv_payment_status_list();
$payment_method_list = nv_payment_method_list();

// Include template
$xtpl = new XTemplate('content.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('GLANG', $lang_global);
$xtpl->assign('MODULE_NAME', $module_name);
$xtpl->assign('OP', $op);
$xtpl->assign('ORDER_ID', $order_id);
$xtpl->assign('ORDER_CODE', $order_code);
$xtpl->assign('CUSTOMER_NAME', $customer_name);
$xtpl->assign('CUSTOMER_PHONE', $customer_phone);
$xtpl->assign('CUSTOMER_ADDRESS', $customer_address);
$xtpl->assign('ORDER_DATE', $order_date);
$xtpl->assign('DELIVERY_DATE', $delivery_date);
$xtpl->assign('NOTE', $note);

// Errors
if (!empty($error)) {
    foreach ($error as $e) {
        $xtpl->assign('ERROR', $e);
        $xtpl->parse('main.error.loop');
    }
    $xtpl->parse('main.error');
}

// Nhân viên
foreach ($staff_list as $staff) {
    $xtpl->assign('STAFF', [
        'userid' => $staff['userid'],
        'full_name' => $staff['full_name'],
        'selected' => $staff['userid'] == $staff_id ? 'selected="selected"' : ''
    ]);
    $xtpl->parse('main.staff');
}

// Trạng thái đơn hàng
foreach ($status_list as $key => $value) {
    $xtpl->assign('STATUS', [
        'key' => $key,
        'value' => $value,
        'selected' => $key == $status ? 'selected="selected"' : ''
    ]);
    $xtpl->parse('main.status');
}

// Trạng thái thanh toán
foreach ($payment_status_list as $key => $value) {
    $xtpl->assign('PAYMENT_STATUS_ITEM', [
        'key' => $key,
        'value' => $value,
        'selected' => $key == $payment_status ? 'selected="selected"' : ''
    ]);
    $xtpl->parse('main.payment_status');
}

// Phương thức thanh toán
foreach ($payment_method_list as $key => $value) {
    $xtpl->assign('PAYMENT_METHOD_ITEM', [
        'key' => $key,
        'value' => $value,
        'selected' => $key == $payment_method ? 'selected="selected"' : ''
    ]);
    $xtpl->parse('main.payment_method');
}

// Danh sách thực đơn cho select
$menu_options = '';
foreach ($menu_list as $menu) {
    $menu_options .= '<option value="' . $menu['menu_id'] . '" data-price="' . $menu['price'] . '">' . $menu['menu_name'] . ' - ' . nv_format_currency($menu['price']) . '</option>';
}
$xtpl->assign('MENU_OPTIONS', $menu_options);

// Chi tiết đơn hàng
if (!empty($order_items)) {
    foreach ($order_items as $item) {
        $xtpl->assign('ITEM', [
            'menu_id' => $item['menu_id'],
            'menu_name' => $item['menu_name'],
            'quantity' => $item['quantity'],
            'price' => number_format($item['price'], 0, ',', '.'),
            'total' => number_format($item['total'], 0, ',', '.'),
            'note' => $item['note']
        ]);
        $xtpl->parse('main.items.loop');
    }
    $xtpl->parse('main.items');
}

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
