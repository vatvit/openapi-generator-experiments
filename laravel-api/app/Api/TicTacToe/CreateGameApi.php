<?php declare(strict_types=1);

namespace App\Api\TicTacToe;

use TicTacToeApiV2\Server\Api\CreateGameApiInterface;
use TicTacToeApiV2\Server\Http\Responses\CreateGameApiInterfaceResponseInterface;
use TicTacToeApiV2\Server\Http\Responses\CreateGameApiInterfaceResponseFactory;
use TicTacToeApiV2\Server\Models\Game;
use TicTacToeApiV2\Server\Models\GameMode;
use TicTacToeApiV2\Server\Models\GameStatus;
use TicTacToeApiV2\Server\Models\CreateGameRequest;
use TicTacToeApiV2\Server\Models\Player;
use TicTacToeApiV2\Server\Models\Mark;
use TicTacToeApiV2\Server\Models\Winner;

/**
 * API for createGame operation
 * Creates a new game
 */
class CreateGameApi implements CreateGameApiInterface
{
    public function handle(\TicTacToeApiV2\Server\Models\CreateGameRequest $createGameRequest): CreateGameApiInterfaceResponseInterface
    {
        // Generate unique game ID
        $gameId = sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );

        // Create empty 3x3 board
        $emptyBoard = [
            [Mark::PERIOD, Mark::PERIOD, Mark::PERIOD],
            [Mark::PERIOD, Mark::PERIOD, Mark::PERIOD],
            [Mark::PERIOD, Mark::PERIOD, Mark::PERIOD]
        ];

        // Create player objects
        $playerX = new Player(
            id: 'player-x',
            username: 'playerx',
            displayName: 'Player X',
            avatarUrl: ''
        );
        $playerO = new Player(
            id: 'player-o',
            username: 'playero',
            displayName: 'Player O',
            avatarUrl: ''
        );

        $now = new \DateTime();
        $game = new Game(
            id: $gameId,
            status: GameStatus::IN_PROGRESS,
            mode: $createGameRequest->mode,
            playerX: $playerX,
            playerO: $playerO,
            currentTurn: Mark::X,
            winner: Winner::PERIOD,
            board: $emptyBoard,
            createdAt: $now,
            updatedAt: $now,
            completedAt: $now
        );

        // Return 201 Created with Location header
        return CreateGameApiInterfaceResponseFactory::status201($game, "/games/{$gameId}");
    }
}
