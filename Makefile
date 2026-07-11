SHELL := /bin/sh

-include .env
export

COMPOSE := docker compose
MOODLE_WWWROOT ?= http://localhost:8080

.PHONY: help config build up down restart ps logs logs-nginx logs-php logs-mysql \
	shell-php shell-mysql shell-redis composer-install install upgrade maintenance-on \
	maintenance-off purge-cache cron check db-import db-backup db-shell reset-data doctor

help: ## Show available commands
	@awk 'BEGIN {FS = ":.*## "; printf "Available commands:\n"} /^[a-zA-Z_-]+:.*## / {printf "  %-20s %s\n", $$1, $$2}' $(MAKEFILE_LIST)

config: ## Create local .env and config.php when missing
	@test -f .env || cp .env.example .env
	@test -f config.php || cp config.docker.php config.php
	@mkdir -p moodledata database/init backups

build: ## Build the PHP image
	$(COMPOSE) build

up: config ## Start the local Moodle stack
	$(COMPOSE) up -d

down: ## Stop containers, preserving data volumes
	$(COMPOSE) down

restart: ## Restart all containers
	$(COMPOSE) restart

ps: ## Show container status
	$(COMPOSE) ps

logs: ## Follow all container logs
	$(COMPOSE) logs -f

logs-nginx: ## Follow Nginx logs
	$(COMPOSE) logs -f nginx

logs-php: ## Follow PHP-FPM logs
	$(COMPOSE) logs -f php-fpm

logs-mysql: ## Follow MySQL logs
	$(COMPOSE) logs -f mysql

shell-php: ## Open a shell in the PHP container
	$(COMPOSE) exec php-fpm bash

shell-mysql: ## Open a MySQL root shell
	$(COMPOSE) exec mysql sh -lc 'MYSQL_PWD="$$MYSQL_ROOT_PASSWORD" mysql -uroot "$$MYSQL_DATABASE"'

shell-redis: ## Open redis-cli
	$(COMPOSE) exec redis redis-cli

composer-install: ## Install production Composer dependencies
	$(COMPOSE) exec php-fpm composer install --no-dev --prefer-dist --optimize-autoloader

install: up ## Install a new empty Moodle database
	$(COMPOSE) exec -u www-data php-fpm sh -lc 'php admin/cli/install_database.php \
		--agree-license \
		--fullname="SecureFood School" \
		--shortname="SFS" \
		--adminuser="$$MOODLE_ADMIN_USER" \
		--adminpass="$$MOODLE_ADMIN_PASSWORD" \
		--adminemail="$$MOODLE_ADMIN_EMAIL"'

upgrade: ## Run Moodle database upgrades
	$(COMPOSE) exec -u www-data php-fpm php admin/cli/upgrade.php --non-interactive

maintenance-on: ## Enable maintenance mode
	$(COMPOSE) exec -u www-data php-fpm php admin/cli/maintenance.php --enable

maintenance-off: ## Disable maintenance mode
	$(COMPOSE) exec -u www-data php-fpm php admin/cli/maintenance.php --disable

purge-cache: ## Purge Moodle caches
	$(COMPOSE) exec -u www-data php-fpm php admin/cli/purge_caches.php

cron: ## Run Moodle cron once
	$(COMPOSE) exec -u www-data php-fpm php admin/cli/cron.php --keep-alive=0

check: ## Run Moodle environment checks
	$(COMPOSE) exec -u www-data php-fpm php admin/cli/checks.php

db-import: ## Import FILE=/path/to/dump.sql into the Moodle database
	@test -n "$(FILE)" || (echo "Usage: make db-import FILE=/path/to/dump.sql"; exit 1)
	@test -f "$(FILE)" || (echo "Dump not found: $(FILE)"; exit 1)
	$(COMPOSE) exec -T mysql sh -lc 'MYSQL_PWD="$$MYSQL_ROOT_PASSWORD" mysql -uroot "$$MYSQL_DATABASE"' < "$(FILE)"

db-backup: ## Write a timestamped database dump to backups/
	@mkdir -p backups
	$(COMPOSE) exec -T mysql sh -lc 'MYSQL_PWD="$$MYSQL_ROOT_PASSWORD" mysqldump --no-tablespaces --single-transaction -uroot "$$MYSQL_DATABASE"' > "backups/moodle_$$(date +%Y%m%d_%H%M%S).sql"

db-shell: shell-mysql ## Alias for shell-mysql

reset-data: ## Delete local MySQL and Redis volumes after confirmation
	@printf "Delete local database and Redis volumes? [y/N] "; read answer; \
		case "$$answer" in y|Y) $(COMPOSE) down -v ;; *) echo "Cancelled" ;; esac

doctor: ## Verify PHP extensions and service connectivity
	$(COMPOSE) exec php-fpm php -r 'foreach (["curl", "dom", "gd", "intl", "mbstring", "mysqli", "redis", "simplexml", "sodium", "xml", "xmlreader", "zip"] as $$ext) { if (!extension_loaded($$ext)) { fwrite(STDERR, "Missing PHP extension: $$ext\n"); exit(1); } } echo "PHP extensions: OK\n";'
	$(COMPOSE) exec mysql sh -lc 'MYSQL_PWD="$$MYSQL_ROOT_PASSWORD" mysqladmin ping -h localhost -uroot --silent'
	$(COMPOSE) exec redis redis-cli ping
	curl --fail --silent --show-error --output /dev/null "$(MOODLE_WWWROOT)/"
	@echo "HTTP: OK ($(MOODLE_WWWROOT))"
