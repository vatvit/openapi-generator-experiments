#!/bin/bash
set -e

echo "Extracting default PHP generator templates for customization..."
docker run --rm \
  -v $(pwd):/local \
  -w /local \
  openapitools/openapi-generator-cli \
  author template \
  -g php \
  -o /local/templates/php-default

echo "Default PHP templates extracted to templates/php-default/"
echo "You can copy and modify templates from there to templates/custom-php/"