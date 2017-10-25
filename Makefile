
quick-test:
	./bin/ci

benchmark:
	docker-compose down
	docker-compose up -d
	sleep 15
	./vendor/bin/phpbench run benchmarks/AvroEncodingBench.php --report=aggregate --retry-threshold=5
	docker-compose down
