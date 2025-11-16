<?php

/**
 * NukeViet Content Management System
 * @version 5.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2021 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

if (!defined('NV_ADMIN')) {
    die('Stop!!!');
}

$submenu['orders'] = $lang_module['orders'];
$submenu['dishes'] = $lang_module['dishes'];
$submenu['staff'] = $lang_module['staff'];
$submenu['reports'] = $lang_module['reports'];
$submenu['config'] = $lang_module['config'];

$allow_func = ['main', 'orders', 'order-content', 'order-del', 'dishes', 'dish-content', 'dish-del', 'staff', 'staff-content', 'staff-del', 'reports', 'config'];
