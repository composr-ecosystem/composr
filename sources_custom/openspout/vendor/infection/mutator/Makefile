SHELL=bash
.DEFAULT_GOAL := help

# See https://tech.davis-hansson.com/p/make/
MAKEFLAGS += --warn-undefined-variables
MAKEFLAGS += --no-builtin-rules

.PHONY: help
help:
	@printf "\033[33mUsage:\033[0m\n  make TARGET\n\n\033[32m#\n# Commands\n#---------------------------------------------------------------------------\033[0m\n\n"
	@fgrep -h "##" $(MAKEFILE_LIST) | fgrep -v fgrep | sed -e 's/\\$$//' | sed -e 's/##//' | awk 'BEGIN {FS = ":"}; {printf "\033[33m%s:\033[0m%s\n", $$1, $$2}'

#
# Variables
#---------------------------------------------------------------------------
PHP_CS_FIXER=./.tools/php-cs-fixer
PHP_CS_FIXER_URL="https://github.com/FriendsOfPHP/PHP-CS-Fixer/releases/download/v3.52.0/php-cs-fixer.phar"
PHP_CS_FIXER_CACHE=.php_cs.cache

PHPSTAN=./vendor/bin/phpstan
RECTOR=./vendor/bin/rector

PSALM=./.tools/psalm
PSALM_URL="https://github.com/vimeo/psalm/releases/download/5.11.0/psalm.phar"

PHPUNIT=vendor/phpunit/phpunit/phpunit

FLOCK=./devTools/flock
COMMIT_HASH=$(shell git rev-parse --short HEAD)

PHPUNIT_GROUP=default

#
# Commands (phony targets)
#---------------------------------------------------------------------------

.PHONY: cs
cs:	  	 	## Runs PHP-CS-Fixer
cs: $(PHP_CS_FIXER)
	$(PHP_CS_FIXER) fix -v --cache-file=$(PHP_CS_FIXER_CACHE) --diff
	LC_ALL=C sort -u .gitignore -o .gitignore

.PHONY: cs-check
cs-check:		## Runs PHP-CS-Fixer in dry-run mode
cs-check: $(PHP_CS_FIXER)
	$(PHP_CS_FIXER) fix -v --cache-file=$(PHP_CS_FIXER_CACHE) --diff --dry-run
	LC_ALL=C sort -c -u .gitignore

.PHONY: phpstan
phpstan: vendor $(PHPSTAN)
	$(PHPSTAN) analyse --configuration devTools/phpstan-src.neon --no-interaction --no-progress
	$(PHPSTAN) analyse --configuration devTools/phpstan-tests.neon --no-interaction --no-progress

.PHONY: phpstan-baseline
phpstan-baseline: vendor $(PHPSTAN)
	$(PHPSTAN) analyse --configuration devTools/phpstan-src.neon --no-interaction --no-progress --generate-baseline devTools/phpstan-src-baseline.neon || true
	$(PHPSTAN) analyse --configuration devTools/phpstan-tests.neon --no-interaction --no-progress --generate-baseline devTools/phpstan-tests-baseline.neon || true

.PHONY: psalm-baseline
psalm-baseline: vendor
	$(PSALM) --threads=max --set-baseline=psalm-baseline.xml

.PHONY: psalm
psalm: vendor $(PSALM)
	$(PSALM) --threads=max

.PHONY: rector
rector: vendor $(RECTOR)
	$(RECTOR) process

.PHONY: rector-check
rector-check: vendor $(RECTOR)
	$(RECTOR) process --dry-run

.PHONY: validate
validate:
	composer validate --strict

.PHONY: autoreview
autoreview: 	 	## Runs various checks (static analysis & AutoReview test suite)
autoreview: phpstan psalm validate rector-check

#
# Rules from files (non-phony targets)
#---------------------------------------------------------------------------

$(PHP_CS_FIXER): Makefile
	wget -q $(PHP_CS_FIXER_URL) --output-document=$(PHP_CS_FIXER)
	chmod a+x $(PHP_CS_FIXER)
	touch -c $@

$(PHPSTAN): vendor
	touch -c $@

$(PSALM): Makefile
	wget -q $(PSALM_URL) --output-document=$(PSALM)
	chmod a+x $(PSALM)
	touch -c $@

vendor: composer.lock
	composer install --prefer-dist
	touch -c $@

composer.lock: composer.json
	composer install --prefer-dist
	touch -c $@

$(PHPUNIT): vendor phpunit.xml.dist
	touch -c $@

phpunit.xml.dist:
	# Not updating phpunit.xml with:
	# phpunit --migrate-configuration || true
	touch -c $@
