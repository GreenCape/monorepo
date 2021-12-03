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

namespace GreenCape\MonoRepo\Test;

use GreenCape\MonoRepo\Terminal\Terminal;

/**
 *
 *
 * @since  Class available since Release __DEPLOY_VERSION__
 */
class Project
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $url;

    /**
     * @var string
     */
    private $path;

    /**
     * Constructor.
     *
     * Create a remote repository and a development environment
     *
     * @param  string  $name
     */
    public function __construct(string $name)
    {
        $this->name = $name;

        $this->createRemoteRepository(__DIR__ . "/repos/remote/{$name}.git");
        $this->createDevEnvironment(__DIR__ . "/repos/local/{$name}", $name, __DIR__ . "/repos/remote/{$name}.git");
    }

    /**
     * Create a bare remote repository
     *
     * If $path does not exist, it is created recursively.
     *
     * @param  string  $path
     */
    protected function createRemoteRepository(string $path): void
    {
        $this->url = $path;

        $cli = new Terminal();

        if (!file_exists($path)) {
            $cli->mkdir($path);
        }

        if (!file_exists($path . '/HEAD')) {
            $cli->exec("git init --bare \"{$path}\"");
        }
    }

    /**
     * @param  string  $path  The path to the development workspace
     * @param  string  $name  The name of the remote repository
     * @param  string  $url   The url or path of the remote repository
     */
    protected function createDevEnvironment(string $path, string $name, string $url): void
    {
        $this->path = $path;

        $cli = new Terminal();

        if (!file_exists($path)) {
            $cli->mkdir($path);
        }

        if (!file_exists($path . '/.git')) {
            $cli->exec("git init");
        }

        file_put_contents($path . '/README.md', "# {$name}\n\nBranch master\n");

        $cli->exec("git remote add \"{$name}\" \"{$url}\"");
        $cli->exec("git add --all");
        $cli->exec("git commit -m \"Create README\"");
        $cli->exec("git push --set-upstream \"{$name}\" master");
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }
}
