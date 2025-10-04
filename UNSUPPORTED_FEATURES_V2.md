# OpenAPI Features Not Fully Supported in V2 Handlers

## Overview

This document lists OpenAPI specification features from `specs/tictactoe.json` that are **defined in the spec** and **generated in scaffolding** but **not implemented in the V2 handler examples**.

The handlers are intentionally simple demonstrations. This document identifies gaps that would need to be addressed for production use.

---

## 1. Error Response Handling ⚠️

### Spec Definition
Multiple operations define error responses (400, 401, 403, 404, 409, 422).

Example from `PUT /games/{gameId}/board/{row}/{column}`:
```json
"responses": {
  "200": { "description": "OK" },
  "400": { "description": "Bad Request" },
  "404": { "description": "Not Found" },
  "409": { "description": "Conflict - Square already occupied or game finished" }
}
```

### Generated Scaffolding
✅ Generator created error response classes:
- `PutSquare200Response`
- `PutSquare400Response`
- `PutSquare404Response`
- `PutSquare409Response`

All implement `PutSquareResponseInterface`.

### Handler Implementation Status
❌ **Not Implemented** - Handlers only return 200/201/204 success responses

**Current Handler**:
```php
public function handle(...): PutSquareResponseInterface
{
    // Always returns 200 OK
    return new PutSquare200Response($status);
}
```

**Production Implementation Needed**:
```php
public function handle(...): PutSquareResponseInterface
{
    // Validate game exists
    if (!$this->gameRepository->exists($gameId)) {
        return new PutSquare404Response(new Error(
            code: 'GAME_NOT_FOUND',
            message: "Game {$gameId} not found"
        ));
    }

    // Validate square is empty
    $game = $this->gameRepository->find($gameId);
    if (!$game->isSquareEmpty($row, $column)) {
        return new PutSquare409Response(new Error(
            code: 'SQUARE_OCCUPIED',
            message: "Square at ({$row}, {$column}) is already occupied"
        ));
    }

    // Validate game not finished
    if ($game->isFinished()) {
        return new PutSquare409Response(new Error(
            code: 'GAME_FINISHED',
            message: "Game is already finished"
        ));
    }

    // Success case
    $game->placeMove($row, $column, $moveRequest->mark);
    return new PutSquare200Response($this->toStatus($game));
}
```

**Affected Operations**:
- `listGames` - Missing 400, 401 error responses
- `createGame` - Missing 400, 401, 422 error responses
- `getGame` - Missing 404 error response
- `deleteGame` - Missing 403, 404 error responses
- `getBoard` - Missing 404 error response
- `getSquare` - Missing 400, 404 error responses
- `putSquare` - Missing 400, 404, 409 error responses
- `getMoves` - Missing 404 error response
- `getPlayerStats` - Missing 404 error response

---

## 2. Response Headers ⚠️

### Spec Definition

#### Pagination Headers (List Games)
```json
"headers": {
  "X-Total-Count": {
    "description": "Total number of games",
    "schema": { "type": "integer" }
  },
  "X-Page-Number": {
    "description": "Current page number",
    "schema": { "type": "integer" }
  }
}
```

#### Location Header (Create Game)
```json
"headers": {
  "Location": {
    "description": "URL of the created game",
    "schema": { "type": "string", "format": "uri" }
  }
}
```

### Generated Scaffolding
✅ Headers documented in PHPDoc comments
❌ No automatic header setting in response classes

### Handler Implementation Status
❌ **Not Implemented** - No headers set in responses

**Production Implementation Needed**:
```php
// ListGamesHandler
public function handle(...): ListGamesResponseInterface
{
    $result = $this->gameRepository->paginate($page, $limit);

    $response = new ListGames200Response($result->data);

    // Set pagination headers
    $response->headers->set('X-Total-Count', $result->total);
    $response->headers->set('X-Page-Number', $page);

    return $response;
}

// CreateGameHandler
public function handle(...): CreateGameResponseInterface
{
    $game = $this->gameRepository->create(...);

    $response = new CreateGame201Response($game);

    // Set Location header
    $response->headers->set('Location', route('api.getGame', ['gameId' => $game->id]));

    return $response;
}
```

**Affected Operations**:
- `listGames` - Missing `X-Total-Count`, `X-Page-Number` headers
- `createGame` - Missing `Location` header

---

## 3. Request Validation (Pattern, Format) ⚠️

### Spec Definition

#### UUID Format Validation
```json
{
  "name": "gameId",
  "schema": {
    "type": "string",
    "format": "uuid"
  }
}
```

#### Username Pattern Validation
```json
"username": {
  "type": "string",
  "minLength": 3,
  "maxLength": 50,
  "pattern": "^[a-zA-Z0-9_-]+$"
}
```

#### Date-Time Format
```json
"createdAt": {
  "type": "string",
  "format": "date-time"
}
```

### Generated Scaffolding
✅ Basic type validation (integer, string)
✅ Min/max constraints for numbers
❌ No UUID validation rules
❌ No regex pattern validation
❌ No date-time format validation

### Handler Implementation Status
❌ **Not Implemented** - Relies on generated validation rules which are incomplete

**Generated Validation (incomplete)**:
```php
protected function listGamesValidationRules(): array
{
    return [
        'page' => 'sometimes|integer|min:1',
        'limit' => 'sometimes|integer|min:1|max:100',
        'status' => 'sometimes',          // ⚠️ No enum validation
        'playerId' => 'sometimes|string', // ⚠️ No UUID validation
    ];
}
```

**Production Implementation Needed**:
```php
protected function listGamesValidationRules(): array
{
    return [
        'page' => 'sometimes|integer|min:1',
        'limit' => 'sometimes|integer|min:1|max:100',
        'status' => ['sometimes', Rule::in(['pending', 'in_progress', 'completed', 'abandoned'])],
        'playerId' => 'sometimes|uuid',  // Add UUID rule
    ];
}

// Custom validation for complex patterns
protected function createGameValidationRules(): array
{
    return [
        'username' => [
            'required',
            'string',
            'min:3',
            'max:50',
            'regex:/^[a-zA-Z0-9_-]+$/',  // Add pattern validation
        ],
    ];
}
```

**Affected Features**:
- UUID path parameters (gameId, playerId) - No UUID format validation
- Username patterns - No regex validation
- Date-time fields - No format validation
- Enum values - Basic validation, but could be more explicit

---

## 4. Security Schemes ⚠️

### Spec Definition
Multiple security schemes defined:
```json
"securitySchemes": {
  "defaultApiKey": { "type": "apiKey", "in": "header", "name": "api-key" },
  "bearerHttpAuthentication": { "type": "http", "scheme": "Bearer" },
  "app2AppOauth": { "type": "oauth2", "flows": {...} },
  "user2AppOauth": { "type": "oauth2", "flows": {...} }
}
```

Applied to operations:
```json
"security": [
  { "bearerHttpAuthentication": [] }
]
```

### Generated Scaffolding
✅ Security documented in spec
❌ No automatic authentication/authorization in controllers
❌ No middleware for security schemes

### Handler Implementation Status
❌ **Not Implemented** - No authentication or authorization logic

**Production Implementation Needed**:
```php
// In Laravel middleware
class AuthenticateWithBearer
{
    public function handle($request, $next)
    {
        $token = $request->bearerToken();

        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = $this->validateToken($token);

        if (!$user) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        $request->setUserResolver(fn() => $user);

        return $next($request);
    }
}

// In DeleteGameHandler
public function handle(string $gameId): DeleteGameResponseInterface
{
    $game = $this->gameRepository->find($gameId);
    $user = $this->request->user();

    // Check authorization
    if ($game->createdBy !== $user->id && !$user->isAdmin()) {
        return new DeleteGame403Response(new Error(
            code: 'FORBIDDEN',
            message: 'You do not have permission to delete this game'
        ));
    }

    $this->gameRepository->delete($gameId);
    return new DeleteGame204Response();
}
```

**Affected Operations**:
- All operations except `getLeaderboard` require authentication
- `deleteGame` requires authorization (owner or admin)

---

## 5. Business Logic & State Management ⚠️

### Spec Implications

#### Game State Validation
- Can't place mark if game is finished
- Can't place mark in occupied square
- Must validate current turn

#### Mode-Specific Logic
```json
"createGameRequest": {
  "properties": {
    "mode": { "$ref": "#/components/schemas/gameMode" },
    "opponentId": {
      "description": "Opponent player ID (required for PvP mode)"
    }
  }
}
```

### Handler Implementation Status
❌ **Not Implemented** - No business rules or state validation

**Production Implementation Needed**:
```php
// CreateGameHandler
public function handle(CreateGameRequest $request): CreateGameResponseInterface
{
    // Validate PvP requires opponent
    if ($request->mode === GameMode::PVP && !$request->opponentId) {
        return new CreateGame422Response(new ValidationError(
            code: 'VALIDATION_ERROR',
            message: 'Validation failed',
            errors: [
                [
                    'field' => 'opponentId',
                    'message' => 'Opponent ID is required for PvP mode'
                ]
            ]
        ));
    }

    // Create game with proper state
    $game = $this->gameRepository->create([
        'mode' => $request->mode,
        'status' => GameStatus::PENDING,
        'playerX' => $this->request->user(),
        'playerO' => $request->opponentId ? $this->userRepository->find($request->opponentId) : null,
    ]);

    return new CreateGame201Response($game);
}
```

**Affected Operations**:
- `createGame` - No mode validation
- `putSquare` - No turn validation, state checking
- All operations - No persistence (in-memory mock data)

---

## 6. Database Persistence ⚠️

### Current Implementation
All handlers return mock/hardcoded data:
- No database queries
- No data persistence
- No relationships between entities

### Production Implementation Needed
```php
// Example with Eloquent ORM
class CreateGameHandler implements CreateGameHandlerInterface
{
    public function __construct(
        private GameRepository $gameRepository,
        private UserRepository $userRepository
    ) {}

    public function handle(CreateGameRequest $request): CreateGameResponseInterface
    {
        $game = $this->gameRepository->create([
            'mode' => $request->mode->value,
            'status' => 'pending',
            'player_x_id' => auth()->id(),
            'player_o_id' => $request->opponentId,
            'is_private' => $request->isPrivate ?? false,
            'metadata' => $request->metadata,
            'board' => json_encode([['.','.','.'], ['.','.','.'], ['.','.','.']])
        ]);

        return new CreateGame201Response(
            $this->mapper->toDto($game)
        );
    }
}
```

**Affected**: All handlers

---

## 7. Additional Properties / Metadata ⚠️

### Spec Definition
```json
"metadata": {
  "type": "object",
  "additionalProperties": true,
  "description": "Additional game metadata"
}
```

### Handler Implementation Status
❌ **Not Implemented** - Metadata ignored in handlers

**Production Implementation**: Store in JSONB column or separate table

---

## Summary Table

| Feature | Spec | Generated | Handler | Gap |
|---------|------|-----------|---------|-----|
| **Error Responses** | ✅ | ✅ | ❌ | High Priority |
| **Response Headers** | ✅ | ⚠️ Documented | ❌ | Medium Priority |
| **UUID Validation** | ✅ | ❌ | ❌ | Medium Priority |
| **Pattern Validation** | ✅ | ❌ | ❌ | Medium Priority |
| **Security/Auth** | ✅ | ❌ | ❌ | High Priority |
| **Business Rules** | ⚠️ Implied | N/A | ❌ | High Priority |
| **Database** | N/A | N/A | ❌ | High Priority |
| **Metadata** | ✅ | ✅ | ❌ | Low Priority |

## Priority for Production

### High Priority (Core Functionality)
1. ✅ Database persistence
2. ✅ Error response handling (404, 409, 422, etc.)
3. ✅ Authentication & authorization
4. ✅ Business logic (game state, turn validation)

### Medium Priority (API Compliance)
1. ⚠️ Response headers (pagination, Location)
2. ⚠️ UUID/pattern validation
3. ⚠️ Enum validation enhancements

### Low Priority (Nice to Have)
1. ℹ️ Metadata storage
2. ℹ️ Advanced validation rules

## Conclusion

The V2 handlers demonstrate the **structure and integration pattern** with generated scaffolding but are **not production-ready**. They serve as starting points that need:

1. Database layer integration
2. Error handling implementation
3. Authentication/authorization middleware
4. Business rule validation
5. Enhanced request validation

The generated scaffolding provides **all necessary interfaces and response classes** to implement these features - the handlers just need to use them properly.
