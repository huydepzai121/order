<?php

/**
 * NukeViet Content Management System
 * @version 5.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2021 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

if (!defined('NV_IS_FILE_MODULES')) {
    die('Stop!!!');
}

$sql_drop_module = [];
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_orders";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_order_details";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_dishes";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_staff";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_config";

$sql_create_module = $sql_drop_module;

// Bảng quản lý thực đơn (món ăn)
$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_dishes (
    id int(11) unsigned NOT NULL AUTO_INCREMENT,
    name varchar(250) NOT NULL,
    alias varchar(250) NOT NULL,
    description text,
    image varchar(255) DEFAULT '',
    price decimal(15,2) NOT NULL DEFAULT 0.00,
    status tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=active, 0=inactive',
    weight int(11) unsigned NOT NULL DEFAULT 0,
    add_time int(11) unsigned NOT NULL DEFAULT 0,
    edit_time int(11) unsigned NOT NULL DEFAULT 0,
    admin_id mediumint(8) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    UNIQUE KEY alias (alias),
    KEY status (status),
    KEY weight (weight)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// Bảng quản lý nhân viên (liên kết với nv5_users)
$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_staff (
    id int(11) unsigned NOT NULL AUTO_INCREMENT,
    userid mediumint(8) unsigned NOT NULL COMMENT 'Link to nv5_users.userid',
    position varchar(100) DEFAULT '' COMMENT 'Chức vụ',
    department varchar(100) DEFAULT '' COMMENT 'Phòng ban',
    salary decimal(15,2) DEFAULT 0.00 COMMENT 'Lương cơ bản',
    commission_rate decimal(5,2) DEFAULT 0.00 COMMENT 'Tỷ lệ hoa hồng %',
    status tinyint(4) NOT NULL DEFAULT 1 COMMENT '1=active, 0=inactive',
    start_date int(11) unsigned NOT NULL DEFAULT 0,
    end_date int(11) unsigned DEFAULT 0,
    notes text,
    add_time int(11) unsigned NOT NULL DEFAULT 0,
    edit_time int(11) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    UNIQUE KEY userid (userid),
    KEY status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// Bảng quản lý đơn hàng
$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_orders (
    id int(11) unsigned NOT NULL AUTO_INCREMENT,
    order_code varchar(50) NOT NULL COMMENT 'Mã đơn hàng',
    customer_name varchar(250) NOT NULL,
    customer_phone varchar(20) DEFAULT '',
    customer_address text,
    staff_id int(11) unsigned DEFAULT 0 COMMENT 'ID nhân viên phục vụ',
    total_amount decimal(15,2) NOT NULL DEFAULT 0.00,
    discount_amount decimal(15,2) DEFAULT 0.00,
    final_amount decimal(15,2) NOT NULL DEFAULT 0.00,
    payment_method varchar(50) DEFAULT 'cash' COMMENT 'cash, card, transfer',
    payment_status tinyint(4) NOT NULL DEFAULT 0 COMMENT '0=unpaid, 1=paid, 2=partial',
    order_status tinyint(4) NOT NULL DEFAULT 0 COMMENT '0=pending, 1=processing, 2=completed, 3=cancelled',
    notes text,
    order_time int(11) unsigned NOT NULL DEFAULT 0,
    complete_time int(11) unsigned DEFAULT 0,
    admin_id mediumint(8) unsigned NOT NULL DEFAULT 0,
    edit_time int(11) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    UNIQUE KEY order_code (order_code),
    KEY staff_id (staff_id),
    KEY order_status (order_status),
    KEY payment_status (payment_status),
    KEY order_time (order_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// Bảng chi tiết đơn hàng
$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_order_details (
    id int(11) unsigned NOT NULL AUTO_INCREMENT,
    order_id int(11) unsigned NOT NULL,
    dish_id int(11) unsigned NOT NULL,
    dish_name varchar(250) NOT NULL,
    quantity int(11) unsigned NOT NULL DEFAULT 1,
    unit_price decimal(15,2) NOT NULL DEFAULT 0.00,
    total_price decimal(15,2) NOT NULL DEFAULT 0.00,
    notes text,
    PRIMARY KEY (id),
    KEY order_id (order_id),
    KEY dish_id (dish_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// Bảng cấu hình module
$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_config (
    config_name varchar(100) NOT NULL,
    config_value text,
    PRIMARY KEY (config_name)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// Insert dữ liệu cấu hình mặc định
$sql_create_module[] = "INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_config
    (config_name, config_value) VALUES
    ('order_prefix', 'ORD'),
    ('tax_rate', '10'),
    ('currency', 'VND'),
    ('auto_order_code', '1')";

// Insert dữ liệu mẫu cho dishes
$sql_create_module[] = "INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_dishes
    (name, alias, description, price, status, weight, add_time, admin_id) VALUES
    ('Cơm gà', 'com-ga', 'Cơm gà Hải Nam thơm ngon', 45000.00, 1, 1, " . NV_CURRENTTIME . ", 1),
    ('Phở bò', 'pho-bo', 'Phở bò truyền thống Hà Nội', 50000.00, 1, 2, " . NV_CURRENTTIME . ", 1),
    ('Bún chả', 'bun-cha', 'Bún chả Hà Nội đặc sản', 45000.00, 1, 3, " . NV_CURRENTTIME . ", 1),
    ('Bánh mì thịt', 'banh-mi-thit', 'Bánh mì thịt pate đầy đủ', 25000.00, 1, 4, " . NV_CURRENTTIME . ", 1),
    ('Cà phê sữa đá', 'ca-phe-sua-da', 'Cà phê phin truyền thống', 20000.00, 1, 5, " . NV_CURRENTTIME . ", 1)";
