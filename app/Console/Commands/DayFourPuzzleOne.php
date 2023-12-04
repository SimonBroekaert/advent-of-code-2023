<?php

namespace App\Console\Commands;

use App\Console\Commands\Traits\SolvesPuzzles;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class DayFourPuzzleOne extends Command
{
    use SolvesPuzzles;

    protected $signature = '4.1';

    protected $description = 'The scratchcards are worth a total of';

    protected $puzzleInputSource = 'day-4_scratchcard_results.txt';

    public function handle()
    {
        $cards = $this->puzzleInputLines()
            ->map(function ($card) {
                $winningNumbers = collect(explode(' ', trim(Str::after(Str::before($card, '|'), ':'))))
                    ->map(function ($number) {
                        return (int) trim($number);
                    })
                    ->filter()
                    ->sort();

                $playerNumbers = collect(explode(' ', trim(Str::after($card, '|'))))
                    ->map(function ($number) {
                        return (int) trim($number);
                    })
                    ->filter()
                    ->sort();

                return (object) [
                    'winningNumbers' => $winningNumbers,
                    'playerNumbers' => $playerNumbers,
                    'matches' => $winningNumbers->intersect($playerNumbers),
                ];
            });

        $scores = $cards->map(function ($card) {
            $count = $card->matches->count();

            return $count > 1 ? pow(2, $count - 1) : $count;
        });

        $this->solution($scores->sum());
    }
}
