#!/bin/bash
set -e

cd /Users/vatvit/projects/vatvit/openapi-generator-experiments

echo "🧪 Testing with apiSuffix=''"
echo ""

rm -rf test-generation/output-no-suffix

docker run --rm \
  -v "$(pwd)":/local \
  openapitools/openapi-generator-cli:latest generate \
  -i /local/test-generation/tictactoe-tagged.json \
  -g php-laravel \
  -o /local/test-generation/output-no-suffix \
  -c /local/test-generation/test-config-no-suffix.json \
  --template-dir /local/openapi-generator-server-laravel

echo ""
echo "✅ Generation complete!"
echo ""
echo "Generated API files:"
ls -1 test-generation/output-no-suffix/lib/Api/ | head -10
