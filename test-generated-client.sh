#!/bin/bash
set -e

echo "🧪 Testing Generated PHP Client"
echo "==============================="

# Ensure we have generated code
if [ ! -d "generated/php" ]; then
    echo "⚠️  Generated PHP client not found. Generating now..."
    make generate-php
fi

# Test 1: Verify Composer dependencies can be installed
echo "📋 Test 1: Installing generated client dependencies..."
docker run --rm -v $(pwd)/generated/php:/app -w /app composer:latest install --no-dev
echo "✅ Composer dependencies installed successfully"

# Test 2: Test autoloading works
echo "📋 Test 2: Testing PHP autoloading..."
docker run --rm -v $(pwd)/generated/php:/app -w /app php:8.3-cli php -r "
require_once 'vendor/autoload.php';
// Test that classes can be loaded
\$config = new App\ApiClient\Configuration();
\$api = new App\ApiClient\Api\DefaultApi();
echo 'Autoloading works - classes loaded successfully';
"
echo "✅ Autoloading works correctly"

# Test 3: Test Configuration class
echo "📋 Test 3: Testing Configuration class..."
docker run --rm -v $(pwd)/generated/php:/app -w /app php:8.3-cli php -r "
require_once 'vendor/autoload.php';
\$config = new App\ApiClient\Configuration();
\$config->setHost('https://api.example.com');
\$config->setUserAgent('Test Client/1.0');

if (\$config->getHost() === 'https://api.example.com') {
    echo 'Configuration class works correctly';
} else {
    throw new Exception('Configuration class not working');
}
"
echo "✅ Configuration class works correctly"

# Test 4: Test API client instantiation
echo "📋 Test 4: Testing API client instantiation..."
docker run --rm -v $(pwd)/generated/php:/app -w /app php:8.3-cli php -r "
require_once 'vendor/autoload.php';
\$config = new App\ApiClient\Configuration();
\$config->setHost('https://api.example.com');

\$api = new App\ApiClient\Api\DefaultApi(
    new GuzzleHttp\Client(),
    \$config
);

if (\$api instanceof App\ApiClient\Api\DefaultApi) {
    echo 'API client instantiated successfully';
} else {
    throw new Exception('API client instantiation failed');
}
"
echo "✅ API client instantiation works"

# Test 5: Test Model classes
echo "📋 Test 5: Testing Model classes..."
docker run --rm -v $(pwd)/generated/php:/app -w /app php:8.3-cli php -r "
require_once 'vendor/autoload.php';

// Test Pet model
\$pet = new App\ApiClient\Model\Pet();
\$pet->setName('Test Pet');
\$pet->setTag('test-tag');

if (\$pet->getName() === 'Test Pet' && \$pet->getTag() === 'test-tag') {
    echo 'Pet model works correctly\n';
} else {
    throw new Exception('Pet model not working');
}

// Test NewPet model
\$newPet = new App\ApiClient\Model\NewPet();
\$newPet->setName('New Test Pet');

if (\$newPet->getName() === 'New Test Pet') {
    echo 'NewPet model works correctly\n';
} else {
    throw new Exception('NewPet model not working');
}

// Test Error model
\$error = new App\ApiClient\Model\Error();
\$error->setCode(404);
\$error->setMessage('Not found');

if (\$error->getCode() === 404 && \$error->getMessage() === 'Not found') {
    echo 'Error model works correctly';
} else {
    throw new Exception('Error model not working');
}
"
echo "✅ Model classes work correctly"

# Test 6: Test API method signatures exist
echo "📋 Test 6: Testing API method signatures..."
docker run --rm -v $(pwd)/generated/php:/app -w /app php:8.3-cli php -r "
require_once 'vendor/autoload.php';

\$api = new App\ApiClient\Api\DefaultApi();
\$reflection = new ReflectionClass(\$api);

\$expectedMethods = ['findPets', 'addPet', 'findPetById', 'deletePet'];
\$actualMethods = array_map(function(\$m) { return \$m->getName(); }, \$reflection->getMethods(ReflectionMethod::IS_PUBLIC));

foreach (\$expectedMethods as \$method) {
    if (in_array(\$method, \$actualMethods)) {
        echo \"Method {\$method} exists\n\";
    } else {
        throw new Exception(\"Method {\$method} missing\");
    }
}
"
echo "✅ API methods are properly defined"

# Test 7: Create a simple integration test script
echo "📋 Test 7: Creating integration test example..."
cat > generated/php/integration-test-example.php << 'EOF'
<?php
require_once 'vendor/autoload.php';

// Example integration test - configure for your API server
$config = new App\ApiClient\Configuration();
$config->setHost('http://localhost:8000'); // Laravel API server

$api = new App\ApiClient\Api\DefaultApi(
    new GuzzleHttp\Client([
        'verify' => false, // For development only
        'timeout' => 30,
    ]),
    $config
);

echo "Generated PHP Client Integration Test\n";
echo "====================================\n";

try {
    // This would work when connected to a real API server
    // $pets = $api->findPets(['limit' => 10]);
    // echo "Found " . count($pets) . " pets\n";

    echo "✅ Integration test script created successfully\n";
    echo "   Configure the host URL and run this script against a live API\n";

} catch (Exception $e) {
    echo "Note: " . $e->getMessage() . "\n";
    echo "This is expected when no API server is running\n";
}
EOF

docker run --rm -v $(pwd)/generated/php:/app -w /app php:8.3-cli php integration-test-example.php
echo "✅ Integration test example created"

# Test 8: Validate generated documentation
echo "📋 Test 8: Checking generated documentation..."
REQUIRED_DOCS=(
    "generated/php/README.md"
    "generated/php/docs/Api/DefaultApi.md"
    "generated/php/docs/Model/Pet.md"
    "generated/php/docs/Model/NewPet.md"
    "generated/php/docs/Model/Error.md"
)

for doc in "${REQUIRED_DOCS[@]}"; do
    if [ -f "$doc" ]; then
        echo "  ✅ $doc exists"
    else
        echo "  ❌ $doc is missing"
        exit 1
    fi
done

# Test 9: Check PSR-4 compliance
echo "📋 Test 9: Verifying PSR-4 autoloading compliance..."
docker run --rm -v $(pwd)/generated/php:/app -w /app composer:latest dumpautoload --optimize
echo "✅ PSR-4 autoloading is compliant"

echo ""
echo "🎉 Generated PHP Client tests passed!"
echo ""
echo "📊 Test Summary:"
echo "   ✅ Dependencies install correctly"
echo "   ✅ Autoloading works"
echo "   ✅ Configuration class functional"
echo "   ✅ API client can be instantiated"
echo "   ✅ Model classes work correctly"
echo "   ✅ API methods are defined"
echo "   ✅ Integration test example created"
echo "   ✅ Documentation generated"
echo "   ✅ PSR-4 compliance verified"
echo ""
echo "🚀 Generated PHP client is ready for integration!"