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
use Atico\SpreadsheetTranslator\Core\Util\BrowserPathManager;

class CommandLineAccessTokenManager
{
    private ?Auth $auth = null;

    private ?BrowserPathManager $browserPathManager = null;

    public function __construct(private readonly ?string $browserName)
    {
    }

    /**
     * @param array<string> $scopes
     * @return array<string, mixed>
     */
    public function obtainAccessTokenInformation(string $clientId, string $clientSecret, string $callbackUri, array $scopes): array
    {
        $this->auth = new Auth($scopes);
        $url = $this->auth->getLogInUrl($clientId, $callbackUri);

        $this->browserPathManager = new BrowserPathManager();
        $path = $this->browserPathManager->getBrowserCommandForOpeningUrl($this->browserName ?? '', $url);

        shell_exec($path);

        echo PHP_EOL . PHP_EOL . $url . PHP_EOL . PHP_EOL;
        echo sprintf('%sCopy paste url into a browser and enter the querystring url param code value:%s. Enter code: ', PHP_EOL . PHP_EOL, PHP_EOL . PHP_EOL);

        $handle = fopen('php://stdin', 'r');
        if ($handle === false) {
            throw new Exception('Could not open stdin for reading');
        }
        $code = fgets($handle);
        fclose($handle);

        if ($code === false) {
            throw new Exception('Could not read code from stdin');
        }

        $code = trim($code);
        $accessTokenInformation = $this->auth->obtainAccessTokenInformation($clientId, $callbackUri, $clientSecret, $code);
        echo sprintf('Obtained Access Token: %s%s%s', PHP_EOL, $accessTokenInformation['access_token'], PHP_EOL);
        return $accessTokenInformation;
    }
}