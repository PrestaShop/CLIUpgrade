default: install-box build

# Install the phar compiler
install-box:
	# Install the phar compiler
	wget -O box.phar "https://github.com/box-project/box/releases/download/4.6.1/box.phar"
	chmod +x box.phar
	@echo "--> Phar compiler installed!"

# Prepare the build and install all dependencies
prepare-build:
	# Remove the var directory and install the dependencies
	rm -rf build/ var/
	composer install --no-interaction
	# Dump the environment variables
	composer dump-env prod
	@echo "--> Build preparation complete!"

# Build the application
build: prepare-build
	php box.phar compile
	@echo "--> Build complete!"

# PHP CS fixer
cs-fixer:
	php vendor/bin/php-cs-fixer fix

# PHPStan
phpstan:
	php vendor/bin/phpstan analyse

.PHONY: install-box prepare-build build cs-fixer phpstan