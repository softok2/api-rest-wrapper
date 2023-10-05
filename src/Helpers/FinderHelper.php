<?php

namespace Softok2\RestApiClient\Helpers;

use Illuminate\Support\Arr;
use SplFileInfo;
use Symfony\Component\Finder\Finder;

class FinderHelper
{
    /**
     * Load all filenames in the given directory.
     *
     * @param $paths
     * @return array
     */
    public static function loadFileNames($paths): array
    {
        return collect(static::load($paths))
            ->map(function (SplFileInfo $file) {
                return $file->getFilename();
            })->toArray();
    }

    /**
     * Load all files in the given directory.
     *
     * @param string|array $paths
     * @return array
     */
    public static function load($paths): array
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
            $files [] = $file;
        }

        return $files;
    }
}
