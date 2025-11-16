# NukeViet Module Template

Secure, production-ready template for NukeViet 5.x modules.

## Features

✅ Full CSRF protection
✅ Prepared statements for all queries
✅ XSS prevention
✅ Input validation
✅ Error handling
✅ Caching support
✅ Bootstrap 5 UI
✅ Multi-language support

## Structure

```
example/
├── action_mysql.php          # Database schema
├── version.php                # Module metadata
├── admin.menu.php            # Admin menu
├── admin.functions.php       # Helper functions
├── admin/
│   ├── main.php             # List items
│   ├── content.php          # Add/Edit item
│   └── del.php              # Delete item
├── language/
│   └── vi/
│       └── admin_example.php
└── templates/
    ├── main.tpl
    └── content.tpl
```

## Usage

1. Copy this template to `modules/yourmodule/`
2. Replace "example" with your module name
3. Customize database schema in `action_mysql.php`
4. Add your business logic
5. Update language files
6. Test thoroughly

## Security Features

- All database queries use PDO prepared statements
- CSRF tokens on all forms
- Input validation on all fields
- XSS prevention with output escaping
- Transaction support for data integrity
- Proper error logging

## Quick Start

```bash
# Copy template
cp -r .claude/skills/templates/nukeviet-module-template modules/mymodule

# Replace module name
find modules/mymodule -type f -exec sed -i 's/example/mymodule/g' {} +

# Install via admin panel
# Admin -> Extensions -> Modules -> Install
```
