# breakfast-survey
![PHP 8.0.8+](https://img.shields.io/badge/PHP-8.0.8%2B-blue)
![PostgreSQL 13](https://img.shields.io/badge/PostgreSQL-13-blue)

### Database configuration:
- `index.php` - line 38
- `data.php` - line 3
```php
new PDO('pgsql:
            host=localhost;
            port=5432;
            dbname=breakfast;
            user=postgres;
            password=postgres');
```
Table will be automatically created if provided with a valid DB connection.

### Todo:
- [ ] DB connection in separate file
- [ ] Use PHP `include` for cleaner code