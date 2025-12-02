# Migration Generator Command

## 📋 Overview

Artisan command để tự động generate Laravel migrations từ schema definition (1 table = 1 file).

## 🚀 Usage

### Generate tất cả migrations
```bash
php artisan db:generate-migrations
```

### Generate với custom options
```bash
# Custom starting number
php artisan db:generate-migrations --start=100

# Custom base date
php artisan db:generate-migrations --date=2025_01_01

# Load schema from file (future feature)
php artisan db:generate-migrations --schema=database/schema.dbml
```

## ⚙️ How It Works

1. **Schema Definition**: Schema được define trong method `defineSchemaManually()` của command
2. **Type Mapping**: DBML types được map sang Laravel Blueprint methods
3. **Dependency Ordering**: Tables được generate theo thứ tự dependencies (Level 1 → Level 13)
4. **File Naming**: `YYYY_MM_DD_NNNNNN_create_{table}_table.php`

## 📝 Schema Structure

Mỗi table được define với:

```php
'table_name' => [
    'columns' => [
        ['name' => 'id', 'type' => 'id'],
        ['name' => 'email', 'type' => 'string', 'unique' => true],
        ['name' => 'user_id', 'type' => 'foreignId', 'references' => 'users', 'onDelete' => 'cascade'],
        ['name' => 'status', 'type' => 'enum', 'values' => ['active', 'inactive']],
        ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
    ],
    'indexes' => [
        ['columns' => ['user_id', 'created_at']],
    ],
    'primary' => ['user_id', 'role_id'], // For composite keys
    'comment' => 'Table description',
]
```

## 🔧 Supported Column Types

| DBML Type | Laravel Type | Example |
|-----------|--------------|---------|
| `id` | `id()` | Primary key |
| `foreignId` | `foreignId()` | Foreign key |
| `string` | `string()` | VARCHAR |
| `text` | `text()` | TEXT |
| `longtext` | `longText()` | LONGTEXT |
| `integer` | `integer()` | INT |
| `bigint` | `bigInteger()` | BIGINT |
| `tinyint` | `tinyInteger()` | TINYINT |
| `decimal` | `decimal()` | DECIMAL |
| `boolean` | `boolean()` | BOOLEAN |
| `date` | `date()` | DATE |
| `datetime` | `dateTime()` | DATETIME |
| `timestamp` | `timestamp()` | TIMESTAMP |
| `json` | `json()` | JSON |
| `enum` | `enum()` | ENUM |

## 🎯 Column Modifiers

```php
['name' => 'email', 'type' => 'string', 'unique' => true]
['name' => 'bio', 'type' => 'text', 'nullable' => true]
['name' => 'quantity', 'type' => 'integer', 'default' => 0]
['name' => 'name', 'type' => 'string', 'length' => 100]
['name' => 'price', 'type' => 'decimal', 'precision' => [15, 2]]
```

## 🔗 Foreign Keys

```php
// Basic FK
['name' => 'user_id', 'type' => 'foreignId', 'references' => 'users']

// FK with cascade delete
['name' => 'user_id', 'type' => 'foreignId', 'references' => 'users', 'onDelete' => 'cascade']

// Nullable FK with set null
['name' => 'user_id', 'type' => 'foreignId', 'references' => 'users', 'nullable' => true, 'onDelete' => 'set null']
```

## 📊 Indexes

```php
'indexes' => [
    ['columns' => ['email']],                    // Single column
    ['columns' => ['user_id', 'created_at']],    // Composite
    ['columns' => ['slug'], 'unique' => true],   // Unique
]
```

## 🔄 Composite Primary Keys

```php
'role_user' => [
    'columns' => [
        ['name' => 'user_id', 'type' => 'foreignId', ...],
        ['name' => 'role_id', 'type' => 'foreignId', ...],
    ],
    'primary' => ['user_id', 'role_id'],
]
```

## ⏱️ Timestamps & Soft Deletes

```php
// Auto-detected and converted to timestamps() / softDeletes()
['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
['name' => 'updated_at', 'type' => 'timestamp', 'nullable' => true],
['name' => 'deleted_at', 'type' => 'timestamp', 'nullable' => true],
```

## 🐛 Troubleshooting

### Circular Dependencies

Nếu có circular reference (vd: `users.default_address_id` → `user_addresses.id`), tạo migration riêng để add FK sau:

```php
// In users migration: use unsignedBigInteger instead of foreignId
$table->unsignedBigInteger('default_address_id')->nullable();

// Create separate migration: 2024_01_01_000057_add_default_address_to_users_table.php
Schema::table('users', function (Blueprint $table) {
    $table->foreignId('default_address_id')
        ->nullable()
        ->constrained('user_addresses')
        ->onDelete('set null');
});
```

### Duplicate Files

Nếu command chạy 2 lần và tạo duplicate files:

```bash
# Xóa tất cả generated migrations
Get-ChildItem database\migrations\ -Filter "2024_01_01_*" | Remove-Item

# Regenerate
php artisan db:generate-migrations
```

### Foreign Key Errors

Đảm bảo referenced table được tạo trước:
- Check thứ tự migration numbers
- Verify referenced table name đúng (singular/plural)
- Check column type matching (bigint unsigned)

## 📦 Generated Output

Command sẽ tạo:
- ✅ 43 migration files cho schema definition
- ✅ Proper indexes và foreign keys
- ✅ Comments for documentation
- ✅ Timestamps và soft deletes
- ✅ Enum types với values

## 🎓 Examples

### Simple Table
```php
'categories' => [
    'columns' => [
        ['name' => 'id', 'type' => 'id'],
        ['name' => 'name', 'type' => 'string'],
        ['name' => 'slug', 'type' => 'string', 'unique' => true],
        ['name' => 'parent_id', 'type' => 'foreignId', 'references' => 'categories', 'nullable' => true],
        ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
        ['name' => 'updated_at', 'type' => 'timestamp', 'nullable' => true],
        ['name' => 'deleted_at', 'type' => 'timestamp', 'nullable' => true],
    ],
    'indexes' => [
        ['columns' => ['slug']],
        ['columns' => ['parent_id']],
    ],
    'comment' => 'Product categories with hierarchy',
]
```

### Pivot Table
```php
'role_user' => [
    'columns' => [
        ['name' => 'user_id', 'type' => 'foreignId', 'references' => 'users', 'onDelete' => 'cascade'],
        ['name' => 'role_id', 'type' => 'foreignId', 'references' => 'roles', 'onDelete' => 'cascade'],
        ['name' => 'created_at', 'type' => 'timestamp', 'nullable' => true],
    ],
    'primary' => ['user_id', 'role_id'],
    'comment' => 'User-Role pivot table',
]
```

## 🔮 Future Enhancements

- [ ] Parse DBML files directly
- [ ] Generate models with relationships
- [ ] Generate factories and seeders
- [ ] Validate schema before generation
- [ ] Interactive mode for schema editing
- [ ] Export existing database to schema

## 📚 Related Commands

```bash
# Run migrations
php artisan migrate

# Rollback
php artisan migrate:rollback

# Fresh migrate (⚠️ deletes all data)
php artisan migrate:fresh

# Check status
php artisan migrate:status

# Pretend mode (show SQL without executing)
php artisan migrate --pretend
```

## ✅ Best Practices

1. **Review generated files** before running migrations
2. **Test on development** database first
3. **Backup production** database before migrating
4. **Use transactions** for complex migrations
5. **Version control** all migration files
6. **Document custom logic** in migration comments

## 🤝 Contributing

To add new tables to schema:

1. Edit `app/Console/Commands/GenerateMigrationsFromSchema.php`
2. Add table definition in `defineSchemaManually()` method
3. Run `php artisan db:generate-migrations`
4. Review generated migration file
5. Test migration with `php artisan migrate --pretend`

---

**Generated by:** `php artisan db:generate-migrations`  
**Command Location:** `app/Console/Commands/GenerateMigrationsFromSchema.php`
