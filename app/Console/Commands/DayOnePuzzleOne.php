<?php

namespace App\Console\Commands;

use App\Console\Commands\Traits\SolvesPuzzles;
use Illuminate\Console\Command;

class DayOnePuzzleOne extends Command
{
    use SolvesPuzzles;

    protected $signature = '1.1';

    protected $description = 'The sum of all of the calibration values equals';

    protected $puzzleInputSource = 'day-1_calibration_document.txt';

    public function handle()
    {
        $lines = $this->puzzleInputLines();

        $sum = $lines->map(function ($line) {
            $digits = collect(str_split($line))
                ->filter(function ($character) {
                    return is_numeric($character);
                });

            $firstDigit = $digits->first();
            $lastDigit = $digits->last();

            return $firstDigit . $lastDigit;
        })
            ->sum();

        $this->solution($sum);
    }
}
