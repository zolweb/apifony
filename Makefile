.DEFAULT_GOAL := qa
.PHONY: qa

# Without this the containers run as root and leave the generated bundle, the Composer cache and
# the Symfony cache owned by root on the host.
UID := $(shell id -u)
GID := $(shell id -g)

qa:
	docker-compose run --rm --user "$(UID):$(GID)" php-lowest php -d memory_limit=-1 /usr/bin/composer update --prefer-dist --prefer-lowest --prefer-stable
	docker-compose run --rm --user "$(UID):$(GID)" php-lowest php vendor/bin/php-cs-fixer -vv fix --diff
	docker-compose run --rm --user "$(UID):$(GID)" php-lowest php vendor/bin/phpstan --memory-limit=-1 analyse
	docker-compose run --rm --user "$(UID):$(GID)" php-lowest rm -rf tests/bundle/* var/cache/
	docker-compose run --rm --user "$(UID):$(GID)" php-lowest ./apifony generate-bundle TestOpenApiServer Zol\\Apifony\\Tests\\TestOpenApiServer zol/test-openapi-server tests/openapi.yaml tests/bundle
	docker-compose run --rm --user "$(UID):$(GID)" php-lowest php -d memory_limit=-1 /usr/bin/composer update --prefer-dist --prefer-lowest --prefer-stable
	docker-compose run --rm --user "$(UID):$(GID)" php-lowest php vendor/bin/phpstan --memory-limit=-1 analyse -c phpstan-bundle.neon
	docker-compose run --rm --user "$(UID):$(GID)" php-lowest php vendor/bin/php-cs-fixer -vv check --diff --config=.php-cs-fixer-bundle.dist.php
	docker-compose run --rm --user "$(UID):$(GID)" -e SYMFONY_DEPRECATIONS_HELPER="max[direct]=0" php-lowest php vendor/bin/phpunit
	docker-compose run --rm --user "$(UID):$(GID)" php-highest php -d memory_limit=-1 /usr/bin/composer update --prefer-dist
	docker-compose run --rm --user "$(UID):$(GID)" php-highest rm -rf tests/bundle/* var/cache/
	docker-compose run --rm --user "$(UID):$(GID)" php-highest ./apifony generate-bundle TestOpenApiServer Zol\\Apifony\\Tests\\TestOpenApiServer zol/test-openapi-server tests/openapi.yaml tests/bundle
	docker-compose run --rm --user "$(UID):$(GID)" php-highest php -d memory_limit=-1 /usr/bin/composer update --prefer-dist
	docker-compose run --rm --user "$(UID):$(GID)" php-highest php vendor/bin/phpstan --memory-limit=-1 analyse -c phpstan-bundle.neon
	docker-compose run --rm --user "$(UID):$(GID)" -e SYMFONY_DEPRECATIONS_HELPER="max[direct]=0" php-highest php vendor/bin/phpunit