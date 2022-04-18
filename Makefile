# customization

PACKAGE_NAME = icanboogie/bind-symfony-dependency-injection
PACKAGE_VERSION = 5.0
PHPUNIT = vendor/bin/phpunit

# do not edit the following lines

.PHONY: usage
usage:
	@echo "test:  Runs the test suite.\ndoc:   Creates the documentation.\nclean: Removes the documentation, the dependencies and the Composer files."

vendor:
	@COMPOSER_ROOT_VERSION=$(PACKAGE_VERSION) composer install

.PHONY: update
update:
	@COMPOSER_ROOT_VERSION=$(PACKAGE_VERSION) composer update

.PHONY: autoload
autoload: vendor
	@composer dump-autoload

test-dependencies: vendor

.PHONY: test
test: test-dependencies
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
	@XDEBUG_MODE=off vendor/bin/phpstan

.PHONY: clean
clean:
	@rm -fR build
	@rm -fR vendor
	@rm -f composer.lock
