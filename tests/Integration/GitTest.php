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
use PHPUnit\Framework\TestCase;

/**
 *
 *
 * @since  Class available since Release __DEPLOY_VERSION__
 */
class GitTest extends TestCase
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
     * @testdox Create directory for the repository, if it does not yet exist
     */
    public function testCreateDirectory(): void
    {
        $repoPath = $this->workspaceBasePath . '/git-test';

        $this->assertFileDoesNotExist($repoPath, "Remove $repoPath before running the tests");

        /** @noinspection PhpUnusedLocalVariableInspection */
        $git = new Git($repoPath);

        $this->assertFileExists($repoPath, "Git::__construct() should have created the directory $repoPath");
    }

    /**
     * @testdox Re-use directory for the repository, if it already exists
     * @depends testCreateDirectory
     */
    public function testReuseDirectory(): void
    {
        $repoPath = $this->workspaceBasePath . '/git-test';

        $this->assertFileExists($repoPath, "Directory $repoPath should have been created in the previous step");

        /** @noinspection PhpUnusedLocalVariableInspection */
        $git = new Git($repoPath);

        $this->assertFileExists($repoPath, "Git::__construct() should have re-used the directory $repoPath");
        shell_exec("rm -rf $repoPath");
        $this->assertFileDoesNotExist($repoPath, "The test case should have removed $repoPath");
    }

    /**
     * @testdox Initialize a shared (bare) repository
     */
    public function testInitBare(): string
    {
        $repoPath = $this->workspaceBasePath . '/git-test.git';
        $git = new Git($repoPath);
        $git->init(true);
        $this->assertFileExists($repoPath . '/HEAD', "Git::init() should have copied the templates to $repoPath");

        return $repoPath;
    }

    /**
     * @testdox Initialize a working (local) repository
     * @depends testInitBare
     */
    public function testInitNonBare($remote): array
    {
        $repoPath = $this->workspaceBasePath . '/git-test';
        $git = new Git($repoPath);
        $git->init(false);
        $this->assertFileExists($repoPath . '/.git/HEAD', "Git::init() should have created a .git subdirectory with the templates in $repoPath");

        return [
            'remote' => $remote,
            'workspace' => $repoPath
        ];
    }

    /**
     * @testdox Add a remote repository
     * @depends testInitNonBare
     */
    public function testAddRemote($repos): string
    {
        $git = new Git($repos['workspace']);
        $this->assertEmpty($git->remotes());
        $git->addRemote('origin', $repos['remote']);

        $expected = "origin\t{$repos['remote']} (fetch)\norigin\t{$repos['remote']} (push)";
        $this->assertEquals($expected, $git->remotes());

        return $repos['workspace'];
    }

    /**
     * @testdox Add files to the index and commit index to the repository
     * @depends testAddRemote
     */
    public function testAddAndCommit(string $workspace): string
    {
        $git = new Git($workspace);

        file_put_contents($workspace . '/text.txt', "Arbitrary file\n");
        $git->add('.');
        $commitMessage = "Add text.txt";
        $git->commit($commitMessage);

        $expected = '~^\w+ ' . $commitMessage . '$~';
        $this->assertMatchesRegularExpression($expected, $git->log(['oneline' => true]));

        return $workspace;
    }

    /**
     * @testdox Retrieve the current branch
     * @depends testAddAndCommit
     */
    public function testCurrentBranch(string $workspace): string
    {
        $git = new Git($workspace);

        $this->assertEquals('master', $git->currentBranch());

        return $workspace;
    }

    /**
     * @testdox Retrieve a list of associated remotes
     * @depends testCurrentBranch
     */
    public function testRemotes(string $workspace): string
    {
        $git = new Git($workspace);
        $remote = $workspace . '.git';

        $expected = "origin\t{$remote} (fetch)\norigin\t{$remote} (push)";
        $this->assertEquals($expected, $git->remotes());

        return $workspace;
    }

    /**
     * @testdox Push commits to the remote repository
     * @depends testRemotes
     */
    public function testPush(string $workspace): string
    {
        $git = new Git($workspace);
        $remote = $workspace . '.git';

        $git->push('origin');

        $this->assertFileExists($remote . '/refs/heads/master');

        return $workspace;
    }

    public static function tearDownAfterClass(): void
    {
        shell_exec('rm -rf ' . dirname(__DIR__) . '/repos');
    }
}
