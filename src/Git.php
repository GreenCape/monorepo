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

namespace GreenCape\MonoRepo;

/**
 * Proxy for git CLI functionality
 *
 * @since  Class available since Release __DEPLOY_VERSION__
 */
class Git
{
    /**
     * @var string
     */
    private $directory;

    /**
     * Constructor.
     *
     * The directory for the repository is created, if it does not yet exist
     *
     * @param  string  $directory
     */
    public function __construct(string $directory)
    {
        $this->directory = $directory;

        if (!is_dir($this->directory) && !mkdir($this->directory, 0777, true) && !is_dir($this->directory)) {
            throw new \RuntimeException(sprintf('Directory "%s" was not created', $this->directory));
        }
    }

    /**
     * Initialize a shared (bare=true) or working (bare=false) repository
     *
     * @param  bool  $bare
     *
     * @return string
     */
    public function init(bool $bare = false): string
    {
        return $this->exec('git init' . ($bare ? ' --bare' : ''));
    }

    /**
     * Retrieve the current branch
     *
     * @return string
     */
    public function currentBranch(): string
    {
        return $this->exec('git rev-parse --abbrev-ref HEAD');
    }

    /**
     * Add a remote repository
     *
     * @param  string  $name
     * @param  string  $url
     *
     * @return string
     */
    public function addRemote(string $name, string $url): string
    {
        return $this->exec('git remote add ' . $name . ' "' . $url . '"');
    }

    /**
     * Add files to the index
     *
     * @param  string  $pattern
     *
     * @return string
     */
    public function add(string $pattern): string
    {
        return $this->exec('git add ' . $pattern);
    }

    /**
     * Commit index to the repository
     *
     * @param  string  $message
     *
     * @return string
     */
    public function commit(string $message): string
    {
        return $this->exec('git commit -m "' . $message . '"');
    }

    /**
     * Push commits to the remote repository
     *
     * @param  string  $remote
     *
     * @return string
     */
    public function push(string $remote): string
    {
        return $this->exec('git push --porcelain --set-upstream ' . $remote . ' ' . $this->currentBranch());
    }

    /**
     * Retrieve a list of associated remotes
     *
     * @return string
     */
    public function remotes(): string
    {
        return $this->exec('git remote -v');
    }

    public function log(array $options = []): string
    {
        $optionString = array_reduce(
            array_keys($options),
            static function ($carry, $option) use ($options) {
                if ($options[$option] === true) {
                    $carry .= " --{$option}";
                }

                return $carry;
            },
            ''
        );

        return $this->exec('git log' . $optionString);
    }

    /**
     * @param  string  $command
     *
     * @return string
     */
    protected function exec(string $command): string
    {
        $command .= ' 2>&1';

        if ($this->directory !== realpath('.')) {
            $command = 'DIR=`pwd` && cd "' . $this->directory . '" && ' . $command . ' && cd "$DIR"';
        }

        return trim(shell_exec($command));
    }
}
