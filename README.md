# HTTP Requests Scheduler

A simple library to schedule HTTP requests. May be used as a testing automation tool.

### Requirements:
- PHP 8.2
- Composer
- Sqlite3

## How to install

```bash
git clone git@github.com:trngl8/http-requests-schedule.git
cd http-requests-schedule
composer install
php bin/init
```

## How to run

```bash
php -S localhost:8080 -t public
```

## How to test

```bash
php vendor/bin/phpunit tests
```
