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

$page_title = $nv_Lang->getModule('order_add');
$order_id = $nv_Request->get_int('order_id', 'get', 0);

// Lấy thông tin đơn hàng nếu đang sửa
$order = [];
$order_items = [];
if ($order_id > 0) {
    $sql = "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_orders WHERE order_id=:order_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
    $stmt->execute();
    if ($stmt->rowCount()) {
        $order = $stmt->fetch();
        $page_title = $nv_Lang->getModule('edit') . ': ' . $order['order_code'];

        // Lấy chi tiết đơn hàng
        $sql = "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_order_items WHERE order_id=:order_id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
        $stmt->execute();
        while ($row = $stmt->fetch()) {
            $order_items[] = $row;
        }
    } else {
        nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=main');
    }
}

$error = [];

// Xử lý POST
if ($nv_Request->isset_request('submit', 'post')) {
    // Verify CSRF token
    $checkss = $nv_Request->get_title('checkss', 'post', '');
    if ($checkss != md5($client_info['session_id'] . $global_config['sitekey'])) {
        $error[] = $nv_Lang->getModule('error_security');
    }

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
        $error[] = $nv_Lang->getModule('customer_name') . ': ' . $nv_Lang->getModule('error_required');
    }
    if (empty($customer_phone)) {
        $error[] = $nv_Lang->getModule('customer_phone') . ': ' . $nv_Lang->getModule('error_required');
    }
    if (empty($order_date)) {
        $error[] = $nv_Lang->getModule('order_date') . ': ' . $nv_Lang->getModule('error_required');
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
            $del_sql = "DELETE FROM " . NV_PREFIXLANG . "_" . $module_data . "_order_items WHERE order_id=:order_id";
            $del_stmt = $db->prepare($del_sql);
            $del_stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
            $del_stmt->execute();

            // Thêm chi tiết đơn hàng mới
            foreach ($items_menu_id as $key => $menu_id) {
                if (!empty($menu_id) && !empty($items_quantity[$key]) && !empty($items_price[$key])) {
                    $quantity = intval($items_quantity[$key]);
                    $price = floatval(str_replace(',', '', $items_price[$key]));
                    $item_total = $quantity * $price;
                    $item_note = isset($items_note[$key]) ? $items_note[$key] : '';

                    // Lấy tên món
                    $menu_name = '';
                    $menu_sql = "SELECT menu_name FROM " . NV_PREFIXLANG . "_" . $module_data . "_menu WHERE menu_id=:menu_id";
                    $menu_stmt = $db->prepare($menu_sql);
                    $menu_stmt->bindParam(':menu_id', $menu_id, PDO::PARAM_INT);
                    $menu_stmt->execute();
                    if ($menu_stmt->rowCount()) {
                        $menu_row = $menu_stmt->fetch();
                        $menu_name = $menu_row['menu_name'];
                    }

                    $item_sql = "INSERT INTO " . NV_PREFIXLANG . "_" . $module_data . "_order_items (
                                order_id, menu_id, menu_name, quantity, price, total, note
                            ) VALUES (
                                :order_id, :menu_id, :menu_name, :quantity, :price, :item_total, :note
                            )";

                    $item_stmt = $db->prepare($item_sql);
                    $item_stmt->bindParam(':order_id', $order_id, PDO::PARAM_INT);
                    $item_stmt->bindParam(':menu_id', $menu_id, PDO::PARAM_INT);
                    $item_stmt->bindParam(':menu_name', $menu_name, PDO::PARAM_STR);
                    $item_stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
                    $item_stmt->bindParam(':price', $price, PDO::PARAM_STR);
                    $item_stmt->bindParam(':item_total', $item_total, PDO::PARAM_STR);
                    $item_stmt->bindParam(':note', $item_note, PDO::PARAM_STR);
                    $item_stmt->execute();
                }
            }

            $db->query('COMMIT');

            nv_insert_logs(NV_LANG_DATA, $module_name, $order_id > 0 ? 'Edit order' : 'Add order', $order_code, $admin_info['userid']);
            nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=main');
        } catch (PDOException $e) {
            $db->query('ROLLBACK');
            nv_insert_logs(NV_LANG_DATA, $module_name, 'ERROR', $e->getMessage(), $admin_info['userid']);
            $error[] = $nv_Lang->getModule('error_save');
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

// Prepare data for Smarty template
$staff_list_data = [];
foreach ($staff_list as $staff) {
    $staff_list_data[] = [
        'userid' => $staff['userid'],
        'full_name' => $staff['full_name'],
        'selected' => $staff['userid'] == $staff_id
    ];
}

$status_list_data = [];
foreach ($status_list as $key => $value) {
    $status_list_data[] = [
        'key' => $key,
        'value' => $value,
        'selected' => $key == $status
    ];
}

$payment_status_list_data = [];
foreach ($payment_status_list as $key => $value) {
    $payment_status_list_data[] = [
        'key' => $key,
        'value' => $value,
        'selected' => $key == $payment_status
    ];
}

$payment_method_list_data = [];
foreach ($payment_method_list as $key => $value) {
    $payment_method_list_data[] = [
        'key' => $key,
        'value' => $value,
        'selected' => $key == $payment_method
    ];
}

// Danh sách thực đơn cho select
$menu_options = '';
foreach ($menu_list as $menu) {
    $menu_options .= '<option value="' . $menu['menu_id'] . '" data-price="' . $menu['price'] . '">' . $menu['menu_name'] . ' - ' . nv_format_currency($menu['price']) . '</option>';
}

// Chi tiết đơn hàng
$order_items_data = [];
if (!empty($order_items)) {
    foreach ($order_items as $item) {
        $order_items_data[] = [
            'menu_id' => $item['menu_id'],
            'menu_name' => $item['menu_name'],
            'quantity' => $item['quantity'],
            'price' => number_format($item['price'], 0, ',', '.'),
            'total' => number_format($item['total'], 0, ',', '.'),
            'note' => $item['note']
        ];
    }
}

// Initialize Smarty template
$tpl = new \NukeViet\Template\NVSmarty();
$tpl->setTemplateDir(get_module_tpl_dir('content.tpl'));
$tpl->assign('LANG', $nv_Lang);
$tpl->assign('MODULE_NAME', $module_name);
$tpl->assign('OP', $op);
$tpl->assign('ORDER_ID', $order_id);
$tpl->assign('ORDER_CODE', $order_code);
$tpl->assign('CUSTOMER_NAME', $customer_name);
$tpl->assign('CUSTOMER_PHONE', $customer_phone);
$tpl->assign('CUSTOMER_ADDRESS', $customer_address);
$tpl->assign('ORDER_DATE', $order_date);
$tpl->assign('DELIVERY_DATE', $delivery_date);
$tpl->assign('NOTE', $note);
$tpl->assign('ERROR', $error);
$tpl->assign('STAFF_LIST', $staff_list_data);
$tpl->assign('STATUS_LIST', $status_list_data);
$tpl->assign('PAYMENT_STATUS_LIST', $payment_status_list_data);
$tpl->assign('PAYMENT_METHOD_LIST', $payment_method_list_data);
$tpl->assign('MENU_OPTIONS', $menu_options);
$tpl->assign('ORDER_ITEMS', $order_items_data);
$tpl->assign('NV_CHECK', md5($client_info['session_id'] . $global_config['sitekey']));

$contents = $tpl->fetch('content.tpl');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
