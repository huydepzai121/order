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
    $sql = "SELECT s.*, u.username, u.first_name, u.last_name, u.email
            FROM " . NV_PREFIXLANG . "_" . $module_data . "_staff s
            INNER JOIN " . NV_USERS_GLOBALTABLE . " u ON s.userid = u.userid
            WHERE s.id = " . $id;
    $row = $db->query($sql)->fetch();

    if (empty($row)) {
        nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=staff');
    }

    $page_title = $lang_module['staff_edit'];
} else {
    $page_title = $lang_module['staff_add'];
    $row = [
        'id' => 0,
        'userid' => 0,
        'position' => '',
        'department' => '',
        'salary' => 0,
        'commission_rate' => 0,
        'status' => 1,
        'start_date' => NV_CURRENTTIME,
        'end_date' => 0,
        'notes' => ''
    ];
}

// Xử lý POST
if ($nv_Request->isset_request('submit', 'post')) {
    $row['userid'] = $nv_Request->get_int('userid', 'post', 0);
    $row['position'] = $nv_Request->get_title('position', 'post', '');
    $row['department'] = $nv_Request->get_title('department', 'post', '');
    $row['salary'] = $nv_Request->get_float('salary', 'post', 0);
    $row['commission_rate'] = $nv_Request->get_float('commission_rate', 'post', 0);
    $row['status'] = $nv_Request->get_int('status', 'post', 0);
    $row['start_date'] = $nv_Request->get_title('start_date', 'post', '');
    $row['end_date'] = $nv_Request->get_title('end_date', 'post', '');
    $row['notes'] = $nv_Request->get_textarea('notes', 'post', '');

    // Convert date
    if (!empty($row['start_date'])) {
        $row['start_date'] = strtotime($row['start_date']);
    } else {
        $row['start_date'] = NV_CURRENTTIME;
    }

    if (!empty($row['end_date'])) {
        $row['end_date'] = strtotime($row['end_date']);
    } else {
        $row['end_date'] = 0;
    }

    // Validate
    if ($row['userid'] <= 0) {
        $error[] = $lang_module['error_required_fields'];
    }

    if (empty($error)) {
        // Kiểm tra user đã tồn tại chưa
        if ($id == 0) {
            $sql = "SELECT COUNT(*) FROM " . NV_PREFIXLANG . "_" . $module_data . "_staff WHERE userid = " . $row['userid'];
            $count = $db->query($sql)->fetchColumn();

            if ($count > 0) {
                $error[] = $lang_module['error_staff_exists'];
            }
        }
    }

    if (empty($error)) {
        try {
            if ($id > 0) {
                // Update
                $sql = "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_staff SET
                    position = :position,
                    department = :department,
                    salary = :salary,
                    commission_rate = :commission_rate,
                    status = :status,
                    start_date = :start_date,
                    end_date = :end_date,
                    notes = :notes,
                    edit_time = " . NV_CURRENTTIME . "
                    WHERE id = " . $id;

                $sth = $db->prepare($sql);
            } else {
                // Insert
                $sql = "INSERT INTO " . NV_PREFIXLANG . "_" . $module_data . "_staff (
                    userid, position, department, salary, commission_rate, status, start_date, end_date, notes, add_time
                ) VALUES (
                    :userid, :position, :department, :salary, :commission_rate, :status, :start_date, :end_date, :notes, " . NV_CURRENTTIME . "
                )";

                $sth = $db->prepare($sql);
                $sth->bindParam(':userid', $row['userid'], PDO::PARAM_INT);
            }

            $sth->bindParam(':position', $row['position'], PDO::PARAM_STR);
            $sth->bindParam(':department', $row['department'], PDO::PARAM_STR);
            $sth->bindParam(':salary', $row['salary'], PDO::PARAM_STR);
            $sth->bindParam(':commission_rate', $row['commission_rate'], PDO::PARAM_STR);
            $sth->bindParam(':status', $row['status'], PDO::PARAM_INT);
            $sth->bindParam(':start_date', $row['start_date'], PDO::PARAM_INT);
            $sth->bindParam(':end_date', $row['end_date'], PDO::PARAM_INT);
            $sth->bindParam(':notes', $row['notes'], PDO::PARAM_STR);
            $sth->execute();

            nv_insert_logs(NV_LANG_DATA, $module_name, $id > 0 ? 'Edit Staff' : 'Add Staff', 'ID: ' . $id, $admin_info['userid']);
            nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=staff');
        } catch (PDOException $e) {
            $error[] = $e->getMessage();
        }
    }
}

// Lấy danh sách users
$users_list = [];
$sql = "SELECT userid, username, first_name, last_name, email FROM " . NV_USERS_GLOBALTABLE . " WHERE active = 1 ORDER BY last_name ASC, first_name ASC";
$result = $db->query($sql);
while ($user = $result->fetch()) {
    $users_list[$user['userid']] = $user['first_name'] . ' ' . $user['last_name'] . ' (' . $user['username'] . ')';
}

// Format dates for input
if ($id > 0) {
    $row['start_date_format'] = $row['start_date'] ? date('Y-m-d', $row['start_date']) : '';
    $row['end_date_format'] = $row['end_date'] ? date('Y-m-d', $row['end_date']) : '';
} else {
    $row['start_date_format'] = date('Y-m-d', $row['start_date']);
    $row['end_date_format'] = '';
}

$xtpl = new XTemplate('staff-content.tpl', NV_ROOTDIR . '/themes/' . $global_config['module_theme'] . '/modules/' . $module_file);
$xtpl->assign('LANG', $lang_module);
$xtpl->assign('GLANG', $lang_global);
$xtpl->assign('DATA', $row);
$xtpl->assign('URL_BACK', NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=staff');

// Errors
if (!empty($error)) {
    $xtpl->assign('ERROR', implode('<br>', $error));
    $xtpl->parse('main.error');
}

// Users list
if ($id == 0) {
    foreach ($users_list as $key => $value) {
        $xtpl->assign('USER_ID', $key);
        $xtpl->assign('USER_NAME', $value);
        $xtpl->assign('USER_SELECTED', $row['userid'] == $key ? ' selected="selected"' : '');
        $xtpl->parse('main.user_add.user_loop');
    }
    $xtpl->parse('main.user_add');
} else {
    $xtpl->assign('USER_NAME_DISPLAY', $row['first_name'] . ' ' . $row['last_name'] . ' (' . $row['username'] . ')');
    $xtpl->parse('main.user_edit');
}

// Status
$xtpl->assign('STATUS_1_CHECKED', $row['status'] == 1 ? ' checked="checked"' : '');
$xtpl->assign('STATUS_0_CHECKED', $row['status'] == 0 ? ' checked="checked"' : '');

$xtpl->parse('main');
$contents = $xtpl->text('main');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
