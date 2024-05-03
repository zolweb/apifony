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
