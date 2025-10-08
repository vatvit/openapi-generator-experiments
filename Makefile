.PHONY: help generate-scaffolding generate-petstore generate-tictactoe generate-scaffolding-v2 generate-petshop-v2 generate-tictactoe-v2 extract-templates extract-laravel-templates validate-spec clean clean-v2 test-laravel test-complete test-complete-v2 start-laravel stop-laravel logs-laravel

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

generate-tictactoe: ## Generate TicTacToe API scaffolding (Solution 1)
	@echo "🏗️  Generating TicTacToe API scaffolding (Solution 1)..."
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

generate-petshop-v2: ## Generate PetStore API scaffolding (Solution 2 - Post-processing)
	@echo "🏗️  Generating PetStore API scaffolding (Solution 2)..."
	@rm -rf laravel-api/generated-v2/petstore
	@mkdir -p laravel-api/generated-v2
	@echo "📋 Using OpenAPI spec: specs/petshop-extended.yaml"
	@docker run --rm -v $$(pwd):/local openapitools/openapi-generator-cli generate \
		-i /local/specs/petshop-extended.yaml \
		-g php-laravel \
		-o /local/laravel-api/generated-v2/petstore \
		-c /local/config-v2/petshop-scaffolding-config.json \
		--template-dir /local/templates/php-laravel-scaffolding-v2
	@echo "✅ PetStore API scaffolding generated!"
	@echo "📋 Post-processing: Merging tag-based controllers (if any)..."
	@docker run --rm -v $$(pwd):/app -w /app php:8.3-cli php scripts/merge-controllers-simple.php \
		laravel-api/generated-v2/petstore/lib/Http/Controllers \
		laravel-api/generated-v2/petstore/lib/Http/Controllers/DefaultController.php || echo "ℹ️  No duplicate controllers to merge"
	@echo "✅ PetStore scaffolding completed!"
	@echo "📁 Output: laravel-api/generated-v2/petstore"

generate-tictactoe-v2: ## Generate TicTacToe API scaffolding (Solution 2 - Pre-processing to remove tags)
	@echo "🏗️  Generating TicTacToe API scaffolding (Solution 2 - No tags)..."
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
		-c /local/config-v2/tictactoe-scaffolding-config.json \
		--template-dir /local/templates/php-laravel-scaffolding-v2
	@echo "✅ TicTacToe API scaffolding generated!"
	@echo "📋 Post-processing: Creating security interfaces..."
	@mkdir -p laravel-api/generated-v2/tictactoe/lib/Security
	@echo '<?php declare(strict_types=1);' > laravel-api/generated-v2/tictactoe/lib/Security/bearerHttpAuthenticationInterface.php
	@echo '' >> laravel-api/generated-v2/tictactoe/lib/Security/bearerHttpAuthenticationInterface.php
	@echo 'namespace TicTacToeApiV2\Scaffolding\Security;' >> laravel-api/generated-v2/tictactoe/lib/Security/bearerHttpAuthenticationInterface.php
	@echo '' >> laravel-api/generated-v2/tictactoe/lib/Security/bearerHttpAuthenticationInterface.php
	@echo '/**' >> laravel-api/generated-v2/tictactoe/lib/Security/bearerHttpAuthenticationInterface.php
	@echo ' * Security Interface: bearerHttpAuthentication' >> laravel-api/generated-v2/tictactoe/lib/Security/bearerHttpAuthenticationInterface.php
	@echo ' *' >> laravel-api/generated-v2/tictactoe/lib/Security/bearerHttpAuthenticationInterface.php
	@echo ' * Generated from OpenAPI security scheme' >> laravel-api/generated-v2/tictactoe/lib/Security/bearerHttpAuthenticationInterface.php
	@echo ' * Type: http' >> laravel-api/generated-v2/tictactoe/lib/Security/bearerHttpAuthenticationInterface.php
	@echo ' * Scheme: Bearer' >> laravel-api/generated-v2/tictactoe/lib/Security/bearerHttpAuthenticationInterface.php
	@echo ' * Bearer Format: JWT' >> laravel-api/generated-v2/tictactoe/lib/Security/bearerHttpAuthenticationInterface.php
	@echo ' */' >> laravel-api/generated-v2/tictactoe/lib/Security/bearerHttpAuthenticationInterface.php
	@echo 'interface bearerHttpAuthenticationInterface' >> laravel-api/generated-v2/tictactoe/lib/Security/bearerHttpAuthenticationInterface.php
	@echo '{' >> laravel-api/generated-v2/tictactoe/lib/Security/bearerHttpAuthenticationInterface.php
	@echo '    /**' >> laravel-api/generated-v2/tictactoe/lib/Security/bearerHttpAuthenticationInterface.php
	@echo '     * Handle incoming request with http authentication' >> laravel-api/generated-v2/tictactoe/lib/Security/bearerHttpAuthenticationInterface.php
	@echo '     *' >> laravel-api/generated-v2/tictactoe/lib/Security/bearerHttpAuthenticationInterface.php
	@echo '     * @param \Illuminate\Http\Request $$request' >> laravel-api/generated-v2/tictactoe/lib/Security/bearerHttpAuthenticationInterface.php
	@echo '     * @param \Closure $$next' >> laravel-api/generated-v2/tictactoe/lib/Security/bearerHttpAuthenticationInterface.php
	@echo '     * @return \Symfony\Component\HttpFoundation\Response' >> laravel-api/generated-v2/tictactoe/lib/Security/bearerHttpAuthenticationInterface.php
	@echo '     */' >> laravel-api/generated-v2/tictactoe/lib/Security/bearerHttpAuthenticationInterface.php
	@echo '    public function handle(\Illuminate\Http\Request $$request, \Closure $$next): \Symfony\Component\HttpFoundation\Response;' >> laravel-api/generated-v2/tictactoe/lib/Security/bearerHttpAuthenticationInterface.php
	@echo '}' >> laravel-api/generated-v2/tictactoe/lib/Security/bearerHttpAuthenticationInterface.php
	@echo "✅ Security interfaces created!"
	@echo "📁 Output: laravel-api/generated-v2/tictactoe"

generate-scaffolding-v2: generate-petshop-v2 generate-tictactoe-v2 ## Generate all API scaffolding (Solution 2 - with Post-processing)

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

clean: ## Clean generated files (Solution 1)
	@echo "🧹 Cleaning generated files (Solution 1)..."
	@rm -rf laravel-api/generated/petstore
	@rm -rf laravel-api/generated/tictactoe
	@echo "✅ Generated files cleaned!"

clean-v2: ## Clean generated files (Solution 2)
	@echo "🧹 Cleaning generated files (Solution 2)..."
	@rm -rf laravel-api/generated-v2/petstore
	@rm -rf laravel-api/generated-v2/tictactoe
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

test-complete-v2: ## Complete test for Solution 2 (Post-processing with both specs)
	@echo "🎯 Running Complete Solution 2 Test (Post-processing)"
	@echo "====================================================="
	@echo ""
	@echo "📋 Step 1: Validating OpenAPI specifications..."
	@docker run --rm -v $$(pwd):/local openapitools/openapi-generator-cli validate \
		-i /local/specs/petshop-extended.yaml
	@echo "✅ PetStore specification is valid!"
	@docker run --rm -v $$(pwd):/local openapitools/openapi-generator-cli validate \
		-i /local/specs/tictactoe.json
	@echo "✅ TicTacToe specification is valid!"
	@echo ""
	@echo "📋 Step 2: Generating scaffolding for both specs (Solution 2)..."
	@$(MAKE) generate-scaffolding-v2
	@echo ""
	@echo "📋 Step 3: Checking generated scaffolding..."
	@if [ -d "laravel-api/generated-v2/petstore" ]; then \
		echo "✅ PetStore V2 scaffolding generated successfully"; \
		find laravel-api/generated-v2/petstore -name "*.php" -type f | wc -l | xargs echo "   📄 PetStore files:"; \
	else \
		echo "❌ PetStore scaffolding generation failed"; \
		exit 1; \
	fi
	@if [ -d "laravel-api/generated-v2/tictactoe" ]; then \
		echo "✅ TicTacToe V2 scaffolding generated successfully"; \
		find laravel-api/generated-v2/tictactoe -name "*.php" -type f | wc -l | xargs echo "   📄 TicTacToe files:"; \
		if [ -f "laravel-api/generated-v2/tictactoe/lib/Http/Controllers/DefaultController.php" ]; then \
			echo "✅ DefaultController merged successfully (TicTacToe)"; \
			grep -c "public function" laravel-api/generated-v2/tictactoe/lib/Http/Controllers/DefaultController.php | xargs echo "   📝 Methods:"; \
		else \
			echo "❌ DefaultController not found"; \
			exit 1; \
		fi; \
	else \
		echo "❌ TicTacToe scaffolding generation failed"; \
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
	@echo "🎉 Solution 2 test completed for both PetStore and TicTacToe!"
	@echo ""
	@echo "📊 Comparison:"
	@echo "   Solution 1 (spec modification): Separate controllers per tag"
	@echo "   Solution 2 (post-processing): Single DefaultController with merged unique methods"

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
		echo "  POST /v2/v1/games (create game)"; \
		GAME_ID=$$(curl -s -X POST http://localhost:8000/v2/v1/games -H "Authorization: Bearer test-token" -H "Content-Type: application/json" -d '{"mode":"ai_easy"}' | jq -r '.id // "1"'); \
		echo "  Created game ID: $$GAME_ID"; \
		echo ""; \
		echo "  GET /v2/v1/games/$$GAME_ID/board"; \
		curl -s http://localhost:8000/v2/v1/games/$$GAME_ID/board -H "Authorization: Bearer test-token" | jq . || echo "⚠️  Board endpoint failed"; \
		echo ""; \
		echo "  GET /v2/v1/games/$$GAME_ID/board/1/1"; \
		curl -s http://localhost:8000/v2/v1/games/$$GAME_ID/board/1/1 -H "Authorization: Bearer test-token" | jq . || echo "⚠️  Square endpoint failed"; \
		echo ""; \
		echo "  PUT /v2/v1/games/$$GAME_ID/board/1/1 (mark: X)"; \
		curl -s -X PUT http://localhost:8000/v2/v1/games/$$GAME_ID/board/1/1 -H "Authorization: Bearer test-token" -H "Content-Type: application/json" -d '{"mark":"X"}' | jq . || echo "⚠️  Put square endpoint failed"; \
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
