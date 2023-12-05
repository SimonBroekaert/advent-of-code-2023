<?php

namespace App\Console\Commands;

use App\Console\Commands\Traits\SolvesPuzzles;
use Illuminate\Console\Command;
use Illuminate\Support\Str;

class DayFivePuzzleOne extends Command
{
    use SolvesPuzzles;

    protected $signature = '5.1';

    protected $description = 'The lowest location number that corresponds to any of the initial seeds is';

    protected $puzzleInputSource = 'day-5_almanac_list.txt';

    public function handle()
    {
        $lines = $this->puzzleInputLines();

        $seedsLine = $lines->firstWhere(function ($line) {
            return str_contains($line, 'seeds:');
        });

        $seeds = collect(explode(' ', Str::after($seedsLine, 'seeds: ')))
            ->map(function ($seed) {
                return (int) trim($seed);
            })
            ->filter();

        $maps = collect([]);

        $lines->each(function ($line) use (&$maps) {
            $isMapStartLine = preg_match('/^(\w+)-to-(\w+) map:/', $line);

            if ($isMapStartLine) {
                $maps->push((object) [
                    'source' => trim(Str::before($line, '-to-')),
                    'destination' => trim(Str::between($line, '-to-', 'map:')),
                    'mappings' => collect([]),
                ]);
            } elseif ($maps->count() > 0) {
                // is a mapping line
                list($destinationStart, $sourceStart, $range) = explode(' ', $line);

                $maps->last()->mappings->push((object) [
                    'source' => (int) trim($sourceStart),
                    'destination' => (int) trim($destinationStart),
                    'range' => (int) $range,
                ]);
            }
        });

        $seedsToLocationsSteps = $this->getPathFromSourceToDestination('seed', 'location', $maps);

        $seedsToLocations = $seeds->map(function ($seed) use ($seedsToLocationsSteps) {
            $value = $seed;

            foreach ($seedsToLocationsSteps as $step) {
                $mapping = $step->mappings->firstWhere(function ($mapping) use ($value) {
                    return $mapping->source <= $value && ($mapping->source + $mapping->range - 1) >= $value;
                });

                if (is_null($mapping)) {
                    continue;
                }

                $sourceDiff = $value - $mapping->source;

                $value = $mapping->destination + $sourceDiff;
            }

            return $value;
        });

        $this->solution($seedsToLocations->min());
    }

    protected function getPathFromSourceToDestination($source, $destination, $maps, $path = [])
    {
        $path = collect($path);

        $map = $maps->firstWhere('source', $source);

        if (is_null($map)) {
            $this->error('No map found from source: ' . $source . ' to destination: ' . $destination);

            return;
        }

        $path->push($map);

        if ($map->destination !== $destination) {
            $path = $this->getPathFromSourceToDestination($map->destination, $destination, $maps, $path);
        }

        return collect($path);
    }
}
