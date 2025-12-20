#!/bin/bash
set -e

cd /Users/vatvit/projects/vatvit/openapi-generator-experiments

echo "🧪 Testing default php-laravel generator (no custom templates)"
echo ""

docker run --rm \
  -v "$(pwd)":/local \
  openapitools/openapi-generator-cli:latest generate \
  -i /local/test-generation/tictactoe-tagged.json \
  -g php-laravel \
  -o /local/test-generation/output-default \
  -c /local/test-generation/test-config-minimal.json

echo ""
echo "✅ Generation complete!"
echo "📁 Checking generated structure..."
echo ""

echo "All lib directories:"
find test-generation/output-default/lib -type d | sort

echo ""
echo "Handler/Response related files:"
find test-generation/output-default -type f -name "*Handler*" -o -name "*Response*" | sort
