# OpenAPI Generator PHP Experiments

This repository provides a complete Docker-based environment for experimenting with OpenAPI Generator's PHP code generation, including custom generator development and a Laravel API server for testing.

## Project Components

1. **OpenAPI Generator Tools** - Generate PHP clients from OpenAPI specifications
2. **Laravel API Server** - Complete Laravel 12 API server in `laravel-api/` directory

## Prerequisites

- Docker (the only requirement - no local PHP, Node.js, or other tools needed)

## Quick Start

1. **Generate standard PHP client:**
   ```bash
   make generate-php
   ```

2. **Extract default templates for customization:**
   ```bash
   make extract-templates
   ```

3. **Customize templates:**
   - Copy templates from `templates/php-default/` to `templates/custom-php/`
   - Modify the templates using Mustache syntax
   - Generate with custom templates: `make generate-custom-php`

4. **View generated code:**
   - Standard PHP client: `generated/php/`
   - Custom PHP client: `generated/custom-php/`

5. **Run tests to verify everything works:**
   ```bash
   make test
   ```

## Available Commands

Run `make help` to see all available commands:

- `make generate-php` - Generate PHP client using standard generator
- `make generate-custom-php` - Generate PHP client using custom templates
- `make extract-templates` - Extract default templates for customization
- `make create-generator` - Create custom generator skeleton
- `make validate-spec` - Validate OpenAPI specification
- `make config-help` - Show PHP generator configuration options
- `make list-generators` - List all available generators
- `make clean` - Clean generated files

### Testing Commands
- `make test` - Run all tests (generator, client, Laravel API, integration)
- `make test-generator` - Test OpenAPI generator functionality only
- `make test-client` - Test generated PHP client only
- `make test-laravel` - Test Laravel API application only

## Customization Options

### 1. Template Customization (Easiest)
Perfect for modifying generated code structure, adding custom headers, or changing naming conventions.

### 2. Configuration Changes
Modify `config/php-config.json` or `config/custom-php-config.json` to change package names, namespaces, and other generator settings.

### 3. Custom Generator Development (Advanced)
Create a completely custom generator with custom logic by running `make create-generator`.

## Project Structure

```
.
├── openapi.yaml                 # Sample OpenAPI specification
├── docker-compose.yml           # Docker services for code generation
├── config/                      # Generator configuration files
├── templates/                   # Custom template directories
├── scripts/                     # Convenience scripts
├── generated/                   # Generated code output
└── Makefile                    # Make targets for common tasks
```

## Docker-Only Environment

This project strictly uses Docker containers for all operations. No local installation of development tools is required or recommended.

## OpenAPI Specification

The included `openapi.yaml` is a sample specification with:
- User management endpoints (CRUD operations)
- Request/response models
- Error handling
- Authentication (Bearer tokens)

You can replace this with your own OpenAPI specification to generate clients for your specific API.

## Testing

The project includes comprehensive tests to verify all components work correctly:

### Quick Test
```bash
make test
```
This runs all tests: generator functionality, generated client code, Laravel API, and integration tests.

### Individual Test Components

1. **Generator Tests** (`make test-generator`):
   - Validates OpenAPI specification
   - Tests PHP client generation
   - Verifies generated file structure
   - Checks PHP syntax
   - Tests template extraction and customization

2. **Generated Client Tests** (`make test-client`):
   - Tests Composer dependency installation
   - Verifies autoloading works
   - Tests Configuration and API classes
   - Validates model classes
   - Checks method signatures
   - Creates integration test examples

3. **Laravel API Tests** (`make test-laravel`):
   - Verifies Laravel installation
   - Tests database configuration
   - Runs Laravel feature tests
   - Validates API routes and models
   - Tests endpoints with HTTP requests

4. **Integration Tests**:
   - Tests generated client against live Laravel API
   - Verifies end-to-end communication
   - Ensures compatibility between components

### Test Requirements
- Docker (only requirement)
- All tests run in Docker containers
- No local PHP, Composer, or other tools needed

## Troubleshooting

1. **Docker daemon not running:** Ensure Docker is started on your system
2. **Permission errors:** Make sure scripts are executable: `chmod +x scripts/*.sh`
3. **Generation errors:** Validate your OpenAPI spec first: `make validate-spec`

## Laravel API Server

The `laravel-api/` directory contains a complete Laravel 12 API server that implements endpoints matching the OpenAPI specification. This provides a real backend for testing generated PHP clients.

### Laravel API Features:
- **RESTful User Management API** (CRUD operations)
- **Docker development environment** (PHP 8.3, MySQL, Redis, Nginx)
- **API endpoints** matching OpenAPI specification
- **Input validation** and error handling
- **Database migrations** ready for use

### Laravel API Quick Start:
```bash
cd laravel-api
docker-compose up -d
docker-compose exec app composer install
docker-compose exec app php artisan migrate
```

Access the API at: `http://localhost:8000/api/v1/`

See `laravel-api/API-README.md` for detailed Laravel API documentation.

## Further Reading

- [OpenAPI Generator Documentation](https://openapi-generator.tech/)
- [PHP Generator Options](https://openapi-generator.tech/docs/generators/php/)
- [Template Customization Guide](https://openapi-generator.tech/docs/templating/)
- [Custom Generator Development](https://openapi-generator.tech/docs/contributing/)