.PHONY: help setup start stop logs test-phpunit test-endpoints dumpautoload

help: ## Show Laravel-specific commands
	@echo "Laravel API - Development Commands"
	@echo "==================================="
	@echo "Available commands:"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-20s\033[0m %s\n", $$1, $$2}'

setup: ## Setup Laravel application and refresh autoload
	@echo "🔧 Setting up Laravel application..."
	@if [ ! -d "vendor" ]; then \
		echo "📦 Starting Docker containers..."; \
		docker-compose up -d; \
		echo "⏳ Waiting for containers to be ready..."; \
		sleep 5; \
		echo "📦 Installing composer dependencies..."; \
		docker-compose exec -T app composer install; \
		echo "✅ Vendor directory created at vendor/"; \
	else \
		echo "✅ Vendor directory already exists"; \
		echo "🔄 Ensuring containers are running..."; \
		docker-compose up -d; \
	fi
	@echo "🔄 Refreshing autoload files..."
	@docker-compose exec -T app composer dumpautoload
	@echo "✅ Laravel setup complete!"

start: setup ## Start Laravel development environment
	@echo "✅ Laravel application started at http://localhost:8000"

stop: ## Stop Laravel development environment
	@echo "🛑 Stopping Laravel development environment..."
	@docker-compose down
	@echo "✅ Laravel application stopped"

logs: ## Show Laravel application logs
	@docker-compose logs -f app

dumpautoload: ## Refresh composer autoload files
	@echo "🔄 Refreshing autoload files..."
	@docker-compose exec -T app composer dumpautoload
	@echo "✅ Autoload refreshed!"

test-phpunit: ## Run PHPUnit tests (Unit and Feature tests)
	@echo "🧪 Running PHPUnit tests..."
	@if docker ps | grep -q laravel-api; then \
		echo "✅ Laravel containers running"; \
		echo ""; \
		docker-compose exec -T app php artisan test; \
	else \
		echo "❌ Laravel containers not running"; \
		echo "   Start with: make start"; \
		exit 1; \
	fi

test-endpoints: ## Test Laravel API endpoints with curl
	@echo "🧪 Testing Laravel application..."
	@if docker ps | grep -q laravel-api; then \
		echo "✅ Laravel containers running"; \
		echo ""; \
		echo "Testing PetStore endpoints:"; \
		echo "  GET /api/health"; \
		curl -s http://localhost:8000/api/health | jq . || echo "❌ Health check failed"; \
		echo ""; \
		echo "  GET /v2/pets"; \
		curl -s http://localhost:8000/v2/pets | jq . || echo "⚠️  Pets endpoint (may be empty)"; \
		echo ""; \
		echo "  GET /v2/pets?limit=3"; \
		curl -s 'http://localhost:8000/v2/pets?limit=3' | jq . || echo "⚠️  Pets endpoint with params (may be empty)"; \
		echo ""; \
		echo "Testing TicTacToe V2 endpoints:"; \
		echo "  POST /v1/games (create game)"; \
		GAME_ID=$$(curl -s -X POST http://localhost:8000/v1/games -H "Authorization: Bearer test-token" -H "Content-Type: application/json" -d '{"mode":"ai_easy"}' | jq -r '.id // "1"'); \
		echo "  Created game ID: $$GAME_ID"; \
		echo ""; \
		echo "  GET /v1/games/$$GAME_ID/board"; \
		curl -s http://localhost:8000/v1/games/$$GAME_ID/board -H "Authorization: Bearer test-token" | jq . || echo "⚠️  Board endpoint failed"; \
		echo ""; \
		echo "  GET /v1/games/$$GAME_ID/board/1/1"; \
		curl -s http://localhost:8000/v1/games/$$GAME_ID/board/1/1 -H "Authorization: Bearer test-token" | jq . || echo "⚠️  Square endpoint failed"; \
		echo ""; \
		echo "  PUT /v1/games/$$GAME_ID/board/1/1 (mark: X)"; \
		curl -s -X PUT http://localhost:8000/v1/games/$$GAME_ID/board/1/1 -H "Authorization: Bearer test-token" -H "Content-Type: application/json" -d '{"mark":"X"}' | jq . || echo "⚠️  Put square endpoint failed"; \
	else \
		echo "❌ Laravel containers not running"; \
		echo "   Start with: make start"; \
	fi
