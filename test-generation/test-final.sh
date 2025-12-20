#!/bin/bash
set -e

cd /Users/vatvit/projects/vatvit/openapi-generator-experiments

echo "🧪 Testing PSR-4 compliant generation with Api namespace"
echo ""

rm -rf test-generation/output-final

docker run --rm \
  -v "$(pwd)":/local \
  openapitools/openapi-generator-cli:latest generate \
  -i /local/test-generation/tictactoe-tagged.json \
  -g php-laravel \
  -o /local/test-generation/output-final \
  -c /local/test-generation/test-config-final.json \
  --template-dir /local/openapi-generator-server-laravel

echo ""
echo "✅ Generation complete!"
echo ""
echo "Generated files in lib/Api/:"
ls -1 test-generation/output-final/lib/Api/ 2>/dev/null | head -10

echo ""
echo "Checking PSR-4 compliance for GetBoardApiInterface:"
echo "  Filename: $(ls test-generation/output-final/lib/Api/ | grep GetBoard)"
echo "  Class inside:"
grep "^interface" test-generation/output-final/lib/Api/GetBoardApiInterface.php 2>/dev/null || echo "    (not found)"
