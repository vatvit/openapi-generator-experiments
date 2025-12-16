# Generation Flow

How OpenAPI Generator creates Laravel server code from OpenAPI specifications.

## High-Level Flow

```
OpenAPI Spec → Remove Tags → Generator → Merge Controllers → Laravel External Library
                (TicTacToe)              (PetStore only)
```

## Step-by-Step Process

### 1. Input Files

- **OpenAPI Spec**: `specs/petshop-extended.yaml` or `specs/tictactoe.json`
- **Generator Config**: `config-v2/{spec}-server-config.json` (namespace, package name, etc.)
- **Templates**: `templates/php-laravel-server-v2/*.mustache` (customized Mustache templates)

### 2. Pre-Processing: Remove Tags

Remove tags from OpenAPI spec to prevent duplicate controllers:
```bash
./scripts/remove-tags.sh specs/tictactoe.json specs/tictactoe-no-tags.json
```

### 3. OpenAPI Generator Execution

```bash
docker run openapitools/openapi-generator-cli generate \
  -i /local/specs/tictactoe-no-tags.json \
  -g php-laravel \
  -o /local/laravel-api/generated-v2/tictactoe \
  -c /local/config-v2/tictactoe-server-config.json \
  --template-dir /local/templates/php-laravel-server-v2
```

**What happens internally:**
1. Parse OpenAPI spec (paths, operations, schemas, security)
2. Load generator config (invokerPackage, appName, etc.)
3. For each template, apply Mustache rendering with spec data
4. Write generated files to output directory

### 4. Templates → Generated Files

| Template | Generated Output | Purpose |
|----------|-----------------|---------|
| `api.mustache` | `lib/Api/*HandlerInterface.php` | Handler interfaces for DI |
| `api_controller.mustache` | `lib/Http/Controllers/DefaultController.php` | Abstract controller with methods |
| `operation_response_interface.mustache` | `lib/Api/*ResponseInterface.php` | Response interfaces |
| `operation_response_classes.mustache` | `lib/Api/*200Response.php` | Typed response classes |
| `model.mustache` | `lib/Models/*.php` | Data models from schemas |
| `routes.mustache` | `routes.php` | Laravel route definitions |
| `SecurityInterfaces.php.mustache` | `lib/Security/SecurityInterfaces.php` | Security scheme interfaces |
| `SecurityValidator.php.mustache` | `lib/Security/SecurityValidator.php` | Validates middleware config |

### 5. Post-Processing (PetStore only)

For specs WITH tags, merge duplicate controllers created by multi-tag operations:
```bash
php scripts/merge-controllers-simple.php \
  laravel-api/generated-v2/petstore/lib/Http/Controllers \
  laravel-api/generated-v2/petstore/lib/Http/Controllers/DefaultController.php
```
*Note: Not needed for specs where tags were removed in pre-processing*

### 6. Final Output Structure

```
laravel-api/generated-v2/{api-name}/
├── lib/
│   ├── Api/                          # Handler & Response interfaces
│   ├── Http/Controllers/             # Abstract controllers
│   ├── Models/                       # Data models
│   └── Security/                     # Security interfaces & validator
├── routes.php                        # Laravel routes (auto-validated)
├── composer.json                     # PSR-4 autoload config
└── README.md                         # Generated docs
```

## Key Concepts

**External Library Pattern**: Generated code lives outside Laravel app, included via PSR-4 autoload

**Template Variables**: Mustache templates use OpenAPI spec data:
- `{{invokerPackage}}` - Base namespace (e.g., `PetStoreApiV2\Server`)
- `{{operationId}}` - Operation identifier (e.g., `findPets`)
- `{{appName}}` - Controller name from config (e.g., `PetStoreApiController`)
- `{{path}}` - API endpoint path
- `{{httpMethod}}` - GET, POST, DELETE, etc.

**Automatic Validation**: Security validator code embedded in routes.php (runs in debug mode)
