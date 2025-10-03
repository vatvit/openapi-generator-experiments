.PHONY: help generate-scaffolding generate-petstore generate-tictactoe extract-templates extract-laravel-templates validate-spec clean test-laravel test-complete start-laravel stop-laravel logs-laravel

help: ## Show this help message
	@echo "Laravel OpenAPI Generator - Development Commands"
	@echo "================================================"
	@echo "Available commands:"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-25s\033[0m %s\n", $$1, $$2}'
	@echo ""
	@echo "🚀 Quick Start:"
	@echo "   1. make generate-scaffolding  # Generate Laravel scaffolding from OpenAPI spec"
	@echo "   2. cd laravel-api && docker-compose up -d  # Start Laravel application"
	@echo "   3. make test-laravel          # Test the Laravel API endpoints"

# Main scaffolding generators
generate-scaffolding: generate-petstore generate-tictactoe ## Generate all API scaffolding libraries

generate-petstore: ## Generate PetStore API scaffolding
	@echo "🏗️  Generating PetStore API scaffolding..."
	@rm -rf laravel-api/generated/petstore
	@mkdir -p laravel-api/generated
	@echo "📋 Using OpenAPI spec: specs/petshop-extended.yaml"
	@docker run --rm -v $$(pwd):/local openapitools/openapi-generator-cli generate \
		-i /local/specs/petshop-extended.yaml \
		-g php-laravel \
		-o /local/laravel-api/generated/petstore \
		-c /local/config/petstore-scaffolding-config.json \
		--template-dir /local/templates/php-laravel-scaffolding
	@echo "✅ PetStore API scaffolding generated!"
	@echo "📁 Output: laravel-api/generated/petstore"

generate-tictactoe: ## Generate TicTacToe API scaffolding
	@echo "🏗️  Generating TicTacToe API scaffolding..."
	@rm -rf laravel-api/generated/tictactoe
	@mkdir -p laravel-api/generated
	@echo "📋 Using OpenAPI spec: specs/tictactoe.json"
	@docker run --rm -v $$(pwd):/local openapitools/openapi-generator-cli generate \
		-i /local/specs/tictactoe.json \
		-g php-laravel \
		-o /local/laravel-api/generated/tictactoe \
		-c /local/config/tictactoe-scaffolding-config.json \
		--template-dir /local/templates/php-laravel-scaffolding
	@echo "✅ TicTacToe API scaffolding generated!"
	@echo "📁 Output: laravel-api/generated/tictactoe"

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
	@rm -rf laravel-api/generated/petstore
	@rm -rf laravel-api/generated/tictactoe
	@echo "✅ Generated files cleaned!"

# Testing targets
test-complete: ## Complete test: generate scaffolding, start Laravel, and test endpoints
	@echo "🎯 Running Complete Solution Test"
	@echo "=================================="
	@echo ""
	@echo "📋 Step 1: Validating OpenAPI specification..."
	@$(MAKE) validate-spec
	@echo ""
	@echo "📋 Step 2: Generating Laravel scaffolding..."
	@$(MAKE) generate-scaffolding
	@echo ""
	@echo "📋 Step 3: Checking generated scaffolding..."
	@if [ -d "laravel-api/generated/petstore" ] && [ -d "laravel-api/generated/tictactoe" ]; then \
		echo "✅ PetStore scaffolding generated successfully"; \
		find laravel-api/generated/petstore -name "*.php" -type f | wc -l | xargs echo "   📄 PetStore files:"; \
		echo "✅ TicTacToe scaffolding generated successfully"; \
		find laravel-api/generated/tictactoe -name "*.php" -type f | wc -l | xargs echo "   📄 TicTacToe files:"; \
	else \
		echo "❌ Scaffolding generation failed"; \
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
	@echo "🎉 Complete test finished!"

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
		echo "Testing TicTacToe endpoints:"; \
		echo "  GET /tictactoe/board"; \
		curl -s http://localhost:8000/tictactoe/board | jq . || echo "⚠️  Board endpoint failed"; \
		echo ""; \
		echo "  GET /tictactoe/board/1/1"; \
		curl -s http://localhost:8000/tictactoe/board/1/1 | jq . || echo "⚠️  Square endpoint failed"; \
		echo ""; \
		echo "  PUT /tictactoe/board/1/1 (mark: X)"; \
		curl -s -X PUT http://localhost:8000/tictactoe/board/1/1 -H "Content-Type: application/json" -d '"X"' | jq . || echo "⚠️  Put square endpoint failed"; \
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
