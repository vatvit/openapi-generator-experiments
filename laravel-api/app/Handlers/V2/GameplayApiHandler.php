<?php declare(strict_types=1);

namespace App\Handlers\V2;

use TicTacToeApiV2\Server\Api\GameplayApiInterface;
use TicTacToeApiV2\Server\Models\Game;
use TicTacToeApiV2\Server\Models\GameStatus;
use TicTacToeApiV2\Server\Models\GameMode;
use TicTacToeApiV2\Server\Models\Player;
use TicTacToeApiV2\Server\Models\Mark;
use TicTacToeApiV2\Server\Models\Winner;
use TicTacToeApiV2\Server\Models\Status;
use TicTacToeApiV2\Server\Models\MoveHistory;
use TicTacToeApiV2\Server\Models\SquareResponse;
use TicTacToeApiV2\Server\Models\MoveRequest;
use TicTacToeApiV2\Server\Models\BadRequestError;
use TicTacToeApiV2\Server\Models\NotFoundError;

/**
 * Handler for Gameplay API operations
 * Implements: getBoard, getGame, getMoves, getSquare, putSquare
 */
class GameplayApiHandler implements GameplayApiInterface
{
    public function getBoard(string $gameId): Status | NotFoundError
    {
        // Mock implementation - return sample board
        return new Status(
            winner: Winner::PERIOD,
            board: [
                [Mark::X, Mark::PERIOD, Mark::PERIOD],
                [Mark::PERIOD, Mark::O, Mark::PERIOD],
                [Mark::PERIOD, Mark::PERIOD, Mark::PERIOD]
            ]
        );
    }

    public function getGame(string $gameId): Game | NotFoundError
    {
        // Mock implementation - return a sample game
        $playerX = new Player(
            id: 'player-x-123',
            username: 'PlayerX',
            displayName: 'Player X',
            avatarUrl: 'https://example.com/avatar-x.png'
        );

        $playerO = new Player(
            id: 'player-o-456',
            username: 'PlayerO',
            displayName: 'Player O',
            avatarUrl: 'https://example.com/avatar-o.png'
        );

        return new Game(
            id: $gameId,
            status: GameStatus::IN_PROGRESS,
            mode: GameMode::PVP,
            playerX: $playerX,
            playerO: $playerO,
            currentTurn: Mark::X,
            winner: Winner::PERIOD,
            board: [
                [Mark::X, Mark::PERIOD, Mark::PERIOD],
                [Mark::PERIOD, Mark::O, Mark::PERIOD],
                [Mark::PERIOD, Mark::PERIOD, Mark::PERIOD]
            ],
            createdAt: new \DateTime('-1 hour'),
            updatedAt: new \DateTime(),
            completedAt: new \DateTime('2099-01-01')
        );
    }

    public function getMoves(string $gameId): MoveHistory | NotFoundError
    {
        // Mock implementation - return empty move history
        return new MoveHistory(
            gameId: $gameId,
            moves: []
        );
    }

    public function getSquare(string $gameId, int $row, int $column): SquareResponse | BadRequestError | NotFoundError
    {
        // Mock implementation - return empty square
        return new SquareResponse(
            row: $row,
            column: $column,
            mark: Mark::PERIOD
        );
    }

    public function putSquare(string $gameId, int $row, int $column, MoveRequest $moveRequest): Status | BadRequestError | NotFoundError
    {
        // Mock implementation - place the mark and return updated board
        $board = [
            [Mark::PERIOD, Mark::PERIOD, Mark::PERIOD],
            [Mark::PERIOD, Mark::PERIOD, Mark::PERIOD],
            [Mark::PERIOD, Mark::PERIOD, Mark::PERIOD]
        ];

        // Place the mark (simplified - no validation)
        if ($row >= 0 && $row < 3 && $column >= 0 && $column < 3) {
            $board[$row][$column] = $moveRequest->mark ?? Mark::X;
        }

        return new Status(
            winner: Winner::PERIOD,
            board: $board
        );
    }
}
