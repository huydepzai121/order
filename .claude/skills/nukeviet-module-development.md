# NukeViet Module Development Skill

Expert skill for developing secure, high-quality NukeViet 5.x modules following official standards and best practices.

## When to use this skill

Use this skill when:
- Creating a new NukeViet module from scratch
- Reviewing existing NukeViet module code
- Fixing security vulnerabilities in modules
- Ensuring code quality and standards compliance

## Core Principles

### 1. Security First
- ✅ ALWAYS use prepared statements for database queries
- ✅ ALWAYS sanitize user input
- ✅ ALWAYS escape output to prevent XSS
- ✅ ALWAYS implement CSRF protection
- ✅ ALWAYS validate and sanitize file uploads
- ❌ NEVER concatenate user input into SQL queries
- ❌ NEVER trust user input without validation
- ❌ NEVER output raw data without escaping

### 2. NukeViet Standards
- Follow NukeViet coding conventions
- Use NukeViet built-in security functions
- Leverage NukeViet core libraries
- Follow module structure conventions

### 3. Performance
- Minimize database queries
- Avoid N+1 query problems
- Use appropriate indexes
- Cache when appropriate

## Module File Structure

```
modules/{module_name}/
├── action_mysql.php          # Database schema (REQUIRED)
├── version.php                # Module metadata (REQUIRED)
├── admin.menu.php            # Admin menu structure
├── admin.functions.php       # Admin helper functions
├── functions.php             # Public-facing functions
├── admin/                    # Admin controllers
│   ├── main.php             # List view
│   ├── content.php          # Add/Edit form
│   ├── del.php              # Delete action
│   └── ...
├── language/                 # Language files
│   ├── vi.php               # Language file (Vietnamese) - both admin & frontend
│   └── en.php               # Language file (English) - both admin & frontend
└── themes/                   # Optional: module-specific themes

themes/admin_future/modules/{module_name}/
├── main.tpl                  # List template
├── content.tpl               # Form template
└── ...
```

## Security Checklist

### SQL Injection Prevention

**❌ WRONG - Direct concatenation:**
```php
$sql = "SELECT * FROM table WHERE id=" . $id;
$sql = "SELECT * FROM table WHERE name='" . $name . "'";
```

**✅ CORRECT - Prepared statements:**
```php
// For SELECT/UPDATE/DELETE
$sql = "SELECT * FROM table WHERE id=:id";
$stmt = $db->prepare($sql);
$stmt->bindParam(':id', $id, PDO::PARAM_INT);
$stmt->execute();

// For numeric values (also acceptable)
$sql = "SELECT * FROM table WHERE id=" . intval($id);

// For string values (acceptable for simple cases)
$sql = "SELECT * FROM table WHERE code=" . $db->quote($code);
```

### XSS Prevention

**❌ WRONG - Raw output:**
```php
echo $user_input;
$xtpl->assign('NAME', $user_input);
```

**✅ CORRECT - Escaped output:**
```php
echo nv_htmlspecialchars($user_input);
$xtpl->assign('NAME', nv_htmlspecialchars($user_input));

// For URLs
echo nv_url_rewrite($url);

// For JavaScript
echo json_encode($data, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
```

### CSRF Protection

**✅ ALWAYS add CSRF token to forms:**
```php
// In controller (Smarty)
$tpl->assign('NV_CHECK', md5($client_info['session_id'] . $global_config['sitekey']));

// In template (Smarty syntax)
<input type="hidden" name="checkss" value="{$NV_CHECK}" />

// Validate on submit
$checkss = $nv_Request->get_title('checkss', 'post', '');
if ($checkss != md5($client_info['session_id'] . $global_config['sitekey'])) {
    nv_jsonOutput([
        'status' => 'error',
        'message' => $nv_Lang->getModule('error_security')
    ]);
}
```

### Input Validation

**✅ ALWAYS validate input:**
```php
// Get and validate
$id = $nv_Request->get_int('id', 'get', 0);
$title = $nv_Request->get_title('title', 'post', '');
$email = $nv_Request->get_string('email', 'post', '');
$content = $nv_Request->get_textarea('content', '', 'post');

// Additional validation
if (empty($title)) {
    $error[] = $nv_Lang->getModule('error_required_title');
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $error[] = $nv_Lang->getModule('error_invalid_email');
}

// Validate date
$date = $nv_Request->get_title('date', 'post', '');
$timestamp = strtotime($date);
if ($timestamp === false || $timestamp < 0) {
    $error[] = $nv_Lang->getModule('error_invalid_date');
}
```

## Database Schema Best Practices

### Table Structure

**✅ CORRECT - Proper schema:**
```php
$sql_create_module[] = "CREATE TABLE " . $db_config['prefix'] . "_" . $lang . "_" . $module_data . "_items (
    item_id mediumint(8) unsigned NOT NULL AUTO_INCREMENT,
    title varchar(255) NOT NULL DEFAULT '',
    alias varchar(255) NOT NULL DEFAULT '',
    content text,
    image varchar(255) NOT NULL DEFAULT '',
    status tinyint(1) unsigned NOT NULL DEFAULT 1 COMMENT '0:Hidden,1:Active',
    weight smallint(5) unsigned NOT NULL DEFAULT 0,
    hits mediumint(8) unsigned NOT NULL DEFAULT 0,
    admin_id mediumint(8) unsigned NOT NULL DEFAULT 0,
    add_time int(11) unsigned NOT NULL DEFAULT 0,
    update_time int(11) unsigned NOT NULL DEFAULT 0,
    PRIMARY KEY (item_id),
    UNIQUE KEY alias (alias),
    KEY status (status),
    KEY weight (weight),
    KEY admin_id (admin_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
```

### Index Strategy

**✅ Add indexes for:**
- Primary keys (automatic)
- Foreign keys
- Fields used in WHERE clauses
- Fields used in ORDER BY
- Unique constraints
- Composite indexes for complex queries

**Example composite index:**
```sql
KEY status_weight (status, weight)  -- For: WHERE status=1 ORDER BY weight
```

## Controller Pattern (Admin)

### Standard CRUD Controller (NukeViet 5.x with Smarty)

```php
<?php
/**
 * NukeViet Content Management System
 * @version 5.x
 * @author VINADES.,JSC <contact@vinades.vn>
 * @copyright (C) 2009-2025 VINADES.,JSC. All rights reserved
 */

if (!defined('NV_IS_FILE_ADMIN')) {
    exit('Stop!!!');
}

$page_title = $nv_Lang->getModule('item_add');
$item_id = $nv_Request->get_int('item_id', 'get', 0);

// Load existing item if editing
$item = [];
if ($item_id > 0) {
    $sql = "SELECT * FROM " . NV_PREFIXLANG . "_" . $module_data . "_items WHERE item_id=:item_id";
    $stmt = $db->prepare($sql);
    $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
    $stmt->execute();

    if ($stmt->rowCount()) {
        $item = $stmt->fetch();
        $page_title = $nv_Lang->getModule('item_edit');
    } else {
        nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=main');
    }
}

$error = [];

// Handle POST submission
if ($nv_Request->isset_request('submit', 'post')) {
    // Verify CSRF token
    $checkss = $nv_Request->get_title('checkss', 'post', '');
    if ($checkss != md5($client_info['session_id'] . $global_config['sitekey'])) {
        $error[] = $nv_Lang->getModule('error_security');
    }

    // Get and validate input
    $title = $nv_Request->get_title('title', 'post', '');
    $alias = $nv_Request->get_title('alias', 'post', '');
    $content = $nv_Request->get_editor('content', '', 'post');
    $status = $nv_Request->get_int('status', 'post', 1);
    $weight = $nv_Request->get_int('weight', 'post', 0);

    // Validation
    if (empty($title)) {
        $error[] = $nv_Lang->getModule('error_required_title');
    }

    // Generate alias if empty
    if (empty($alias)) {
        $alias = change_alias($title);
    }

    // Check unique alias
    if (!empty($alias)) {
        $sql = "SELECT COUNT(*) FROM " . NV_PREFIXLANG . "_" . $module_data . "_items
                WHERE alias=:alias AND item_id!=:item_id";
        $stmt = $db->prepare($sql);
        $stmt->bindParam(':alias', $alias, PDO::PARAM_STR);
        $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->fetchColumn()) {
            $error[] = $nv_Lang->getModule('error_alias_exists');
        }
    }

    if (empty($error)) {
        try {
            $db->query('BEGIN');

            if ($item_id > 0) {
                // Update
                $sql = "UPDATE " . NV_PREFIXLANG . "_" . $module_data . "_items SET
                        title=:title,
                        alias=:alias,
                        content=:content,
                        status=:status,
                        weight=:weight,
                        update_time=:update_time
                        WHERE item_id=:item_id";

                $stmt = $db->prepare($sql);
                $stmt->bindParam(':item_id', $item_id, PDO::PARAM_INT);
            } else {
                // Insert
                $sql = "INSERT INTO " . NV_PREFIXLANG . "_" . $module_data . "_items (
                        title, alias, content, status, weight, admin_id, add_time, update_time
                    ) VALUES (
                        :title, :alias, :content, :status, :weight, :admin_id, :add_time, :update_time
                    )";

                $stmt = $db->prepare($sql);
                $stmt->bindValue(':admin_id', $admin_info['userid'], PDO::PARAM_INT);
                $stmt->bindValue(':add_time', NV_CURRENTTIME, PDO::PARAM_INT);
            }

            // Bind common parameters
            $stmt->bindParam(':title', $title, PDO::PARAM_STR);
            $stmt->bindParam(':alias', $alias, PDO::PARAM_STR);
            $stmt->bindParam(':content', $content, PDO::PARAM_STR, strlen($content));
            $stmt->bindParam(':status', $status, PDO::PARAM_INT);
            $stmt->bindParam(':weight', $weight, PDO::PARAM_INT);
            $stmt->bindValue(':update_time', NV_CURRENTTIME, PDO::PARAM_INT);

            $stmt->execute();

            if ($item_id == 0) {
                $item_id = $db->lastInsertId();
            }

            $db->query('COMMIT');

            // Clear cache
            $nv_Cache->delMod($module_name);

            // Log
            nv_insert_logs(NV_LANG_DATA, $module_name, $item_id > 0 ? 'Edit item' : 'Add item', $title, $admin_info['userid']);

            nv_redirect_location(NV_BASE_ADMINURL . 'index.php?' . NV_LANG_VARIABLE . '=' . NV_LANG_DATA . '&' . NV_NAME_VARIABLE . '=' . $module_name . '&' . NV_OP_VARIABLE . '=main');
        } catch (PDOException $e) {
            $db->query('ROLLBACK');
            nv_insert_logs(NV_LANG_DATA, $module_name, 'ERROR', $e->getMessage(), $admin_info['userid']);
            $error[] = $nv_Lang->getModule('error_save');
        }
    }
} else {
    // Load existing data for editing
    if (!empty($item)) {
        $title = $item['title'];
        $alias = $item['alias'];
        $content = $item['content'];
        $status = $item['status'];
        $weight = $item['weight'];
    } else {
        // Default values for new item
        $title = '';
        $alias = '';
        $content = '';
        $status = 1;
        $weight = 0;
    }
}

// Prepare Smarty template
$tpl = new \NukeViet\Template\NVSmarty();
$tpl->setTemplateDir(get_module_tpl_dir('content.tpl'));
$tpl->assign('LANG', $nv_Lang);
$tpl->assign('MODULE_NAME', $module_name);
$tpl->assign('OP', $op);
$tpl->assign('ITEM_ID', $item_id);
$tpl->assign('TITLE', $title);
$tpl->assign('ALIAS', $alias);
$tpl->assign('CONTENT', $content);
$tpl->assign('STATUS', $status);
$tpl->assign('WEIGHT', $weight);
$tpl->assign('ERROR', $error);
$tpl->assign('NV_CHECK', md5($client_info['session_id'] . $global_config['sitekey']));

$contents = $tpl->fetch('content.tpl');

include NV_ROOTDIR . '/includes/header.php';
echo nv_admin_theme($contents);
include NV_ROOTDIR . '/includes/footer.php';
```

## NukeViet 4.x vs 5.x Comparison

### Template Engine Migration

| Feature | NukeViet 4.x (XTemplate) | NukeViet 5.x (Smarty) |
|---------|-------------------------|----------------------|
| **Template Engine** | XTemplate | Smarty 3.x |
| **Language Access** | `$lang_module` array | `$nv_Lang` object |
| **Initialization** | `new XTemplate('file.tpl', $path)` | `new \NukeViet\Template\NVSmarty()` |
| **Template Directory** | Full path concatenation | `get_module_tpl_dir('file.tpl')` |
| **Assign Language** | `$xtpl->assign('LANG', $lang_module)` | `$tpl->assign('LANG', $nv_Lang)` |
| **Render Template** | `$xtpl->parse('main')` + `$xtpl->text('main')` | `$tpl->fetch('file.tpl')` |
| **Comments** | `<!-- comment -->` | `{* comment *}` |
| **Language Keys** | `{LANG.key}` | `{$LANG->getModule('key')}` |
| **Variables** | `{VAR}` | `{$var}` |
| **Constants** | `{NV_BASE_ADMINURL}` | `{$smarty.const.NV_BASE_ADMINURL}` |
| **Loops** | `<!-- BEGIN: loop -->...<!-- END: loop -->` | `{foreach from=$items item=item}...{/foreach}` |
| **Conditionals** | `<!-- BEGIN: condition -->...<!-- END: condition -->` | `{if condition}...{/if}` |
| **Escaping** | Manual `nv_htmlspecialchars()` | Smarty modifiers `{$var\|escape}` |

### Language File Structure

| Aspect | NukeViet 4.x | NukeViet 5.x |
|--------|--------------|--------------|
| **Language File Path** | `language/vi/admin_{module}.php` (admin)<br>`language/vi/{module}.php` (frontend) | `language/vi.php` (both admin & frontend) |
| **Guard Check** | `if (!defined('NV_ADMIN') or !defined('NV_MAINFILE'))` | `if (!defined('NV_ADMIN'))` |
| **Language Variable** | `$lang_module` array | Returns via `$nv_Lang->getModule()` |
| **Usage in PHP** | `$lang_module['key']` | `$nv_Lang->getModule('key')` |
| **Usage in Templates** | `{LANG.key}` | `{$LANG->getModule('key')}` |

### Migration Examples

**XTemplate → Smarty: Comments**
```html
<!-- XTemplate (NV4) -->
<!-- This is a comment -->

<!-- Smarty (NV5) -->
{* This is a comment *}
```

**XTemplate → Smarty: Language Keys**
```html
<!-- XTemplate (NV4) -->
<h1>{LANG.welcome}</h1>

<!-- Smarty (NV5) -->
<h1>{$LANG->getModule('welcome')}</h1>
```

**XTemplate → Smarty: Variables**
```html
<!-- XTemplate (NV4) -->
<p>{TITLE}</p>
<p>{USER.name}</p>

<!-- Smarty (NV5) -->
<p>{$TITLE}</p>
<p>{$user.name}</p>
```

**XTemplate → Smarty: Loops**
```html
<!-- XTemplate (NV4) -->
<!-- BEGIN: items -->
<!-- BEGIN: loop -->
<div>{ITEM.name}</div>
<!-- END: loop -->
<!-- END: items -->

<!-- Smarty (NV5) -->
{if not empty($ITEMS)}
{foreach from=$ITEMS item=item}
<div>{$item.name}</div>
{/foreach}
{/if}
```

**XTemplate → Smarty: Conditionals**
```html
<!-- XTemplate (NV4) -->
<!-- BEGIN: has_items -->
<p>Has items</p>
<!-- END: has_items -->

<!-- Smarty (NV5) -->
{if not empty($ITEMS)}
<p>Has items</p>
{/if}
```

## Template Best Practices (Bootstrap 5 with Smarty)

### Standard List Template (Smarty Syntax)

```smarty
{* BEGIN: main *}
<div class="card">
    <div class="card-header text-bg-primary">
        <h5 class="mb-0"><i class="bi bi-list"></i> {$LANG->getModule('item_list')}</h5>
    </div>
    <div class="card-body">
        {* Filters *}
        <form action="{$smarty.const.NV_BASE_ADMINURL}index.php" method="get" class="mb-4">
            <input type="hidden" name="{$smarty.const.NV_LANG_VARIABLE}" value="{$smarty.const.NV_LANG_DATA}">
            <input type="hidden" name="{$smarty.const.NV_NAME_VARIABLE}" value="{$MODULE_NAME}">
            <input type="hidden" name="{$smarty.const.NV_OP_VARIABLE}" value="{$OP}">

            <div class="row g-3">
                <div class="col-md-6">
                    <input type="text" name="search" value="{$SEARCH}" class="form-control" placeholder="{$LANG->getModule('search')}...">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> {$LANG->getModule('search')}
                    </button>
                </div>
            </div>
        </form>

        {* Add button *}
        <div class="mb-3">
            <a href="{$smarty.const.NV_BASE_ADMINURL}index.php?{$smarty.const.NV_LANG_VARIABLE}={$smarty.const.NV_LANG_DATA}&amp;{$smarty.const.NV_NAME_VARIABLE}={$MODULE_NAME}&amp;{$smarty.const.NV_OP_VARIABLE}=content" class="btn btn-success">
                <i class="bi bi-plus-circle"></i> {$LANG->getModule('item_add')}
            </a>
        </div>

        {if not empty($ITEMS)}
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>{$LANG->getModule('title')}</th>
                        <th>{$LANG->getModule('status')}</th>
                        <th class="text-center">{$LANG->getModule('actions')}</th>
                    </tr>
                </thead>
                <tbody>
                    {foreach from=$ITEMS item=item}
                    <tr>
                        <td>{$item.id}</td>
                        <td><strong>{$item.title}</strong></td>
                        <td><span class="badge bg-{$item.status_class}">{$item.status_text}</span></td>
                        <td class="text-center">
                            <div class="btn-group btn-group-sm">
                                <a href="{$smarty.const.NV_BASE_ADMINURL}index.php?{$smarty.const.NV_LANG_VARIABLE}={$smarty.const.NV_LANG_DATA}&amp;{$smarty.const.NV_NAME_VARIABLE}={$MODULE_NAME}&amp;{$smarty.const.NV_OP_VARIABLE}=content&amp;item_id={$item.id}" class="btn btn-primary" title="{$LANG->getModule('edit')}">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-danger" onclick="confirmDelete({$item.id}, '{$item.title|escape:'javascript'}');" title="{$LANG->getModule('delete')}">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    {/foreach}
                </tbody>
            </table>
        </div>
        {else}
        <div class="alert alert-info">
            <i class="bi bi-info-circle"></i> {$LANG->getModule('no_data')}
        </div>
        {/if}

        {if not empty($GENERATE_PAGE)}
        <div class="mt-3">
            {$GENERATE_PAGE}
        </div>
        {/if}
    </div>
</div>

<script>
function confirmDelete(id, title) {
    if (confirm('{$LANG->getModule("confirm_delete")} "' + title + '"?')) {
        $.ajax({
            url: '{$smarty.const.NV_BASE_ADMINURL}index.php?{$smarty.const.NV_LANG_VARIABLE}={$smarty.const.NV_LANG_DATA}&{$smarty.const.NV_NAME_VARIABLE}={$MODULE_NAME}&{$smarty.const.NV_OP_VARIABLE}=del',
            type: 'POST',
            data: {
                id: id,
                checkss: '{$NV_CHECK}'
            },
            success: function(response) {
                if (response.status == 'OK') {
                    alert(response.message);
                    location.reload();
                } else {
                    alert(response.message);
                }
            }
        });
    }
}
</script>
{* END: main *}
```

### Standard Form Template (Smarty Syntax)

```smarty
{* BEGIN: main *}
<div class="card">
    <div class="card-header text-bg-primary">
        <h5 class="mb-0"><i class="bi bi-pencil"></i> {$LANG->getModule('item_add')}</h5>
    </div>
    <div class="card-body">
        {if not empty($ERROR)}
        <div class="alert alert-danger">
            {$ERROR|@join:"<br />"}
        </div>
        {/if}

        <form action="{$smarty.const.NV_BASE_ADMINURL}index.php?{$smarty.const.NV_LANG_VARIABLE}={$smarty.const.NV_LANG_DATA}&amp;{$smarty.const.NV_NAME_VARIABLE}={$MODULE_NAME}&amp;{$smarty.const.NV_OP_VARIABLE}={$OP}&amp;item_id={$ITEM_ID}" method="post">
            <input type="hidden" name="checkss" value="{$NV_CHECK}" />

            <div class="mb-3">
                <label class="form-label">{$LANG->getModule('title')} <span class="text-danger">*</span></label>
                <input type="text" name="title" value="{$TITLE}" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">{$LANG->getModule('alias')}</label>
                <input type="text" name="alias" value="{$ALIAS}" class="form-control">
                <small class="form-text text-muted">{$LANG->getModule('alias_hint')}</small>
            </div>

            <div class="mb-3">
                <label class="form-label">{$LANG->getModule('content')}</label>
                <textarea name="content" class="form-control" rows="10">{$CONTENT}</textarea>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{$LANG->getModule('status')}</label>
                        <select name="status" class="form-select">
                            <option value="1" {if $STATUS == 1}selected{/if}>{$LANG->getModule('status_active')}</option>
                            <option value="0" {if $STATUS == 0}selected{/if}>{$LANG->getModule('status_inactive')}</option>
                        </select>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">{$LANG->getModule('weight')}</label>
                        <input type="number" name="weight" value="{$WEIGHT}" class="form-control" min="0">
                    </div>
                </div>
            </div>

            <div class="text-end">
                <a href="{$smarty.const.NV_BASE_ADMINURL}index.php?{$smarty.const.NV_LANG_VARIABLE}={$smarty.const.NV_LANG_DATA}&amp;{$smarty.const.NV_NAME_VARIABLE}={$MODULE_NAME}&amp;{$smarty.const.NV_OP_VARIABLE}=main" class="btn btn-secondary">
                    <i class="bi bi-arrow-left"></i> {$LANG->getModule('back')}
                </a>
                <button type="submit" name="submit" class="btn btn-primary">
                    <i class="bi bi-save"></i> {$LANG->getModule('save')}
                </button>
            </div>
        </form>
    </div>
</div>
{* END: main *}
```

## Performance Optimization

### Avoid N+1 Queries

**❌ WRONG:**
```php
foreach ($items as $item) {
    $sql = "SELECT * FROM categories WHERE id=" . $item['cat_id'];
    $category = $db->query($sql)->fetch();
    $item['category_name'] = $category['name'];
}
```

**✅ CORRECT:**
```php
// Load all categories once
$categories = [];
$sql = "SELECT * FROM categories";
$result = $db->query($sql);
while ($row = $result->fetch()) {
    $categories[$row['id']] = $row;
}

// Use in loop
foreach ($items as $item) {
    $item['category_name'] = $categories[$item['cat_id']]['name'] ?? '';
}
```

### Use Caching

```php
// Cache list data
$cache_key = $module_name . '_items_' . md5(serialize($filters));
$items = $nv_Cache->getItem($module_name, $cache_key);

if (empty($items)) {
    // Load from database
    $items = load_items_from_db($filters);

    // Cache for 1 hour
    $nv_Cache->setItem($module_name, $cache_key, $items, 3600);
}

// Clear cache when data changes
$nv_Cache->delMod($module_name);
```

## Testing Checklist

Before submitting code, verify:

### Security Tests
- [ ] All SQL queries use prepared statements
- [ ] All user input is validated and sanitized
- [ ] All output is escaped
- [ ] CSRF protection is implemented
- [ ] File upload validation is implemented
- [ ] No sensitive data in logs or error messages

### Functionality Tests
- [ ] Create new item works
- [ ] Edit existing item works
- [ ] Delete item works
- [ ] List view with pagination works
- [ ] Search/filter works
- [ ] All form validations work

### Code Quality
- [ ] No unused variables
- [ ] No hardcoded values
- [ ] PHPDoc comments for all functions
- [ ] Consistent coding style
- [ ] No debug code (var_dump, print_r, etc.)

### Performance
- [ ] No N+1 query problems
- [ ] Appropriate database indexes
- [ ] Caching implemented where appropriate

### Compatibility
- [ ] Works with PHP 7.4+
- [ ] Works with MySQL 5.7+
- [ ] Bootstrap 5 templates responsive
- [ ] Multi-language support works

## Common Mistakes to Avoid

1. **SQL Injection**: Never concatenate user input into SQL
2. **XSS**: Always escape output
3. **Missing CSRF**: Always add CSRF tokens to forms
4. **N+1 Queries**: Load related data in batch
5. **No Transactions**: Use transactions for multi-step operations
6. **Weak Random**: Use `random_int()` not `rand()`
7. **No Validation**: Validate all input
8. **Magic Numbers**: Define constants
9. **No Error Logging**: Log errors properly
10. **Ignoring Cache**: Clear cache when data changes

## Resources

- Official NukeViet Docs: https://github.com/nukeviet/nukeviet
- Security Guidelines: OWASP Top 10
- PHP Standards: PSR-1, PSR-2, PSR-12
- Bootstrap 5 Docs: https://getbootstrap.com/docs/5.0/

## Example: Complete Minimal Module

See `.claude/skills/templates/nukeviet-module-template/` for a complete working example.
