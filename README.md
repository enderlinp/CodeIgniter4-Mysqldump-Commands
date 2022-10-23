# CodeIgniter4 Mysqldump Commands

Mysqldump Spark Commands (CLI) for CodeIgniter 4.

## Installation
Copy `Mysqldump.php` to your CodeIgniter4 `app/Commands` directory.

### Requirements
- [Composer](https://getcomposer.org)
- [ifsnop/mysqldump-php](https://www.github.com/ifsnop/mysqldump-php)

Install [ifsnop/mysqldump-php](https://www.github.com/ifsnop/mysqldump-php) library with composer, like so:
```console
> composer require ifsnop/mysqldump-php
```

## Usage

Perform a backup of your MySQL default database (**MySQLi driver only**) and store the backup file within your "{WRITEPATH}/sql" directory. 
Prompt the user to create it if it does not exists. 

The backup file name format is `dbname_Ymd_His.sql` or `dbname_Ymd_His.sql.gz` with Gzip compression enabled (see below).

View the [ifsnop/mysqldump-php](https://www.github.com/ifsnop/mysqldump-php) documentation for more information.

### Command Line (CLI)

Regular usage via command line is:
```console
> php spark db:mysqldump
```

Available options are:
- --c: Enable Gzip compression
- --d: Add DROP TABLE statements 
- --i: Ignore all tables
- --r: Reset AUTO_INCREMENT statements
- --u: Set default character set to UTF8MB4

To compress (Gzip) the backup file, use:
```console
> php spark db:mysqldump --c
```

To add `DROP TABLE` statements to the backup file, use:
```console
> php spark db:mysqldump --d
```

To ignore all tables and remove `AUTO_INCREMENT` statements (equivalent to a `TRUNCATE` statement), use:
```console
> php spark db:mysqldump --i --r
```

### Running Commands (Controller)

Like any other spark command, you may use it within your controller with the `command()` function, like so:
```php
<?php
// Database `ci4` successfully backup.
echo command('db:mysqldump');
```

or:

```php
<?php
// Database `ci4` successfully backup.
$message = command('db:mysqldump -c');

return redirect()->back()->with('message', $message);
```

See the [Running Commands](https://codeigniter.com/user_guide/cli/spark_commands.html#running-commands) section from CodeIgniter 4 documentation for more information.

# License
Released under the [MIT License](./LICENSE).