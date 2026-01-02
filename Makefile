.DEFAULT_GOAL := qa
.PHONY: qa

qa:
	docker-compose run --rm php-lowest php -d memory_limit=-1 /usr/bin/composer update --prefer-dist --prefer-lowest --prefer-stable
	docker-compose run --rm php-lowest php vendor/bin/php-cs-fixer -vv fix --diff
	docker-compose run --rm php-lowest php vendor/bin/phpstan --memory-limit=-1 analyse
	docker-compose run --rm php-lowest rm -rf tests/bundle/*
	docker-compose run --rm php-lowest ./apifony generate-bundle TestOpenApiServer Zol\\Apifony\\Tests\\TestOpenApiServer zol/test-openapi-server tests/openapi.yaml tests/bundle
	docker-compose run --rm php-lowest php -d memory_limit=-1 /usr/bin/composer update --prefer-dist --prefer-lowest --prefer-stable
	docker-compose run --rm php-lowest php vendor/bin/phpstan --memory-limit=-1 analyse -c phpstan-bundle.neon
	docker-compose run --rm php-lowest php vendor/bin/php-cs-fixer -vv check --diff --config=.php-cs-fixer-bundle.dist.php
	docker-compose run --rm php-lowest php vendor/bin/phpunit
	docker-compose run --rm php-highest php -d memory_limit=-1 /usr/bin/composer update --prefer-dist
	docker-compose run --rm php-highest rm -rf tests/bundle/*
	docker-compose run --rm php-highest ./apifony generate-bundle TestOpenApiServer Zol\\Apifony\\Tests\\TestOpenApiServer zol/test-openapi-server tests/openapi.yaml tests/bundle
	docker-compose run --rm php-highest php -d memory_limit=-1 /usr/bin/composer update --prefer-dist
	docker-compose run --rm php-highest php vendor/bin/phpstan --memory-limit=-1 analyse -c phpstan-bundle.neon
	docker-compose run --rm php-highest php vendor/bin/phpunit