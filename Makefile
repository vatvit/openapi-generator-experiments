.PHONY: help generate-server generate-petshop generate-tictactoe validate-spec clean test-complete
.PHONY: setup-laravel start-laravel stop-laravel logs-laravel test-laravel test-laravel-phpunit dumpautoload
.PHONY: extract-templates extract-laravel-templates check-version update-generator-version

# OpenAPI Generator version (using latest to get 7.18.0-SNAPSHOT)
OPENAPI_GENERATOR_VERSION := latest

help: ## Show this help message
	@echo "OpenAPI Generator Experiments - Development Commands"
	@echo "===================================================="
	@echo ""
	@echo "🚀 Quick Start:"
	@echo "   1. make generate-server      # Generate Laravel server from OpenAPI specs"
	@echo "   2. make start-laravel        # Start Laravel (auto-refreshes autoload)"
	@echo "   3. make test-laravel-phpunit # Run PHPUnit tests"
	@echo ""
	@echo "📋 Generation Commands:"
	@echo "  \033[36mgenerate-server\033[0m          Generate all API server libraries"
	@echo "  \033[36mgenerate-petshop\033[0m         Generate PetStore API server"
	@echo "  \033[36mgenerate-tictactoe\033[0m       Generate TicTacToe API server"
	@echo "  \033[36mvalidate-spec\033[0m            Validate OpenAPI specifications"
	@echo "  \033[36mclean\033[0m                    Clean generated files"
	@echo ""
	@echo "🧪 Testing Commands:"
	@echo "  \033[36mtest-complete\033[0m            Complete test: validate → generate → test"
	@echo "  \033[36mtest-laravel\033[0m             Test Laravel API endpoints (curl)"
	@echo "  \033[36mtest-laravel-phpunit\033[0m     Run PHPUnit tests"
	@echo ""
	@echo "🐳 Laravel Commands:"
	@echo "  \033[36msetup-laravel\033[0m            Setup Laravel application"
	@echo "  \033[36mstart-laravel\033[0m            Start Laravel development environment"
	@echo "  \033[36mstop-laravel\033[0m             Stop Laravel development environment"
	@echo "  \033[36mlogs-laravel\033[0m             Show Laravel application logs"
	@echo "  \033[36mdumpautoload\033[0m             Refresh composer autoload files"
	@echo ""
	@echo "🔧 OpenAPI Generator Utilities:"
	@echo "  \033[36mextract-templates\033[0m        Extract default PHP templates"
	@echo "  \033[36mextract-laravel-templates\033[0m Extract default php-laravel templates"
	@echo "  \033[36mcheck-version\033[0m            Verify generator version matches expected"
	@echo "  \033[36mupdate-generator-version\033[0m Update to new generator version"
	@echo ""
	@echo "💡 Tip: Each subdirectory has its own Makefile with more commands:"
	@echo "   cd laravel-api && make help"
	@echo "   cd openapi-generator && make help"
	@echo ""
	@echo "🔖 Current OpenAPI Generator version: $(OPENAPI_GENERATOR_VERSION)"

# Generation Commands
generate-server: generate-petshop generate-tictactoe ## Generate all API server libraries

generate-petshop: ## Generate PetStore API server
	@$(MAKE) -C openapi-generator generate \
		SPEC_NAME=petshop \
		SPEC_FILE=petshop-extended.yaml \
		OUTPUT_NAME=petstore \
		CONFIG=petshop-server-config.json

generate-tictactoe: ## Generate TicTacToe API server
	@$(MAKE) -C openapi-generator generate \
		SPEC_NAME=tictactoe \
		SPEC_FILE=tictactoe.json \
		OUTPUT_NAME=tictactoe \
		CONFIG=tictactoe-server-config.json
	@echo "ℹ️  Security interfaces generated via templates (SecurityInterfaces.php, SecurityValidator.php)"

validate-spec: ## Validate the OpenAPI specifications
	@echo "📋 Validating PetStore OpenAPI specification..."
	@docker run --rm -v $$(pwd):/local openapitools/openapi-generator-cli:$(OPENAPI_GENERATOR_VERSION) validate \
		-i /local/petshop/petshop-extended.yaml
	@echo "✅ PetStore specification is valid!"
	@echo ""
	@echo "📋 Validating TicTacToe OpenAPI specification..."
	@docker run --rm -v $$(pwd):/local openapitools/openapi-generator-cli:$(OPENAPI_GENERATOR_VERSION) validate \
		-i /local/tictactoe/tictactoe.json
	@echo "✅ TicTacToe specification is valid!"

clean: ## Clean generated files
	@echo "🧹 Cleaning generated files..."
	@rm -rf generated/petstore
	@rm -rf generated/tictactoe
	@echo "✅ Generated files cleaned!"

# Testing Orchestration
test-complete: ## Complete test: validate → generate → version check → test
	@echo "🎯 Running Complete Test"
	@echo "========================"
	@echo ""
	@echo "📋 Step 1: Validating OpenAPI specifications..."
	@$(MAKE) validate-spec
	@echo ""
	@echo "📋 Step 2: Generating server for both specs..."
	@$(MAKE) generate-server
	@echo ""
	@echo "📋 Step 3: Verifying generator version..."
	@$(MAKE) check-version
	@echo ""
	@echo "📋 Step 4: Checking generated server..."
	@if [ -d "generated/petstore" ]; then \
		echo "✅ PetStore server generated successfully"; \
		find generated/petstore -name "*.php" -type f | wc -l | xargs echo "   📄 PetStore files:"; \
	else \
		echo "❌ PetStore server generation failed"; \
		exit 1; \
	fi
	@if [ -d "generated/tictactoe" ]; then \
		echo "✅ TicTacToe server generated successfully"; \
		find generated/tictactoe -name "*.php" -type f | wc -l | xargs echo "   📄 TicTacToe files:"; \
		if [ -f "generated/tictactoe/lib/Http/Controllers/GameManagementController.php" ]; then \
			echo "✅ Controllers created successfully (PSR-4 compliant)"; \
			find generated/tictactoe/lib/Http/Controllers -name "*Controller.php" -type f | wc -l | xargs echo "   📝 Controllers:"; \
		else \
			echo "❌ Controllers not found"; \
			exit 1; \
		fi; \
	else \
		echo "❌ TicTacToe server generation failed"; \
		exit 1; \
	fi
	@echo ""
	@echo "📋 Step 5: Starting Laravel and refreshing autoload..."
	@$(MAKE) -C laravel-api start
	@echo ""
	@echo "📋 Step 6: Running PHPUnit tests..."
	@$(MAKE) test-laravel-phpunit
	@echo ""
	@echo "🎉 Complete test finished for both PetStore and TicTacToe!"

# Laravel Commands (delegated to laravel-api/Makefile)
setup-laravel: ## Setup Laravel application and refresh autoload
	@$(MAKE) -C laravel-api setup

start-laravel: ## Start Laravel development environment
	@$(MAKE) -C laravel-api start

stop-laravel: ## Stop Laravel development environment
	@$(MAKE) -C laravel-api stop

logs-laravel: ## Show Laravel application logs
	@$(MAKE) -C laravel-api logs

dumpautoload: ## Refresh composer autoload files
	@$(MAKE) -C laravel-api dumpautoload

test-laravel: ## Test Laravel API endpoints (curl)
	@$(MAKE) -C laravel-api test-endpoints

test-laravel-phpunit: ## Run PHPUnit tests (Unit and Feature tests)
	@$(MAKE) -C laravel-api test-phpunit

# OpenAPI Generator Utilities (delegated to openapi-generator/Makefile)
extract-templates: ## Extract default PHP client templates for customization
	@$(MAKE) -C openapi-generator extract-templates

extract-laravel-templates: ## Extract default php-laravel templates for customization
	@$(MAKE) -C openapi-generator extract-laravel-templates

check-version: ## Verify generator version matches expected
	@$(MAKE) -C openapi-generator check-version

update-generator-version: ## Update OpenAPI Generator version (Usage: make update-generator-version VERSION=v7.19.0)
	@$(MAKE) -C openapi-generator update-generator-version VERSION=$(VERSION)
