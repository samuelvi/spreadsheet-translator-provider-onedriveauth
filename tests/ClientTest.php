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

use Atico\SpreadsheetTranslator\Provider\OneDriveAuth\Client;
use PHPUnit\Framework\TestCase;
use Exception;
use Mockery;
use GuzzleHttp\Client as GuzzleClient;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;

class ClientTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function testGetExcelFileContentReturnsStreamInterface(): void
    {
        $accessToken = 'test-access-token';
        $fileId = 'file.abc123.ABC123';

        $sourceUrl = 'https://storage.live.com/items/file.abc123.ABC123/content';

        // Mock the first API call response (get file info)
        $fileInfoResponse = [
            'id' => $fileId,
            'name' => 'test.xlsx',
            'source' => $sourceUrl
        ];

        $stream1Mock = Mockery::mock(StreamInterface::class);
        $stream1Mock->shouldReceive('__toString')
            ->once()
            ->andReturn(json_encode($fileInfoResponse));

        $response1Mock = Mockery::mock(ResponseInterface::class);
        $response1Mock->shouldReceive('getBody')
            ->once()
            ->andReturn($stream1Mock);

        // Mock the second API call response (get file content)
        $fileContent = 'excel file binary content';
        $stream2Mock = Mockery::mock(StreamInterface::class);

        $response2Mock = Mockery::mock(ResponseInterface::class);
        $response2Mock->shouldReceive('getBody')
            ->once()
            ->andReturn($stream2Mock);

        // Mock Guzzle client
        $guzzleClientMock = Mockery::mock('overload:' . GuzzleClient::class);
        $guzzleClientMock->shouldReceive('get')
            ->once()
            ->with(Mockery::pattern('/apis\.live\.net\/v5\.0\/' . preg_quote($fileId, '/') . '\?access_token=/'))
            ->andReturn($response1Mock);

        $guzzleClientMock->shouldReceive('get')
            ->once()
            ->with($sourceUrl)
            ->andReturn($response2Mock);

        $client = new Client($accessToken);
        $result = $client->getExcelFileContent($fileId);

        $this->assertInstanceOf(StreamInterface::class, $result);
    }

    public function testGetContentThrowsExceptionOnApiError(): void
    {
        $this->expectException(Exception::class);
        $this->expectExceptionMessageMatches('/Error while getting remote content/');
        $this->expectExceptionMessageMatches('/Remote error message:/');

        $accessToken = 'test-access-token';
        $fileId = 'file.abc123.ABC123';

        // Mock an error response
        $errorResponse = [
            'error' => [
                'code' => 'request_token_invalid',
                'message' => 'The access token is not valid.'
            ]
        ];

        $streamMock = Mockery::mock(StreamInterface::class);
        $streamMock->shouldReceive('__toString')
            ->once()
            ->andReturn(json_encode($errorResponse));

        $responseMock = Mockery::mock(ResponseInterface::class);
        $responseMock->shouldReceive('getBody')
            ->once()
            ->andReturn($streamMock);

        $guzzleClientMock = Mockery::mock('overload:' . GuzzleClient::class);
        $guzzleClientMock->shouldReceive('get')
            ->once()
            ->andReturn($responseMock);

        $client = new Client($accessToken);
        $client->getContent($fileId, $accessToken);
    }

    public function testGetContentBuildsCorrectApiUrl(): void
    {
        $accessToken = 'test-access-token-123';
        $fileId = 'file.xyz789.XYZ789';

        $expectedUrlPattern = '/^https:\/\/apis\.live\.net\/v5\.0\/' .
            preg_quote($fileId, '/') .
            '\?access_token=' .
            preg_quote(urlencode($accessToken), '/') . '$/';

        $sourceUrl = 'https://storage.live.com/items/file.xyz789.XYZ789/content';

        $fileInfoResponse = [
            'id' => $fileId,
            'source' => $sourceUrl
        ];

        $stream1Mock = Mockery::mock(StreamInterface::class);
        $stream1Mock->shouldReceive('__toString')
            ->once()
            ->andReturn(json_encode($fileInfoResponse));

        $response1Mock = Mockery::mock(ResponseInterface::class);
        $response1Mock->shouldReceive('getBody')
            ->once()
            ->andReturn($stream1Mock);

        $stream2Mock = Mockery::mock(StreamInterface::class);

        $response2Mock = Mockery::mock(ResponseInterface::class);
        $response2Mock->shouldReceive('getBody')
            ->once()
            ->andReturn($stream2Mock);

        $guzzleClientMock = Mockery::mock('overload:' . GuzzleClient::class);
        $guzzleClientMock->shouldReceive('get')
            ->once()
            ->with(Mockery::pattern($expectedUrlPattern))
            ->andReturn($response1Mock);

        $guzzleClientMock->shouldReceive('get')
            ->once()
            ->with($sourceUrl)
            ->andReturn($response2Mock);

        $client = new Client($accessToken);
        $result = $client->getContent($fileId, $accessToken);

        $this->assertInstanceOf(StreamInterface::class, $result);
    }
}
