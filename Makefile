# no buildin rules and variables
MAKEFLAGS =+ -rR --warn-undefined-variables

.PHONY: composer-install composer-update phpstan cs-fixer examples docker run

-include variables.mk
export

ifndef CONFLUENT_VERSION
    CONFLUENT_VERSION = 4.0.0
endif

ifndef COMPOSER
    COMPOSER = bin/composer.phar
endif

ifndef PHP
    PHP = bin/php
endif

ifndef PHP_VERSION
    PHP_VERSION = 7.1
endif

ifndef XDEBUG_VERSION
    XDEBUG_VERSION = 2.5.5
endif

docker:
	docker build \
	  --build-arg PHP_VERSION=$(PHP_VERSION) \
	  --build-arg XDEBUG_VERSION=$(XDEBUG_VERSION) \
	  -t avro-serializer-php:$(PHP_VERSION) \
	  -f Dockerfile \
	  .

composer-install:
	PHP_VERSION=$(PHP_VERSION) $(PHP) $(COMPOSER) install --no-interaction --no-progress --no-suggest --no-scripts

composer-update:
	PHP_VERSION=$(PHP_VERSION) $(PHP) $(COMPOSER) update --no-interaction --no-progress --no-suggest --no-scripts

phpstan:
	PHP_VERSION=$(PHP_VERSION) $(PHP) vendor/bin/phpstan analyse -l 7 src

cs-fixer:
	PHP_VERSION=$(PHP_VERSION) $(PHP) vendor/bin/php-cs-fixer fix --config=.php_cs.dist -v --dry-run \
	  --path-mode=intersection --allow-risky=yes src test

cs-fixer-modify:
	PHP_VERSION=$(PHP_VERSION) $(PHP) vendor/bin/php-cs-fixer fix --config=.php_cs.dist -v \
	  --path-mode=intersection --allow-risky=yes src test

phpunit:
	PHP_VERSION=$(PHP_VERSION) $(PHP) vendor/bin/phpunit

run:
	PHP_VERSION=$(PHP_VERSION) $(PHP) $(ARGS)

ci-local: cs-fixer phpstan phpunit

examples:
	PHP_VERSION=$(PHP_VERSION) $(PHP) examples/*

install-phars:
	curl http://cs.sensiolabs.org/download/php-cs-fixer-v2.phar -o bin/php-cs-fixer.phar -LR -z bin/php-cs-fixer.phar
	chmod a+x bin/php-cs-fixer.phar

platform:
	docker-compose down
	docker-compose up -d
	sleep 15

clean:
	docker-compose down

benchmark:
	docker-compose down
	docker-compose up -d
	sleep 15
	PHP_VERSION=$(PHP_VERSION) $(PHP) ./vendor/bin/phpbench run benchmarks/AvroEncodingBench.php --report=aggregate --retry-threshold=5
	docker-compose down
