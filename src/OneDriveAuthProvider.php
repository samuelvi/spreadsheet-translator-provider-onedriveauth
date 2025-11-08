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
use Atico\SpreadsheetTranslator\Core\Configuration\Configuration;
use Atico\SpreadsheetTranslator\Core\Provider\ProviderInterface;
use Atico\SpreadsheetTranslator\Core\Resource\Resource;

class OneDriveAuthProvider implements ProviderInterface
{
    protected OneDriveAuthConfigurationManager $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = new OneDriveAuthConfigurationManager($configuration);
    }

    public function getProvider(): string
    {
        return 'onedrive_auth';
    }

    protected function obtainAccessToken(): string
    {
        $accessToken = $this->configuration->getAccessToken();
        if (!empty($accessToken)) {
            return $accessToken;
        }

        if (empty($accessToken) && $this->configuration->getPromptForAccessToken()) {
            $commandLineAccessTokenManager = new CommandLineAccessTokenManager($this->configuration->getPromptAccessTokenBrowser());
            $accessTokenInformation = $commandLineAccessTokenManager->obtainAccessTokenInformation(
                $this->configuration->getClientId(),
                $this->configuration->getClientSecret(),
                $this->configuration->getCallbackUri(),
                explode(',', (string)$this->configuration->getScopes())
            );

            return $accessTokenInformation['access_token'];
        } else {
            throw new Exception('option parameter required: access_token or prompt_for_access_token');
        }
    }

    public function handleSourceResource(): Resource
    {
        $tempLocalResource = $this->configuration->getTempLocalSourceFile();
        $excelFileId = $this->configuration->getDocumentId();

        $content = $this->getExcelFileContent($excelFileId);

        file_put_contents($tempLocalResource, $content);
        return new Resource($tempLocalResource, $this->configuration->getFormat());
    }

    protected function getExcelFileContent(string $excelFileId): StreamInterface
    {
        $accessToken = $this->obtainAccessToken();

        /** @var Client $oneDriveClient */
        $oneDriveClient = new Client($accessToken);
        return $oneDriveClient->getExcelFileContent($excelFileId);
    }
}