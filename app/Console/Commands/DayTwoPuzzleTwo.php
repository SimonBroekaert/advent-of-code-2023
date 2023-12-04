<?php

namespace App\Console\Commands;

use App\Console\Commands\Traits\SolvesPuzzles;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class DayTwoPuzzleTwo extends Command
{
    use SolvesPuzzles;

    protected $signature = '2.2';

    protected $description = 'The sum of the power of these sets';

    protected $puzzleInputSource = 'day-2_game_results.txt';

    protected $colors = [
        'red',
        'green',
        'blue',
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
            })
            ->map(function ($game) {
                $game->power = collect($this->colors)
                    ->map(function ($color) use ($game) {
                        return $game->draws->map(function ($draw) use ($color) {
                            return $draw->get($color, 0);
                        })->max();
                    })
                    ->reduce(function ($carry, $max) {
                        return $carry * $max;
                    }, 1);

                return $game;
            });

        $this->solution($games->sum('power'));
    }
}
