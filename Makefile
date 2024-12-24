.PHONY: help install dev build clean fresh test

# Default target when just running 'make'
.DEFAULT_GOAL := help

help: ## Show this help
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[36m%-30s\033[0m %s\n", $$1, $$2}'

install: ## Install dependencies and set up the project
	@echo "🚀 Installing project dependencies..."
	composer install
	npm install
	npm run build
	@echo "📁 Creating storage link..."
	php artisan storage:link
	@echo "🗄️ Setting up database..."
	php artisan migrate
	php artisan db:seed
	@echo "✅ Project setup complete!"

dev: ## Start development servers
	@echo "🚀 Starting development servers..."
	php artisan serve & npm run dev

build: ## Build assets for production
	npm run build

migrate: ## Run database migrations
	php artisan migrate

seed: ## Run database seeders
	php artisan db:seed

fresh: ## Reset and re-seed the database
	php artisan migrate:fresh --seed

clean: ## Clean up generated files and dependencies
	@echo "🧹 Cleaning up..."
	rm -rf vendor
	rm -rf node_modules
	rm -rf public/build
	rm -rf public/storage
	@echo "✨ Cleanup complete!"

test: ## Run tests
	php artisan test

cache-clear: ## Clear application cache
	@echo "🧹 Clearing application cache..."
	php artisan cache:clear
	php artisan config:clear
	php artisan route:clear
	php artisan view:clear
	@echo "✨ Cache cleared!"
