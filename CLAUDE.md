# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is an OpenAPI Generator experiments repository for creating and testing custom PHP generators. The project includes:

1. **OpenAPI Generator Tools**: Docker-based code generation for both PHP clients and servers
2. **Laravel API Server**: A complete Laravel 12 API that serves endpoints based on the OpenAPI specification
3. **Server-Side Code Generation**: Generate Laravel controllers, Slim APIs, and custom server implementations

The project uses OpenAPITools/openapi-generator in Docker containers to generate both client and server-side PHP code from OpenAPI specifications, with support for Laravel, Slim, and custom server frameworks.

## Project Structure

```
.
├── openapi.yaml                 # Sample OpenAPI specification
├── docker-compose.yml           # Docker services for code generation
├── config/                      # Generator configuration files
│   ├── php-config.json         # Standard PHP client generator config
│   ├── custom-php-config.json  # Custom PHP client generator config
│   └── php-laravel-form-requests-config.json # Laravel server with Form Requests config
├── templates/                   # Custom template directories
│   ├── custom-php/             # Custom PHP client generator templates
│   └── php-laravel-form-requests/ # Laravel server with Form Requests templates
├── scripts/                     # Convenience scripts
│   ├── generate-php.sh         # Generate with standard PHP generator
│   ├── generate-custom-php.sh  # Generate with custom templates
│   ├── extract-default-templates.sh  # Extract default templates
│   └── create-custom-generator.sh    # Create custom generator skeleton
├── generated/                   # Generated code output
│   ├── php/                    # Standard PHP client
│   ├── custom-php/             # Custom PHP client
│   └── server/                 # Laravel server with Form Requests
├── laravel-api/                 # Laravel API server
│   ├── app/Http/Controllers/Api/ # API controllers
│   ├── routes/api.php          # API routes
│   ├── docker-compose.yml      # Laravel development environment
│   └── API-README.md           # Laravel API documentation
└── Makefile                    # Make targets for common tasks

## Development Setup

**Docker-Only Environment**: This project uses Docker containers for all development tools and runtimes. No local installation of Node.js, PHP, Python, or other development tools is required - only Docker.

All development commands should be executed through Docker containers:
- Use `docker run` or `docker-compose` for running development tools
- Mount the project directory as a volume for file access
- Consider using development containers or docker-compose for consistent environments

When adding project files, include:
- Dockerfile(s) for development environments
- docker-compose.yml for multi-service setups
- Package configuration files (package.json, requirements.txt, Cargo.toml, etc.)
- Scripts that wrap Docker commands for common tasks

## Common Commands

### Quick Start with Make
```bash
make help                    # Show all available commands

# Client-side generation
make generate-php           # Generate PHP client using standard generator
make generate-custom-php    # Generate PHP client using custom templates

# Server-side generation
make generate-server            # Generate Laravel server with API-specific Form Requests

# Utilities
make extract-templates      # Extract default templates for customization
make create-generator       # Create custom generator skeleton
make validate-spec          # Validate OpenAPI specification
make clean                  # Clean generated files
```

### Docker Commands
```bash
# Client-side generation
# Generate PHP client with standard generator
docker run --rm -v $(pwd):/local openapitools/openapi-generator-cli generate \
  -i /local/openapi.yaml -g php -o /local/generated/php -c /local/config/php-config.json

# Generate with custom templates
docker run --rm -v $(pwd):/local openapitools/openapi-generator-cli generate \
  -i /local/openapi.yaml -g php -o /local/generated/custom-php \
  -c /local/config/custom-php-config.json --template-dir /local/templates/custom-php

# Server-side generation
# Generate Laravel server with API-specific Form Requests
docker run --rm -v $(pwd):/local openapitools/openapi-generator-cli generate \
  -i /local/openapi.yaml -g php-laravel -o /local/generated/server \
  -c /local/config/php-laravel-form-requests-config.json \
  --template-dir /local/templates/php-laravel-form-requests

# Extract default templates for customization
docker run --rm -v $(pwd):/local openapitools/openapi-generator-cli author template \
  -g php -o /local/templates/php-default

# Extract Laravel server templates for customization
docker run --rm -v $(pwd):/local openapitools/openapi-generator-cli author template \
  -g php-laravel -o /local/templates/php-laravel-default

# Validate OpenAPI spec
docker run --rm -v $(pwd):/local openapitools/openapi-generator-cli validate \
  -i /local/openapi.yaml
```

### Docker Compose
```bash
# Client-side generation
docker-compose --profile generate run generate-php
docker-compose --profile generate run generate-custom-php

# Server-side generation
docker-compose --profile generate run generate-server

# Create custom generator skeleton
docker-compose --profile meta run create-generator
```

## Generator Customization Workflow

### 1. Template Customization (Recommended for beginners)
1. Extract default templates: `make extract-templates`
2. Copy desired templates from `templates/php-default/` to `templates/custom-php/`
3. Modify templates using Mustache syntax
4. Generate code: `make generate-custom-php`

### 2. Configuration Customization
- Modify `config/php-config.json` or `config/custom-php-config.json`
- Available options: `make config-help`

### 3. Custom Generator Creation (Advanced)
1. Create generator skeleton: `make create-generator`
2. Implement custom logic in `generators/my-php-generator/`
3. Build custom generator JAR
4. Use with `--custom-generator` option

## Pure OpenAPI Generator Form Request Features

The server generator (`make generate-server`) uses **pure OpenAPI Generator with custom Mustache templates** to generate Laravel servers with Form Request integration:

### How It Works
- **Custom Mustache Templates**: Uses `templates/php-laravel-form-requests/` with custom `form_request.mustache` and `api.mustache` templates
- **Single Generation Command**: Everything generated in one OpenAPI Generator call - no custom scripts required
- **Template-Based**: Leverages OpenAPI Generator's native templating system with Mustache syntax

### Generated Form Request Classes
- **One Form Request per API operation** (e.g., `FindPetsRequest`, `AddPetRequest`)
- **Automatic validation rules** based on OpenAPI specification parameters
- **Type-safe parameter access** methods with proper PHP types
- **Clean data extraction** with helper methods

### Form Request Features
```php
// Example: FindPetsRequest.php (generated from template)
class FindPetsRequest extends FormRequest
{
    public function rules(): array {
        return [
            'tags' => ['sometimes', 'array'],
            'limit' => ['sometimes', 'integer']
        ];
    }

    // Auto-generated typed parameter access
    public function getFindPetstags(): ?array { ... }
    public function getFindPetslimit(): ?int { ... }

    // Template-generated helper methods
    public function getQueryParams(): array { ... }
    public function getOperationData(): array { ... }
    public function getCleanData(): array { ... }
}
```

### Enhanced Controllers (Template-Generated)
- **Form Request type hints** in method signatures instead of generic `Request`
- **Automatic validation** through Laravel's Form Request system
- **Clean parameter extraction** using generated Form Request methods
- **Template comments** showing how to access each parameter type

### Benefits
- **Pure OpenAPI Generator** - No custom scripts, uses standard tooling
- **Template Customization** - Modify Mustache templates for different patterns
- **Laravel Best Practices** - Uses Form Requests as intended
- **Type Safety** - Strong typing for all API parameters generated from OpenAPI spec
- **Maintainable** - Standard OpenAPI Generator workflow

## Template Variables
Common variables available in Mustache templates:
- `{{packageName}}` - PHP package name
- `{{invokerPackage}}` - Base namespace
- `{{modelPackage}}` - Model namespace
- `{{apiPackage}}` - API namespace
- `{{classname}}` - Generated class name
- `{{operations}}` - API operations
- `{{models}}` - Data models

See OpenAPI Generator PHP documentation for complete variable list.