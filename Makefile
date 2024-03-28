SHELL := /bin/bash

unit:
	XDEBUG_MODE=coverage vendor/bin/phpunit tests/unit --coverage-text
.PHONY: unit

e2e:
	XDEBUG_MODE=coverage vendor/bin/phpunit tests/e2e --coverage-text
.PHONY: e2e