<?php

namespace Stepanenko3\Version\Package\Console\Commands;

class Minor extends Base
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'version:minor';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Increment app minor version';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->checkIfCanIncrement('current', 'version')) {
            $number = app('stepanenko3.version')->incrementMinor();

            $this->info("New minor version: {$number}");

            $this->displayAppVersion();
        }
    }
}
