# Issue: Empty README.mustache Causes Generation Exception

## Problem Description

Generation fails with error when `README.mustache` template file is empty.

## Error Message

```
Exception in thread "main" org.openapitools.codegen.TemplateProcessingException: Could not generate supporting file 'README.mustache'
```

## When This Occurs

- After deleting the `generated-v2/` directory
- On first run with custom templates
- When `templates/php-laravel-scaffolding-v2/README.mustache` is empty or missing content

## Root Cause

OpenAPI Generator requires template files to have content. The generator attempts to process the template and fails if it's empty or malformed.

This is a validation check in the OpenAPI Generator core to catch template errors early.

## Solution

Ensure `templates/php-laravel-scaffolding-v2/README.mustache` has content.

### Minimal Valid Template

```mustache
# {{packageName}}

{{packageDescription}}

## Generated API Scaffolding

This scaffolding was generated from an OpenAPI specification.
```

### Complete Template Example

```mustache
# {{packageName}}

{{packageDescription}}

## Generated API Scaffolding

This package contains Laravel server-side scaffolding generated from an OpenAPI specification.

### Components

- **API Interfaces**: Handler interfaces and response types
- **Abstract Controllers**: Base controllers with validation
- **Routes**: Laravel route definitions
- **Models**: Request/response models

### Usage

See the main Laravel application for implementation examples.

### Generated From

- **Spec**: {{specPath}}
- **Generator**: php-laravel
- **Version**: {{generatorVersion}}
```

## Prevention

When creating custom templates:
1. **Always add content** to every `.mustache` file
2. **Never leave templates empty** - at minimum add a comment
3. **Test generation** after creating new templates

## Quick Fix

If you encounter this error:

```bash
# Add minimal content to README template
echo "# {{packageName}}\n\n{{packageDescription}}" > templates/php-laravel-scaffolding-v2/README.mustache

# Regenerate
make generate-scaffolding-v2
```

## Related Template Files

All template files must have content:
- `README.mustache` ⚠️ Common issue
- `api.mustache`
- `api_controller.mustache`
- `routes.mustache`
- `composer.mustache`
- All other `.mustache` files

## Why This Validation Exists

OpenAPI Generator validates templates to catch errors early:
- Detects accidental empty files
- Prevents partial generation
- Ensures template completeness
- Provides clear error messages

This is a feature, not a bug - it helps catch configuration issues before generating invalid code.

## Related Resources

- Main documentation: [CLAUDE.md](CLAUDE.md)
- Template customization guide: OpenAPI Generator docs
- All known issues: [KNOWN_ISSUES.md](KNOWN_ISSUES.md)
