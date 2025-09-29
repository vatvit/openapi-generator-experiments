.PHONY: help generate-php generate-custom-php extract-templates create-generator clean test test-generator test-client test-laravel

help: ## Show this help message
	@echo "Available commands:"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-20s\033[0m %s\n", $$1, $$2}'

generate-php: ## Generate PHP client using standard generator
	@./scripts/generate-php.sh

generate-custom-php: ## Generate PHP client using custom templates
	@./scripts/generate-custom-php.sh

extract-templates: ## Extract default PHP templates for customization
	@./scripts/extract-default-templates.sh

create-generator: ## Create a new custom generator skeleton
	@./scripts/create-custom-generator.sh

clean: ## Clean generated files
	@echo "Cleaning generated files..."
	@rm -rf generated/php generated/custom-php templates/php-default generators/
	@echo "Cleaned successfully"

validate-spec: ## Validate OpenAPI specification
	@echo "Validating OpenAPI specification..."
	@docker run --rm -v $$(pwd):/local openapitools/openapi-generator-cli validate -i /local/openapi.yaml

list-generators: ## List available generators
	@echo "Available generators:"
	@docker run --rm openapitools/openapi-generator-cli list

config-help: ## Show configuration options for PHP generator
	@echo "Configuration options for PHP generator:"
	@docker run --rm openapitools/openapi-generator-cli config-help -g php

test: ## Run all tests (generator, client, Laravel API, integration)
	@./test-all.sh

test-generator: ## Test OpenAPI generator functionality
	@./test-generator.sh

test-client: ## Test generated PHP client
	@./test-generated-client.sh

test-laravel: ## Test Laravel API application
	@cd laravel-api && ./test-laravel.sh