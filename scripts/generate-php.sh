#!/bin/bash
set -e

echo "Generating PHP client using standard PHP generator..."
docker run --rm \
  -v $(pwd):/local \
  -w /local \
  openapitools/openapi-generator-cli \
  generate \
  -i /local/openapi.yaml \
  -g php \
  -o /local/generated/php \
  -c /local/config/php-config.json

echo "PHP client generated successfully in generated/php/"