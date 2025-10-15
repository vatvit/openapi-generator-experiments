.PHONY: help generate-server generate-petshop generate-tictactoe extract-templates extract-laravel-templates validate-spec clean test-laravel test-complete start-laravel stop-laravel logs-laravel

help: ## Show this help message
	@echo "Laravel OpenAPI Generator - Development Commands"
	@echo "================================================"
	@echo "Available commands:"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-25s\033[0m %s\n", $$1, $$2}'
	@echo ""
	@echo "🚀 Quick Start:"
	@echo "   1. make generate-server  # Generate Laravel server from OpenAPI spec"
	@echo "   2. cd laravel-api && docker-compose up -d  # Start Laravel application"
	@echo "   3. make test-laravel          # Test the Laravel API endpoints"

# Main server generators
generate-server: generate-petshop generate-tictactoe ## Generate all API server libraries

generate-petshop: ## Generate PetStore API server
	@echo "🏗️  Generating PetStore API server..."
	@rm -rf laravel-api/generated-v2/petstore
	@mkdir -p laravel-api/generated-v2
	@echo "📋 Using OpenAPI spec: specs/petshop-extended.yaml"
	@docker run --rm -v $$(pwd):/local openapitools/openapi-generator-cli generate \
		-i /local/specs/petshop-extended.yaml \
		-g php-laravel \
		-o /local/laravel-api/generated-v2/petstore \
		-c /local/config-v2/petshop-server-config.json \
		--template-dir /local/templates/php-laravel-server-v2
	@echo "✅ PetStore API server generated!"
	@echo "📋 Post-processing: Merging tag-based controllers (if any)..."
	@docker run --rm -v $$(pwd):/app -w /app php:8.3-cli php scripts/merge-controllers-simple.php \
		laravel-api/generated-v2/petstore/lib/Http/Controllers \
		laravel-api/generated-v2/petstore/lib/Http/Controllers/DefaultController.php || echo "ℹ️  No duplicate controllers to merge"
	@echo "✅ PetStore server completed!"
	@echo "📁 Output: laravel-api/generated-v2/petstore"

generate-tictactoe: ## Generate TicTacToe API server
	@echo "🏗️  Generating TicTacToe API server..."
	@rm -rf laravel-api/generated-v2/tictactoe
	@mkdir -p laravel-api/generated-v2
	@echo "📋 Pre-processing: Removing tags from OpenAPI spec..."
	@./scripts/remove-tags.sh specs/tictactoe.json specs/tictactoe-no-tags.json
	@echo ""
	@echo "📋 Generating from spec without tags: specs/tictactoe-no-tags.json"
	@docker run --rm -v $$(pwd):/local openapitools/openapi-generator-cli generate \
		-i /local/specs/tictactoe-no-tags.json \
		-g php-laravel \
		-o /local/laravel-api/generated-v2/tictactoe \
		-c /local/config-v2/tictactoe-server-config.json \
		--template-dir /local/templates/php-laravel-server-v2
	@echo "✅ TicTacToe API server generated!"
	@echo "ℹ️  Security interfaces generated via templates (SecurityInterfaces.php, SecurityValidator.php)"
	@echo "📁 Output: laravel-api/generated-v2/tictactoe"

# Utilities
extract-templates: ## Extract default PHP client templates for customization
	@./scripts/extract-default-templates.sh

extract-laravel-templates: ## Extract default php-laravel templates for customization
	@echo "📦 Extracting php-laravel templates..."
	@mkdir -p templates/php-laravel-default
	@docker run --rm -v $$(pwd):/local openapitools/openapi-generator-cli author template \
		-g php-laravel -o /local/templates/php-laravel-default
	@echo "✅ Laravel templates extracted to: templates/php-laravel-default/"

validate-spec: ## Validate the OpenAPI specification
	@echo "📋 Validating PetStore OpenAPI specification..."
	@docker run --rm -v $$(pwd):/local openapitools/openapi-generator-cli validate \
		-i /local/specs/petshop-extended.yaml
	@echo "✅ PetStore specification is valid!"
	@echo ""
	@echo "📋 Validating TicTacToe OpenAPI specification..."
	@docker run --rm -v $$(pwd):/local openapitools/openapi-generator-cli validate \
		-i /local/specs/tictactoe.json
	@echo "✅ TicTacToe specification is valid!"

clean: ## Clean generated files
	@echo "🧹 Cleaning generated files..."
	@rm -rf laravel-api/generated-v2/petstore
	@rm -rf laravel-api/generated-v2/tictactoe
	@echo "✅ Generated files cleaned!"

# Testing targets
test-complete: ## Complete test: generate server, start Laravel, and test endpoints
	@echo "🎯 Running Complete Test"
	@echo "========================"
	@echo ""
	@echo "📋 Step 1: Validating OpenAPI specifications..."
	@docker run --rm -v $$(pwd):/local openapitools/openapi-generator-cli validate \
		-i /local/specs/petshop-extended.yaml
	@echo "✅ PetStore specification is valid!"
	@docker run --rm -v $$(pwd):/local openapitools/openapi-generator-cli validate \
		-i /local/specs/tictactoe.json
	@echo "✅ TicTacToe specification is valid!"
	@echo ""
	@echo "📋 Step 2: Generating server for both specs..."
	@$(MAKE) generate-server
	@echo ""
	@echo "📋 Step 3: Checking generated server..."
	@if [ -d "laravel-api/generated-v2/petstore" ]; then \
		echo "✅ PetStore server generated successfully"; \
		find laravel-api/generated-v2/petstore -name "*.php" -type f | wc -l | xargs echo "   📄 PetStore files:"; \
	else \
		echo "❌ PetStore server generation failed"; \
		exit 1; \
	fi
	@if [ -d "laravel-api/generated-v2/tictactoe" ]; then \
		echo "✅ TicTacToe server generated successfully"; \
		find laravel-api/generated-v2/tictactoe -name "*.php" -type f | wc -l | xargs echo "   📄 TicTacToe files:"; \
		if [ -f "laravel-api/generated-v2/tictactoe/lib/Http/Controllers/DefaultController.php" ]; then \
			echo "✅ DefaultController created successfully"; \
			grep -c "public function" laravel-api/generated-v2/tictactoe/lib/Http/Controllers/DefaultController.php | xargs echo "   📝 Methods:"; \
		else \
			echo "❌ DefaultController not found"; \
			exit 1; \
		fi; \
	else \
		echo "❌ TicTacToe server generation failed"; \
		exit 1; \
	fi
	@echo ""
	@echo "📋 Step 4: Ensuring Laravel is running..."
	@if ! docker ps | grep -q laravel-api; then \
		echo "🚀 Starting Laravel containers..."; \
		cd laravel-api && docker-compose up -d; \
		echo "⏳ Waiting for Laravel to be ready..."; \
		sleep 5; \
	else \
		echo "✅ Laravel containers already running"; \
	fi
	@echo ""
	@echo "📋 Step 5: Running composer dumpautoload..."
	@cd laravel-api && docker-compose exec -T app composer dumpautoload || echo "⚠️  Autoload update skipped"
	@echo ""
	@echo "📋 Step 6: Testing API endpoints..."
	@$(MAKE) test-laravel
	@echo ""
	@echo "🎉 Complete test finished for both PetStore and TicTacToe!"

test-laravel: ## Test Laravel application endpoints
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
		echo "   Start with: cd laravel-api && docker-compose up -d"; \
	fi

start-laravel: ## Start Laravel development environment
	@echo "🚀 Starting Laravel development environment..."
	@cd laravel-api && docker-compose up -d
	@echo "✅ Laravel application started at http://localhost:8000"

stop-laravel: ## Stop Laravel development environment
	@echo "🛑 Stopping Laravel development environment..."
	@cd laravel-api && docker-compose down
	@echo "✅ Laravel application stopped"

logs-laravel: ## Show Laravel application logs
	@cd laravel-api && docker-compose logs -f app
