SHELL := /bin/bash

unit:
	XDEBUG_MODE=coverage vendor/bin/phpunit tests/unit --coverage-text
.PHONY: unit

e2e:
	XDEBUG_MODE=coverage vendor/bin/phpunit tests/e2e --coverage-text
.PHONY: e2e

serve:
	php -S localhost:8080 -t public
.PHONY: serve

init:
	composer install
	php bin/init
.PHONY: init

cs:
	vendor/bin/php-cs-fixer fix src
.PHONY: cs

deploy:
	php bin/data
.PHONY: deploy
