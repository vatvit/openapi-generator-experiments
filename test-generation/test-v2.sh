#!/bin/bash
set -e

cd /Users/vatvit/projects/vatvit/openapi-generator-experiments

echo "🧪 Testing new API-specific templates"
echo ""

rm -rf test-generation/output-v2

docker run --rm \
  -v "$(pwd)":/local \
  openapitools/openapi-generator-cli:latest generate \
  -i /local/test-generation/tictactoe-tagged.json \
  -g php-laravel \
  -o /local/test-generation/output-v2 \
  -c /local/test-generation/test-config-v2.json \
  --template-dir /local/openapi-generator-server-laravel

echo ""
echo "✅ Generation complete!"
echo ""

echo "Handler files:"
find test-generation/output-v2/lib/Handlers -name "*.php" 2>/dev/null | sort || echo "  (none)"

echo ""
echo "Response files:"
find test-generation/output-v2/lib/Http/Responses -name "*.php" 2>/dev/null | sort || echo "  (none)"
