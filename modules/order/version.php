<?php

/**
 * NukeViet Content Management System
 * @version 5.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2025 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

if (!defined('NV_ADMIN') or !defined('NV_MAINFILE')) {
    exit('Stop!!!');
}

$module_version = [
    'name' => 'Order',
    'modfuncs' => 'main',
    'change_alias' => '',
    'submenu' => '',
    'is_sysmod' => 0,
    'virtual' => 0,
    'version' => '5.0.00',
    'date' => 'Saturday, November 16, 2025',
    'author' => 'VINADES.,JSC <contact@vinades.vn>',
    'note' => 'Module quản lý đơn hàng',
    'uploads_dir' => [
        $module_upload
    ]
];
