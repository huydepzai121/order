<?php

/**
 * NukeViet Content Management System
 * @version 5.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2025 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

if (!defined('NV_IS_FILE_MODULES')) {
    exit('Stop!!!');
}

// Bảng danh mục thực đơn
$sql_create_module[] = 'CREATE TABLE IF NOT EXISTS ' . $db_config['prefix'] . '_' . $lang . '_' . $module_data . '_menu (
    id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
    parentid mediumint(8) unsigned NOT NULL DEFAULT 0,
    title varchar(250) NOT NULL,
    alias varchar(250) NOT NULL,
    description text,
    price decimal(15,2) NOT NULL DEFAULT 0,
    image varchar(255) NOT NULL DEFAULT "",
    weight smallint(4) unsigned NOT NULL DEFAULT 0,
    status tinyint(1) unsigned NOT NULL DEFAULT 1,
    addtime int(11) unsigned NOT NULL DEFAULT 0,
    updatetime int(11) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    UNIQUE KEY alias (alias),
    KEY parentid (parentid),
    KEY status (status)
) ENGINE=InnoDB';

// Bảng đơn hàng
$sql_create_module[] = 'CREATE TABLE IF NOT EXISTS ' . $db_config['prefix'] . '_' . $lang . '_' . $module_data . '_orders (
    id int(11) unsigned NOT NULL AUTO_INCREMENT,
    order_code varchar(50) NOT NULL,
    userid mediumint(8) unsigned NOT NULL DEFAULT 0,
    employee_id mediumint(8) unsigned NOT NULL DEFAULT 0,
    customer_name varchar(100) NOT NULL DEFAULT "",
    customer_phone varchar(20) NOT NULL DEFAULT "",
    customer_address varchar(255) NOT NULL DEFAULT "",
    total_amount decimal(15,2) NOT NULL DEFAULT 0,
    discount_amount decimal(15,2) NOT NULL DEFAULT 0,
    final_amount decimal(15,2) NOT NULL DEFAULT 0,
    payment_method varchar(50) NOT NULL DEFAULT "",
    payment_status tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT "0=Chưa thanh toán, 1=Đã thanh toán, 2=Thanh toán một phần",
    order_status tinyint(1) unsigned NOT NULL DEFAULT 0 COMMENT "0=Mới, 1=Đang xử lý, 2=Hoàn thành, 3=Đã hủy",
    note text,
    order_date int(11) unsigned NOT NULL DEFAULT 0,
    complete_date int(11) unsigned NOT NULL DEFAULT 0,
    addtime int(11) unsigned NOT NULL DEFAULT 0,
    updatetime int(11) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    UNIQUE KEY order_code (order_code),
    KEY userid (userid),
    KEY employee_id (employee_id),
    KEY order_status (order_status),
    KEY order_date (order_date)
) ENGINE=InnoDB';

// Bảng chi tiết đơn hàng
$sql_create_module[] = 'CREATE TABLE IF NOT EXISTS ' . $db_config['prefix'] . '_' . $lang . '_' . $module_data . '_order_details (
    id int(11) unsigned NOT NULL AUTO_INCREMENT,
    order_id int(11) unsigned NOT NULL,
    menu_id mediumint(8) unsigned NOT NULL,
    menu_title varchar(250) NOT NULL,
    quantity smallint(4) unsigned NOT NULL DEFAULT 1,
    price decimal(15,2) NOT NULL DEFAULT 0,
    total decimal(15,2) NOT NULL DEFAULT 0,
    note varchar(255) NOT NULL DEFAULT "",
    PRIMARY KEY (id),
    KEY order_id (order_id),
    KEY menu_id (menu_id)
) ENGINE=InnoDB';

// Bảng công nhân viên (lưu lại lịch sử công việc của nhân viên)
$sql_create_module[] = 'CREATE TABLE IF NOT EXISTS ' . $db_config['prefix'] . '_' . $lang . '_' . $module_data . '_employee_work (
    id int(11) unsigned NOT NULL AUTO_INCREMENT,
    employee_id mediumint(8) unsigned NOT NULL,
    order_id int(11) unsigned NOT NULL,
    work_date int(11) unsigned NOT NULL,
    work_hours decimal(5,2) NOT NULL DEFAULT 0 COMMENT "Số giờ làm việc",
    commission decimal(15,2) NOT NULL DEFAULT 0 COMMENT "Hoa hồng",
    note varchar(255) NOT NULL DEFAULT "",
    addtime int(11) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (id),
    KEY employee_id (employee_id),
    KEY order_id (order_id),
    KEY work_date (work_date)
) ENGINE=InnoDB';

// Bảng cấu hình module
$sql_create_module[] = 'CREATE TABLE IF NOT EXISTS ' . $db_config['prefix'] . '_' . $lang . '_' . $module_data . '_config (
    config_name varchar(50) NOT NULL,
    config_value text,
    PRIMARY KEY (config_name)
) ENGINE=InnoDB';

// Dữ liệu mẫu cấu hình
$sql_create_module[] = "INSERT INTO " . $db_config['prefix'] . '_' . $lang . '_' . $module_data . "_config (config_name, config_value) VALUES
('commission_rate', '10'),
('currency_symbol', 'đ'),
('tax_rate', '10'),
('allow_discount', '1')";
