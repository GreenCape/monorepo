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

namespace GreenCape\MonoRepo\Test\Acceptance;

use GreenCape\MonoRepo\Command\Git\AddCommand;
use GreenCape\MonoRepo\Command\Git\SplitCommand;
use GreenCape\MonoRepo\Logger\EchoLogger;
use GreenCape\MonoRepo\Terminal\LoggingTerminalDecorator;
use GreenCape\MonoRepo\Terminal\Terminal;
use GreenCape\MonoRepo\Terminal\TerminalInterface;
use GreenCape\MonoRepo\Test\Project;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Input\StringInput;
use Symfony\Component\Console\Output\BufferedOutput;

/**
 *
 *
 * @since  Class available since Release __DEPLOY_VERSION__
 */
class MonorepoTest extends TestCase
{
    public function tearDown(): void
    {
        (new Terminal())->rmdir(TEST_DIR . '/repos');
    }

    /**
     * @testdox Use Case 1: Create a monorepo from existing subprojects
     * @throws \Exception
     * @throws \Throwable
     */
    public function testUseCase1(): void
    {
        // Given subprojects 'ProjectA' and 'ProjectB' and the monorepo 'monorepo'
        $projectA  = new Project('ProjectA');
        $projectB  = new Project('ProjectB');
        $monorepo  = new Project('monorepo');
        $directory = $monorepo->getPath();
        $logger    = new EchoLogger();
        $cli       = new LoggingTerminalDecorator(new Terminal(), $logger);

        // When I work in 'monorepo'
        $cli->cd($directory);
        $cli->exec('git remote -v');

        // And I add the subprojects
        $projectAUrl = $projectA->getUrl();
        $this->runCmd(
            AddCommand::class,
            '--dir=lib/project-a ProjectA ' . $projectAUrl,
            $cli
        );

        $cli->ls();

        $projectBUrl = $projectB->getUrl();
        $this->runCmd(
            AddCommand::class,
            '--dir=lib/project-b ProjectB ' . $projectBUrl,
            $cli
        );

        $cli->ls();

        // Then the files of the subprojects should be in their subdirectories
        $this->assertFileExists($directory . '/lib/project-a/README.md');
        $this->assertFileExists($directory . '/lib/project-b/README.md');

        // And the monorepo config file should have been updated accordingly
        $config = yaml_parse_file($directory . '/monorepo.yml');
        $this->assertEquals('lib/project-a', $config['Packages']['ProjectA']['Directory']);
        $this->assertStringEndsWith('repos/remote/ProjectA.git', $config['Packages']['ProjectA']['Repository']);
        $this->assertEquals('lib/project-b', $config['Packages']['ProjectB']['Directory']);
        $this->assertStringEndsWith('repos/remote/ProjectB.git', $config['Packages']['ProjectB']['Repository']);
    }

    /**
     * @testdox Use Case 2: Convert a subdirectory into a subproject
     * @throws \Exception
     */
    public function testUseCase2(): void
    {
        // Given a monorepo 'monorepo' with a file `lib/ProjectC/README.md`
        $monorepo  = new Project('monorepo');
        $directory = $monorepo->getPath();
        $logger    = new EchoLogger();
        $cli       = new LoggingTerminalDecorator(new Terminal(), $logger);

        $cli->cd($directory);
        $cli->ls();
        $cli->mkdir('lib/project-c');
        $cli->pushd('lib/project-c');
        $cli->ls();
        file_put_contents('README.md', "# ProjectC\n\nBranch master\n");
        $cli->ls();
        $cli->popd();
        $cli->ls();
        $cli->exec("git add --all");
        $cli->exec("git commit -m \"Add ProjectC\"");
        $cli->exec('git push --set-upstream monorepo master');

        // When I turn 'ProjectC' into a subproject
        $remote = TEST_DIR . "/repos/remote/ProjectC.git";
        $this->runCmd(
            SplitCommand::class,
            '--dir=lib/project-c ProjectC ' . $remote,
            $cli
        );

        // And I clone 'ProjectC' into a new workspace
        #$this->cli->exec("git clone \"{$remote}\"" . $targetDirectory);

        // Then 'ProjectC' should contain the README.md file
        $this->assertFileExists(TEST_DIR . '/repos/local/ProjectC/README.md');
    }

    /**
     * @param  string                                               $commandClass
     * @param  string                                               $arguments
     * @param  \GreenCape\MonoRepo\Terminal\TerminalInterface|null  $terminal
     *
     * @return array
     * @throws \Exception
     */
    private function runCmd(string $commandClass, string $arguments, ?TerminalInterface $terminal = null): array
    {
        /** @var \GreenCape\MonoRepo\Command\Command $command */
        $command = new $commandClass(null, $terminal);
        $input   = new StringInput($arguments);
        $output  = new BufferedOutput();
        $status  = $command->run($input, $output);

        $this->assertEquals(0, $status);

        return ['status' => $status, 'output' => $output];
    }
}
