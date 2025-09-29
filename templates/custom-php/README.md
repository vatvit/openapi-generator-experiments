# Custom PHP Generator Templates

This directory contains custom Mustache templates for generating PHP code with OpenAPI Generator.

## Template Files

To customize the PHP generator, you can override any of the default templates by placing your custom templates in this directory. The template files use Mustache syntax and follow the same naming convention as the default PHP generator.

## Common Template Files

- `model.mustache` - Template for model/data classes
- `api.mustache` - Template for API client classes
- `configuration.mustache` - Template for client configuration
- `composer.mustache` - Template for composer.json
- `README.mustache` - Template for generated README
- `apiException.mustache` - Template for API exception classes

## Getting Started

1. First, examine the default templates to understand the structure:
   ```bash
   docker run --rm -v $(pwd):/local openapitools/openapi-generator-cli author template -g php -o /local/templates/php-default
   ```

2. Copy the templates you want to customize to this directory

3. Modify the templates according to your needs

4. Generate code using your custom templates:
   ```bash
   docker-compose --profile generate run generate-custom-php
   ```

## Template Variables

Templates have access to various variables provided by the OpenAPI Generator. Check the official documentation for a complete list of available variables for the PHP generator.