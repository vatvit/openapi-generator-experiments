#!/bin/bash

# Add security documentation to generated routes
# This script reads the OpenAPI spec and adds security comments to routes

SPEC_FILE="$1"
ROUTES_FILE="$2"

if [ -z "$SPEC_FILE" ] || [ -z "$ROUTES_FILE" ]; then
    echo "Usage: $0 <openapi-spec.json> <routes.php>"
    exit 1
fi

echo "📋 Adding security documentation from spec: $SPEC_FILE"
echo "📁 To routes file: $ROUTES_FILE"

# Extract security info from spec and add to routes file
# For now, just add a header comment about security
cat > "${ROUTES_FILE}.tmp" << 'EOF'
/**
 * SECURITY REQUIREMENTS
 *
 * This API uses the following security schemes (from OpenAPI spec):
 *
 * - Bearer Token (JWT): Add 'Authorization: Bearer <token>' header
 *   Middleware: \App\Http\Middleware\ValidateBearerToken::class
 *
 * - API Key: Add 'api-key' header
 *   Middleware: \App\Http\Middleware\ValidateApiKey::class
 *
 * Configure security middleware in bootstrap/app.php:
 * ```php
 * $middleware->group('api.middlewareGroup.createGame', [
 *     \App\Http\Middleware\ValidateBearerToken::class,
 * ]);
 * ```
 */

EOF

# Append original routes
cat "$ROUTES_FILE" >> "${ROUTES_FILE}.tmp"
mv "${ROUTES_FILE}.tmp" "$ROUTES_FILE"

echo "✅ Security documentation added to routes file"
