<?php

namespace App\Console\Commands;

use App\Console\Commands\Traits\SolvesPuzzles;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class DayTwoPuzzleOne extends Command
{
    use SolvesPuzzles;

    protected $signature = '2.1';

    protected $description = 'The sum of the IDs of those games';

    protected $puzzleInputSource = 'day-2_game_results.txt';

    protected $maxCubesPerColor = [
        'red' => 12,
        'green' => 13,
        'blue' => 14,
    ];

    public function handle()
    {
        $games = $this->puzzleInputLines()
            ->map(function ($game) {
                return (object) [
                    'id' => (int) Str::after(Str::before($game, ':'), 'Game '),
                    'draws' => collect(explode(';', Str::after($game, ':')))
                        ->map(function ($draw) {
                            return collect(explode(',', $draw))
                                ->mapWithKeys(function ($cubeWithCount) {
                                    $cubeWithCount = trim($cubeWithCount);

                                    list($count, $color) = explode(' ', $cubeWithCount);

                                    return [$color => (int) $count];
                                });
                        }),
                ];
            });

        $validGames = $games->filter(function ($game) {
            return $game->draws->every(function ($draw) {
                return $draw->every(function ($count, $color) {
                    return $count <= $this->maxCubesPerColor[$color];
                });
            });
        });

        $this->solution($validGames->sum('id'));
    }
}
