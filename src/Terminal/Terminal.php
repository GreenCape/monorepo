<?php
/**
 * GreenCape MonoRepo Command Line Interface
 *
 * MIT License
 *
 * Copyright (c) 2021, Niels Braczek <nbraczek@bsds.de>. All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and
 * to permit persons to whom the Software is furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all copies or substantial portions
 * of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO
 * THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT,
 * TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 *
 * @author          Niels Braczek <nbraczek@bsds.de>
 * @copyright   (C) 2021 GreenCape, Niels Braczek <nbraczek@bsds.de>
 * @license         http://opensource.org/licenses/MIT The MIT license (MIT)
 * @link            http://greencape.github.io
 * @since           File available since Release __DEPLOY_VERSION__
 */

namespace GreenCape\MonoRepo\Terminal;

use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Proxy for CLI functionality
 *
 * @since  Class available since Release __DEPLOY_VERSION__
 */
class Terminal implements TerminalInterface
{
    /**
     * Stack for pushd() and popd()
     *
     * @var array
     */
    private $directoryStack = [];

    /**
     * @var string
     */
    private $currentDir;

    /**
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    private $output;

    public function __construct(OutputInterface $output = null)
    {
        $this->output     = $output ?? new BufferedOutput();
        $this->currentDir = $this->makeAbsolute(getcwd());
    }

    /**
     * Create a directory
     *
     * @param  string  $directory
     */
    public function mkdir(string $directory, int $permissions = 0755): int
    {
        $directory = $this->makeAbsolute($directory);

        if (file_exists($directory)) {
            $this->output->writeln("mkdir: directory »{$directory}« can not be created: file already exists");

            return 0;
        }

        try {
            mkdir($directory, $permissions, true);
        } catch (\Throwable $exception) {
            $this->output->writeln("mkdir: directory »{$directory}« can not be created");

            return 2;
        }

        if (!is_dir($directory)) {
            $this->output->writeln("mkdir: directory »{$directory}« can not be created");

            return 1;
        }

        return 0;
    }

    /**
     * Change directory
     *
     * @param  string  $directory
     *
     * @return int
     */
    public function cd(string $directory): int
    {
        $this->currentDir = realpath($directory);

        return 0;
    }

    /**
     * Get the current directory
     *
     * @return string
     */
    public function pwd(): string
    {
        return $this->currentDir;
    }

    /**
     * Remove a directory and its content
     *
     * @param  string  $directory
     *
     * @return int
     */
    public function rmdir(string $directory): int
    {
        $directory = $this->makeAbsolute($directory);

        return $this->exec("rm -rf \"{$directory}\"");
    }

    /**
     * Remove a file
     *
     * @param  string  $filename
     *
     * @return int
     */
    public function rm(string $filename): int
    {
        $filename = $this->makeAbsolute($filename);

        return $this->exec("rm \"{$filename}\"");
    }

    /**
     * @param  string  $directory
     *
     * @return int
     */
    public function pushd(string $directory): int
    {
        $this->directoryStack[] = $this->pwd();

        return $this->cd($directory);
    }

    /**
     * @return int
     */
    public function popd(): int
    {
        return $this->cd(array_pop($this->directoryStack));
    }

    /**
     * @return string
     */
    public function ls(?string $directory = null): string
    {
        $directory = $this->makeAbsolute($directory ?? $this->currentDir);

        return $this->exec('ls -al "' . $directory . '"');
    }

    /**
     * Execute a command
     *
     * @param  string  $command
     *
     * @return int
     */
    public function exec(string $command): int
    {
        $dir = getcwd();
        chdir($this->currentDir);
        exec($command . ' 2>&1', $output, $result_code);
        $this->output->writeln(trim(implode("\n", $output)));
        chdir($dir);

        return $result_code;
    }

    /**
     * @param  string  $directory
     *
     * @return string
     */
    private function makeAbsolute(string $directory): string
    {
        if (empty($directory)) {
            $directory = $this->currentDir;
        } elseif ($directory[0] !== '/') {
            $directory = $this->currentDir . '/' . $directory;
        }

        return $directory;
    }

    /**
     * @return string
     */
    public function getOutput(): string
    {
        return $this->output->fetch();
    }
}
