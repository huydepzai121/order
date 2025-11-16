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

$lang_translator['author'] = 'VINADES.,JSC <contact@vinades.vn>';
$lang_translator['createdate'] = '16/11/2025, 12:00';
$lang_translator['copyright'] = '@Copyright (C) 2009-2025 VINADES.,JSC. All rights reserved';
$lang_translator['info'] = '';
$lang_translator['langtype'] = 'lang_module';

// Menu
$lang_module['order_manage'] = 'Quản lý đơn hàng';
$lang_module['order_add'] = 'Thêm đơn hàng';
$lang_module['menu_manage'] = 'Quản lý thực đơn';
$lang_module['menu_add'] = 'Thêm món ăn';
$lang_module['staff_manage'] = 'Quản lý nhân viên';
$lang_module['staff_work_manage'] = 'Công nhân viên';
$lang_module['report'] = 'Báo cáo';
$lang_module['config'] = 'Cấu hình';

// Order management
$lang_module['order_list'] = 'Danh sách đơn hàng';
$lang_module['order_code'] = 'Mã đơn hàng';
$lang_module['customer_name'] = 'Tên khách hàng';
$lang_module['customer_phone'] = 'Số điện thoại';
$lang_module['customer_address'] = 'Địa chỉ';
$lang_module['order_date'] = 'Ngày đặt';
$lang_module['delivery_date'] = 'Ngày giao';
$lang_module['total_amount'] = 'Tổng tiền';
$lang_module['status'] = 'Trạng thái';
$lang_module['payment_status'] = 'Trạng thái thanh toán';
$lang_module['payment_method'] = 'Phương thức thanh toán';
$lang_module['note'] = 'Ghi chú';
$lang_module['staff'] = 'Nhân viên';
$lang_module['action'] = 'Thao tác';

// Order status
$lang_module['status_new'] = 'Đơn mới';
$lang_module['status_processing'] = 'Đang xử lý';
$lang_module['status_completed'] = 'Hoàn thành';
$lang_module['status_cancelled'] = 'Đã hủy';

// Payment status
$lang_module['payment_unpaid'] = 'Chưa thanh toán';
$lang_module['payment_paid'] = 'Đã thanh toán';

// Payment method
$lang_module['payment_cash'] = 'Tiền mặt';
$lang_module['payment_transfer'] = 'Chuyển khoản';
$lang_module['payment_card'] = 'Thẻ';

// Order details
$lang_module['order_info'] = 'Thông tin đơn hàng';
$lang_module['order_items'] = 'Chi tiết đơn hàng';
$lang_module['menu_item'] = 'Món ăn';
$lang_module['quantity'] = 'Số lượng';
$lang_module['price'] = 'Đơn giá';
$lang_module['total'] = 'Thành tiền';
$lang_module['add_item'] = 'Thêm món';
$lang_module['remove_item'] = 'Xóa';

// Menu management
$lang_module['menu_list'] = 'Danh sách thực đơn';
$lang_module['menu_name'] = 'Tên món';
$lang_module['menu_code'] = 'Mã món';
$lang_module['category'] = 'Danh mục';
$lang_module['description'] = 'Mô tả';
$lang_module['image'] = 'Hình ảnh';
$lang_module['weight'] = 'Thứ tự';
$lang_module['active'] = 'Hiển thị';
$lang_module['inactive'] = 'Ẩn';

// Staff management
$lang_module['staff_list'] = 'Danh sách nhân viên';
$lang_module['staff_name'] = 'Tên nhân viên';
$lang_module['staff_code'] = 'Mã nhân viên';
$lang_module['staff_phone'] = 'Số điện thoại';
$lang_module['staff_email'] = 'Email';
$lang_module['staff_position'] = 'Vị trí';
$lang_module['staff_select'] = 'Chọn nhân viên';

// Staff work
$lang_module['work_date'] = 'Ngày làm việc';
$lang_module['shift'] = 'Ca làm';
$lang_module['start_time'] = 'Giờ vào';
$lang_module['end_time'] = 'Giờ ra';
$lang_module['work_hours'] = 'Số giờ';
$lang_module['order_count'] = 'Số đơn';
$lang_module['total_revenue'] = 'Doanh thu';
$lang_module['shift_morning'] = 'Ca sáng';
$lang_module['shift_afternoon'] = 'Ca chiều';
$lang_module['shift_evening'] = 'Ca tối';

// Report
$lang_module['report_revenue'] = 'Báo cáo doanh thu';
$lang_module['report_order'] = 'Báo cáo đơn hàng';
$lang_module['report_menu'] = 'Báo cáo thực đơn';
$lang_module['report_staff'] = 'Báo cáo nhân viên';
$lang_module['from_date'] = 'Từ ngày';
$lang_module['to_date'] = 'Đến ngày';
$lang_module['view_report'] = 'Xem báo cáo';
$lang_module['export'] = 'Xuất báo cáo';

// Common
$lang_module['add'] = 'Thêm mới';
$lang_module['edit'] = 'Sửa';
$lang_module['delete'] = 'Xóa';
$lang_module['save'] = 'Lưu';
$lang_module['cancel'] = 'Hủy';
$lang_module['back'] = 'Quay lại';
$lang_module['search'] = 'Tìm kiếm';
$lang_module['filter'] = 'Lọc';
$lang_module['all'] = 'Tất cả';
$lang_module['select'] = 'Chọn';
$lang_module['yes'] = 'Có';
$lang_module['no'] = 'Không';

// Messages
$lang_module['error_required'] = 'Vui lòng nhập đầy đủ thông tin bắt buộc';
$lang_module['error_save'] = 'Có lỗi xảy ra khi lưu dữ liệu';
$lang_module['error_delete'] = 'Có lỗi xảy ra khi xóa dữ liệu';
$lang_module['success_save'] = 'Lưu dữ liệu thành công';
$lang_module['success_delete'] = 'Xóa dữ liệu thành công';
$lang_module['confirm_delete'] = 'Bạn có chắc chắn muốn xóa?';
$lang_module['no_data'] = 'Không có dữ liệu';

// Permissions
$lang_module['error_permission'] = 'Bạn không có quyền thực hiện thao tác này';
