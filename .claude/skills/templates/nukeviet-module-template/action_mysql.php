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
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_items";
$sql_drop_module[] = "DROP TABLE IF EXISTS " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_categories";

$sql_create_module = $sql_drop_module;

// Categories table
$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_categories (
    cat_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
    parent_id mediumint(8) unsigned NOT NULL DEFAULT 0,
    title varchar(255) NOT NULL DEFAULT '',
    alias varchar(255) NOT NULL DEFAULT '',
    description text,
    image varchar(255) NOT NULL DEFAULT '',
    weight smallint(5) unsigned NOT NULL DEFAULT 0,
    status tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '0:Inactive,1:Active',
    add_time int(11) unsigned NOT NULL DEFAULT 0,
    update_time int(11) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (cat_id),
    UNIQUE KEY alias (alias),
    KEY parent_id (parent_id),
    KEY status (status),
    KEY weight (weight)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// Items table
$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_items (
    item_id int(11) unsigned NOT NULL AUTO_INCREMENT,
    cat_id mediumint(8) unsigned NOT NULL DEFAULT 0,
    title varchar(255) NOT NULL DEFAULT '',
    alias varchar(255) NOT NULL DEFAULT '',
    description text,
    content text,
    image varchar(255) NOT NULL DEFAULT '',
    status tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '0:Draft,1:Published,2:Pending',
    weight smallint(5) unsigned NOT NULL DEFAULT 0,
    hits mediumint(8) unsigned NOT NULL DEFAULT 0,
    admin_id mediumint(8) unsigned NOT NULL DEFAULT 0,
    add_time int(11) unsigned NOT NULL DEFAULT 0,
    update_time int(11) unsigned NOT NULL DEFAULT 0,
    publish_time int(11) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (item_id),
    UNIQUE KEY alias (alias),
    KEY cat_id (cat_id),
    KEY status (status),
    KEY weight (weight),
    KEY admin_id (admin_id),
    KEY publish_time (publish_time),
    KEY status_publish (status, publish_time)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";

// Sample data for categories
$sql_create_module[] = "INSERT INTO " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_categories
    (title, alias, status, weight, add_time, update_time) VALUES
    ('Uncategorized', 'uncategorized', 1, 1, " . NV_CURRENTTIME . ", " . NV_CURRENTTIME . ")";
