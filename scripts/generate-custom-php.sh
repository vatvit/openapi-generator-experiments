#!/bin/bash
set -e

echo "Generating PHP client using custom PHP generator templates..."
docker run --rm \
  -v $(pwd):/local \
  -w /local \
  openapitools/openapi-generator-cli \
  generate \
  -i /local/openapi.yaml \
  -g php \
  -o /local/generated/custom-php \
  -c /local/config/custom-php-config.json \
  --template-dir /local/templates/custom-php

echo "Custom PHP client generated successfully in generated/custom-php/"