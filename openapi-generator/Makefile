# OpenAPI Generator version (pinned for reproducibility)
OPENAPI_GENERATOR_VERSION := latest

.PHONY: help generate extract-templates extract-laravel-templates check-version update-generator-version

help: ## Show OpenAPI Generator utility commands
	@echo "OpenAPI Generator - Utility Commands"
	@echo "===================================="
	@echo "Available commands:"
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-25s\033[0m %s\n", $$1, $$2}'
	@echo ""
	@echo "Usage example:"
	@echo "  make generate SPEC_NAME=petshop SPEC_FILE=petshop-extended.yaml \\"
	@echo "                OUTPUT_NAME=petstore CONFIG=petshop-server-config.json"

generate: ## Generate server code (requires: SPEC_NAME, SPEC_FILE, OUTPUT_NAME, CONFIG, optional: PREPROCESS=yes)
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
	@echo "🏗️  Generating $(SPEC_NAME) API server with custom templates (PSR-4 compliant)..."
	@rm -rf ../generated/$(OUTPUT_NAME)
	@mkdir -p ../generated
ifeq ($(PREPROCESS),yes)
	@echo "🔧 Preprocessing: Setting operation-specific tags..."
	@SPEC_EXT=$${SPEC_FILE##*.}; \
	PROCESSED_FILE="../$(SPEC_NAME)/$(SPEC_NAME)-tagged.$$SPEC_EXT"; \
	./scripts/set-operation-tags.sh "../$(SPEC_NAME)/$(SPEC_FILE)" "$$PROCESSED_FILE"; \
	echo "📋 Generating from preprocessed spec: $(SPEC_NAME)/$(SPEC_NAME)-tagged.$$SPEC_EXT"; \
	docker run --rm -v $$(pwd)/..:/local openapitools/openapi-generator-cli:$(OPENAPI_GENERATOR_VERSION) generate \
		-i /local/$(SPEC_NAME)/$(SPEC_NAME)-tagged.$${SPEC_EXT} \
		-g php-laravel \
		-o /local/generated/$(OUTPUT_NAME) \
		-c /local/laravel-api/openapi-generator-configs/$(CONFIG) \
		-t /local/openapi-generator-server-laravel
else
	@echo "📋 Generating from spec: $(SPEC_NAME)/$(SPEC_FILE)"
	@docker run --rm -v $$(pwd)/..:/local openapitools/openapi-generator-cli:$(OPENAPI_GENERATOR_VERSION) generate \
		-i /local/$(SPEC_NAME)/$(SPEC_FILE) \
		-g php-laravel \
		-o /local/generated/$(OUTPUT_NAME) \
		-c /local/laravel-api/openapi-generator-configs/$(CONFIG) \
		-t /local/openapi-generator-server-laravel
endif
	@echo "✅ $(SPEC_NAME) API server generated (PSR-4: one class per file)!"
	@echo "📁 Output: generated/$(OUTPUT_NAME)"

extract-templates: ## Extract default PHP client templates for customization
	@echo "📦 Extracting default PHP templates..."
	@mkdir -p ../templates/php-default
	@docker run --rm -v $$(pwd)/..:/local openapitools/openapi-generator-cli:$(OPENAPI_GENERATOR_VERSION) author template \
		-g php -o /local/templates/php-default
	@echo "✅ PHP templates extracted to: ../templates/php-default/"
	@echo "ℹ️  You can copy and modify templates from there to templates/custom-php/"

extract-laravel-templates: ## Extract default php-laravel templates for customization
	@echo "📦 Extracting php-laravel templates..."
	@mkdir -p ../templates/php-laravel-default
	@docker run --rm -v $$(pwd)/..:/local openapitools/openapi-generator-cli:$(OPENAPI_GENERATOR_VERSION) author template \
		-g php-laravel -o /local/templates/php-laravel-default
	@echo "✅ Laravel templates extracted to: ../templates/php-laravel-default/"

check-version: ## Check generated code version matches expected (accepts same major.minor, any patch)
	@echo "🔍 Checking OpenAPI Generator version..."
	@if [ "$(OPENAPI_GENERATOR_VERSION)" = "latest" ]; then \
		echo "ℹ️  Using 'latest' version - skipping strict version check"; \
		for version_file in ../generated/*/.openapi-generator/VERSION; do \
			if [ -f "$$version_file" ]; then \
				api_name=$$(basename $$(dirname $$(dirname $$version_file))); \
				actual_version=$$(cat $$version_file | tr -d '\n'); \
				printf "  📋 %-12s: %s\n" "$$api_name" "$$actual_version"; \
			fi; \
		done; \
		echo "✅ Version check passed (using latest)"; \
	else \
		expected_version=$$(echo $(OPENAPI_GENERATOR_VERSION) | sed 's/^v//'); \
		expected_major_minor=$$(echo $$expected_version | cut -d. -f1,2); \
		all_valid=true; \
		for version_file in ../generated/*/.openapi-generator/VERSION; do \
			if [ -f "$$version_file" ]; then \
				api_name=$$(basename $$(dirname $$(dirname $$version_file))); \
				actual_version=$$(cat $$version_file | tr -d '\n' | sed 's/-SNAPSHOT//'); \
				actual_major_minor=$$(echo $$actual_version | cut -d. -f1,2); \
				printf "  📋 %-12s: %s" "$$api_name" "$$actual_version"; \
				if [ "$$actual_major_minor" != "$$expected_major_minor" ]; then \
					echo " ❌ (expected $$expected_major_minor.x)"; \
					all_valid=false; \
				else \
					echo " ✅"; \
				fi; \
			fi; \
		done; \
		if [ "$$all_valid" = "false" ]; then \
			echo ""; \
			echo "❌ Version mismatch detected!"; \
			echo "   Expected: $$expected_major_minor.x"; \
			echo "   Run: make update-generator-version VERSION=vX.Y.Z"; \
			exit 1; \
		fi; \
		echo "✅ All generated APIs use compatible OpenAPI Generator version ($$expected_major_minor.x)"; \
	fi

update-generator-version: ## Update OpenAPI Generator version (Usage: make update-generator-version VERSION=v7.19.0)
ifndef VERSION
	$(error VERSION required. Usage: make update-generator-version VERSION=v7.19.0)
endif
	@echo "📥 Updating OpenAPI Generator to $(VERSION)..."
	@sed -i '' 's/OPENAPI_GENERATOR_VERSION := .*/OPENAPI_GENERATOR_VERSION := $(VERSION)/' Makefile
	@echo "🐳 Pulling Docker image: openapitools/openapi-generator-cli:$(VERSION)"
	@docker pull openapitools/openapi-generator-cli:$(VERSION)
	@echo "✅ Updated to $(VERSION)"
	@echo "ℹ️  Run 'make generate-server' to regenerate code with new version"
