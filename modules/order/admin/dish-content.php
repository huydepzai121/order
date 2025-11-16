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
    $sql = "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_dishes WHERE id = " . $id;
    $row = $db->query($sql)->fetch();

    if (empty($row)) {
        nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=dishes');
    }

    $page_title = $lang_module['dish_edit'];
} else {
    $page_title = $lang_module['dish_add'];
    $row = [
        'id' => 0,
        'name' => '',
        'alias' => '',
        'description' => '',
        'image' => '',
        'price' => 0,
        'status' => 1,
        'weight' => 0
    ];
}

// Xử lý POST
if ($nv_Request->isset_request('submit', 'post')) {
    $row['name'] = $nv_Request->get_title('name', 'post', '');
    $row['alias'] = $nv_Request->get_title('alias', 'post', '');
    $row['description'] = $nv_Request->get_textarea('description', 'post', '');
    $row['image'] = $nv_Request->get_title('image', 'post', '');
    $row['price'] = $nv_Request->get_float('price', 'post', 0);
    $row['status'] = $nv_Request->get_int('status', 'post', 0);
    $row['weight'] = $nv_Request->get_int('weight', 'post', 0);

    // Auto generate alias
    if (empty($row['alias'])) {
        $row['alias'] = change_alias($row['name']);
    }

    // Validate
    if (empty($row['name'])) {
        $error[] = $lang_module['error_required_fields'];
    }

    if (empty($error)) {
        // Kiểm tra alias trùng
        $sql = "SELECT COUNT(*) FROM " . NV_PREFIXLANG . "_" . $module_data . "_dishes WHERE alias = :alias AND id != " . $id;
        $sth = $db->prepare($sql);
        $sth->bindParam(':alias', $row['alias'], PDO::PARAM_STR);
        $sth->execute();

        if ($sth->fetchColumn()) {
            $row['alias'] = $row['alias'] . '-' . $id;
        }

        try {
            if ($id > 0) {
                // Update
                $sql = "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_dishes SET
                    name = :name,
                    alias = :alias,
                    description = :description,
                    image = :image,
                    price = :price,
                    status = :status,
                    weight = :weight,
                    edit_time = " . NV_CURRENTTIME . "
                    WHERE id = " . $id;

                $sth = $db->prepare($sql);
            } else {
                // Insert
                $sql = "INSERT INTO " . NV_PREFIXLANG . "_" . $module_data . "_dishes (
                    name, alias, description, image, price, status, weight, add_time, admin_id
                ) VALUES (
                    :name, :alias, :description, :image, :price, :status, :weight, " . NV_CURRENTTIME . ", " . $admin_info['admin_id'] . "
                )";

                $sth = $db->prepare($sql);
            }

            $sth->bindParam(':name', $row['name'], PDO::PARAM_STR);
            $sth->bindParam(':alias', $row['alias'], PDO::PARAM_STR);
            $sth->bindParam(':description', $row['description'], PDO::PARAM_STR);
            $sth->bindParam(':image', $row['image'], PDO::PARAM_STR);
            $sth->bindParam(':price', $row['price'], PDO::PARAM_STR);
            $sth->bindParam(':status', $row['status'], PDO::PARAM_INT);
            $sth->bindParam(':weight', $row['weight'], PDO::PARAM_INT);
            $sth->execute();

            nv_insert_logs(NV_LANG_DATA, $module_name, $id > 0 ? 'Edit Dish' : 'Add Dish', $row['name'], $admin_info['userid']);
            nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=dishes');
        } catch (PDOException $e) {
            $error[] = $e->getMessage();
        }
    }
}

$xtpl = new XTemplate('dish-content.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('GLANG', $lang_global);
$xtpl->assign('DATA', $row);
$xtpl->assign('URL_BACK', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=dishes');

// Errors
if (!empty($error)) {
    $xtpl->assign('ERROR', implode('<br>', $error));
    $xtpl->parse('main.error');
}

// Status
$xtpl->assign('STATUS_1_CHECKED', $row['status'] == 1 ? ' checked="checked"' : '');
$xtpl->assign('STATUS_0_CHECKED', $row['status'] == 0 ? ' checked="checked"' : '');

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
