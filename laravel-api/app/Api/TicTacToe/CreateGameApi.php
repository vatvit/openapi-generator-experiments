<?php declare(strict_types=1);

namespace App\Api\TicTacToe;

use TicTacToeApiV2\Server\Api\CreateGameApiInterface;
use TicTacToeApiV2\Server\Http\Responses\CreateGameResponseInterface;
use TicTacToeApiV2\Server\Http\Responses\CreateGame201Response;
use TicTacToeApiV2\Server\Models\Game;
use TicTacToeApiV2\Server\Models\GameMode;
use TicTacToeApiV2\Server\Models\GameStatus;

/**
 * API for createGame operation
 * Creates a new game
 */
class CreateGameApi implements CreateGameApiInterface
{
    public function handle(): CreateGameResponseInterface
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

        $game = new Game(
            id: $gameId,
            mode: GameMode::PVP,
            status: GameStatus::IN_PROGRESS,
            createdAt: new \DateTime()
        );

        // Return 201 Created with Location header
        $response = new CreateGame201Response($game);
        $response->setLocation("/games/{$gameId}");

        return $response;
    }
}
