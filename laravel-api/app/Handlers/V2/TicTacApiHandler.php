<?php declare(strict_types=1);

namespace App\Handlers\V2;

use TicTacToeApiV2\Server\Api\TicTacApiInterface;
use TicTacToeApiV2\Server\Models\Status;
use TicTacToeApiV2\Server\Models\Mark;
use TicTacToeApiV2\Server\Models\Winner;
use TicTacToeApiV2\Server\Models\GameStatus;
use TicTacToeApiV2\Server\Models\NotFoundError;

/**
 * Handler for TicTac API operations (untagged operations)
 * Implements: getBoard
 */
class TicTacApiHandler implements TicTacApiInterface
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
}
