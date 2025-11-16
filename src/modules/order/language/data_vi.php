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

/**
 * Note:
 *  - Module var is: $lang, $module_file, $module_data, $module_upload, $module_theme, $module_name
 *  - Accept global var: $db, $db_config, $global_config
 */

// Insert sample menu items
$sth = $db->prepare('INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_menu (menu_name, menu_code, category, description, price, status, weight, admin_id, add_time) VALUES (:menu_name, :menu_code, :category, :description, :price, :status, :weight, :admin_id, :add_time)');

$menu_items = [
    [
        'menu_name' => 'Cơm rang dương châu',
        'menu_code' => 'COM001',
        'category' => 'Cơm',
        'description' => 'Cơm rang với tôm, xúc xích, rau củ',
        'price' => 45000.00,
        'weight' => 1
    ],
    [
        'menu_name' => 'Phở bò',
        'menu_code' => 'PHO001',
        'category' => 'Phở',
        'description' => 'Phở bò Hà Nội truyền thống',
        'price' => 50000.00,
        'weight' => 2
    ],
    [
        'menu_name' => 'Bún chả',
        'menu_code' => 'BUN001',
        'category' => 'Bún',
        'description' => 'Bún chả Hà Nội',
        'price' => 45000.00,
        'weight' => 3
    ],
    [
        'menu_name' => 'Bánh mì thịt',
        'menu_code' => 'BMI001',
        'category' => 'Bánh mì',
        'description' => 'Bánh mì thịt đặc biệt',
        'price' => 25000.00,
        'weight' => 4
    ],
    [
        'menu_name' => 'Cafe sữa đá',
        'menu_code' => 'CAF001',
        'category' => 'Đồ uống',
        'description' => 'Cafe sữa đá truyền thống',
        'price' => 20000.00,
        'weight' => 5
    ],
    [
        'menu_name' => 'Trà đá',
        'menu_code' => 'TRA001',
        'category' => 'Đồ uống',
        'description' => 'Trà đá miễn phí',
        'price' => 0.00,
        'weight' => 6
    ]
];

$admin_id = 1; // Default admin user
$add_time = NV_CURRENTTIME;
$status = 1;

foreach ($menu_items as $item) {
    $sth->bindParam(':menu_name', $item['menu_name'], PDO::PARAM_STR);
    $sth->bindParam(':menu_code', $item['menu_code'], PDO::PARAM_STR);
    $sth->bindParam(':category', $item['category'], PDO::PARAM_STR);
    $sth->bindParam(':description', $item['description'], PDO::PARAM_STR);
    $sth->bindParam(':price', $item['price'], PDO::PARAM_STR);
    $sth->bindValue(':status', $status, PDO::PARAM_INT);
    $sth->bindParam(':weight', $item['weight'], PDO::PARAM_INT);
    $sth->bindValue(':admin_id', $admin_id, PDO::PARAM_INT);
    $sth->bindValue(':add_time', $add_time, PDO::PARAM_INT);
    $sth->execute();
}

// Insert default config values
$sth = $db->prepare('INSERT INTO ' . NV_PREFIXLANG . '_' . $module_data . '_config (config_name, config_value) VALUES (:config_name, :config_value)');

$configs = [
    [
        'config_name' => 'order_prefix',
        'config_value' => 'ORD'
    ],
    [
        'config_name' => 'auto_order_code',
        'config_value' => '1'
    ],
    [
        'config_name' => 'default_payment_method',
        'config_value' => 'cash'
    ],
    [
        'config_name' => 'work_shifts',
        'config_value' => 'Sáng,Chiều,Tối'
    ]
];

foreach ($configs as $config) {
    $sth->bindParam(':config_name', $config['config_name'], PDO::PARAM_STR);
    $sth->bindParam(':config_value', $config['config_value'], PDO::PARAM_STR);
    $sth->execute();
}
