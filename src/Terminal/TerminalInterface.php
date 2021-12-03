<?php

namespace GreenCape\MonoRepo\Terminal;


/**
 * Proxy for CLI functionality
 *
 * @since  Class available since Release __DEPLOY_VERSION__
 */
interface TerminalInterface
{
    /**
     * Create a directory
     *
     * @param  string  $directory
     * @param  int     $permissions
     *
     * @return int
     */
    public function mkdir(string $directory, int $permissions = 0755): int;

    /**
     * Change directory
     *
     * @param  string  $directory
     *
     * @return int
     */
    public function cd(string $directory): int;

    /**
     * Get the current directory
     *
     * @return string
     */
    public function pwd(): string;

    /**
     * Remove a directory and its content
     *
     * @param  string  $directory
     *
     * @return int
     */
    public function rmdir(string $directory): int;

    /**
     * Remove a file
     *
     * @param  string  $filename
     *
     * @return int
     */
    public function rm(string $filename): int;

    /**
     * @param  string  $directory
     *
     * @return int
     */
    public function pushd(string $directory): int;

    /**
     * @return int
     */
    public function popd(): int;

    /**
     * Execute a command
     *
     * @param  string  $command
     *
     * @return int
     */
    public function exec(string $command): int;
}
