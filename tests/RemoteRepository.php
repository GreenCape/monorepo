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

use GreenCape\MonoRepo\Git;
use PHPUnit\Framework\TestCase;

/**
 *
 *
 * @since  Class available since Release __DEPLOY_VERSION__
 */
class RemoteRepository
{
    /**
     * @var \PHPUnit\Framework\TestCase
     */
    private $test;

    /**
     * @var string
     */
    private $path;

    /**
     * Constructor.
     *
     * @param  \PHPUnit\Framework\TestCase  $test
     * @param  string                       $path
     */
    public function __construct(TestCase $test, string $path)
    {
        $this->test = $test;
        $this->path = $path;
        $git = new Git($this->path);

        if (!file_exists($this->path . '/.git')) {
            $git->init(true);
        }
    }

    /**
     * @return string
     */
    public function getUrl(): string
    {
        return $this->path;
    }
}
