<?php

namespace App\Console\Commands;

use App\Console\Commands\Traits\SolvesPuzzles;
use Illuminate\Console\Command;

class DayOnePuzzleTwo extends Command
{
    use SolvesPuzzles;

    protected $signature = '1.2';

    protected $description = 'The sum of all of the calibration values (with spelled out digits) equals';

    protected $puzzleInputSource = 'day-1_calibration_document.txt';

    protected $validStringDigits = [
        '0' => 0,
        '1' => 1,
        '2' => 2,
        '3' => 3,
        '4' => 4,
        '5' => 5,
        '6' => 6,
        '7' => 7,
        '8' => 8,
        '9' => 9,
        'one' => 1,
        'two' => 2,
        'three' => 3,
        'four' => 4,
        'five' => 5,
        'six' => 6,
        'seven' => 7,
        'eight' => 8,
        'nine' => 9,
    ];

    public function handle()
    {
        $lines = $this->puzzleInputLines();

        $sum = $lines->map(function ($line) {
            $foundDigits = collect($this->validStringDigits)
                ->map(function ($numericDigit, $stringDigit) use ($line) {
                    return (object) [
                        'numericDigit' => $numericDigit,
                        'stringDigit' => $stringDigit,
                        'firstIndex' => strpos($line, $stringDigit),
                        'lastIndex' => strrpos($line, $stringDigit),
                    ];
                })
                ->filter(function ($digit) {
                    return $digit->firstIndex !== false || $digit->lastIndex !== false;
                });

            $firstDigit = $foundDigits->sortBy('firstIndex')->first()->numericDigit;
            $lastDigit = $foundDigits->sortByDesc('lastIndex')->first()->numericDigit;

            return $firstDigit . $lastDigit;
        })
            ->sum();

        $this->solution($sum);
    }
}
