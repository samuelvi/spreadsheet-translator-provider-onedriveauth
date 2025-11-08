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

use Atico\SpreadsheetTranslator\Provider\OneDriveAuth\OneDriveAuthConfigurationManager;
use Atico\SpreadsheetTranslator\Core\Configuration\Configuration;
use PHPUnit\Framework\TestCase;

class OneDriveAuthConfigurationManagerTest extends TestCase
{
    private Configuration $configuration;

    protected function setUp(): void
    {
        parent::setUp();

        $this->configuration = new Configuration([
            'provider' => [
                'options' => [
                    'client_id' => 'test-client-id',
                    'client_secret' => 'test-client-secret',
                    'callback_uri' => 'http://localhost/callback',
                    'obtained_code' => 'test-code',
                    'access_token' => 'test-access-token',
                    'prompt_for_access_token' => true,
                    'prompt_access_token_browser' => 'chrome',
                    'scopes' => 'wl.offline_access,wl.skydrive_update',
                    'document_id' => 'file.abc123.ABC123'
                ]
            ]
        ], 'options');
    }

    public function testGetClientIdReturnsConfiguredValue(): void
    {
        $manager = new OneDriveAuthConfigurationManager($this->configuration);
        $this->assertSame('test-client-id', $manager->getClientId());
    }

    public function testGetClientSecretReturnsConfiguredValue(): void
    {
        $manager = new OneDriveAuthConfigurationManager($this->configuration);
        $this->assertSame('test-client-secret', $manager->getClientSecret());
    }

    public function testGetCallbackUriReturnsConfiguredValue(): void
    {
        $manager = new OneDriveAuthConfigurationManager($this->configuration);
        $this->assertSame('http://localhost/callback', $manager->getCallbackUri());
    }

    public function testGetObtainedCodeReturnsConfiguredValue(): void
    {
        $manager = new OneDriveAuthConfigurationManager($this->configuration);
        $this->assertSame('test-code', $manager->getObtainedCode());
    }

    public function testGetAccessTokenReturnsConfiguredValue(): void
    {
        $manager = new OneDriveAuthConfigurationManager($this->configuration);
        $this->assertSame('test-access-token', $manager->getAccessToken());
    }

    public function testGetPromptForAccessTokenReturnsConfiguredValue(): void
    {
        $manager = new OneDriveAuthConfigurationManager($this->configuration);
        $this->assertTrue($manager->getPromptForAccessToken());
    }

    public function testGetPromptAccessTokenBrowserReturnsConfiguredValue(): void
    {
        $manager = new OneDriveAuthConfigurationManager($this->configuration);
        $this->assertSame('chrome', $manager->getPromptAccessTokenBrowser());
    }

    public function testGetPromptAccessTokenBrowserReturnsEmptyStringWhenNotSet(): void
    {
        $config = new Configuration([
            'provider' => [
                'options' => [
                    'client_id' => 'test'
                ]
            ]
        ], 'options');

        $manager = new OneDriveAuthConfigurationManager($config);
        $this->assertSame('', $manager->getPromptAccessTokenBrowser());
    }

    public function testGetScopesReturnsConfiguredValue(): void
    {
        $manager = new OneDriveAuthConfigurationManager($this->configuration);
        $this->assertSame('wl.offline_access,wl.skydrive_update', $manager->getScopes());
    }

    public function testGetScopesReturnsEmptyStringWhenNotSet(): void
    {
        $config = new Configuration([
            'provider' => [
                'options' => [
                    'client_id' => 'test'
                ]
            ]
        ], 'options');

        $manager = new OneDriveAuthConfigurationManager($config);
        $this->assertSame('', $manager->getScopes());
    }

    public function testGetDocumentIdReturnsConfiguredValue(): void
    {
        $manager = new OneDriveAuthConfigurationManager($this->configuration);
        $this->assertSame('file.abc123.ABC123', $manager->getDocumentId());
    }
}
