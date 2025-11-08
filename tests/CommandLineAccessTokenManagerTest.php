<?php

/*
 * This file is part of the Atico/SpreadsheetTranslator package.
 *
 * (c) Samuel Vicent <samuelvicent@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Atico\SpreadsheetTranslator\Provider\OneDriveAuth\Tests;

use Atico\SpreadsheetTranslator\Provider\OneDriveAuth\CommandLineAccessTokenManager;
use PHPUnit\Framework\TestCase;

class CommandLineAccessTokenManagerTest extends TestCase
{
    public function testCommandLineAccessTokenManagerCanBeInstantiated(): void
    {
        $browserName = 'chrome';
        $manager = new CommandLineAccessTokenManager($browserName);

        $this->assertInstanceOf(CommandLineAccessTokenManager::class, $manager);
    }

    public function testCommandLineAccessTokenManagerAcceptsNullBrowserName(): void
    {
        $manager = new CommandLineAccessTokenManager(null);

        $this->assertInstanceOf(CommandLineAccessTokenManager::class, $manager);
    }

    public function testCommandLineAccessTokenManagerAcceptsEmptyStringBrowserName(): void
    {
        $manager = new CommandLineAccessTokenManager('');

        $this->assertInstanceOf(CommandLineAccessTokenManager::class, $manager);
    }

    /**
     * Note: obtainAccessTokenInformation() requires user interaction and shell execution,
     * so it's difficult to unit test without integration testing or mocking global functions.
     * For now, we just verify the class can be instantiated correctly.
     * Integration tests should be created separately to test the full OAuth flow.
     */
}
