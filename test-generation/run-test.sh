#!/bin/bash
set -e

cd /Users/vatvit/projects/vatvit/openapi-generator-experiments

echo "🧪 Testing per-operation file generation with templateType: API"
echo ""

docker run --rm \
  -v "$(pwd)":/local \
  openapitools/openapi-generator-cli:latest generate \
  -i /local/test-generation/tictactoe-tagged.json \
  -g php-laravel \
  -o /local/test-generation/output \
  -c /local/test-generation/test-config.json \
  --template-dir /local/openapi-generator-server-laravel

echo ""
echo "✅ Generation complete!"
echo "📁 Checking generated files..."
echo ""

# List generated handler files
echo "Handler files:"
find test-generation/output/lib/Handlers -name "*.php" 2>/dev/null | sort

echo ""
echo "Response interface files:"
find test-generation/output/lib/Http/Responses -name "*Interface.php" 2>/dev/null | sort
