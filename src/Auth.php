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

use Exception;
use GuzzleHttp\Client;
class Auth
{
    const AUTH_URL = 'https://login.live.com/oauth20_authorize.srf';
    const TOKEN_URL = 'https://login.live.com/oauth20_token.srf';

    /**
     * @param array<string> $scopes
     */
    public function __construct(protected array $scopes)
    {
    }

    /**
     * @return array<string>
     */
    public function getScopes(): array
    {
        return $this->scopes;
    }

    public function getLogInUrl(?string $clientId, string $redirectUri): string
    {
        if (null === $clientId) {
            throw new Exception('The client ID must be set to call getLoginUrl()');
        }

        $imploded = implode(',', $this->getScopes());

        return self::AUTH_URL
            . '?client_id=' . urlencode($clientId)
            . '&scope=' . urlencode($imploded)
            . '&response_type=code'
            . '&redirect_uri=' . urlencode($redirectUri)
            . '&display=popup'
            . '&locale=en';
    }

    /**
     * @return array<string, mixed>
     */
    public function obtainAccessTokenInformation(string $cliendId, string $redirectUri, string $clientSecret, string $code): array
    {
        $fields = [
            'client_id' => $cliendId,
            'redirect_uri' => $redirectUri,
            'client_secret' => $clientSecret,
            'code' => $code,
            'grant_type' => 'authorization_code',

        ];
        $url = self::TOKEN_URL;

        $guzzleHttpClient = new Client();
        $result = $guzzleHttpClient->post($url, [ 'form_params' => $fields ])->getBody();


        $response = json_decode((string)$result, true);
        if (null === $response) {
            throw new Exception('json_decode() failed');
        }

        return $response;
    }
}