<?php declare(strict_types=1);

namespace App\Api\V2;

use TicTacToeApiOverride\Server\Api\CreateGameApiInterface;
use TicTacToeApiOverride\Server\Http\Responses\CreateGameResponseInterface;
use TicTacToeApiOverride\Server\Http\Responses\CreateGame201Response;
use TicTacToeApiOverride\Server\Models\Game;
use TicTacToeApiOverride\Server\Models\GameMode;
use TicTacToeApiOverride\Server\Models\GameStatus;

/**
 * CreateGame API implementation
 * Creates a new game
 */
class CreateGameApi implements CreateGameApiInterface
{
    public function handle(): CreateGameResponseInterface
    {
        // Create new game
        $game = new Game(
            id: $this->generateGameId(),
            mode: GameMode::PVP,
            status: GameStatus::IN_PROGRESS,
            createdAt: new \DateTime()
        );

        // Return 201 Created with Location header
        $response = new CreateGame201Response($game);
        $response->setLocation("/games/{$game->id}");

        return $response;
    }

    private function generateGameId(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
