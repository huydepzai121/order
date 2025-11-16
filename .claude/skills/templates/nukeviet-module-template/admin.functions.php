<?php

/**
 * NukeViet Content Management System
 * @version 5.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2025 VINADES.,JSC. All rights reserved
 * @license GNU/GPL version 2 or any later version
 * @see https://github.com/nukeviet The NukeViet CMS GitHub project
 */

if (!defined('NV_ADMIN') or !defined('NV_MAINFILE') or !defined('NV_IS_MODADMIN')) {
    exit('Stop!!!');
}

define('NV_IS_FILE_ADMIN', true);

$allow_func = [
    'main',
    'content',
    'del'
];

/**
 * Get list of categories
 *
 * @param int $status -1 for all, 0 for inactive, 1 for active
 * @return array
 */
function nv_get_categories($status = -1)
{
    global $db, $module_data;

    $where = [];
    if ($status >= 0) {
        $where[] = "status=" . intval($status);
    }

    $sql = "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_categories";
    if (!empty($where)) {
        $sql .= " WHERE " . implode(' AND ', $where);
    }
    $sql .= " ORDER BY weight ASC, cat_id ASC";

    $result = $db->query($sql);
    $categories = [];

    while ($row = $result->fetch()) {
        $categories[$row['cat_id']] = $row;
    }

    return $categories;
}

/**
 * Get status list
 *
 * @return array
 */
function nv_get_status_list()
{
    global $lang_module;

    return [
        0 => $lang_module['status_draft'],
        1 => $lang_module['status_published'],
        2 => $lang_module['status_pending']
    ];
}

/**
 * Validate CSRF token
 *
 * @param string $token
 * @return bool
 */
function nv_validate_csrf($token)
{
    global $client_info, $global_config;

    return $token === md5($client_info['session_id'] . $global_config['sitekey']);
}
