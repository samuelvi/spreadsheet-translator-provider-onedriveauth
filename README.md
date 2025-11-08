# Spreadsheet Translator - Microsoft OneDrive Provider (with Authentication)

[![PHP Version](https://img.shields.io/badge/php-%3E%3D8.4-blue.svg)](https://php.net/)
[![License](https://img.shields.io/badge/license-MIT-green.svg)](LICENSE)

This package provides a provider for the Spreadsheet Translator project that fetches shared spreadsheet documents from Microsoft OneDrive using OAuth 2.0 authentication.

## Features

- OAuth 2.0 authentication with Microsoft Live
- Supports both direct access token and interactive authentication flow
- Downloads Excel files from OneDrive via the Microsoft Live API
- Integrates seamlessly with the Spreadsheet Translator ecosystem
- Built with modern PHP 8.4 features

## Installation

Install via Composer:

```bash
composer require samuelvi/spreadsheet-translator-provider-onedriveauth
```

## Requirements

- PHP >= 8.4
- Guzzle HTTP client ^7.7
- Microsoft Azure application credentials (client ID and secret)
- OneDrive file ID of the spreadsheet to fetch

## Usage

### Configuration Options

The provider requires the following configuration options:

- `client_id`: Your Microsoft application client ID
- `client_secret`: Your Microsoft application client secret
- `callback_uri`: OAuth redirect URI configured in your Microsoft app
- `document_id`: The OneDrive file ID to fetch
- `access_token`: (Optional) Pre-obtained access token
- `prompt_for_access_token`: (Optional) Enable interactive OAuth flow
- `prompt_access_token_browser`: (Optional) Browser preference for OAuth (e.g., 'chrome', 'firefox')
- `scopes`: (Optional) Comma-separated OAuth scopes (e.g., 'wl.offline_access,wl.skydrive_update')

### Example Configuration

```php
use Atico\SpreadsheetTranslator\Core\Configuration\Configuration;
use Atico\SpreadsheetTranslator\Provider\OneDriveAuth\OneDriveAuthProvider;

$config = new Configuration([
    'provider' => [
        'options' => [
            'client_id' => 'your-client-id',
            'client_secret' => 'your-client-secret',
            'callback_uri' => 'http://localhost/callback',
            'document_id' => 'file.abc123.ABC123',
            'access_token' => 'your-access-token', // or use prompt_for_access_token
            'temp_local_source_file' => '/tmp/spreadsheet.xlsx',
            'format' => 'xlsx'
        ]
    ]
]);

$provider = new OneDriveAuthProvider($config);
$resource = $provider->handleSourceResource();
```

### Interactive Authentication

If you don't have an access token, enable interactive authentication:

```php
$config = new Configuration([
    'provider' => [
        'options' => [
            'client_id' => 'your-client-id',
            'client_secret' => 'your-client-secret',
            'callback_uri' => 'http://localhost/callback',
            'document_id' => 'file.abc123.ABC123',
            'prompt_for_access_token' => true,
            'prompt_access_token_browser' => 'chrome',
            'scopes' => 'wl.offline_access,wl.skydrive_update',
            'temp_local_source_file' => '/tmp/spreadsheet.xlsx',
            'format' => 'xlsx'
        ]
    ]
]);
```

## Development

### Available Commands

The project includes a Makefile with common development tasks:

```bash
make help              # Show all available commands
make install           # Install dependencies
make update            # Update dependencies
make test              # Run unit tests
make test-coverage     # Run tests with coverage report
make phpstan           # Run PHPStan static analysis
make rector            # Run Rector to upgrade code
make rector-dry        # Run Rector in dry-run mode (preview changes)
make lint              # Run all linting tools
make fix               # Auto-fix code with Rector
make clean             # Clean generated files
make all               # Install, lint, and test
make ci                # Run CI pipeline
```

### Running Tests

```bash
# Run all tests
make test

# Run tests with coverage
make test-coverage

# Run specific test
vendor/bin/phpunit tests/AuthTest.php
```

### Code Quality

```bash
# Static analysis
make phpstan

# Code refactoring (dry-run)
make rector-dry

# Apply code refactoring
make rector
```

## Architecture

This provider implements the `ProviderInterface` from the Core package and consists of:

- **OneDriveAuthProvider**: Main provider class that orchestrates the OAuth flow
- **Auth**: Handles OAuth 2.0 authentication with Microsoft Live
- **Client**: OneDrive API client for fetching file content
- **CommandLineAccessTokenManager**: Interactive CLI tool for obtaining OAuth tokens
- **OneDriveAuthConfigurationManager**: Configuration management

## Related Packages

- [Core Bundle](https://github.com/samuelvi/spreadsheet-translator-core) - Base interfaces and utilities
- [Symfony Bundle](https://github.com/samuelvi/spreadsheet-translator-symfony-bundle) - Symfony integration

## Contributing

We welcome contributions to this project, including pull requests and issues (and discussions on existing issues).

If you'd like to contribute code but aren't sure what, the issues list is a good place to start. If you're a first-time code contributor, you may find Github's guide to [forking projects](https://guides.github.com/activities/forking/) helpful.

All contributors (whether contributing code, involved in issue discussions, or involved in any other way) must abide by our code of conduct.

### Development Setup

1. Clone the repository
2. Run `make install` to install dependencies
3. Run `make test` to ensure tests pass
4. Make your changes
5. Run `make lint` to check code quality
6. Run `make test` to verify tests still pass
7. Submit a pull request

## License

Spreadsheet Translator OneDrive Auth Provider is licensed under the MIT License. See the LICENSE file for full details.