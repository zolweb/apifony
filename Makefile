zol_common_version=v8.1.0
zol_common_entrypoint=.zol/zol-common/makefile/entrypoint.mk

ifneq ("$(wildcard $(zol_common_entrypoint))","")
  include $(zol_common_entrypoint)
endif

zol-common:
	@$(shell mkdir -p .zol)
	@$(shell rm -rf .zol/zol-common || true)
	@cd .zol && git clone --branch $(zol_common_version) git@gitlab.com:zolteam/zol-common.git

test:
	@$(RUNNER_DOCKER_EXEC) 'rm -rf /var/www/html/tests/bundle/*'
	@$(RUNNER_DOCKER_EXEC) './ogen TestOpenApiServer Zol\\Ogen\\Tests\\TestOpenApiServer zol/test-openapi-server /var/www/html/tests/openapi.yaml /var/www/html/tests/bundle'
	@$(RUNNER_DOCKER_EXEC) 'php vendor/bin/phpunit'

phpstan-bundle:
	@$(RUNNER_DOCKER_EXEC) 'php vendor/bin/phpstan analyse -c phpstan-bundle.neon'

php-cs-fixer-fix-bundle:
	@$(RUNNER_DOCKER_EXEC) 'php vendor/bin/php-cs-fixer -vv fix --diff --config=.php-cs-fixer-bundle.dist.php'

composer-update-cli-lowest=php -d memory_limit=-1 /usr/bin/composer update $(composer_default_options) --prefer-dist --prefer-lowest --prefer-stable $(COMMAND_ARGS)

composer-update-lowest:
	@echo "$(step_start) Composer update lowest $(step_end)"
	@$(COMPOSER_RUN) '$(composer-update-cli-lowest)'
	@$(CHECK_COMPOSER_CACHE)

composer-update-cli-highest=php -d memory_limit=-1 /usr/bin/composer update $(composer_default_options) --prefer-dist $(COMMAND_ARGS)

composer-update-highest:
	@echo "$(step_start) Composer update highest $(step_end)"
	@$(COMPOSER_RUN) '$(composer-update-cli-highest)'
	@$(CHECK_COMPOSER_CACHE)
