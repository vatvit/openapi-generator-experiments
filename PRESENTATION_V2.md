# Laravel Server Scaffolding from OpenAPI: V2 Solution

> **Automated generation of type-safe Laravel server scaffolding from OpenAPI specifications**

**Target Audience:** Developers and Architects
**Presentation Date:** 2025-10-09

---

## Table of Contents

1. [The Problem We Solved](#1-the-problem-we-solved)
2. [Solution Overview](#2-solution-overview)
3. [Architecture & Design](#3-architecture--design)
4. [Data Flow](#4-data-flow)
5. [Project Structure](#5-project-structure)
6. [How It Works: Step by Step](#6-how-it-works-step-by-step)
7. [Multi-Spec Support](#7-multi-spec-support)
8. [Type Safety & Validation](#8-type-safety--validation)
9. [Security Implementation](#9-security-implementation)
10. [Post-Processing Pipeline](#10-post-processing-pipeline)
11. [How to Use](#11-how-to-use)
12. [Design Decisions](#12-design-decisions)
13. [Limitations & Trade-offs](#13-limitations--trade-offs)
14. [Demo Files Reference](#14-demo-files-reference)

---

## 1. The Problem We Solved

### Challenge: Generate Laravel Server Code from OpenAPI Specs

**Requirements:**
- ✅ Generate from **multiple OpenAPI specs** (PetStore, TicTacToe)
- ✅ Support **security schemes** (Bearer tokens, API keys, OAuth2)
- ✅ Handle **operations with multiple tags** (avoid duplication)
- ✅ Create **type-safe responses** (enforce HTTP status codes)
- ✅ Implement **dependency injection** (handlers for business logic)
- ✅ Keep **specs as source of truth** (no modifications)

**Why OpenAPI Generator alone isn't enough:**
- ❌ Doesn't generate security interfaces
- ❌ Duplicates controllers when operations have multiple tags
- ❌ Generates monolithic API interface files
- ❌ No built-in handler-based dependency injection

---

## 2. Solution Overview

### V2: OpenAPI Generator + Custom Templates + Post-Processing

```
OpenAPI Spec → Pre-Process → Generate → Post-Process → Laravel Scaffolding
     ↓              ↓            ↓           ↓              ↓
Source of     Remove tags   Custom      Create        Type-safe,
  Truth       (optional)   templates   security      deduplicated,
                                       interfaces    production-ready
```

### Key Components

1. **Custom Mustache Templates** - Generate Laravel-specific code
2. **Pre-Processing** - Clean specs (remove tags for simpler generation)
3. **Post-Processing** - Merge duplicate controllers, create security interfaces
4. **External Library Pattern** - Generated code as PSR-4 autoloaded library
5. **Handler-Based DI** - Separation of concerns (controllers → handlers)

---

## 3. Architecture & Design

### External Library Pattern

Generated scaffolding = **external PHP library** that Laravel includes via PSR-4 autoloading.

```
laravel-api/
├── generated-v2/                    # External libraries (generated)
│   ├── petstore/lib/                # PetStoreApiV2\Scaffolding
│   └── tictactoe/lib/               # TicTacToeApiV2\Scaffolding
│
├── app/                             # Laravel application (manual)
│   ├── Handlers/V2/                 # Business logic
│   ├── Http/Middleware/             # Security implementations
│   └── ...
│
└── bootstrap/app.php                # DI bindings & route registration
```

**Benefits:**
- ✅ Clean separation: generated vs application code
- ✅ Easy regeneration without conflicts
- ✅ Multiple specs = multiple namespaces (no collisions)
- ✅ IDE navigation works perfectly

### Generated Components per Spec

Each OpenAPI spec generates:

```
lib/
├── Api/
│   └── DefaultApiInterface.php      # All interfaces & response classes
│
├── Http/Controllers/
│   └── DefaultController.php        # Abstract controller with validation
│
├── Security/
│   └── *AuthenticationInterface.php # Security interfaces (post-processed)
│
├── Models/
│   └── *.php                        # Request/response models (optional)
│
└── routes.php                       # Laravel routes with middleware
```

---

## 4. Data Flow

### Request → Response Flow

```
1. HTTP Request
   ↓
2. Laravel Route (generated-v2/*/routes.php)
   ↓
3. Middleware Chain (Security, Validation, etc.)
   ↓
4. Controller Method (generated-v2/*/lib/Http/Controllers/DefaultController.php)
   ├─→ Validates input (validation rules from OpenAPI)
   ├─→ Injects Handler Interface (DI)
   └─→ Calls Handler
       ↓
5. Handler (app/Handlers/V2/*Handler.php)
   ├─→ Business logic
   ├─→ Database queries
   └─→ Returns typed response object
       ↓
6. Response Class (generated-v2/*/lib/Api/*Response.php)
   ├─→ Enforces HTTP status code
   ├─→ Enforces response structure
   └─→ Returns JsonResponse
       ↓
7. HTTP Response (JSON)
```

### Dependency Injection Flow

```
OpenAPI Spec defines operation
        ↓
Generator creates GetBoardHandlerInterface
        ↓
Laravel binds interface → implementation (bootstrap/app.php)
        ↓
Controller constructor receives handler
        ↓
Controller calls handler method
        ↓
Handler returns typed response
```

**Example files:**
- Interface: `laravel-api/generated-v2/tictactoe/lib/Api/DefaultApiInterface.php:155-169`
- Binding: `laravel-api/bootstrap/app.php:71-78`
- Controller: `laravel-api/generated-v2/tictactoe/lib/Http/Controllers/DefaultController.php:36-47`
- Handler: `laravel-api/app/Handlers/V2/GetBoardHandler.php`

---

## 5. Project Structure

### Root Directory

```
openapi-generator-experiments/
├── specs/                           # OpenAPI specifications
│   ├── petshop-extended.yaml        # PetStore API (REST CRUD)
│   └── tictactoe.json               # TicTacToe API (game logic)
│
├── config-v2/                       # Generator configurations
│   ├── petshop-scaffolding-config.json
│   └── tictactoe-scaffolding-config.json
│
├── templates/                       # Custom Mustache templates
│   └── php-laravel-scaffolding-v2/
│       ├── api.mustache             # API interfaces & responses
│       ├── api_controller.mustache  # Abstract controllers
│       ├── routes.mustache          # Laravel routes
│       └── ...
│
├── scripts/                         # Processing scripts
│   ├── merge-controllers-simple.php # Post-process: merge duplicates
│   └── remove-tags.sh               # Pre-process: clean spec
│
├── laravel-api/                     # Laravel application
│   └── (see next section)
│
└── Makefile                         # Build automation
```

### Laravel Application

```
laravel-api/
├── generated-v2/                    # Generated scaffolding
│   ├── petstore/
│   │   ├── lib/
│   │   │   ├── Api/DefaultApiInterface.php
│   │   │   ├── Http/Controllers/DefaultController.php
│   │   │   ├── Security/bearerHttpAuthenticationInterface.php
│   │   │   └── Models/...
│   │   └── routes.php
│   │
│   └── tictactoe/
│       ├── lib/                     # Same structure
│       └── routes.php
│
├── app/
│   ├── Handlers/V2/                 # Business logic handlers
│   │   ├── CreateGameHandler.php
│   │   ├── GetBoardHandler.php
│   │   └── ...
│   │
│   └── Http/Middleware/             # Security middleware
│       ├── ValidateBearerToken.php
│       └── ValidateApiKey.php
│
├── bootstrap/
│   └── app.php                      # DI bindings, route registration
│
├── composer.json                    # PSR-4 autoload config
│
└── docker-compose.yml               # Dev environment
```

---

## 6. How It Works: Step by Step

### Step 1: Define OpenAPI Specification

**Example:** `specs/tictactoe.json`

Key elements:
- Operations with `operationId`, `tags`, `security`
- Request/response schemas
- Security schemes (Bearer, API Key, OAuth2)

### Step 2: Create Generator Configuration

**Example:** `config-v2/tictactoe-scaffolding-config.json`

```json
{
  "invokerPackage": "TicTacToeApiV2\\Scaffolding",
  "modelPackage": "Models",
  "apiPackage": "Api"
}
```

Defines:
- Namespace for generated code
- Package structure
- Controller naming

### Step 3: Run Generation Pipeline

```bash
make generate-tictactoe-v2
```

**What happens:**

1. **Pre-process**: `scripts/remove-tags.sh` removes tags from spec
   - Creates `specs/tictactoe-no-tags.json`
   - Avoids tag-based controller duplication

2. **Generate**: OpenAPI Generator runs with custom templates
   - Uses `templates/php-laravel-scaffolding-v2/`
   - Outputs to `laravel-api/generated-v2/tictactoe/`

3. **Post-process**: Creates security interfaces
   - Reads security schemes from spec
   - Generates `lib/Security/*Interface.php` files
   - (Note: Merge step not needed if tags removed in pre-process)

### Step 4: Configure PSR-4 Autoloading

**File:** `laravel-api/composer.json`

```json
{
  "autoload": {
    "psr-4": {
      "TicTacToeApiV2\\Scaffolding\\": "generated-v2/tictactoe/lib/"
    }
  }
}
```

### Step 5: Implement Business Logic

**Generated Interface:** `laravel-api/generated-v2/tictactoe/lib/Api/DefaultApiInterface.php:155-169`

**Your Implementation:** `laravel-api/app/Handlers/V2/GetBoardHandler.php`

```php
class GetBoardHandler implements GetBoardHandlerInterface
{
    public function getBoard(string $gameId): GetBoardResponseInterface
    {
        // Business logic here
        return new GetBoard200Response($board);
    }
}
```

### Step 6: Implement Security Middleware

**Generated Interface:** `laravel-api/generated-v2/tictactoe/lib/Security/bearerHttpAuthenticationInterface.php`

**Your Implementation:** `laravel-api/app/Http/Middleware/ValidateBearerToken.php`

### Step 7: Register Bindings & Routes

**File:** `laravel-api/bootstrap/app.php`

```php
// Bind interfaces to implementations
app()->bind(GetBoardHandlerInterface::class, GetBoardHandler::class);

// Register middleware groups (optional, per operation)
$middleware->group('api.middlewareGroup.getBoard', [
    ValidateBearerToken::class,
]);

// Include generated routes
Route::group([], function () {
    $router = app('router');
    require base_path('generated-v2/tictactoe/routes.php');
});
```

### Step 8: Test Endpoints

```bash
curl http://localhost:8000/api/v1/games/123/board \
  -H "Authorization: Bearer test-token"
```

---

## 7. Multi-Spec Support

### Configuration per Spec

**PetStore:**
- Spec: `specs/petshop-extended.yaml`
- Config: `config-v2/petshop-scaffolding-config.json`
- Namespace: `PetStoreApiV2\Scaffolding`
- Output: `laravel-api/generated-v2/petstore/`

**TicTacToe:**
- Spec: `specs/tictactoe.json`
- Config: `config-v2/tictactoe-scaffolding-config.json`
- Namespace: `TicTacToeApiV2\Scaffolding`
- Output: `laravel-api/generated-v2/tictactoe/`

### No Collisions

Different namespaces = different classes:
- `PetStoreApiV2\Scaffolding\Http\Controllers\DefaultController`
- `TicTacToeApiV2\Scaffolding\Http\Controllers\DefaultController`

Same class name, different namespace = ✅ Works perfectly

### Route Registration

**File:** `laravel-api/bootstrap/app.php:56-107`

Each spec gets its own route group:
```php
// PetStore routes
Route::group([], function () {
    require base_path('generated-v2/petstore/routes.php');
});

// TicTacToe routes
Route::group([], function () {
    require base_path('generated-v2/tictactoe/routes.php');
});
```

**See:** [MULTI-SPEC-SETUP.md](MULTI-SPEC-SETUP.md)

---

## 8. Type Safety & Validation

### Handler Interface (DI Contract)

**Generated:** `laravel-api/generated-v2/tictactoe/lib/Api/DefaultApiInterface.php:155-169`

```php
interface GetBoardHandlerInterface
{
    /**
     * Handle the getBoard operation
     * @return GetBoardResponseInterface
     */
    public function getBoard(string $gameId): GetBoardResponseInterface;
}
```

Enforces:
- ✅ Method signature (parameters, return type)
- ✅ Must return response interface

### Response Interface (Contract)

**Generated:** `laravel-api/generated-v2/tictactoe/lib/Api/DefaultApiInterface.php:171-180`

```php
interface GetBoardResponseInterface extends ResponseInterface
{
    // Marker interface - ensures correct response type
}
```

### Response Classes (Implementation)

**Generated:** `laravel-api/generated-v2/tictactoe/lib/Api/DefaultApiInterface.php:183-218`

One class per HTTP status code:
```php
class GetBoard200Response implements GetBoardResponseInterface
{
    public function __construct(
        private readonly array $board,
        private readonly ?string $winner = null
    ) {}

    public function getStatusCode(): int { return 200; }
    public function toJson(): array { /* ... */ }
}

class GetBoard404Response implements GetBoardResponseInterface
{
    public function getStatusCode(): int { return 404; }
}
```

Enforces:
- ✅ Correct HTTP status code
- ✅ Correct response structure
- ✅ Type-safe data

### Validation Rules

**Generated:** `laravel-api/generated-v2/tictactoe/lib/Http/Controllers/DefaultController.php:98-106`

```php
protected function getSquareValidationRules(): array
{
    return [
        'gameId' => 'required|string',
        'position' => 'required|integer|min:0|max:8',
    ];
}
```

Controller can call validation:
```php
$validated = $request->validate($this->getSquareValidationRules());
```

---

## 9. Security Implementation

### OpenAPI Security Schemes

**Example from:** `specs/tictactoe.json`

```json
{
  "securitySchemes": {
    "bearerHttpAuthentication": {
      "type": "http",
      "scheme": "bearer",
      "bearerFormat": "JWT"
    },
    "defaultApiKey": {
      "type": "apiKey",
      "in": "header",
      "name": "api-key"
    }
  }
}
```

### Generated Security Interface

**Post-processed file:** `laravel-api/generated-v2/tictactoe/lib/Security/bearerHttpAuthenticationInterface.php`

```php
interface bearerHttpAuthenticationInterface
{
    public function handle(
        \Illuminate\Http\Request $request,
        \Closure $next
    ): \Symfony\Component\HttpFoundation\Response;
}
```

### Middleware Implementation

**Your code:** `laravel-api/app/Http/Middleware/ValidateBearerToken.php`

Implements the interface, validates JWT token.

### Conditional Middleware Application

**Generated routes:** `laravel-api/generated-v2/tictactoe/routes.php:88-93`

```php
// Only attach middleware if group is defined
if ($router->hasMiddlewareGroup('api.middlewareGroup.getBoard')) {
    $route->middleware('api.middlewareGroup.getBoard');
}
```

**Benefits:**
- ✅ Zero overhead if middleware not needed
- ✅ No errors if group undefined
- ✅ Flexible per-operation security

### Security Flow

```
1. Request arrives
   ↓
2. Route checks if middleware group exists
   ↓
3. If exists, run middleware chain
   ├─→ ValidateBearerToken (implements bearerHttpAuthenticationInterface)
   ├─→ ValidateApiKey (implements defaultApiKeyInterface)
   └─→ Next
   ↓
4. Controller receives authenticated request
```

**See:** [SECURITY.md](SECURITY.md)

---

## 10. Post-Processing Pipeline

### Why Post-Processing?

OpenAPI Generator limitations:
- ❌ Doesn't generate security interfaces
- ❌ Duplicates controllers for multi-tag operations
- ❌ No built-in customization for these cases

### Post-Processing Steps

**Configured in:** `Makefile:71-103`

#### Step 1: Create Security Interfaces

**What:** Parse OpenAPI security schemes, generate PHP interfaces

**How:** Echo commands create files based on spec metadata

**Example output:** `laravel-api/generated-v2/tictactoe/lib/Security/bearerHttpAuthenticationInterface.php`

#### Step 2: Merge Duplicate Controllers (if needed)

**What:** Combine tag-based controllers into single `DefaultController`

**Script:** `scripts/merge-controllers-simple.php`

**When:** Only if spec has operations with multiple tags AND tags not removed in pre-processing

**How:**
1. Read all `*Controller.php` files
2. Extract methods with regex
3. Deduplicate by method name
4. Generate merged `DefaultController.php`
5. Delete original tag-based controllers

**Note:** In V2, we pre-process to remove tags, so merging is usually not needed.

### Automation

```bash
make generate-tictactoe-v2
```

Runs entire pipeline:
1. Pre-process (remove tags)
2. Generate (OpenAPI Generator)
3. Post-process (security interfaces)
4. Validate (PHP syntax check)

**See:** [TAG_DUPLICATION_SOLUTION.md](TAG_DUPLICATION_SOLUTION.md)

---

## 11. How to Use

### Prerequisites

- Docker (only requirement - no local PHP/Composer needed)

### Quick Start

```bash
# 1. Generate scaffolding for both APIs
make generate-scaffolding-v2

# 2. Start Laravel containers
cd laravel-api && docker-compose up -d

# 3. Refresh autoloader
docker-compose exec app composer dumpautoload

# 4. Test endpoints
curl http://localhost:8000/api/v1/games
curl http://localhost:8000/api/v2/pets
```

### Individual API Generation

```bash
make generate-petshop-v2     # PetStore only
make generate-tictactoe-v2   # TicTacToe only
```

### Adding a New API

1. **Create OpenAPI spec:** `specs/myapi.yaml`

2. **Create config:** `config-v2/myapi-scaffolding-config.json`
   ```json
   {
     "invokerPackage": "MyApiV2\\Scaffolding",
     "modelPackage": "Models",
     "apiPackage": "Api"
   }
   ```

3. **Add Makefile target:** (follow pattern in `Makefile:71-103`)

4. **Add PSR-4 autoload:** `laravel-api/composer.json`
   ```json
   "MyApiV2\\Scaffolding\\": "generated-v2/myapi/lib/"
   ```

5. **Implement handlers:** `laravel-api/app/Handlers/V2/`

6. **Register bindings & routes:** `laravel-api/bootstrap/app.php`

7. **Generate:**
   ```bash
   make generate-myapi-v2
   cd laravel-api && docker-compose exec app composer dumpautoload
   ```

### Testing

```bash
# Full test suite
make test-complete-v2

# Individual API tests
make test-petshop-v2
make test-tictactoe-v2

# Validate OpenAPI specs
make validate-spec
```

---

## 12. Design Decisions

### 1. External Library Pattern

**Decision:** Generate scaffolding as external library, not in `app/`

**Reasoning:**
- ✅ Clean separation (generated vs manual code)
- ✅ Easy regeneration without conflicts
- ✅ Multiple specs = multiple libraries (no collisions)
- ✅ IDE navigation works perfectly

**Alternative considered:** Generate into `app/Http/Controllers/` - rejected due to regeneration conflicts

### 2. Handler-Based Dependency Injection

**Decision:** Controllers inject handler interfaces, handlers contain business logic

**Reasoning:**
- ✅ Separation of concerns (routing → validation → business logic)
- ✅ Testable (mock handlers in tests)
- ✅ Type-safe contracts via interfaces
- ✅ Flexible implementations (swap handlers without changing controllers)

**Alternative considered:** Business logic in controllers - rejected due to tight coupling

### 3. Type-Safe Response Objects

**Decision:** Each operation returns response interface, handlers return concrete response classes

**Reasoning:**
- ✅ Enforces HTTP status codes
- ✅ Enforces response structure from OpenAPI spec
- ✅ Type safety at compile time (PHP static analysis)
- ✅ Self-documenting code

**Alternative considered:** Return arrays/JsonResponse directly - rejected due to lack of type safety

### 4. Conditional Middleware

**Decision:** Routes check if middleware group exists before applying

**Reasoning:**
- ✅ Zero overhead for operations without security
- ✅ No errors if middleware groups not defined
- ✅ Per-operation security configuration
- ✅ Flexible (define only what you need)

**Alternative considered:** Always apply middleware - rejected due to unnecessary overhead and errors

### 5. Post-Processing Over Custom Generator

**Decision:** Use standard OpenAPI Generator + post-processing scripts

**Reasoning:**
- ✅ No Java development required
- ✅ No generator maintenance burden
- ✅ Easy to understand and modify (60 lines of PHP)
- ✅ Transparent (visible in Makefile)

**Alternative considered:** Custom Java generator - rejected due to complexity and maintenance burden

**See:** [TAG_DUPLICATION_SOLUTION.md](TAG_DUPLICATION_SOLUTION.md)

---

## 13. Limitations & Trade-offs

### Monolithic API Interface File

**Issue:** `DefaultApiInterface.php` can be 1000+ lines for complex APIs

**Impact:** Minor (harder to navigate, but IDE "Go to Definition" works fine)

**Recommendation:** Keep as-is (works correctly, no functional issues)

**See:** [ISSUE_MONOLITHIC_API_INTERFACE.md](ISSUE_MONOLITHIC_API_INTERFACE.md)

### PSR-4 Model Namespace Duplication

**Issue:** Generated models have duplicate namespace declaration

**Impact:** None (models not used in current architecture)

**Recommendation:** Ignore warnings, or create custom model templates if needed

**See:** [ISSUE_PSR4_COMPLIANCE.md](ISSUE_PSR4_COMPLIANCE.md)

### Post-Processing Required

**Trade-off:** Additional step after generation (security interfaces)

**Benefit:** Gets features that OpenAPI Generator doesn't provide

**Automation:** Fully automated via Makefile

### Template Maintenance

**Trade-off:** Custom templates must be maintained across OpenAPI Generator updates

**Mitigation:** Templates are simple Mustache files, stable API

### No Model Classes Used

**Trade-off:** Controllers work with arrays/JSON, not typed model objects

**Reasoning:**
- Response classes provide type safety where it matters (return values)
- Request validation via Laravel's rules (from OpenAPI spec)
- Simpler architecture (fewer layers)

**Alternative:** Could use models if needed (implement in custom templates)

---

## 14. Demo Files Reference

### OpenAPI Specifications

- **PetStore:** `specs/petshop-extended.yaml`
- **TicTacToe:** `specs/tictactoe.json`
- **TicTacToe (no tags):** `specs/tictactoe-no-tags.json` (generated by pre-processing)

### Configuration

- **PetStore config:** `config-v2/petshop-scaffolding-config.json`
- **TicTacToe config:** `config-v2/tictactoe-scaffolding-config.json`

### Templates

- **Template directory:** `templates/php-laravel-scaffolding-v2/`
- **API interfaces:** `templates/php-laravel-scaffolding-v2/api.mustache`
- **Controllers:** `templates/php-laravel-scaffolding-v2/api_controller.mustache`
- **Routes:** `templates/php-laravel-scaffolding-v2/routes.mustache`

### Generated Code (TicTacToe Example)

- **API interfaces:** `laravel-api/generated-v2/tictactoe/lib/Api/DefaultApiInterface.php`
  - Lines 1-153: Main API interface
  - Lines 155-169: Handler interfaces (e.g., `GetBoardHandlerInterface`)
  - Lines 171-180: Response interfaces (e.g., `GetBoardResponseInterface`)
  - Lines 183-600+: Response classes (e.g., `GetBoard200Response`)

- **Abstract controller:** `laravel-api/generated-v2/tictactoe/lib/Http/Controllers/DefaultController.php`
  - Lines 20-47: Abstract operation methods
  - Lines 98-106: Validation rules

- **Routes:** `laravel-api/generated-v2/tictactoe/routes.php`
  - Lines 50-93: Example route with security and middleware

- **Security interface:** `laravel-api/generated-v2/tictactoe/lib/Security/bearerHttpAuthenticationInterface.php`

### Application Code

- **Bootstrap:** `laravel-api/bootstrap/app.php`
  - Lines 56-79: PetStore setup (bindings, routes)
  - Lines 81-107: TicTacToe setup (bindings, routes)

- **Handlers:** `laravel-api/app/Handlers/V2/`
  - Example: `GetBoardHandler.php`
  - Example: `CreateGameHandler.php`

- **Middleware:** `laravel-api/app/Http/Middleware/`
  - `ValidateBearerToken.php` (implements `bearerHttpAuthenticationInterface`)
  - `ValidateApiKey.php` (implements `defaultApiKeyInterface`)

- **PSR-4 config:** `laravel-api/composer.json` (lines 27-30)

### Scripts

- **Post-processor:** `scripts/merge-controllers-simple.php` (60 lines)
- **Pre-processor:** `scripts/remove-tags.sh`

### Build Automation

- **Makefile targets:** `Makefile`
  - Lines 37-53: PetShop V2 generation
  - Lines 71-103: TicTacToe V2 generation
  - Lines 105-109: Generate both APIs

### Documentation

- **Main docs:** `CLAUDE.md`
- **Multi-spec setup:** `MULTI-SPEC-SETUP.md`
- **Tag duplication solution:** `TAG_DUPLICATION_SOLUTION.md`
- **Security details:** `SECURITY.md`
- **Troubleshooting:** `TROUBLESHOOTING.md`
- **Known issues index:** `KNOWN_ISSUES.md`

### Test Scripts

- **Complete test:** `scripts/test-complete-v2.sh`
- **PetShop test:** `scripts/test-petshop-v2.sh`
- **TicTacToe test:** `scripts/test-tictactoe-v2.sh`

---

## Questions & Discussion

### Key Takeaways

1. **Automated scaffolding generation** from OpenAPI specs
2. **Type-safe** handlers and responses
3. **Multi-spec support** with namespace isolation
4. **Security** via generated interfaces + middleware
5. **Post-processing** solves generator limitations
6. **Production-ready** architecture with DI and separation of concerns

### Try It Yourself

```bash
# Clone and run
git clone <repo>
cd openapi-generator-experiments
make generate-scaffolding-v2
cd laravel-api && docker-compose up -d
curl http://localhost:8000/api/v1/games
```

### Resources

- 📖 [Full Documentation](CLAUDE.md)
- 🔧 [Troubleshooting Guide](TROUBLESHOOTING.md)
- 🐛 [Known Issues](KNOWN_ISSUES.md)
- 🏗️ [Architecture Details](TAG_DUPLICATION_SOLUTION.md)

---

**End of Presentation**

*Questions? Let's discuss the architecture, design decisions, or implementation details.*
