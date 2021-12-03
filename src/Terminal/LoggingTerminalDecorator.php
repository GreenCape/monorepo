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
 * Proxy for CLI functionality
 *
 * @since  Class available since Release __DEPLOY_VERSION__
 */
class LoggingTerminalDecorator extends AbstractTerminalDecorator
{
    /**
     * @var \GreenCape\MonoRepo\Terminal\TerminalInterface
     */
    private $terminal;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @param  \GreenCape\MonoRepo\Terminal\TerminalInterface  $terminal
     * @param  \Psr\Log\LoggerInterface                        $logger
     */
    public function __construct(TerminalInterface $terminal, LoggerInterface $logger)
    {
        $this->terminal = $terminal;
        $this->logger   = $logger;
    }

    /**
     * @param  string  $method
     * @param  array   $arguments
     *
     * @return mixed
     */
    public function decorate(string $method, array $arguments)
    {
        $this->logCommand($method . '(' . implode(', ', $arguments) . ')');
        $result = trim($this->terminal->$method(...$arguments));
        if (method_exists($this->terminal, 'getOutput')) {
            $this->logResult($this->terminal->getOutput(), '>');
        }
        if (method_exists($this->terminal, 'getStdErr')) {
            $this->logResult($this->terminal->getStdErr(), '#');
        }

        return $result;
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
        $this->logCommand("\$ {$command}");
        $result = trim($this->terminal->exec($command));
        if (method_exists($this->terminal, 'getOutput')) {
            $this->logResult($this->terminal->getOutput(), '>');
        }

        return $result;
    }

    /**
     * @param  string  $command
     */
    private function logCommand(string $command): void
    {
        $this->logger->log(
            LogLevel::INFO,
            $command,
            [
                'source' => 'Terminal',
                'directory' => $this->terminal->pwd(),
            ]
        );
    }

    /**
     * @param  string  $result
     *
     * @return void
     */
    private function logResult(string $result, string $mark = '>'): void
    {
        if ($result > '') {
            $result = "$mark   " . str_replace("\n", "\n$mark   ", $result);
            $this->logger->log(LogLevel::INFO, $result, ['source' => 'Terminal']);
        }
    }
}
