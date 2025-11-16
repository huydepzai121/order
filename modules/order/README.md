# Module Quản Lý Order - NukeViet 5.0

Module quản lý đơn hàng, thực đơn và nhân viên cho hệ thống NukeViet 5.0.

## Tính năng

### 1. Quản lý Đơn hàng
- ✅ Tạo, sửa, xóa đơn hàng
- ✅ Quản lý thông tin khách hàng (tên, SĐT, địa chỉ)
- ✅ Gán nhân viên phục vụ cho từng đơn
- ✅ Quản lý chi tiết món ăn trong đơn (món, số lượng, giá)
- ✅ Tính toán tự động tổng tiền, giảm giá, thành tiền
- ✅ Quản lý trạng thái đơn hàng (Chờ xử lý, Đang xử lý, Hoàn thành, Đã hủy)
- ✅ Quản lý trạng thái thanh toán (Chưa thanh toán, Đã thanh toán, Thanh toán một phần)
- ✅ Hỗ trợ nhiều phương thức thanh toán (Tiền mặt, Thẻ, Chuyển khoản)
- ✅ Tự động tạo mã đơn hàng theo format: ORD + Ngày + STT

### 2. Quản lý Thực đơn (Món ăn)
- ✅ Thêm, sửa, xóa món ăn
- ✅ Quản lý tên món, mô tả, giá, hình ảnh
- ✅ Tạo liên kết tĩnh (alias) tự động
- ✅ Sắp xếp thứ tự hiển thị (weight)
- ✅ Bật/tắt trạng thái món ăn
- ✅ Tìm kiếm và lọc theo trạng thái

### 3. Quản lý Nhân viên
- ✅ Kết nối với bảng người dùng NukeViet (nv5_users)
- ✅ Quản lý thông tin nhân viên: chức vụ, phòng ban, lương
- ✅ Thiết lập tỷ lệ hoa hồng cho nhân viên
- ✅ Theo dõi ngày vào làm, ngày nghỉ việc
- ✅ Tìm kiếm theo tên, phòng ban, trạng thái

### 4. Báo cáo & Thống kê
- ✅ Báo cáo tổng quan: Tổng đơn, Hoàn thành, Doanh thu
- ✅ Báo cáo theo khoảng thời gian tùy chỉnh
- ✅ Top 10 món ăn bán chạy nhất
- ✅ Báo cáo hiệu suất nhân viên (số đơn, doanh thu)
- ✅ Biểu đồ doanh thu theo ngày (Chart.js)
- ✅ Thống kê thanh toán (Đã thu, Chưa thu)

### 5. Cấu hình Module
- ✅ Thiết lập tiền tố mã đơn hàng
- ✅ Cấu hình thuế VAT
- ✅ Đơn vị tiền tệ
- ✅ Bật/tắt tự động tạo mã đơn hàng

## Cấu trúc Database

### Bảng `orders` (Đơn hàng)
```sql
- id: ID đơn hàng
- order_code: Mã đơn hàng (unique)
- customer_name: Tên khách hàng
- customer_phone: SĐT khách hàng
- customer_address: Địa chỉ
- staff_id: ID nhân viên phục vụ
- total_amount: Tổng tiền
- discount_amount: Giảm giá
- final_amount: Thành tiền
- payment_method: Phương thức thanh toán
- payment_status: Trạng thái thanh toán
- order_status: Trạng thái đơn hàng
- order_time: Thời gian đặt
```

### Bảng `order_details` (Chi tiết đơn hàng)
```sql
- id: ID chi tiết
- order_id: ID đơn hàng
- dish_id: ID món ăn
- dish_name: Tên món ăn
- quantity: Số lượng
- unit_price: Đơn giá
- total_price: Thành tiền
```

### Bảng `dishes` (Thực đơn)
```sql
- id: ID món ăn
- name: Tên món
- alias: Liên kết tĩnh
- description: Mô tả
- image: Hình ảnh
- price: Giá
- status: Trạng thái (1=active, 0=inactive)
- weight: Thứ tự sắp xếp
```

### Bảng `staff` (Nhân viên)
```sql
- id: ID nhân viên
- userid: ID user (liên kết nv5_users)
- position: Chức vụ
- department: Phòng ban
- salary: Lương cơ bản
- commission_rate: Tỷ lệ hoa hồng (%)
- status: Trạng thái
- start_date: Ngày vào làm
- end_date: Ngày nghỉ việc
```

### Bảng `config` (Cấu hình)
```sql
- config_name: Tên cấu hình
- config_value: Giá trị
```

## Cài đặt

### 1. Upload Module
Copy thư mục `modules/order` vào thư mục `modules/` của NukeViet.

### 2. Upload Theme Templates
Copy thư mục `themes/admin_default/modules/order` vào `themes/admin_default/modules/`.

### 3. Cài đặt Module
1. Đăng nhập vào trang quản trị NukeViet
2. Vào **Quản lý module** (Extensions > Modules)
3. Tìm module **Order** trong danh sách
4. Click **Cài đặt** (Install)

### 4. Cấu hình Ban đầu
1. Vào **Order > Cấu hình** để thiết lập:
   - Tiền tố mã đơn hàng
   - Thuế VAT
   - Đơn vị tiền tệ

2. Vào **Order > Thực đơn** để thêm món ăn

3. Vào **Order > Nhân viên** để thêm nhân viên từ danh sách users

## Sử dụng

### Tạo đơn hàng mới
1. Vào **Order > Quản lý đơn hàng**
2. Click **Thêm đơn hàng**
3. Điền thông tin khách hàng
4. Chọn nhân viên phục vụ
5. Thêm món ăn và số lượng
6. Nhập giảm giá (nếu có)
7. Chọn phương thức thanh toán và trạng thái
8. Click **Lưu**

### Xem báo cáo
1. Vào **Order > Báo cáo**
2. Chọn khoảng thời gian
3. Click **Xem báo cáo**
4. Xem các thống kê:
   - Tổng quan doanh thu
   - Top món ăn bán chạy
   - Hiệu suất nhân viên
   - Biểu đồ doanh thu

## Công nghệ sử dụng

- **Backend**: PHP 7.4+, MySQL/MariaDB
- **Frontend**: Bootstrap 5, jQuery
- **Charts**: Chart.js 3.9.1
- **Template Engine**: XTemplate (NukeViet)
- **Security**: PDO Prepared Statements, Input Validation

## File Structure

```
modules/order/
├── action_mysql.php          # Database schema
├── admin.functions.php        # Admin helper functions
├── admin.menu.php            # Admin menu definition
├── functions.php             # Frontend functions
├── version.php               # Module metadata
├── admin/                    # Admin controllers
│   ├── main.php             # Dashboard
│   ├── orders.php           # List orders
│   ├── order-content.php    # Add/Edit order
│   ├── order-del.php        # Delete order
│   ├── dishes.php           # List dishes
│   ├── dish-content.php     # Add/Edit dish
│   ├── dish-del.php         # Delete dish
│   ├── staff.php            # List staff
│   ├── staff-content.php    # Add/Edit staff
│   ├── staff-del.php        # Delete staff
│   ├── reports.php          # Reports
│   └── config.php           # Configuration
├── funcs/                    # Frontend functions
│   └── main.php
├── language/                 # Language files
│   └── admin_vi.php         # Vietnamese admin
└── blocks/                   # Blocks (if needed)

themes/admin_default/modules/order/
├── main.tpl                  # Dashboard template
├── orders.tpl               # Orders list template
├── order-content.tpl        # Order form template
├── dishes.tpl               # Dishes list template
├── dish-content.tpl         # Dish form template
├── staff.tpl                # Staff list template
├── staff-content.tpl        # Staff form template
├── reports.tpl              # Reports template
└── config.tpl               # Config template
```

## Tác giả

Module được phát triển cho NukeViet 5.0
- Framework: NukeViet CMS
- Version: 5.0.00
- Date: November 16, 2025

## License

GNU/GPL version 2 or any later version
