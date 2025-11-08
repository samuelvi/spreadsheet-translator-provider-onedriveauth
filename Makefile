.PHONY: help install update test test-coverage phpstan rector rector-dry clean lint fix all

# Colors for output
BLUE := \033[0;34m
GREEN := \033[0;32m
YELLOW := \033[0;33m
NC := \033[0m # No Color

help: ## Show this help message
	@echo '$(BLUE)Available targets:$(NC)'
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "  $(GREEN)%-20s$(NC) %s\n", $$1, $$2}'

install: ## Install dependencies
	@echo "$(BLUE)Installing dependencies...$(NC)"
	composer install

update: ## Update dependencies
	@echo "$(BLUE)Updating dependencies...$(NC)"
	composer update

test: ## Run unit tests
	@echo "$(BLUE)Running unit tests...$(NC)"
	vendor/bin/phpunit

test-coverage: ## Run tests with coverage report
	@echo "$(BLUE)Running tests with coverage...$(NC)"
	XDEBUG_MODE=coverage vendor/bin/phpunit --coverage-html=coverage --coverage-text

phpstan: ## Run PHPStan static analysis
	@echo "$(BLUE)Running PHPStan static analysis...$(NC)"
	vendor/bin/phpstan analyse

rector: ## Run Rector to upgrade code
	@echo "$(BLUE)Running Rector to upgrade code...$(NC)"
	vendor/bin/rector process

rector-dry: ## Run Rector in dry-run mode
	@echo "$(BLUE)Running Rector in dry-run mode...$(NC)"
	vendor/bin/rector process --dry-run

lint: ## Run all linting tools (PHPStan + Rector dry-run)
	@echo "$(BLUE)Running linters...$(NC)"
	@make phpstan
	@make rector-dry

fix: ## Auto-fix code with Rector
	@echo "$(BLUE)Auto-fixing code...$(NC)"
	@make rector

clean: ## Clean generated files
	@echo "$(BLUE)Cleaning generated files...$(NC)"
	rm -rf vendor
	rm -rf coverage
	rm -rf .phpunit.cache
	rm -f composer.lock

all: install lint test ## Install, lint, and test
	@echo "$(GREEN)All tasks completed successfully!$(NC)"

ci: install lint test ## Run CI pipeline (install, lint, test)
	@echo "$(GREEN)CI pipeline completed!$(NC)"
