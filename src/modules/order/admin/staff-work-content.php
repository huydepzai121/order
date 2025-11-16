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

$page_title = $nv_Lang->getModule('staff_work_add');
$work_id = $nv_Request->get_int('work_id', 'get', 0);

// Lấy thông tin công việc nếu đang sửa
$work = [];
if ($work_id > 0) {
    $sql = "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_staff_work WHERE work_id=:work_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':work_id', $work_id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount()) {
        $work = $stmt->fetch();
        $page_title = $nv_Lang->getModule('staff_work_edit');
    } else {
        nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=staff-work');
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

    $staff_id = $nv_Request->get_int('staff_id', 'post', 0);
    $work_date = $nv_Request->get_title('work_date', 'post', '');
    $shift = $nv_Request->get_title('shift', 'post', '');
    $start_time = $nv_Request->get_title('start_time', 'post', '');
    $end_time = $nv_Request->get_title('end_time', 'post', '');
    $note = $nv_Request->get_textarea('note', '', 'post');

    // Validate
    if ($staff_id == 0) {
        $error[] = $nv_Lang->getModule('staff') . ': ' . $nv_Lang->getModule('error_required');
    }
    if (empty($work_date)) {
        $error[] = $nv_Lang->getModule('work_date') . ': ' . $nv_Lang->getModule('error_required');
    }
    if (empty($shift)) {
        $error[] = $nv_Lang->getModule('shift') . ': ' . $nv_Lang->getModule('error_required');
    }

    // Tính số giờ làm việc
    $work_hours = 0;
    if (!empty($start_time) && !empty($end_time)) {
        $start = strtotime($start_time);
        $end = strtotime($end_time);
        $work_hours = ($end - $start) / 3600;
    }

    // Convert date to timestamp
    $work_date_timestamp = strtotime($work_date);

    // Thống kê đơn hàng trong ngày của nhân viên
    $order_count = 0;
    $total_revenue = 0;
    $stats_sql = "SELECT COUNT(*) as order_count, SUM(total_amount) as total_revenue
                  FROM " . NV_PREFIXLANG . "_" . $module_data . "_orders
                  WHERE staff_id=" . $staff_id . "
                  AND order_date >= " . strtotime(date('Y-m-d 00:00:00', $work_date_timestamp)) . "
                  AND order_date <= " . strtotime(date('Y-m-d 23:59:59', $work_date_timestamp));
    $stats_result = $db->query($stats_sql);
    if ($stats_result->rowCount()) {
        $stats = $stats_result->fetch();
        $order_count = $stats['order_count'];
        $total_revenue = $stats['total_revenue'] ? $stats['total_revenue'] : 0;
    }

    if (empty($error)) {
        try {
            if ($work_id > 0) {
                // Cập nhật công việc
                $sql = "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_staff_work SET
                        staff_id=:staff_id,
                        work_date=:work_date,
                        shift=:shift,
                        start_time=:start_time,
                        end_time=:end_time,
                        work_hours=:work_hours,
                        order_count=:order_count,
                        total_revenue=:total_revenue,
                        note=:note,
                        update_time=:update_time
                        WHERE work_id=" . $work_id;
            } else {
                // Thêm mới công việc
                $sql = "INSERT INTO " . NV_PREFIXLANG . "_" . $module_data . "_staff_work (
                        staff_id, work_date, shift, start_time, end_time, work_hours,
                        order_count, total_revenue, note, admin_id, add_time, update_time
                    ) VALUES (
                        :staff_id, :work_date, :shift, :start_time, :end_time, :work_hours,
                        :order_count, :total_revenue, :note, :admin_id, :add_time, :update_time
                    )";
            }

            $stmt = $db->prepare($sql);
            $stmt->bindParam(':staff_id', $staff_id, PDO::PARAM_INT);
            $stmt->bindParam(':work_date', $work_date_timestamp, PDO::PARAM_INT);
            $stmt->bindParam(':shift', $shift, PDO::PARAM_STR);
            $stmt->bindParam(':start_time', $start_time, PDO::PARAM_STR);
            $stmt->bindParam(':end_time', $end_time, PDO::PARAM_STR);
            $stmt->bindParam(':work_hours', $work_hours, PDO::PARAM_STR);
            $stmt->bindParam(':order_count', $order_count, PDO::PARAM_INT);
            $stmt->bindParam(':total_revenue', $total_revenue, PDO::PARAM_STR);
            $stmt->bindParam(':note', $note, PDO::PARAM_STR);
            $stmt->bindValue(':update_time', NV_CURRENTTIME, PDO::PARAM_INT);

            if ($work_id == 0) {
                $stmt->bindValue(':admin_id', $admin_info['userid'], PDO::PARAM_INT);
                $stmt->bindValue(':add_time', NV_CURRENTTIME, PDO::PARAM_INT);
            }

            $stmt->execute();

            nv_insert_logs(NV_LANG_DATA, $module_name, $work_id > 0 ? 'Edit staff work' : 'Add staff work', '', $admin_info['userid']);
            nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=staff-work');
        } catch (PDOException $e) {
            $error[] = $nv_Lang->getModule('error_save');
        }
    }
} else {
    if (!empty($work)) {
        $staff_id = $work['staff_id'];
        $work_date = date('Y-m-d', $work['work_date']);
        $shift = $work['shift'];
        $start_time = $work['start_time'];
        $end_time = $work['end_time'];
        $note = $work['note'];
    } else {
        $staff_id = 0;
        $work_date = date('Y-m-d');
        $shift = '';
        $start_time = '';
        $end_time = '';
        $note = '';
    }
}

// Lấy danh sách nhân viên
$staff_list = nv_get_staff_list();

// Lấy danh sách ca làm việc
$shifts = nv_get_work_shifts();

// Prepare staff list data
$staff_list_data = [];
foreach ($staff_list as $staff) {
    $staff_list_data[] = [
        'userid' => $staff['userid'],
        'full_name' => $staff['full_name'],
        'selected' => $staff['userid'] == $staff_id
    ];
}

// Prepare shifts data
$shifts_data = [];
foreach ($shifts as $s) {
    $shifts_data[] = [
        'value' => $s,
        'selected' => $s == $shift
    ];
}

// Initialize Smarty template
$tpl = new \NukeViet\Template\NVSmarty();
$tpl->setTemplateDir(get_module_tpl_dir('staff_work_content.tpl'));
$tpl->assign('LANG', $nv_Lang);
$tpl->assign('MODULE_NAME', $module_name);
$tpl->assign('OP', $op);
$tpl->assign('WORK_ID', $work_id);
$tpl->assign('WORK_DATE', $work_date);
$tpl->assign('START_TIME', $start_time);
$tpl->assign('END_TIME', $end_time);
$tpl->assign('NOTE', $note);
$tpl->assign('ERROR', $error);
$tpl->assign('STAFF_LIST', $staff_list_data);
$tpl->assign('SHIFTS', $shifts_data);
$tpl->assign('NV_CHECK', md5($client_info['session_id'] . $global_config['sitekey']));

$contents = $tpl->fetch('staff_work_content.tpl');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
