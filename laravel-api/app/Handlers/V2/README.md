# TicTacToe V2 API Handlers

Simple handler implementations for the extended TicTacToe API (10 operations).

## Overview

These handlers implement the business logic for the TicTacToe API v2. They are intentionally simple, returning mock data to demonstrate the handler pattern and integration with the generated server.

## Handlers

### Game Management

#### `CreateGameHandler`
- **Operation**: `POST /games`
- **Purpose**: Creates a new TicTacToe game
- **Implementation**: Returns a mock game with empty board and generated UUID
- **Response**: HTTP 201 with Game object

#### `ListGamesHandler`
- **Operation**: `GET /games`
- **Purpose**: Lists games with pagination and filtering
- **Implementation**: Returns empty game list with pagination metadata
- **Parameters**: `page`, `limit`, `status`, `playerId` (all optional)
- **Response**: HTTP 200 with GameListResponse containing games array and pagination

#### `GetGameHandler`
- **Operation**: `GET /games/{gameId}`
- **Purpose**: Retrieves detailed information about a specific game
- **Implementation**: Returns mock game in progress with sample board
- **Response**: HTTP 200 with Game object

#### `DeleteGameHandler`
- **Operation**: `DELETE /games/{gameId}`
- **Purpose**: Deletes a game
- **Implementation**: Returns success status (no database interaction)
- **Response**: HTTP 204 (No Content)

### Gameplay

#### `GetBoardHandler`
- **Operation**: `GET /games/{gameId}/board`
- **Purpose**: Retrieves current board state for a game
- **Implementation**: Returns empty 3x3 board with no winner
- **Response**: HTTP 200 with Status object (board + winner)

#### `GetSquareHandler`
- **Operation**: `GET /games/{gameId}/board/{row}/{column}`
- **Purpose**: Retrieves a single board square
- **Implementation**: Returns empty square (`.`) at requested coordinates
- **Response**: HTTP 200 with SquareResponse object

#### `PutSquareHandler`
- **Operation**: `PUT /games/{gameId}/board/{row}/{column}`
- **Purpose**: Places a mark on the board
- **Implementation**: Returns board with mark placed at requested position
- **Parameters**: `gameId`, `row`, `column`, `MoveRequest` body
- **Response**: HTTP 200 with Status object showing updated board

#### `GetMovesHandler`
- **Operation**: `GET /games/{gameId}/moves`
- **Purpose**: Retrieves complete move history for a game
- **Implementation**: Returns empty move history
- **Response**: HTTP 200 with MoveHistory object

### Statistics

#### `GetPlayerStatsHandler`
- **Operation**: `GET /players/{playerId}/stats`
- **Purpose**: Retrieves comprehensive statistics for a player
- **Implementation**: Returns mock stats (10 games, 50% win rate)
- **Response**: HTTP 200 with PlayerStats object

#### `GetLeaderboardHandler`
- **Operation**: `GET /leaderboard`
- **Purpose**: Retrieves global leaderboard
- **Implementation**: Returns empty leaderboard
- **Parameters**: `timeframe` (daily/weekly/monthly/all-time), `limit`
- **Response**: HTTP 200 with Leaderboard object

## Integration with Generated Server

Each handler:
1. **Implements the generated interface** from `lib/Api/*ApiInterface.php`
2. **Uses generated model classes** for type safety
3. **Returns response objects** that implement `*ResponseInterface`
4. **Converts to JSON** via `toJsonResponse()` method

Example flow:
```
Request → Controller → Handler → Response Object → JsonResponse
         (generated)  (custom)   (generated)       (generated)
```

## Usage in Laravel

Handlers are bound to the service container and automatically injected into controllers via dependency injection:

```php
// In DefaultController (generated)
public function createGame(
    CreateGameHandlerInterface $handler,  // Auto-injected
    Request $request
): JsonResponse
{
    $validated = $request->validate($this->createGameValidationRules());
    $createGameRequest = $serde->deserialize(...);

    $response = $handler->handle($createGameRequest);

    return $response->toJsonResponse();
}
```

## Extending for Production

To make these production-ready:

1. **Add database persistence**
   ```php
   use App\Models\Game as GameModel;

   public function handle(CreateGameRequest $request): CreateGameResponseInterface
   {
       $gameModel = GameModel::create([...]);
       $game = $this->mapToDto($gameModel);
       return new CreateGame201Response($game);
   }
   ```

2. **Add validation logic**
   ```php
   public function handle(string $gameId, int $row, int $column, MoveRequest $request)
   {
       $game = GameModel::findOrFail($gameId);

       if ($game->isFinished()) {
           throw new ConflictException('Game already finished');
       }

       if (!$game->isSquareEmpty($row, $column)) {
           throw new ConflictException('Square already occupied');
       }

       $game->placeMarc($row, $column, $request->mark);
       // ...
   }
   ```

3. **Add business rules**
   ```php
   public function handle(CreateGameRequest $request): CreateGameResponseInterface
   {
       if ($request->mode === GameMode::PVP && !$request->opponentId) {
           throw new ValidationException('opponentId required for PvP mode');
       }
       // ...
   }
   ```

4. **Add error responses**
   ```php
   try {
       $game = GameModel::findOrFail($gameId);
       return new GetGame200Response($this->mapToDto($game));
   } catch (ModelNotFoundException $e) {
       return new GetGame404Response(new Error(
           code: 'GAME_NOT_FOUND',
           message: "Game {$gameId} not found"
       ));
   }
   ```

## Testing

Example test for a handler:

```php
use Tests\TestCase;
use App\Handlers\V2\CreateGameHandler;

class CreateGameHandlerTest extends TestCase
{
    public function test_creates_game_with_empty_board(): void
    {
        $handler = new CreateGameHandler();
        $request = new CreateGameRequest(mode: GameMode::PVP);

        $response = $handler->handle($request);
        $jsonResponse = $response->toJsonResponse();

        $this->assertEquals(201, $jsonResponse->getStatusCode());
        $data = json_decode($jsonResponse->getContent(), true);
        $this->assertEquals('pending', $data['status']);
        $this->assertCount(3, $data['board']);
    }
}
```

## File Structure

```
app/Handlers/V2/
├── CreateGameHandler.php         # Game Management
├── DeleteGameHandler.php         # Game Management
├── GetGameHandler.php            # Game Management
├── ListGamesHandler.php          # Game Management
├── GetBoardHandler.php           # Gameplay
├── GetMovesHandler.php           # Gameplay
├── GetSquareHandler.php          # Gameplay
├── PutSquareHandler.php          # Gameplay
├── GetPlayerStatsHandler.php    # Statistics
├── GetLeaderboardHandler.php    # Statistics
└── README.md                     # This file
```

## Key Benefits

1. **Type Safety** - All parameters and returns are strongly typed via generated interfaces
2. **Clear Separation** - Business logic separated from HTTP concerns
3. **Testable** - Handlers can be unit tested without HTTP layer
4. **Maintainable** - Simple, focused classes with single responsibility
5. **Scalable** - Easy to add database persistence and complex business rules
