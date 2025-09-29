#!/bin/bash
set -e

echo "Creating a new custom PHP generator skeleton..."
docker run --rm \
  -v $(pwd):/local \
  -w /local \
  openapitools/openapi-generator-cli \
  meta \
  -o /local/generators/my-php-generator \
  -n my-php-generator \
  -p com.app.openapi.codegen

echo "Custom generator skeleton created in generators/my-php-generator/"
echo "You can build and use this as a completely custom generator JAR file."