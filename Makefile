# Docker Compose (For Local Development)
#
up: docker-compose-down docker-compose-up
down: docker-compose-down
clean: docker-cleanup
kill: docker-compose-down docker-cleanup
logs: docker-compose-logs
cu: composer-update
ci: composer-install
test: phpunit-tests

#
# Docker Compose
#
docker-compose-up:
	@echo "===============  Docker Compose [UP](with build)  ======================="
	docker compose up --build -d
docker-compose-down:
	@echo "====================      Docker Compose [DOWN]      ====================="
	docker compose down
docker-compose-logs:
	@echo "====================      Docker Compose [Logs]      ====================="
	docker compose logs -f
docker-cleanup:
	@echo "====================      Docker Cleanup      ====================="
	docker image prune -af
composer-update:
	@echo "====================      Composer UPDATE      ====================="
	docker exec -it app-php composer update
composer-install:
	@echo "====================      Composer INSTALL      ====================="
	docker exec -it app-php composer install
phpunit-tests:
	@echo "====================      PHPUnit tests      ====================="
	docker exec -it app-php vendor/bin/phpunit --display-warnings --display-skipped --display-deprecations --display-errors --display-notices