<?php

namespace Stepanenko3\Version\Package\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class Base extends Command
{
    /**
     * Display the current app version.
     *
     * @param string $format
     */
    public function displayAppVersion($format = 'full')
    {
        $this->info(
            config('app.name').' '.app('stepanenko3.version')->format($format)
        );
    }

    /**
     * Display the current app version.
     *
     * @param string $type
     *
     * @return bool
     */
    public function checkIfCanIncrement($type, $section)
    {
        $method = sprintf('is%sInAbsorbMode', $section = Str::studly($section));

        if (app('stepanenko3.version')->$method($type)) {
            $this->error(
                "{$section} is in git absorb mode, cannot be incremented"
            );

            return false;
        }

        return true;
    }
}
