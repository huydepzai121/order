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

$page_title = $nv_Lang->getModule('menu_add');
$menu_id = $nv_Request->get_int('menu_id', 'get', 0);

// Lấy thông tin món ăn nếu đang sửa
$menu = [];
if ($menu_id > 0) {
    $sql = "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_menu WHERE menu_id=:menu_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':menu_id', $menu_id, PDO::PARAM_INT);
    $stmt->execute();
    if ($stmt->rowCount()) {
        $menu = $stmt->fetch();
        $page_title = $nv_Lang->getModule('edit') . ': ' . $menu['menu_name'];
    } else {
        nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=menu');
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

    $menu_name = $nv_Request->get_title('menu_name', 'post', '');
    $menu_code = $nv_Request->get_title('menu_code', 'post', '');
    $category = $nv_Request->get_title('category', 'post', '');
    $description = $nv_Request->get_textarea('description', '', 'post');
    $price = $nv_Request->get_title('price', 'post', '0');
    $status = $nv_Request->get_int('status', 'post', 1);
    $weight = $nv_Request->get_int('weight', 'post', 0);

    // Validate
    if (empty($menu_name)) {
        $error[] = $nv_Lang->getModule('menu_name') . ': ' . $nv_Lang->getModule('error_required');
    }
    if (empty($menu_code)) {
        $error[] = $nv_Lang->getModule('menu_code') . ': ' . $nv_Lang->getModule('error_required');
    }
    if (empty($category)) {
        $error[] = $nv_Lang->getModule('category') . ': ' . $nv_Lang->getModule('error_required');
    }

    // Kiểm tra trùng mã món
    if (!empty($menu_code)) {
        $check_sql = "SELECT COUNT(*) FROM " . NV_PREFIXLANG . "_" . $module_data . "_menu
                      WHERE menu_code=:menu_code";
        if ($menu_id > 0) {
            $check_sql .= " AND menu_id!=:menu_id";
        }
        $check_stmt = $db->prepare($check_sql);
        $check_stmt->bindParam(':menu_code', $menu_code, PDO::PARAM_STR);
        if ($menu_id > 0) {
            $check_stmt->bindParam(':menu_id', $menu_id, PDO::PARAM_INT);
        }
        $check_stmt->execute();
        if ($check_stmt->fetchColumn()) {
            $error[] = $nv_Lang->getModule('menu_code') . ': Mã món đã tồn tại';
        }
    }

    // Convert price
    $price = floatval(str_replace(',', '', $price));

    if (empty($error)) {
        try {
            if ($menu_id > 0) {
                // Cập nhật món ăn
                $sql = "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_menu SET
                        menu_name=:menu_name,
                        menu_code=:menu_code,
                        category=:category,
                        description=:description,
                        price=:price,
                        status=:status,
                        weight=:weight,
                        update_time=:update_time
                        WHERE menu_id=" . $menu_id;
            } else {
                // Thêm mới món ăn
                $sql = "INSERT INTO " . NV_PREFIXLANG . "_" . $module_data . "_menu (
                        menu_name, menu_code, category, description, price, status, weight,
                        admin_id, add_time, update_time
                    ) VALUES (
                        :menu_name, :menu_code, :category, :description, :price, :status, :weight,
                        :admin_id, :add_time, :update_time
                    )";
            }

            $stmt = $db->prepare($sql);
            $stmt->bindParam(':menu_name', $menu_name, PDO::PARAM_STR);
            $stmt->bindParam(':menu_code', $menu_code, PDO::PARAM_STR);
            $stmt->bindParam(':category', $category, PDO::PARAM_STR);
            $stmt->bindParam(':description', $description, PDO::PARAM_STR);
            $stmt->bindParam(':price', $price, PDO::PARAM_STR);
            $stmt->bindParam(':status', $status, PDO::PARAM_INT);
            $stmt->bindParam(':weight', $weight, PDO::PARAM_INT);
            $stmt->bindValue(':update_time', NV_CURRENTTIME, PDO::PARAM_INT);

            if ($menu_id == 0) {
                $stmt->bindValue(':admin_id', $admin_info['userid'], PDO::PARAM_INT);
                $stmt->bindValue(':add_time', NV_CURRENTTIME, PDO::PARAM_INT);
            }

            $stmt->execute();

            nv_insert_logs(NV_LANG_DATA, $module_name, $menu_id > 0 ? 'Edit menu' : 'Add menu', $menu_name, $admin_info['userid']);
            nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=menu');
        } catch (PDOException $e) {
            $error[] = $nv_Lang->getModule('error_save');
        }
    }
} else {
    if (!empty($menu)) {
        $menu_name = $menu['menu_name'];
        $menu_code = $menu['menu_code'];
        $category = $menu['category'];
        $description = $menu['description'];
        $price = $menu['price'];
        $status = $menu['status'];
        $weight = $menu['weight'];
    } else {
        $menu_name = '';
        $menu_code = '';
        $category = '';
        $description = '';
        $price = 0;
        $status = 1;
        $weight = 0;
    }
}

// Lấy danh sách danh mục
$sql = "SELECT DISTINCT category FROM " . NV_PREFIXLANG . "_" . $module_data . "_menu ORDER BY category ASC";
$result = $db->query($sql);
$categories = [];
while ($row = $result->fetch()) {
    $categories[] = $row['category'];
}

// Prepare status list
$status_list = [
    [
        'key' => 1,
        'value' => $nv_Lang->getModule('active'),
        'selected' => $status == 1
    ],
    [
        'key' => 0,
        'value' => $nv_Lang->getModule('inactive'),
        'selected' => $status == 0
    ]
];

// Initialize Smarty template
$tpl = new \NukeViet\Template\NVSmarty();
$tpl->setTemplateDir(get_module_tpl_dir('menu_content.tpl'));
$tpl->assign('LANG', $nv_Lang);
$tpl->assign('MODULE_NAME', $module_name);
$tpl->assign('OP', $op);
$tpl->assign('MENU_ID', $menu_id);
$tpl->assign('MENU_NAME', $menu_name);
$tpl->assign('MENU_CODE', $menu_code);
$tpl->assign('CATEGORY', $category);
$tpl->assign('DESCRIPTION', $description);
$tpl->assign('PRICE', number_format($price, 0, ',', '.'));
$tpl->assign('STATUS_LIST', $status_list);
$tpl->assign('WEIGHT', $weight);
$tpl->assign('ERROR', $error);
$tpl->assign('CATEGORIES', $categories);
$tpl->assign('NV_CHECK', md5($client_info['session_id'] . $global_config['sitekey']));

$contents = $tpl->fetch('menu_content.tpl');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
