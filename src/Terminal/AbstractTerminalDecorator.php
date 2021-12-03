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

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 *
 *
 * @since  Class available since Release __DEPLOY_VERSION__
 */
abstract class AbstractTerminalDecorator implements TerminalInterface
{
    /**
     * @param  string  $method
     * @param  array   $arguments
     *
     * @return mixed
     */
    abstract public function decorate(string $method, array $arguments);

    public function mkdir(string $directory, int $permissions = 0755): int
    {
        return $this->decorate('mkdir', func_get_args());
    }

    public function cd(string $directory): int
    {
        return $this->decorate('cd', func_get_args());
    }

    public function pwd(): string
    {
        return $this->decorate('pwd', func_get_args());
    }

    public function rmdir(string $directory): int
    {
        return $this->decorate('rmdir', func_get_args());
    }

    public function rm(string $filename): int
    {
        return $this->decorate('rm', func_get_args());
    }

    public function pushd(string $directory): int
    {
        return $this->decorate('pushd', func_get_args());
    }

    public function popd(): int
    {
        return $this->decorate('popd', func_get_args());
    }

    public function ls(): string
    {
        return $this->decorate('ls', func_get_args());
    }
}
