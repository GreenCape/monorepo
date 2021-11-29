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

use PHPUnit\Framework\TestCase;

/**
 *
 *
 * @since  Class available since Release __DEPLOY_VERSION__
 */
class Project
{
    /**
     * @var \PHPUnit\Framework\TestCase
     */
    private $test;

    /**
     * @var string
     */
    private $name;

    /**
     * @var \GreenCape\MonoRepo\Test\RemoteRepository
     */
    private $remoteRepository;

    /**
     * @var \GreenCape\MonoRepo\Test\DevEnvironment
     */
    private $devEnvironment;

    /**
     * Constructor.
     *
     * Create a remote repository and a development environment
     *
     * @param  \PHPUnit\Framework\TestCase  $test
     * @param  string                       $name
     */
    public function __construct(TestCase $test, string $name)
    {
        $this->test = $test;
        $this->name = $name;

        $this->createRemoteRepository(__DIR__ . "/repos/remote/{$name}.git");
        $this->createDevEnvironment(__DIR__ . "/repos/local/{$name}");
    }

    /**
     * @param  string  $path
     */
    protected function createRemoteRepository(string $path): void
    {
        $this->remoteRepository = new RemoteRepository($this->test, $path);
    }

    /**
     * @param  string  $path
     */
    protected function createDevEnvironment(string $path): void
    {
        $this->devEnvironment = new DevEnvironment($this->test, $path);
        $this->devEnvironment->addRemote($this->name, $this->remoteRepository->getUrl());
        $this->devEnvironment->createContent($this->name, 'master');
        $this->devEnvironment->commit('docs: Create README');
    }

    /**
     * @return \GreenCape\MonoRepo\Test\RemoteRepository
     */
    public function getRemoteRepository(): RemoteRepository
    {
        return $this->remoteRepository;
    }

    /**
     * @return \GreenCape\MonoRepo\Test\DevEnvironment
     */
    public function getDevEnvironment(): DevEnvironment
    {
        return $this->devEnvironment;
    }
}
