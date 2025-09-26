.PHONY: serve install setup schema clean deploy deploy-files deploy-schema ssh

# Default target
serve: setup
	@echo "Starting PHP development server on http://localhost:8000"
	@php -S localhost:8000

# Install composer dependencies
install:
	@echo "Installing composer dependencies..."
	@composer install

# Setup project (install deps and create schema)
setup: install
	@echo "Setting up database schema..."
	@php deploy-schema.php

# Create/update database schema
schema:
	@echo "Updating database schema..."
	@php deploy-schema.php

# Clean up generated files
clean:
	@echo "Cleaning up..."
	@rm -rf var/cache/* var/data.db*

# Load environment variables
include .env
export

# Deploy to production server
deploy-files:
	@echo "Deploying to production server..."
	@echo "Host: $(HOST)"
	@echo "Directory: $(FTP_DIRECTORY)"
	@rsync -avz --delete \
		--exclude='.sl' \
		--exclude='.git' \
		--exclude='var/data.db*' \
		--exclude='.env.local' \
		--exclude='node_modules' \
		--exclude='.DS_Store' \
		--exclude='public/' \
		./ $(FTP_USER)@$(HOST):$(FTP_DIRECTORY)
	@echo "✓ Deployment complete!"

# SSH to production server and run database migrations
deploy-schema:
	@echo "Running database migrations on production server..."
	@ssh $(FTP_USER)@$(HOST) "cd $(FTP_DIRECTORY) && php-8.3 deploy-schema.php"
	@echo "✓ Database migrations complete!"


# SSH to production server
ssh:
	@ssh $(FTP_USER)@$(HOST)

# Help
help:
	@echo "Available commands:"
	@echo "  serve                - Start PHP development server (default)"
	@echo "  install              - Install composer dependencies"
	@echo "  setup                - Install deps and setup database"
	@echo "  schema               - Update database schema"
	@echo "  clean                - Clean up generated files"
	@echo "  deploy-files         - Deploy to production server via rsync"
	@echo "  deploy-schema        - Run database migrations on production"
	@echo "  deploy               - Full deployment (files + schema)"
	@echo "  ssh                  - SSH to production server"

deploy: deploy-files deploy-schema
