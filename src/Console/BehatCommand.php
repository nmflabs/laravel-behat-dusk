<?php

namespace Nmflabs\LaravelBehatDusk\Console;

use Closure;
use Exception;
use Dotenv\Dotenv;
use Illuminate\Support\Str;
use Illuminate\Console\Command;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Process\Process;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Exception\ProcessSignaledException;

class BehatCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'behat';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Serve the application and run Behat tests';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->ignoreValidationErrors();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->purgeScreenshots();

        $this->purgeConsoleLogs();

        $this->setupBehatEnvironment();

        $this->startHttpServer(function () {
            $behatProcess = (new Process(array_merge(
                $this->binary(), $this->behatArguments($_SERVER['argv'])
            )))->setTimeout(null);

            try {
                $behatProcess->setTty(true);
            } catch (RuntimeException $e) {
                $this->output->writeln('Warning: '.$e->getMessage());
            }

            $behatProcess->start();
            $behatProcess->wait(function ($type, $line) {
                $this->output->write($line);
            });
            $this->killChildsProcess($behatProcess->getPid(), 15, false);
            $behatProcess->stop();

            $this->teardownBehatEnviroment();
        });

        return true;
    }

    /**
     * Build a process to run Laravel Http Server.
     *
     * @param  \Closure  $callback
     * @return void
     */
    protected function startHttpServer(Closure $callback)
    {
        $arguments = [
            PHP_BINARY,
            'artisan',
            'serve',
            $this->input->getOption('host') ? '--host=' . $this->input->getOption('host') : null,
            $this->input->getOption('port') ? '--port=' . $this->input->getOption('port') : null,
            version_compare($this->laravel->version(), '5.8.0', '>=') ? '--tries=0' : null,
        ];

        $process = (new Process(array_filter($arguments)))
            ->setTimeout(null);

        try {
            $process->start();

            $process->waitUntil(function () use ($callback) {
                $this->output->writeln(sprintf(
                    "Laravel HTTP Server started: http://%s:%s",
                    $this->input->getOption('host') ?? '127.0.0.1',
                    $this->input->getOption('port') ?? env('SERVER_PORT', 8000)
                ));
                $this->output->newLine();

                $callback();

                return true;
            });
        } catch (ProcessSignaledException $e) {
            $this->killChildsProcess($process->getPid(), 15, false);
            $process->stop();

            if (extension_loaded('pcntl') && $e->getSignal() !== SIGINT) {
                throw $e;
            }
        }

        $this->killChildsProcess($process->getPid(), 15, false);
        $process->stop();

        $this->output->writeln("\nShutdown Laravel HTTP Server.");
    }

    /**
     * Get the console command options.
     *
     * @return array
     */
    protected function getOptions()
    {
        return [
            ['host', null, InputOption::VALUE_OPTIONAL, 'The host address to serve the application on'],

            ['port', null, InputOption::VALUE_OPTIONAL, 'The port to serve the application on'],
        ];
    }

    /**
     * Get the Behat binary to execute.
     *
     * @return array
     */
    protected function binary()
    {
        if ('phpdbg' === PHP_SAPI) {
            return [PHP_BINARY, '-qrr', 'vendor/behat/behat/bin/behat'];
        }

        return [PHP_BINARY, 'vendor/behat/behat/bin/behat'];
    }

    /**
     * Get the array of arguments for running Behat.
     *
     * @param  array  $options
     * @return array
     */
    protected function behatArguments($options)
    {
        $options = array_slice($options, 2);

        $options = array_values(array_filter($options, function ($option) {
            return ! Str::startsWith($option, [
                '--env=',
                '--host',
                '--port',
            ]);
        }));

        return $options;
    }

    /**
     * Purge the failure screenshots.
     *
     * @return void
     */
    protected function purgeScreenshots()
    {
        $path = config('behat-dusk.screenshot_path');

        if (! is_dir($path)) {
            return;
        }

        $files = Finder::create()->files()
            ->in($path)
            ->name('failure-*');

        foreach ($files as $file) {
            @unlink($file->getRealPath());
        }
    }

    /**
     * Purge the console logs.
     *
     * @return void
     */
    protected function purgeConsoleLogs()
    {
        $path = config('behat-dusk.console_log_path');

        if (! is_dir($path)) {
            return;
        }

        $files = Finder::create()->files()
            ->in($path)
            ->name('*.log');

        foreach ($files as $file) {
            @unlink($file->getRealPath());
        }
    }

    /**
     * Setup the Behat environment.
     *
     * @return void
     */
    protected function setupBehatEnvironment()
    {
        if (file_exists(base_path($this->behatEnvironmentFile()))) {
            if (file_get_contents(base_path('.env')) !== file_get_contents(base_path($this->behatEnvironmentFile()))) {
                $this->backupEnvironment();
            }

            $this->refreshEnvironment();
        }

        $this->setupSignalHandler();
    }

    /**
     * Backup the current environment file.
     *
     * @return void
     */
    protected function backupEnvironment()
    {
        copy(base_path('.env'), base_path('.env-behat.backup'));

        copy(base_path($this->behatEnvironmentFile()), base_path('.env'));
    }

    /**
     * Refresh the current environment variables.
     *
     * @return void
     */
    protected function refreshEnvironment()
    {
        if (! method_exists(Dotenv::class, 'create')) {
            (new Dotenv(base_path()))->overload();

            return;
        }

        Dotenv::create(base_path())->overload();
    }

    /**
     * Setup the SIGINT signal handler for CTRL+C exits.
     *
     * @return void
     */
    protected function setupSignalHandler()
    {
        if (extension_loaded('pcntl')) {
            pcntl_async_signals(true);

            pcntl_signal(SIGINT, function () {
                $this->teardownBehatEnviroment();
            });
        }
    }

    /**
     * Restore the original environment.
     *
     * @return void
     */
    protected function teardownBehatEnviroment()
    {
        if (file_exists(base_path($this->behatEnvironmentFile())) && file_exists(base_path('.env-behat.backup'))) {
            $this->restoreEnvironment();
        }
    }

    /**
     * Restore the backed-up environment file.
     *
     * @return void
     */
    protected function restoreEnvironment()
    {
        copy(base_path('.env-behat.backup'), base_path('.env'));

        unlink(base_path('.env-behat.backup'));
    }

    /**
     * Get the name of the Behat file for the environment.
     *
     * @return string
     */
    protected function behatEnvironmentFile()
    {
        return '.env.behat';
    }

    /**
     * Kill childs process created by Symfony Process.
     *
     * @see https://github.com/symfony/symfony/issues/34406
     *
     * @param  int  $pid
     * @param  int  $signal
     * @param  bool  $throwException
     * @return bool
     */
    protected function killChildsProcess($pid, int $signal, bool $throwException): bool
    {
        try {
            if ('\\' === \DIRECTORY_SEPARATOR) {
                $ret = proc_open(sprintf('wmic process where ParentProcessId=%d get ProcessId', $pid), [1 => ['pipe', 'w']], $pipes);
            } else {
                $ret = proc_open(sprintf('pgrep -P %d', $pid), [1 => ['pipe', 'w']], $pipes);
            }
        } catch (Exception $e) {
            return false;
        }

        if ($ret && $output = fgets($pipes[1])) {
            proc_close($ret);
            $pids = explode("\n", $output);

            foreach ($pids as $childPid) {
                if (empty($childPid)) {
                    continue;
                }

                $childPid = (int) $childPid;

                if ('\\' === \DIRECTORY_SEPARATOR) {
                    exec(sprintf('taskkill /F /T /PID %d 2>&1', $childPid), $output, $exitCode);
                    if ($exitCode) {
                        if ($throwException) {
                            throw new RuntimeException(sprintf('Unable to kill the child process (%s).', implode(' ', $output)));
                        }

                        return false;
                    }
                } else {
                    if (\function_exists('posix_kill')) {
                        $ok = @posix_kill($childPid, $signal);
                    } elseif ($ok = proc_open(sprintf('kill -%d %d', $signal, $childPid), [2 => ['pipe', 'w']], $pipes)) {
                        $resource = $ok;
                        $ok = false === fgets($pipes[2]);
                        proc_close($resource);
                    }

                    if (!$ok) {
                        if ($throwException) {
                            throw new RuntimeException(sprintf('Error while sending signal "%s" to child.', $signal));
                        }

                        return false;
                    }
                }
            }

            return true;
        }

        return false;
    }
}
