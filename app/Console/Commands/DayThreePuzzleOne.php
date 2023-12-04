<?php

namespace App\Console\Commands;

use App\Console\Commands\Traits\SolvesPuzzles;
use Illuminate\Console\Command;

class DayThreePuzzleOne extends Command
{
    use SolvesPuzzles;

    protected $signature = '3.1';

    protected $description = 'The sum of all of the part numbers in the engine schematic';

    protected $puzzleInputSource = 'day-3_engine_schematic.txt';

    public function handle()
    {
        $numbers = $this->puzzleInputLines()
            ->map(function ($line, $y) {
                // Group all digits together (except if they are separated by a symbol or dot)
                preg_match_all('/\d+/', $line, $matches, PREG_OFFSET_CAPTURE);

                return collect($matches[0])
                    ->map(function ($match) use ($y) {
                        return (object) [
                            'y' => $y,
                            'x' => (object) [
                                'start' => $match[1],
                                'end' => $match[1] + strlen($match[0]) - 1,
                            ],
                            'value' => (int) $match[0],
                        ];
                    });
            })
            ->flatten(1);

        $symbols = $this->puzzleInputLines()
            ->map(function ($line, $y) {
                // Get all symbols (except dots) and their positions
                preg_match_all('/[^0-9.]/', $line, $matches, PREG_OFFSET_CAPTURE);

                return collect($matches[0])
                    ->map(function ($match) use ($y) {
                        return (object) [
                            'y' => $y,
                            'x' => $match[1],
                        ];
                    });
            })
            ->flatten(1);

        $numbersTouchingSymbols = $symbols
            ->map(function ($symbol) use ($numbers) {
                return $numbers
                    ->filter(function ($number) use ($symbol) {
                        // X direction
                        if ($symbol->y === $number->y) {
                            return $symbol->x - 1 === $number->x->end
                                || $symbol->x + 1 === $number->x->start;
                        }

                        // Y direction (also diagonal)
                        if (in_array($symbol->x, range($number->x->start -1, $number->x->end + 1))) {
                            return $symbol->y - 1 === $number->y
                                || $symbol->y + 1 === $number->y;
                        }

                        return false;
                    });
            })
            ->flatten(1);

        $this->solution($numbersTouchingSymbols->sum('value'));
    }
}
