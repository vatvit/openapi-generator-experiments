#!/bin/bash
set -e

cd /Users/vatvit/projects/vatvit/openapi-generator-experiments

echo "🧪 Testing with overridden api.mustache template"
echo ""

rm -rf test-generation/output-override

docker run --rm \
  -v "$(pwd)":/local \
  openapitools/openapi-generator-cli:latest generate \
  -i /local/test-generation/tictactoe-tagged.json \
  -g php-laravel \
  -o /local/test-generation/output-override \
  -c /local/test-generation/test-config-override.json \
  --template-dir /local/openapi-generator-server-laravel

echo ""
echo "✅ Generation complete!"
echo ""
echo "Checking PSR-4 compliance:"
echo "  Filename: GetBoardApiInterface.php"
echo "  Interface inside:"
grep "^interface" test-generation/output-override/lib/Api/GetBoardApiInterface.php 2>/dev/null
echo ""
echo "  Method signature:"
grep -A 2 "public function" test-generation/output-override/lib/Api/GetBoardApiInterface.php | head -10
