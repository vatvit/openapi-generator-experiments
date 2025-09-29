# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

This is an OpenAPI Generator experiments repository for creating and testing custom PHP generators. The project includes:

1. **OpenAPI Generator Tools**: Docker-based code generation for PHP clients
2. **Laravel API Server**: A complete Laravel 12 API that serves endpoints based on the OpenAPI specification

The project uses OpenAPITools/openapi-generator in Docker containers to generate PHP client code from OpenAPI specifications, and includes a Laravel API server for testing and development.

## Project Structure

```
.
├── openapi.yaml                 # Sample OpenAPI specification
├── docker-compose.yml           # Docker services for code generation
├── config/                      # Generator configuration files
│   ├── php-config.json         # Standard PHP generator config
│   └── custom-php-config.json  # Custom PHP generator config
├── templates/                   # Custom template directories
│   └── custom-php/             # Custom PHP generator templates
├── scripts/                     # Convenience scripts
│   ├── generate-php.sh         # Generate with standard PHP generator
│   ├── generate-custom-php.sh  # Generate with custom templates
│   ├── extract-default-templates.sh  # Extract default templates
│   └── create-custom-generator.sh    # Create custom generator skeleton
├── generated/                   # Generated code output
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
make generate-php           # Generate PHP client using standard generator
make generate-custom-php    # Generate PHP client using custom templates
make extract-templates      # Extract default templates for customization
make create-generator       # Create custom generator skeleton
make validate-spec          # Validate OpenAPI specification
make clean                  # Clean generated files
```

### Docker Commands
```bash
# Generate PHP client with standard generator
docker run --rm -v $(pwd):/local openapitools/openapi-generator-cli generate \
  -i /local/openapi.yaml -g php -o /local/generated/php -c /local/config/php-config.json

# Generate with custom templates
docker run --rm -v $(pwd):/local openapitools/openapi-generator-cli generate \
  -i /local/openapi.yaml -g php -o /local/generated/custom-php \
  -c /local/config/custom-php-config.json --template-dir /local/templates/custom-php

# Extract default templates for customization
docker run --rm -v $(pwd):/local openapitools/openapi-generator-cli author template \
  -g php -o /local/templates/php-default

# Validate OpenAPI spec
docker run --rm -v $(pwd):/local openapitools/openapi-generator-cli validate \
  -i /local/openapi.yaml
```

### Docker Compose
```bash
# Generate PHP client
docker-compose --profile generate run generate-php

# Generate with custom templates
docker-compose --profile generate run generate-custom-php

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