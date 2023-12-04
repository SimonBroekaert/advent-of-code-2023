<?php

namespace App\Console\Commands\Traits;

use Exception;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

trait SolvesPuzzles
{
    public function puzzleInput(): string
    {
        if (! $this->puzzleInputSource) {
            throw new Exception('No puzzle input source defined');
        }

        return Storage::disk('puzzle-input')
            ->read($this->puzzleInputSource);
    }

    public function puzzleInputLines(): Collection
    {
        return collect(
            explode("\n", $this->puzzleInput())
        )
            ->filter();
    }

    public function solution($solution)
    {
        info('----------------------------------');
        info('The solution to the puzzle "' . Str::headline(class_basename($this)) . '" is:');
        note($this->description);
        info($solution);
        info('----------------------------------');
    }
}
