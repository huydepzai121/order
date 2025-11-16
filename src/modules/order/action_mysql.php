<?php

/**
 * NukeViet Content Management System
 * @version 5.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2025 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

if (!defined('NV_IS_FILE_MODULES')) {
    exit('Stop!!!');
}

$sql_drop_module = [];
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_orders";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_order_items";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_menu";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_staff_work";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_config";

$sql_create_module = $sql_drop_module;

// Bảng đơn hàng chính
$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_orders (
    order_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
    order_code varchar(50) NOT NULL DEFAULT '',
    staff_id mediumint(8) unsigned NOT NULL DEFAULT 0,
    customer_name varchar(255) NOT NULL DEFAULT '',
    customer_phone varchar(20) NOT NULL DEFAULT '',
    customer_address varchar(500) NOT NULL DEFAULT '',
    order_date int(11) unsigned NOT NULL DEFAULT 0,
    delivery_date int(11) unsigned NOT NULL DEFAULT 0,
    total_amount decimal(15,2) NOT NULL DEFAULT 0.00,
    status tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '0:Mới,1:Đang xử lý,2:Hoàn thành,3:Hủy',
    payment_status tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT '0:Chưa thanh toán,1:Đã thanh toán',
    payment_method varchar(50) NOT NULL DEFAULT '',
    note text,
    admin_id mediumint(8) unsigned NOT NULL DEFAULT 0,
    add_time int(11) unsigned NOT NULL DEFAULT 0,
    update_time int(11) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (order_id),
    UNIQUE KEY order_code (order_code),
    KEY staff_id (staff_id),
    KEY order_date (order_date),
    KEY status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// Bảng chi tiết đơn hàng
$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_order_items (
    item_id int(11) unsigned NOT NULL AUTO_INCREMENT,
    order_id mediumint(8) unsigned NOT NULL DEFAULT 0,
    menu_id mediumint(8) unsigned NOT NULL DEFAULT 0,
    menu_name varchar(255) NOT NULL DEFAULT '',
    quantity smallint(5) unsigned NOT NULL DEFAULT 1,
    price decimal(15,2) NOT NULL DEFAULT 0.00,
    total decimal(15,2) NOT NULL DEFAULT 0.00,
    note text,
    PRIMARY KEY (item_id),
    KEY order_id (order_id),
    KEY menu_id (menu_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// Bảng thực đơn
$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_menu (
    menu_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
    menu_name varchar(255) NOT NULL DEFAULT '',
    menu_code varchar(50) NOT NULL DEFAULT '',
    category varchar(100) NOT NULL DEFAULT '',
    description text,
    price decimal(15,2) NOT NULL DEFAULT 0.00,
    image varchar(255) NOT NULL DEFAULT '',
    status tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '0:Ẩn,1:Hiện',
    weight smallint(5) unsigned NOT NULL DEFAULT 0,
    admin_id mediumint(8) unsigned NOT NULL DEFAULT 0,
    add_time int(11) unsigned NOT NULL DEFAULT 0,
    update_time int(11) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (menu_id),
    UNIQUE KEY menu_code (menu_code),
    KEY status (status),
    KEY weight (weight)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// Bảng công nhân viên (ghi nhận giờ làm việc, ca làm)
$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_staff_work (
    work_id int(11) unsigned NOT NULL AUTO_INCREMENT,
    staff_id mediumint(8) unsigned NOT NULL DEFAULT 0,
    work_date int(11) unsigned NOT NULL DEFAULT 0,
    shift varchar(50) NOT NULL DEFAULT '' COMMENT 'Ca làm: Sáng, Chiều, Tối',
    start_time varchar(10) NOT NULL DEFAULT '',
    end_time varchar(10) NOT NULL DEFAULT '',
    work_hours decimal(5,2) NOT NULL DEFAULT 0.00,
    order_count smallint(5) unsigned NOT NULL DEFAULT 0,
    total_revenue decimal(15,2) NOT NULL DEFAULT 0.00,
    note text,
    admin_id mediumint(8) unsigned NOT NULL DEFAULT 0,
    add_time int(11) unsigned NOT NULL DEFAULT 0,
    update_time int(11) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (work_id),
    KEY staff_id (staff_id),
    KEY work_date (work_date)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// Bảng cấu hình module
$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_config (
    config_name varchar(50) NOT NULL,
    config_value text,
    PRIMARY KEY (config_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// Dữ liệu mẫu cho cấu hình
$sql_create_module[] = "INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_config (config_name, config_value) VALUES
('order_prefix', 'ORD'),
('auto_order_code', '1'),
('default_payment_method', 'cash'),
('work_shifts', 'Sáng,Chiều,Tối')";

// Dữ liệu mẫu cho thực đơn
$sql_create_module[] = "INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_menu (menu_name, menu_code, category, description, price, status, weight, admin_id, add_time) VALUES
('Cơm rang dương châu', 'COM001', 'Cơm', 'Cơm rang với tôm, xúc xích, rau củ', 45000.00, 1, 1, 1, " . NV_CURRENTTIME . "),
('Phở bò', 'PHO001', 'Phở', 'Phở bò Hà Nội truyền thống', 50000.00, 1, 2, 1, " . NV_CURRENTTIME . "),
('Bún chả', 'BUN001', 'Bún', 'Bún chả Hà Nội', 45000.00, 1, 3, 1, " . NV_CURRENTTIME . "),
('Bánh mì thịt', 'BMI001', 'Bánh mì', 'Bánh mì thịt đặc biệt', 25000.00, 1, 4, 1, " . NV_CURRENTTIME . "),
('Cafe sữa đá', 'CAF001', 'Đồ uống', 'Cafe sữa đá truyền thống', 20000.00, 1, 5, 1, " . NV_CURRENTTIME . ")";
