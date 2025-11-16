<?php

/**
 * NukeViet Content Management System
 * @version 5.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2025 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

if (!defined('NV_MAINFILE')) {
    exit('Stop!!!');
}

$lang_translator['author'] = 'VINADES.,JSC <contact@vinades.vn>';
$lang_translator['createdate'] = '16/11/2025, 08:00';
$lang_translator['copyright'] = '@Copyright (C) 2025 VINADES.,JSC';
$lang_translator['info'] = '';
$lang_translator['langtype'] = 'lang_module';

// Frontend
nv_Lang::$lang_module['main'] = 'Trang chủ';

// Admin - Menu
nv_Lang::$lang_module['order_list'] = 'Danh sách đơn hàng';
nv_Lang::$lang_module['order_add'] = 'Thêm đơn hàng';
nv_Lang::$lang_module['menu_list'] = 'Quản lý thực đơn';
nv_Lang::$lang_module['employee_work'] = 'Công nhân viên';
nv_Lang::$lang_module['report'] = 'Báo cáo';
nv_Lang::$lang_module['config'] = 'Cấu hình';

// Admin - Order
nv_Lang::$lang_module['order_code'] = 'Mã đơn hàng';
nv_Lang::$lang_module['customer_name'] = 'Tên khách hàng';
nv_Lang::$lang_module['customer_phone'] = 'Số điện thoại';
nv_Lang::$lang_module['customer_address'] = 'Địa chỉ';
nv_Lang::$lang_module['employee'] = 'Nhân viên';
nv_Lang::$lang_module['total_amount'] = 'Tổng tiền';
nv_Lang::$lang_module['discount_amount'] = 'Giảm giá';
nv_Lang::$lang_module['final_amount'] = 'Thành tiền';
nv_Lang::$lang_module['payment_method'] = 'Phương thức thanh toán';
nv_Lang::$lang_module['payment_status'] = 'Trạng thái thanh toán';
nv_Lang::$lang_module['order_status'] = 'Trạng thái đơn hàng';
nv_Lang::$lang_module['order_date'] = 'Ngày đặt';
nv_Lang::$lang_module['complete_date'] = 'Ngày hoàn thành';
nv_Lang::$lang_module['note'] = 'Ghi chú';
nv_Lang::$lang_module['action'] = 'Thao tác';

// Order status
nv_Lang::$lang_module['status_new'] = 'Đơn hàng mới';
nv_Lang::$lang_module['status_processing'] = 'Đang xử lý';
nv_Lang::$lang_module['status_completed'] = 'Hoàn thành';
nv_Lang::$lang_module['status_cancelled'] = 'Đã hủy';

// Payment status
nv_Lang::$lang_module['payment_unpaid'] = 'Chưa thanh toán';
nv_Lang::$lang_module['payment_paid'] = 'Đã thanh toán';
nv_Lang::$lang_module['payment_partial'] = 'Thanh toán một phần';

// Payment methods
nv_Lang::$lang_module['payment_cash'] = 'Tiền mặt';
nv_Lang::$lang_module['payment_bank'] = 'Chuyển khoản';
nv_Lang::$lang_module['payment_card'] = 'Thẻ';
nv_Lang::$lang_module['payment_ewallet'] = 'Ví điện tử';

// Menu
nv_Lang::$lang_module['menu_title'] = 'Tên món';
nv_Lang::$lang_module['menu_alias'] = 'Liên kết tĩnh';
nv_Lang::$lang_module['menu_parent'] = 'Danh mục cha';
nv_Lang::$lang_module['menu_price'] = 'Giá';
nv_Lang::$lang_module['menu_image'] = 'Hình ảnh';
nv_Lang::$lang_module['menu_description'] = 'Mô tả';
nv_Lang::$lang_module['menu_weight'] = 'Thứ tự';
nv_Lang::$lang_module['menu_status'] = 'Trạng thái';
nv_Lang::$lang_module['menu_add'] = 'Thêm món';
nv_Lang::$lang_module['menu_edit'] = 'Sửa món';
nv_Lang::$lang_module['menu_delete'] = 'Xóa món';
nv_Lang::$lang_module['menu_active'] = 'Hoạt động';
nv_Lang::$lang_module['menu_inactive'] = 'Tạm ngưng';

// Order details
nv_Lang::$lang_module['order_details'] = 'Chi tiết đơn hàng';
nv_Lang::$lang_module['menu_item'] = 'Món ăn';
nv_Lang::$lang_module['quantity'] = 'Số lượng';
nv_Lang::$lang_module['price'] = 'Đơn giá';
nv_Lang::$lang_module['total'] = 'Thành tiền';
nv_Lang::$lang_module['add_item'] = 'Thêm món';
nv_Lang::$lang_module['remove_item'] = 'Xóa';

// Employee work
nv_Lang::$lang_module['employee_name'] = 'Tên nhân viên';
nv_Lang::$lang_module['work_date'] = 'Ngày làm việc';
nv_Lang::$lang_module['work_hours'] = 'Số giờ';
nv_Lang::$lang_module['commission'] = 'Hoa hồng';
nv_Lang::$lang_module['total_orders'] = 'Tổng đơn hàng';
nv_Lang::$lang_module['total_commission'] = 'Tổng hoa hồng';

// Report
nv_Lang::$lang_module['report_title'] = 'Báo cáo thống kê';
nv_Lang::$lang_module['report_daily'] = 'Báo cáo theo ngày';
nv_Lang::$lang_module['report_monthly'] = 'Báo cáo theo tháng';
nv_Lang::$lang_module['report_employee'] = 'Báo cáo nhân viên';
nv_Lang::$lang_module['report_menu'] = 'Báo cáo thực đơn';
nv_Lang::$lang_module['from_date'] = 'Từ ngày';
nv_Lang::$lang_module['to_date'] = 'Đến ngày';
nv_Lang::$lang_module['select_employee'] = 'Chọn nhân viên';
nv_Lang::$lang_module['all_employees'] = 'Tất cả nhân viên';
nv_Lang::$lang_module['view_report'] = 'Xem báo cáo';
nv_Lang::$lang_module['total_revenue'] = 'Tổng doanh thu';
nv_Lang::$lang_module['total_discount'] = 'Tổng giảm giá';
nv_Lang::$lang_module['net_revenue'] = 'Doanh thu thuần';

// Config
nv_Lang::$lang_module['commission_rate'] = 'Tỷ lệ hoa hồng (%)';
nv_Lang::$lang_module['currency_symbol'] = 'Ký hiệu tiền tệ';
nv_Lang::$lang_module['tax_rate'] = 'Thuế VAT (%)';
nv_Lang::$lang_module['allow_discount'] = 'Cho phép giảm giá';

// Messages
nv_Lang::$lang_module['save_success'] = 'Lưu thành công!';
nv_Lang::$lang_module['save_error'] = 'Lỗi! Không thể lưu dữ liệu';
nv_Lang::$lang_module['delete_confirm'] = 'Bạn có chắc muốn xóa?';
nv_Lang::$lang_module['delete_success'] = 'Xóa thành công!';
nv_Lang::$lang_module['delete_error'] = 'Lỗi! Không thể xóa';
nv_Lang::$lang_module['error_required'] = 'Vui lòng nhập đầy đủ thông tin bắt buộc';
nv_Lang::$lang_module['error_exists'] = 'Dữ liệu đã tồn tại';
nv_Lang::$lang_module['select'] = 'Chọn';
nv_Lang::$lang_module['search'] = 'Tìm kiếm';
nv_Lang::$lang_module['reset'] = 'Làm lại';
nv_Lang::$lang_module['save'] = 'Lưu';
nv_Lang::$lang_module['cancel'] = 'Hủy';
nv_Lang::$lang_module['edit'] = 'Sửa';
nv_Lang::$lang_module['delete'] = 'Xóa';
nv_Lang::$lang_module['view'] = 'Xem';
nv_Lang::$lang_module['back'] = 'Quay lại';
nv_Lang::$lang_module['yes'] = 'Có';
nv_Lang::$lang_module['no'] = 'Không';
nv_Lang::$lang_module['all'] = 'Tất cả';
nv_Lang::$lang_module['empty'] = 'Không có dữ liệu';
nv_Lang::$lang_module['leave_blank_auto'] = 'Để trống để tự động tạo';
