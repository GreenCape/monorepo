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

namespace GreenCape\MonoRepo\Command;

use GreenCape\MonoRepo\Git;
use GreenCape\MonoRepo\Terminal\Terminal;
use GreenCape\MonoRepo\Terminal\TerminalInterface;
use Symfony\Component\Console\Command\Command as BaseCommand;
use Symfony\Component\Console\Input\InputOption;

/**
 *
 *
 * @since  Class available since Release __DEPLOY_VERSION__
 */
abstract class Command extends BaseCommand
{
    /**
     * @var \GreenCape\MonoRepo\Terminal\TerminalInterface
     */
    protected $cli;

    /**
     * Constructor.
     *
     * @param  string|null                                          $name  The name of the command
     * @param  \GreenCape\MonoRepo\Terminal\TerminalInterface|null  $terminal
     */
    public function __construct(?string $name = null, ?TerminalInterface $terminal = null)
    {
        parent::__construct($name);
        $this->cli = $terminal ?? new Terminal();

        $this->addGlobalOptions();
    }

    /**
     * @param $configFile
     *
     * @return array|false|mixed
     */
    protected function getConfig($configFile)
    {
        $config = ['Version' => '1.0'];

        if (file_exists($configFile)) {
            $config = yaml_parse_file($configFile);
        }

        return $config;
    }

    /**
     * @param $configFile
     * @param $config
     */
    protected function writeConfig($configFile, $config): void
    {
        $success = yaml_emit_file($configFile, $config, YAML_UTF8_ENCODING, YAML_LN_BREAK);

        if ($success === false) {
            throw new \RuntimeException('Unable to write configuration file ' . $configFile);
        }
    }

    /**
     *
     */
    private function addGlobalOptions(): void
    {
        $this->addOption(
            'config',
            'c',
            InputOption::VALUE_REQUIRED,
            'Path to the configuration file',
            'monorepo.yml'
        );
    }
}
