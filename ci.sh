#!/bin/sh

# Get composer
if [ ! -f $(pwd)/composer.phar ]; then
  echo "=> Download composer.phar and run composer update"
  curl -sS https://getcomposer.org/installer | php;
fi

# Update vendors
php composer.phar update;

# Run tests
php composer.phar test-ci;

echo " ";
echo "ci script => END.";
