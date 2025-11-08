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

use Atico\SpreadsheetTranslator\Provider\OneDriveAuth\OneDriveAuthProvider;
use Atico\SpreadsheetTranslator\Provider\OneDriveAuth\Client;
use Atico\SpreadsheetTranslator\Core\Configuration\Configuration;
use Atico\SpreadsheetTranslator\Core\Resource\Resource;
use PHPUnit\Framework\TestCase;
use Mockery;
use Psr\Http\Message\StreamInterface;
use Exception;

class OneDriveAuthProviderTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();

        // Clean up temp files
        $tempFile = sys_get_temp_dir() . '/onedrive_auth_test.xlsx';
        if (file_exists($tempFile)) {
            unlink($tempFile);
        }
    }

    public function testGetProviderReturnsCorrectIdentifier(): void
    {
        $configuration = new Configuration([
            'provider' => [
                'options' => [
                    'access_token' => 'test-token',
                    'document_id' => 'file.123'
                ]
            ]
        ], 'options');

        $provider = new OneDriveAuthProvider($configuration);
        $this->assertSame('onedrive_auth', $provider->getProvider());
    }

    /**
     * Note: This test requires integration testing with actual OneDrive API or
     * refactoring OneDriveAuthProvider to support dependency injection for Client.
     * Skipping for now to focus on unit testing other components.
     */
    public function testHandleSourceResourceWithDirectAccessToken(): void
    {
        $this->markTestSkipped(
            'This test requires refactoring OneDriveAuthProvider to support dependency injection ' .
            'or actual integration testing with OneDrive API. Consider implementing a factory pattern ' .
            'or dependency injection for the Client class to enable proper unit testing.'
        );
    }

    public function testHandleSourceResourceThrowsExceptionWhenNoAccessTokenProvided(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('option parameter required: access_token or prompt_for_access_token');

        $configuration = new Configuration([
            'provider' => [
                'options' => [
                    'document_id' => 'file.abc123.ABC123',
                    'temp_local_source_file' => sys_get_temp_dir() . '/test.xlsx',
                    'format' => 'xlsx',
                    'prompt_for_access_token' => false,
                    'access_token' => ''
                ]
            ]
        ], 'options');

        $provider = new OneDriveAuthProvider($configuration);
        $provider->handleSourceResource();
    }
}
