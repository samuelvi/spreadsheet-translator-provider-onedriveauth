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

use Atico\SpreadsheetTranslator\Core\Configuration\ProviderConfigurationInterface;
use Atico\SpreadsheetTranslator\Core\Provider\DefaultProviderManager;

class OneDriveAuthConfigurationManager extends DefaultProviderManager implements ProviderConfigurationInterface
{

    public function getClientId(): mixed
    {
        return $this->getRequiredOption('client_id');
    }

    public function getClientSecret(): mixed
    {
        return $this->getRequiredOption('client_secret');
    }

    public function getCallbackUri(): mixed
    {
        return $this->getRequiredOption('callback_uri');
    }

    public function getObtainedCode(): mixed
    {
        return $this->getRequiredOption('obtained_code');
    }

    public function getAccessToken(): mixed
    {
        return $this->getRequiredOption('access_token');
    }

    public function getPromptForAccessToken(): mixed
    {
        return $this->getRequiredOption('prompt_for_access_token');
    }

    public function getPromptAccessTokenBrowser(): mixed
    {
        return $this->getNonRequiredOption('prompt_access_token_browser', '');
    }

    public function getScopes(): mixed
    {
        return $this->getNonRequiredOption('scopes', '');
    }

    public function getDocumentId(): mixed
    {
        return $this->getRequiredOption('document_id');
    }

}