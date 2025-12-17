<?php

namespace Tests\Feature;

use Tests\TestCase;
use TicTacToeApiV2\Server\Api\CreateGame201Response;
use TicTacToeApiV2\Server\Api\ListGames200Response;
use TicTacToeApiV2\Server\Models\Game;
use TicTacToeApiV2\Server\Models\GameListResponse;
use TicTacToeApiV2\Server\Models\GameStatus;
use TicTacToeApiV2\Server\Models\GameMode;
use TicTacToeApiV2\Server\Models\Mark;
use TicTacToeApiV2\Server\Models\Winner;
use TicTacToeApiV2\Server\Models\Player;
use TicTacToeApiV2\Server\Models\Pagination;

class ResponseHeaderValidationTest extends TestCase
{
    /**
     * Test that required Location header throws exception when missing
     *
     * NOTE: This test is skipped because the PSR-4 compliant generator
     * does not generate response wrapper classes with header validation.
     * Controllers return models directly and set headers manually.
     */
    public function test_create_game_response_throws_exception_when_location_header_missing(): void
    {
        $this->markTestSkipped('Response wrapper classes not generated in PSR-4 mode');

        $game = new Game(
            id: 'test-game-id',
            status: GameStatus::PENDING,
            mode: GameMode::PVP,
            playerX: new Player(
                id: 'player-x',
                username: 'PlayerX',
                displayName: 'Player X',
                avatarUrl: 'https://example.com/avatar-x.png'
            ),
            playerO: new Player(
                id: 'player-o',
                username: 'PlayerO',
                displayName: 'Player O',
                avatarUrl: 'https://example.com/avatar-o.png'
            ),
            currentTurn: Mark::X,
            winner: Winner::PERIOD,
            board: [
                [Mark::PERIOD, Mark::PERIOD, Mark::PERIOD],
                [Mark::PERIOD, Mark::PERIOD, Mark::PERIOD],
                [Mark::PERIOD, Mark::PERIOD, Mark::PERIOD]
            ],
            createdAt: new \DateTime(),
            updatedAt: new \DateTime(),
            completedAt: new \DateTime()
        );

        $response = new CreateGame201Response($game);

        // Should throw RuntimeException when Location header is not set
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Required header "Location" is not set for CreateGame201Response');

        $response->toJsonResponse();
    }

    /**
     * Test that Location header is added when set via setter
     */
    public function test_create_game_response_includes_location_header_when_set(): void
    {
        $this->markTestSkipped('Response wrapper classes not generated in PSR-4 mode');

        $game = new Game(
            id: 'test-game-id',
            status: GameStatus::PENDING,
            mode: GameMode::PVP,
            playerX: new Player(
                id: 'player-x',
                username: 'PlayerX',
                displayName: 'Player X',
                avatarUrl: 'https://example.com/avatar-x.png'
            ),
            playerO: new Player(
                id: 'player-o',
                username: 'PlayerO',
                displayName: 'Player O',
                avatarUrl: 'https://example.com/avatar-o.png'
            ),
            currentTurn: Mark::X,
            winner: Winner::PERIOD,
            board: [
                [Mark::PERIOD, Mark::PERIOD, Mark::PERIOD],
                [Mark::PERIOD, Mark::PERIOD, Mark::PERIOD],
                [Mark::PERIOD, Mark::PERIOD, Mark::PERIOD]
            ],
            createdAt: new \DateTime(),
            updatedAt: new \DateTime(),
            completedAt: new \DateTime()
        );

        $response = (new CreateGame201Response($game))
            ->setLocation('/v1/games/test-game-id');

        $jsonResponse = $response->toJsonResponse();

        // Verify status code
        $this->assertEquals(201, $jsonResponse->getStatusCode());

        // Verify Location header is present
        $this->assertTrue($jsonResponse->headers->has('Location'));
        $this->assertEquals('/v1/games/test-game-id', $jsonResponse->headers->get('Location'));
    }

    /**
     * Test that required X-Total-Count header throws exception when missing
     */
    public function test_list_games_response_throws_exception_when_total_count_header_missing(): void
    {
        $this->markTestSkipped('Response wrapper classes not generated in PSR-4 mode');

        $gameListResponse = new GameListResponse(
            games: [],
            pagination: new Pagination(
                page: 1,
                limit: 20,
                total: 0,
                hasNext: false,
                hasPrevious: false
            )
        );

        $response = new ListGames200Response($gameListResponse);

        // Should throw RuntimeException when X-Total-Count header is not set
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Required header "X-Total-Count" is not set for ListGames200Response');

        $response->toJsonResponse();
    }

    /**
     * Test that optional X-Page-Number header does not throw exception when missing
     */
    public function test_list_games_response_allows_optional_page_number_header_missing(): void
    {
        $this->markTestSkipped('Response wrapper classes not generated in PSR-4 mode');

        $gameListResponse = new GameListResponse(
            games: [],
            pagination: new Pagination(
                page: 1,
                limit: 20,
                total: 0,
                hasNext: false,
                hasPrevious: false
            )
        );

        $response = (new ListGames200Response($gameListResponse))
            ->setXTotalCount(0); // Only set required header

        $jsonResponse = $response->toJsonResponse();

        // Verify status code
        $this->assertEquals(200, $jsonResponse->getStatusCode());

        // Verify X-Total-Count is present
        $this->assertTrue($jsonResponse->headers->has('X-Total-Count'));
        $this->assertEquals('0', $jsonResponse->headers->get('X-Total-Count'));

        // Verify X-Page-Number is NOT present (optional, not set)
        $this->assertFalse($jsonResponse->headers->has('X-Page-Number'));
    }

    /**
     * Test that both headers are included when both are set
     */
    public function test_list_games_response_includes_both_headers_when_set(): void
    {
        $this->markTestSkipped('Response wrapper classes not generated in PSR-4 mode');

        $gameListResponse = new GameListResponse(
            games: [],
            pagination: new Pagination(
                page: 2,
                limit: 20,
                total: 42,
                hasNext: true,
                hasPrevious: true
            )
        );

        $response = (new ListGames200Response($gameListResponse))
            ->setXTotalCount(42)
            ->setXPageNumber(2);

        $jsonResponse = $response->toJsonResponse();

        // Verify status code
        $this->assertEquals(200, $jsonResponse->getStatusCode());

        // Verify both headers are present
        $this->assertTrue($jsonResponse->headers->has('X-Total-Count'));
        $this->assertEquals('42', $jsonResponse->headers->get('X-Total-Count'));

        $this->assertTrue($jsonResponse->headers->has('X-Page-Number'));
        $this->assertEquals('2', $jsonResponse->headers->get('X-Page-Number'));
    }

    /**
     * Test fluent interface (method chaining)
     */
    public function test_setter_methods_support_fluent_interface(): void
    {
        $this->markTestSkipped('Response wrapper classes not generated in PSR-4 mode');

        $gameListResponse = new GameListResponse(
            games: [],
            pagination: new Pagination(
                page: 1,
                limit: 20,
                total: 10,
                hasNext: false,
                hasPrevious: false
            )
        );

        // Test that setters return self for chaining
        $response = new ListGames200Response($gameListResponse);
        $result = $response->setXTotalCount(10);

        $this->assertSame($response, $result);

        // Test full chain
        $response2 = (new ListGames200Response($gameListResponse))
            ->setXTotalCount(10)
            ->setXPageNumber(1);

        $this->assertInstanceOf(ListGames200Response::class, $response2);
    }
}
