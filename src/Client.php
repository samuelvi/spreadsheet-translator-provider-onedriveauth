<?php

/*
 * This file is part of the Atico/SpreadsheetTranslator package.
 *
 * (c) Samuel Vicent <samuelvicent@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Atico\SpreadsheetTranslator\Provider\OneDriveAuth;

use Psr\Http\Message\StreamInterface;
use Exception;
use GuzzleHttp;

class Client
{
    const API_URL = 'https://apis.live.net/v5.0/';

    function __construct(private readonly string $accesToken)
    {
    }

    public function getContent(string $fileId, string $accessToken): StreamInterface
    {
        $guzzleHttpClient = new GuzzleHttp\Client();
        $url = self::API_URL . $fileId . '?access_token=' . urlencode($accessToken);

        $result = $guzzleHttpClient->get($url)->getBody();
        $decoded = json_decode((string)$result, true);
        if (is_array($decoded) && isset($decoded['error'])) {
            $remoteErrorMessage = sprintf('Remote error message: "%s"', $decoded['error']['message']);
            throw new Exception(sprintf('Error while getting remote content. %s', $remoteErrorMessage));
        }
        if (!is_array($decoded) || !isset($decoded['source'])) {
            throw new Exception('Invalid response from OneDrive API');
        }
        return $guzzleHttpClient->get($decoded['source'])->getBody();
    }

    public function getExcelFileContent(string $excelFileId): StreamInterface
    {
        return $this->getContent($excelFileId, $this->accesToken);
    }

}