.SILENT:

# Include .env file
include ./docker/.env

# Make `help` the default target to make sure it will display when make is called without a target.
.DEFAULT_GOAL := help

help: ## Show this help message.
	@grep -E '^[a-zA-Z0-9\._-]+:.*?## .*$$' $(MAKEFILE_LIST) | \
		sed -e 's/Makefile://g' | \
	    sort | \
		awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'
.PHONY: help

audit-php: ## Audit PHP by using PHPInsights. The main purpose is to check the code quality.
	docker-compose -f 'docker/docker-compose.yml' exec wp_single \
    bash -i '/var/www/tools/phpinsights/analyse.sh'
.PHONY: audit-php

audit-php-coverage: ## Run code coverage on 'single' workspace.
	docker-compose -f 'docker/docker-compose.yml' exec wp_single \
	bash -c 'cd /var/www/tools/codeception ; vendor/bin/codecept run --env single --coverage --coverage-html'
.PHONY: audit-php-coverage

build-dist: ## BETA - Build the distribution package (gp-machine-translate.zip). The package is used by 'single-test' and 'multi-test'.
	cd build ; \
	./build.sh
.PHONY: build-dist

clean-up: ## Clean up the /build/tmp, /dist, /tests/_data and /tests/_output directories.
	# Remove .DS_Store files
	find ./ -name ".DS_Store" -print0 | xargs -0 rm
	# Remove all files in /build/tmp
	find ./build/tmp/* -print0 | xargs -0 rm -rf
	# Remove all files in /dist
	find ./dist/* -print0 | xargs -0 rm -rf
	# Remove all files in /tests/_data
	find ./tests/_data/* -print0 | xargs -0 rm -rf
	# Remove all files in /tests/_output
	find ./tests/_output/* -print0 | xargs -0 rm -rf
.PHONY: clean-up

clean-up-full: ## WARNING: this deletes your workspace! Clean up the /build/tmp, /dev, /dist, /tests/_data and /tests/_output directories.
	# Remove all files in /dev
	find ./dev/* -print0 | xargs -0 rm -rf
	make clean-up
.PHONY: clean-up-full

dev-php-error-detection: ## Audit PHP by using PHPStan. The main purpose is to focus on finding errors.
	docker-compose -f 'docker/docker-compose.yml' exec wp_single \
    bash -i '/var/www/tools/phpstan/analyse.sh'
.PHONY: dev-php-error-detection

docker-start: ## Start docker containers.
	docker-compose -f docker/docker-compose.yml up -d
.PHONY: docker-start

docker-down: ## Stop and remove docker containers and networks
	docker-compose -f docker/docker-compose.yml down
.PHONY: docker-down

init-project: ## Install all composer and npm packages. The targets 'dev-resource-init', 'build-assets' and 'build-dist' are executed too.
	cd ./tools/codeception/ ;  \
	composer install

	cd ./tools/phpinsights/ ; \
	composer install

	cd ./tools/phpstan/ ; \
	composer install

	cd ./src/ ; \
	composer install ; \
	composer update

	make build-dist
.PHONY: project-init

reset-workspace-db: ## Drop 'single' and 'multi' database and restart the Docker containers. The containers MUST run already.
	docker-compose -f 'docker/docker-compose.yml' exec wp_single \
	bash -c 'mysql --user="${MYSQL_ROOT_USER}" --password="${MYSQL_ROOT_PASSWORD}" \
	--host=${MYSQL_HOST} --port=${MYSQL_PORT} --execute="DROP DATABASE IF EXISTS ${WORDPRESS_DATABASE_SINGLE};"'
	docker-compose -f 'docker/docker-compose.yml' restart wp_single
	docker-compose -f 'docker/docker-compose.yml' exec wp_multi \
	bash -c 'mysql --user="${MYSQL_ROOT_USER}" --password="${MYSQL_ROOT_PASSWORD}" \
	--host=${MYSQL_HOST} --port=${MYSQL_PORT} --execute="DROP DATABASE IF EXISTS ${WORDPRESS_DATABASE_MULTI};"'
	docker-compose -f 'docker/docker-compose.yml' restart wp_multi
.PHONY: reset-workspace-db

run-acceptance: ## Run acceptance tests on 'single' workspace.
	docker-compose -f 'docker/docker-compose.yml' exec wp_single \
	bash -i '/var/www/tools/codeception/test.sh' --env single --suite acceptance
.PHONY: run-acceptance

run-acceptance-multi: ## Run acceptance tests on 'multi' workspace.
	docker-compose -f 'docker/docker-compose.yml' exec wp_multi \
	bash -i '/var/www/tools/codeception/test.sh' --env multi --suite acceptance
.PHONY: run-acceptance-multi

run-wpunit: ## ## Run wpunit tests on 'single' workspace.
	docker-compose -f 'docker/docker-compose.yml' exec wp_single \
	bash -i '/var/www/tools/codeception/test.sh' --env single --suite wpunit
.PHONY: run-wpunit

run-wpunit-multi: ## ## Run wpunit tests on 'multi' workspace.
	docker-compose -f 'docker/docker-compose.yml' exec wp_multi \
	bash -i '/var/www/tools/codeception/test.sh' --env multi --suite wpunit
.PHONY: run-wpunit-multi

test-acceptance: ## Run acceptance tests on 'single_test' test environment.
	docker-compose -f 'docker/docker-compose.yml' exec wp_single_test \
	bash -i '/var/www/tools/codeception/test.sh' --env single_test --suite acceptance
.PHONY: test-acceptance

test-acceptance-multi: ## Run acceptance tests on 'multi_test' test environment.
	docker-compose -f 'docker/docker-compose.yml' exec wp_multi_test \
	bash -i '/var/www/tools/codeception/test.sh' --env multi_test --suite acceptance
.PHONY: test-acceptance-multi

test-wpunit: ## ## Run wpunit tests on 'single_test' test environment.
	docker-compose -f 'docker/docker-compose.yml' exec wp_single_test \
	bash -i '/var/www/tools/codeception/test.sh' --env single_test --suite wpunit
.PHONY: test-wpunit

test-wpunit-multi: ## ## Run wpunit tests on 'multi_test' test environment.
	docker-compose -f 'docker/docker-compose.yml' exec wp_multi_test \
	bash -i '/var/www/tools/codeception/test.sh' --env multi_test --suite wpunit
.PHONY: test-wpunit-multi
