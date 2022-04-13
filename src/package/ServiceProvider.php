<?php

namespace Stepanenko3\Version\Package;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider as IlluminateServiceProvider;
use Stepanenko3\Version\Package\Console\Commands\Absorb;
use Stepanenko3\Version\Package\Console\Commands\Commit;
use Stepanenko3\Version\Package\Console\Commands\Major;
use Stepanenko3\Version\Package\Console\Commands\Minor;
use Stepanenko3\Version\Package\Console\Commands\Patch;
use Stepanenko3\Version\Package\Console\Commands\Show;
use Stepanenko3\Version\Package\Console\Commands\Timestamp;
use Stepanenko3\Version\Package\Console\Commands\Version as VersionCommand;
use Stepanenko3\Version\Package\Support\Config;
use Stepanenko3\Version\Package\Support\Constants;
use PragmaRX\Yaml\Package\Yaml;

class ServiceProvider extends IlluminateServiceProvider
{
    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * The package config.
     *
     * @var Config
     */
    protected $config;

    /**
     * Console commands to be instantiated.
     *
     * @var array
     */
    protected $commandList = [
        'stepanenko3.version.command' => VersionCommand::class,

        'stepanenko3.version.commit.command' => Commit::class,

        'stepanenko3.version.show.command' => Show::class,

        'stepanenko3.version.major.command' => Major::class,

        'stepanenko3.version.minor.command' => Minor::class,

        'stepanenko3.version.patch.command' => Patch::class,

        'stepanenko3.version.absorb.command' => Absorb::class,

        'stepanenko3.version.absorb.timestamp' => Timestamp::class,
    ];

    /**
     * Boot Service Provider.
     */
    public function boot()
    {
        $this->publishConfiguration();

        $this->registerBlade();
    }

    /**
     * Get the config file path.
     *
     * @return string
     */
    protected function getConfigFile()
    {
        return config_path('version.yml');
    }

    /**
     * Get the original config file.
     *
     * @return string
     */
    protected function getConfigFileStub()
    {
        return __DIR__.'/../config/version.yml';
    }

    /**
     * Load config.
     */
    protected function loadConfig()
    {
        $this->config = new Config(new Yaml());

        $this->config->setConfigFile($this->getConfigFile());

        $this->config->loadConfig();
    }

    /**
     * Configure config path.
     */
    protected function publishConfiguration()
    {
        $this->publishes([
            $this->getConfigFileStub() => $this->getConfigFile(),
        ]);
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerService();

        $this->loadConfig();

        $this->registerCommands();
    }

    /**
     * Register Blade directives.
     */
    protected function registerBlade()
    {
        Blade::directive(
            $this->config->get('blade-directive', 'version'),
            function ($format = Constants::DEFAULT_FORMAT) {
                return "<?php echo app('stepanenko3.version')->format($format); ?>";
            }
        );
    }

    /**
     * Register command.
     *
     * @param $name
     * @param $commandClass string
     */
    protected function registerCommand($name, $commandClass)
    {
        $this->app->singleton($name, function () use ($commandClass) {
            return new $commandClass();
        });

        $this->commands($name);
    }

    /**
     * Register Artisan commands.
     */
    protected function registerCommands()
    {
        collect($this->commandList)->each(function ($commandClass, $key) {
            $this->registerCommand($key, $commandClass);
        });
    }

    /**
     * Register service service.
     */
    protected function registerService()
    {
        $this->app->singleton('stepanenko3.version', function () {
            $version = new Version($this->config);

            $version->setConfigFileStub($this->getConfigFileStub());

            return $version;
        });
    }
}
