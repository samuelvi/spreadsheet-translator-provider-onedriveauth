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

use Atico\SpreadsheetTranslator\Provider\OneDriveAuth\Auth;
use PHPUnit\Framework\TestCase;
use Exception;
use Mockery;
use GuzzleHttp\Client;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class AuthTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testGetScopesReturnsExpectedScopes(): void
    {
        $scopes = ['wl.offline_access', 'wl.skydrive_update'];
        $auth = new Auth($scopes);

        $this->assertSame($scopes, $auth->getScopes());
    }

    public function testGetLogInUrlGeneratesCorrectUrl(): void
    {
        $scopes = ['wl.offline_access', 'wl.skydrive_update'];
        $clientId = 'test-client-id';
        $redirectUri = 'http://localhost/callback';

        $auth = new Auth($scopes);
        $url = $auth->getLogInUrl($clientId, $redirectUri);

        $this->assertStringContainsString('client_id=' . urlencode($clientId), $url);
        $this->assertStringContainsString('scope=' . urlencode('wl.offline_access,wl.skydrive_update'), $url);
        $this->assertStringContainsString('response_type=code', $url);
        $this->assertStringContainsString('redirect_uri=' . urlencode($redirectUri), $url);
        $this->assertStringContainsString('display=popup', $url);
        $this->assertStringContainsString('locale=en', $url);
        $this->assertStringStartsWith('https://login.live.com/oauth20_authorize.srf', $url);
    }

    public function testGetLogInUrlThrowsExceptionWhenClientIdIsNull(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('The client ID must be set to call getLoginUrl()');

        $scopes = ['wl.offline_access'];
        $auth = new Auth($scopes);
        $auth->getLogInUrl(null, 'http://localhost/callback');
    }

    public function testObtainAccessTokenInformationReturnsValidResponse(): void
    {
        $clientId = 'test-client-id';
        $clientSecret = 'test-client-secret';
        $redirectUri = 'http://localhost/callback';
        $code = 'test-auth-code';

        $expectedResponse = [
            'access_token' => 'test-access-token',
            'token_type' => 'bearer',
            'expires_in' => 3600,
            'refresh_token' => 'test-refresh-token'
        ];

        // Mock the HTTP response
        $streamMock = Mockery::mock(StreamInterface::class);
        $streamMock->shouldReceive('__toString')
            ->once()
            ->andReturn(json_encode($expectedResponse));

        $responseMock = Mockery::mock(ResponseInterface::class);
        $responseMock->shouldReceive('getBody')
            ->once()
            ->andReturn($streamMock);

        $clientMock = Mockery::mock('overload:' . Client::class);
        $clientMock->shouldReceive('post')
            ->once()
            ->with('https://login.live.com/oauth20_token.srf', [
                'form_params' => [
                    'client_id' => $clientId,
                    'redirect_uri' => $redirectUri,
                    'client_secret' => $clientSecret,
                    'code' => $code,
                    'grant_type' => 'authorization_code',
                ]
            ])
            ->andReturn($responseMock);

        $scopes = ['wl.offline_access'];
        $auth = new Auth($scopes);
        $result = $auth->obtainAccessTokenInformation($clientId, $redirectUri, $clientSecret, $code);

        $this->assertSame($expectedResponse, $result);
    }

    public function testObtainAccessTokenInformationThrowsExceptionOnInvalidJson(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessage('json_decode() failed');

        $clientId = 'test-client-id';
        $clientSecret = 'test-client-secret';
        $redirectUri = 'http://localhost/callback';
        $code = 'test-auth-code';

        // Mock the HTTP response with invalid JSON
        $streamMock = Mockery::mock(StreamInterface::class);
        $streamMock->shouldReceive('__toString')
            ->once()
            ->andReturn('invalid json');

        $responseMock = Mockery::mock(ResponseInterface::class);
        $responseMock->shouldReceive('getBody')
            ->once()
            ->andReturn($streamMock);

        $clientMock = Mockery::mock('overload:' . Client::class);
        $clientMock->shouldReceive('post')
            ->once()
            ->andReturn($responseMock);

        $scopes = ['wl.offline_access'];
        $auth = new Auth($scopes);
        $auth->obtainAccessTokenInformation($clientId, $redirectUri, $clientSecret, $code);
    }
}
