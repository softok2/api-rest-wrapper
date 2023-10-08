<?php

namespace Softok2\RestApiClient\Helpers;

use Illuminate\Support\Arr;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

class FinderHelper
{
    /**
     * Load all files in the given directory.
     *
     * @return array<SplFileInfo>
     */
    public static function load(mixed $paths)
    {
        $paths = array_unique(Arr::wrap($paths));

        $paths = array_filter($paths, function ($path) {
            return is_dir($path);
        });

        if (empty($paths)) {
            return [];
        }

        $files = [];

        foreach ((new Finder())->in($paths)->files() as $file) {
            $files[] = $file;
        }

        return $files;
    }
}
