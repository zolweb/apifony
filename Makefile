#------------------------------------------------------------------------------------------------
# Project VARS
#------------------------------------------------------------------------------------------------
zol_common_version=v8.0.0
zol_common_entrypoint=.zol/zol-common/makefile/entrypoint.mk

zflow_version=v2.3.0
zflow_entrypoint=.zol/zflow/makefile/entrypoint.mk
ansible_target_runner=zflow3


db_service_name=dbverdie


#------------------------------------------------------------------------------------------------
# Include zol common library
#------------------------------------------------------------------------------------------------
ifneq ("$(wildcard $(zol_common_entrypoint))","")
  include $(zol_common_entrypoint)
endif

zol-common:
	@$(shell mkdir -p .zol)
	@$(shell rm -rf .zol/zol-common || true)
	@cd .zol && git clone --branch $(zol_common_version) git@gitlab.com:zolteam/zol-common.git

#------------------------------------------------------------------------------------------------
# Include zflow library
#------------------------------------------------------------------------------------------------
ifneq ("$(wildcard $(zflow_entrypoint))","")
   include $(zflow_entrypoint)
endif

zflow:
	@$(shell mkdir -p .zol)
	@$(shell rm -rf .zol/zflow || true)
	@cd .zol && git clone --branch $(zflow_version) git@gitlab.com:zolteam/zflow.git

#------------------------------------------------------------------------------------------------
# Project related commands
#------------------------------------------------------------------------------------------------
install-app: clean-app build composer-install yarn-install webpack-build start assets-install cache-clear

# Once there are migrations and fixtures in your project, you can replace this target by :
# install-db: database fixtures-append
install-db: database-drop database-create

install: install-app wait-for-database install-db

# This target is needed to deploy on AWS, feel free to tune it
build-aws: clean-app build composer-install composer-dump yarn-install webpack-build start assets-install

gen:
	rm -rf symfony/openapi/invoicing/bundle/*
	# wget -O symfony/openapi/invoicing/openapi.yaml https://stoplight.io/api/v1/projects/bfav-zol/zol-skeleton/nodes/zol-invoicing.yaml
	@$(RUNNER_DOCKER_EXEC) '$(symfony_console_path) gen -vvv'
	make cc
