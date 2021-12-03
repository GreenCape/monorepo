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

namespace GreenCape\MonoRepo\Command\Git;

use GreenCape\MonoRepo\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 *
 *
 * @since  Class available since Release __DEPLOY_VERSION__
 */
class AddCommand extends Command
{
    /**
     * Configure the options for the command
     *
     * @return  void
     */
    protected function configure(): void
    {
        $this->setName('add')->setDescription('Add a sub-project to the monorepo')->addArgument(
            'name',
            InputArgument::REQUIRED,
            'Name of the sub-project'
        )->addArgument(
            'repository',
            InputArgument::REQUIRED,
            'URL of the git repository for the sub-project'
        )->addArgument(
            'ref',
            InputArgument::OPTIONAL,
            'URL of the git repository for the sub-project'
        )->addOption(
            'dir',
            'd',
            InputOption::VALUE_REQUIRED,
            'Target directory for the sub-project',
            '.'
        )->addOption(
            'squash',
            null,
            InputOption::VALUE_NONE | InputOption::VALUE_NEGATABLE,
            'Squash (--squash) or don\'t squash (--no-squash) the sub-project\'s history on import'
        )->setHelp(
            'Create the <prefix> subtree by importing its contents from the given <commit> or <repository> and remote <ref>. A new commit is created automatically, joining the imported project’s history with your own. With --squash, imports only a
           single commit from the subproject, rather than its entire history.'
        );
    }

    /**
     * Execute the command
     *
     * @param  InputInterface   $input   An InputInterface instance
     * @param  OutputInterface  $output  An OutputInterface instance
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $configFile = $input->getOption('config');
        $config     = $this->getConfig($configFile);

        $name       = $input->getArgument('name');
        $repository = $input->getArgument('repository');
        $directory  = $input->getOption('dir');
        $squash     = $input->getOption('squash') ? ' --squash' : '';
        $branch     = $this->cli->exec('git rev-parse --abbrev-ref HEAD');

        $config['Packages'][$name] = [
            'Repository' => $repository,
            'Directory'  => $directory,
        ];

        $this->cli->exec("git remote add -f \"{$name}\" \"{$repository}\"");
        $this->cli->exec("git subtree add --prefix \"{$directory}\" \"{$name}\" \"{$branch}\"{$squash}");
        $this->cli->exec("git subtree pull --prefix \"{$directory}\" \"{$name}\" \"{$branch}\"{$squash}");

        $this->writeConfig($configFile, $config);

        $output->writeln("Added project {$name} in directory {$directory}");

        return 0;
    }
}
