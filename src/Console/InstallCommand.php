<?php

namespace Nmflabs\LaravelBehatDusk\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'behat-dusk:install';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install LaravelBehatDusk into the application';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->call('dusk:install');

        if (! is_dir(config('behat-dusk.base_path'))) {
            mkdir(config('behat-dusk.base_path'), 0755, true);
        }

        if (! is_dir(config('behat-dusk.context_path'))) {
            mkdir(config('behat-dusk.context_path'), 0755, true);
        }

        $this->createFeaturesDirectory();

        $this->createScreenshotsDirectory();

        $this->createConsoleDirectory();

        $stubs = [
            'FeatureContext.stub' => config('behat-dusk.context_path') . DIRECTORY_SEPARATOR . 'FeatureContext.php',
            'BehatDuskTestCase.stub' => config('behat-dusk.base_path') . DIRECTORY_SEPARATOR . 'BehatDuskTestCase.php',
            'behat.yml.stub' => base_path('behat.yml'),
        ];

        foreach ($stubs as $stub => $file) {
            if (! is_file($file)) {
                copy(__DIR__.'/../../stubs/'.$stub, $file);
            }
        }

        $this->info('LaravelBehatDusk scaffolding installed successfully.');
    }

    /**
     * Create the features directory.
     *
     * @return void
     */
    protected function createFeaturesDirectory()
    {
        if (! is_dir(config('behat-dusk.feature_path'))) {
            mkdir(config('behat-dusk.feature_path'), 0755, true);

            file_put_contents(config('behat-dusk.feature_path') . DIRECTORY_SEPARATOR . '.gitkeep', '');
        }
    }

    /**
     * Create the screenshots directory.
     *
     * @return void
     */
    protected function createScreenshotsDirectory()
    {
        if (! is_dir(config('behat-dusk.screenshot_path'))) {
            mkdir(config('behat-dusk.screenshot_path'), 0755, true);

            file_put_contents(config('behat-dusk.screenshot_path') . DIRECTORY_SEPARATOR . '.gitignore', '*
!.gitignore
');
        }
    }

    /**
     * Create the console directory.
     *
     * @return void
     */
    protected function createConsoleDirectory()
    {
        if (! is_dir(config('behat-dusk.console_log_path'))) {
            mkdir(config('behat-dusk.console_log_path'), 0755, true);

            file_put_contents(config('behat-dusk.console_log_path') . DIRECTORY_SEPARATOR . '.gitignore', '*
!.gitignore
');
        }
    }
}
