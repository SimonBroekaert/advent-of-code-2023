<?php

namespace App\Console\Commands;

use App\Console\Commands\Traits\SolvesPuzzles;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class DayFourPuzzleTwo extends Command
{
    use SolvesPuzzles;

    protected $signature = '4.2';

    protected $description = 'The total amount of scratchcards you end up with is';

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
                    'instances' => 1,
                ];
            });

        $cards->each(function ($card, $index) use (&$cards) {
            $matchesCount = $card->matches->count();

            if ($index === $cards->count() - 1) {
                return;
            }

            if ($matchesCount === 0) {
                return;
            }

            $cards->slice($index + 1, $matchesCount)->each(function ($nextCard) use ($card) {
                $nextCard->instances = $nextCard->instances + $card->instances;
            });
        });

        $this->solution($cards->sum('instances'));
    }
}
