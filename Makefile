.PHONY: help generate-scaffolding extract-templates extract-laravel-templates validate-spec clean test-laravel test-complete

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

# Main scaffolding generator
generate-scaffolding: ## Generate Laravel scaffolding (models, controllers) from petshop-extended.yaml
	@echo "🏗️  Generating Laravel API scaffolding..."
	@rm -rf generated/scaffolding
	@mkdir -p generated/scaffolding
	@chmod 777 generated/scaffolding
	@echo "📋 Using OpenAPI spec: petshop-extended.yaml"
	@docker run --rm -v $$(pwd):/local openapitools/openapi-generator-cli generate \
		-i /local/petshop-extended.yaml \
		-g php-laravel \
		-o /local/generated/scaffolding \
		-c /local/config/php-laravel-scaffolding-config.json \
		--template-dir /local/templates/php-laravel-scaffolding
	@echo "✅ Laravel API scaffolding generated!"
	@echo "📁 Output: generated/scaffolding"
	@echo ""
	@echo "📋 Next Steps:"
	@echo "   1. Review generated models in generated/scaffolding/"
	@echo "   2. Copy models to laravel-api/app/Models/Generated/"
	@echo "   3. Update controllers in laravel-api/app/Http/Controllers/Api/"

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
	@echo "📋 Validating OpenAPI specification..."
	@docker run --rm -v $$(pwd):/local openapitools/openapi-generator-cli validate \
		-i /local/petshop-extended.yaml
	@echo "✅ Specification is valid!"

clean: ## Clean generated files
	@echo "🧹 Cleaning generated files..."
	@rm -rf generated/scaffolding
	@rm -rf laravel-api/app/Models/Generated
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
	@echo "📋 Step 3: Checking generated models..."
	@if [ -d "generated/scaffolding" ]; then \
		echo "✅ Scaffolding generated successfully"; \
		find generated/scaffolding -name "*.php" -type f | wc -l | xargs echo "   📄 Generated files:"; \
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
		echo "Testing endpoints:"; \
		echo "  GET /api/health"; \
		curl -s http://localhost:8000/api/health | jq . || echo "❌ Health check failed"; \
		echo ""; \
		echo "  GET /api/pets"; \
		curl -s http://localhost:8000/api/pets | jq . || echo "⚠️  Pets endpoint (may be empty)"; \
		echo ""; \
		echo "  GET /api/users"; \
		curl -s http://localhost:8000/api/users | jq . || echo "⚠️  Users endpoint (may be empty)"; \
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
