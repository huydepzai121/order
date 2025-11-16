<?php

/**
 * NukeViet Content Management System
 * @version 5.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2025 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

if (!defined('NV_ADMIN') or !defined('NV_MAINFILE')) {
    exit('Stop!!!');
}

$module_version = [
    'name' => 'Example',
    'modfuncs' => 'main',
    'submenu' => 'main',
    'is_sysmod' => 0,
    'virtual' => 1,
    'version' => '5.0.00',
    'date' => 'Monday, November 18, 2025 12:00:00 PM GMT+07:00',
    'author' => 'VINADES.,JSC <contact@vinades.vn>',
    'note' => 'Secure module template with best practices',
    'uploads_dir' => [
        $module_name
    ],
    'files_dir' => []
];
