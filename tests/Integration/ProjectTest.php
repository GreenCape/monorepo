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

namespace GreenCape\MonoRepo\Test\Integration;

use GreenCape\MonoRepo\Git;
use GreenCape\MonoRepo\Test\Project;
use PHPUnit\Framework\TestCase;

/**
 *
 *
 * @since  Class available since Release __DEPLOY_VERSION__
 */
class ProjectTest extends TestCase
{
    /**
     * @var string
     */
    private $workspaceBasePath;

    public function __construct(?string $name = null, array $data = [], $dataName = '')
    {
        parent::__construct($name, $data, $dataName);
        $this->workspaceBasePath = dirname(__DIR__) . '/repos';
    }

    /**
     * @testdox Create a remote repository and a development environment
     */
    public function testCreateProject(): void
    {
        $project = new Project($this, 'ProjectA');

        $expectedRepositoryUrl = $this->workspaceBasePath . '/remote/ProjectA.git';
        self::assertEquals(
            $expectedRepositoryUrl,
            $project->getRemoteRepository()->getUrl(),
            'Project does not report the expected repository URL'
        );

        self::assertDirectoryExists($expectedRepositoryUrl, 'Remote repository was not created');

        $expectedDevPath = $this->workspaceBasePath . '/local/ProjectA';
        self::assertEquals(
            $expectedDevPath,
            $project->getDevEnvironment()->getPath(),
            'Project does not report the expected path to the dev environment'
        );

        self::assertDirectoryExists($expectedDevPath, 'Dev environment was not created');

        $git = new Git($expectedDevPath);

        self::assertStringContainsString(
            'docs: Create README',
            $git->log(['oneline' => true]),
            'Expected commit message is not present in log'
        );

        self::assertFileExists($expectedDevPath . '/README.md', 'README file was not created');
    }

    public static function tearDownAfterClass(): void
    {
        shell_exec('rm -rf ' . dirname(__DIR__) . '/repos');
    }
}
