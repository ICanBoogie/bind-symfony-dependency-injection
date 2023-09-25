# customization

PACKAGE_NAME = icanboogie/bind-symfony-dependency-injection
PHPUNIT = vendor/bin/phpunit

# do not edit the following lines

.PHONY: usage
usage:
	@echo "test:  Runs the test suite.\ndoc:   Creates the documentation.\nclean: Removes the documentation, the dependencies and the Composer files."

vendor:
	@composer install

.PHONY: update
update:
	@composer update

.PHONY: autoload
autoload: vendor
	@composer dump-autoload

test-dependencies: vendor

.PHONY: test
test: test-dependencies clean-sandbox
	@$(PHPUNIT)

.PHONY: test-coverage
test-coverage: test-dependencies
	@mkdir -p build/coverage
	@$(PHPUNIT) --coverage-html ../build/coverage

.PHONY: test-coveralls
test-coveralls: test-dependencies
	@mkdir -p build/logs
	@$(PHPUNIT) --coverage-clover ../build/logs/clover.xml

.PHONY: test-container
test-container:
	@-docker-compose run --rm app bash
	@docker-compose down -v

.PHONY: lint
lint:
	@XDEBUG_MODE=off phpcs -s
	@XDEBUG_MODE=off vendor/bin/phpstan

.PHONY: clean-sandbox
clean-sandbox:
	@rm -f ./tests/sandbox/*
