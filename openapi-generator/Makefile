.PHONY: help generate extract-templates extract-laravel-templates

help: ## Show OpenAPI Generator utility commands
	@echo "OpenAPI Generator - Utility Commands"
	@echo "===================================="
	@echo "Available commands:"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-25s\033[0m %s\n", $$1, $$2}'
	@echo ""
	@echo "Usage example:"
	@echo "  make generate SPEC_NAME=petshop SPEC_FILE=petshop-extended.yaml \\"
	@echo "                OUTPUT_NAME=petstore CONFIG=petshop-server-config.json"

generate: ## Generate server code (requires: SPEC_NAME, SPEC_FILE, OUTPUT_NAME, CONFIG)
ifndef SPEC_NAME
	$(error SPEC_NAME is required. Example: SPEC_NAME=petshop)
endif
ifndef SPEC_FILE
	$(error SPEC_FILE is required. Example: SPEC_FILE=petshop-extended.yaml)
endif
ifndef OUTPUT_NAME
	$(error OUTPUT_NAME is required. Example: OUTPUT_NAME=petstore)
endif
ifndef CONFIG
	$(error CONFIG is required. Example: CONFIG=petshop-server-config.json)
endif
	@echo "🏗️  Generating $(SPEC_NAME) API server with default templates (PSR-4 compliant)..."
	@rm -rf ../generated/$(OUTPUT_NAME)
	@mkdir -p ../generated
	@echo "📋 Generating from spec: $(SPEC_NAME)/$(SPEC_FILE)"
	@docker run --rm -v $$(pwd)/..:/local openapitools/openapi-generator-cli generate \
		-i /local/$(SPEC_NAME)/$(SPEC_FILE) \
		-g php-laravel \
		-o /local/generated/$(OUTPUT_NAME) \
		-c /local/laravel-api/openapi-generator-configs/$(CONFIG)
	@echo "✅ $(SPEC_NAME) API server generated (PSR-4: one class per file)!"
	@echo "📁 Output: generated/$(OUTPUT_NAME)"

extract-templates: ## Extract default PHP client templates for customization
	@echo "📦 Extracting default PHP templates..."
	@mkdir -p ../templates/php-default
	@docker run --rm -v $$(pwd)/..:/local openapitools/openapi-generator-cli author template \
		-g php -o /local/templates/php-default
	@echo "✅ PHP templates extracted to: ../templates/php-default/"
	@echo "ℹ️  You can copy and modify templates from there to templates/custom-php/"

extract-laravel-templates: ## Extract default php-laravel templates for customization
	@echo "📦 Extracting php-laravel templates..."
	@mkdir -p ../templates/php-laravel-default
	@docker run --rm -v $$(pwd)/..:/local openapitools/openapi-generator-cli author template \
		-g php-laravel -o /local/templates/php-laravel-default
	@echo "✅ Laravel templates extracted to: ../templates/php-laravel-default/"
