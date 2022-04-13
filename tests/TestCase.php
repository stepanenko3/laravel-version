<?php

namespace Stepanenko3\Version\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;
use Stepanenko3\Version\Package\ServiceProvider as VersionServiceProvider;
use PragmaRX\Yaml\Package\ServiceProvider as YamlServiceProvider;

abstract class TestCase extends OrchestraTestCase
{
    protected function getPackageProviders($app)
    {
        copy(
            __DIR__.'/../src/config/version.yml',
            config_path('version.yml')
        );

        return [VersionServiceProvider::class, YamlServiceProvider::class];
    }
}
