zol_common_version=v8.2.0
zol_common_entrypoint=.zol/zol-common/makefile/entrypoint.mk

ifneq ("$(wildcard $(zol_common_entrypoint))","")
  include $(zol_common_entrypoint)
endif

zol-common:
	@$(shell mkdir -p .zol)
	@$(shell rm -rf .zol/zol-common || true)
	@cd .zol && git clone --branch $(zol_common_version) git@gitlab.com:zolteam/zol-common.git

test:
	make start
	@$(RUNNER_DOCKER_EXEC) './ogen TestOpenApiServer Zol\\TestOpenApiServer zol/test-openapi-server /var/www/html/tests/openapi.yaml /var/www/html/tests/bundle'
	@$(RUNNER_DOCKER_EXEC) "composer install --dev"
	@$(RUNNER_DOCKER_EXEC) "php vendor/bin/phpunit"
	make stop
