.PHONY: serve install setup schema clean

# Default target
serve: setup
	@echo "Starting PHP development server on http://localhost:8000"
	@php -S localhost:8000 -t public

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

# Help
help:
	@echo "Available commands:"
	@echo "  serve   - Start PHP development server (default)"
	@echo "  install - Install composer dependencies"
	@echo "  setup   - Install deps and setup database"
	@echo "  schema  - Update database schema"
	@echo "  clean   - Clean up generated files"