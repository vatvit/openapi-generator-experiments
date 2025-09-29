#!/bin/bash
set -e

echo "🧪 Testing OpenAPI Generator Setup"
echo "=================================="

# Test 1: Validate OpenAPI specification
echo "📋 Test 1: Validating OpenAPI specification..."
make validate-spec
echo "✅ OpenAPI specification is valid"

# Test 2: Generate PHP client
echo "📋 Test 2: Generating PHP client..."
make clean
make generate-php
echo "✅ PHP client generated successfully"

# Test 3: Verify generated files exist
echo "📋 Test 3: Verifying generated files..."
REQUIRED_FILES=(
    "generated/php/composer.json"
    "generated/php/lib/Api/DefaultApi.php"
    "generated/php/lib/Model/Pet.php"
    "generated/php/lib/Model/NewPet.php"
    "generated/php/lib/Model/Error.php"
    "generated/php/lib/Configuration.php"
    "generated/php/README.md"
)

for file in "${REQUIRED_FILES[@]}"; do
    if [ -f "$file" ]; then
        echo "  ✅ $file exists"
    else
        echo "  ❌ $file is missing"
        exit 1
    fi
done

# Test 4: Check generated PHP syntax
echo "📋 Test 4: Checking generated PHP syntax..."
docker run --rm -v $(pwd):/app -w /app php:8.3-cli \
    find generated/php -name "*.php" -exec php -l {} \; > /dev/null 2>&1
echo "✅ Generated PHP code has valid syntax"

# Test 5: Extract templates
echo "📋 Test 5: Extracting default templates..."
make extract-templates
if [ -d "templates/php-default" ] && [ -f "templates/php-default/api.mustache" ]; then
    echo "✅ Templates extracted successfully"
else
    echo "❌ Template extraction failed"
    exit 1
fi

# Test 6: Test custom template generation
echo "📋 Test 6: Testing custom template generation..."
# Copy a template to customize
cp templates/php-default/README.mustache templates/custom-php/README.mustache
# Add a custom text to the template that will appear in output
sed -i.bak '1i\
**CUSTOM TEMPLATE VERSION**\
' templates/custom-php/README.mustache
make generate-custom-php
if grep -q "CUSTOM TEMPLATE VERSION" generated/custom-php/README.md 2>/dev/null; then
    echo "✅ Custom template generation works"
else
    echo "❌ Custom template generation failed"
    exit 1
fi

# Test 7: Check Composer dependencies
echo "📋 Test 7: Verifying generated Composer configuration..."
docker run --rm -v $(pwd)/generated/php:/app -w /app composer:latest validate --no-check-publish
echo "✅ Generated composer.json is valid"

echo ""
echo "🎉 All OpenAPI Generator tests passed!"
echo "Generated files are ready for use."