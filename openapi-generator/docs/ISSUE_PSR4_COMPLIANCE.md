# Issue: PSR-4 Compliance Errors in Models

## Problem Description

Generated model classes show PSR-4 compliance warnings with duplicate namespace in their namespace declaration.

## Error Message

```
Class PetStoreApi\Server\PetStoreApi\Server\Models\Pet does not comply with psr-4 autoloading standard
```

## Root Cause

OpenAPI Generator's php-laravel template has a bug where the model namespace is duplicated:

**Generated code:**
```php
<?php
namespace PetStoreApi\Server\PetStoreApi\Server\Models;  // Duplicate!

class Pet {
    // ...
}
```

**Expected code:**
```php
<?php
namespace PetStoreApi\Server\Models;  // Correct

class Pet {
    // ...
}
```

The template incorrectly concatenates the base namespace with the model package namespace.

## Impact

**Functional Impact:** ❌ None
- The warning is cosmetic
- Models are not actually used in the current architecture
- Controllers work with arrays/JSON directly, not model objects

**Developer Experience:** ⚠️ Minor
- Warning messages appear in logs
- PSR-4 validation tools report the issue
- May cause confusion during development

## Status

**Known issue in OpenAPI Generator's php-laravel templates**

This is a template bug, not specific to this project. It affects all users of the php-laravel generator.

## Current Workaround

**Models are not used in current architecture:**
- Controllers receive `Request` objects (JSON/arrays)
- Controllers return `JsonResponse` or typed response classes
- No need to instantiate model classes for request/response handling

**If you need to use models:**
- Create custom model templates with corrected namespace
- Copy from `templates/php-laravel-server-v2/model.mustache`
- Fix the namespace concatenation logic

## Solution Options

### Option 1: Ignore the Warning ✅ (Current approach)
- Models aren't used, so warning has no functional impact
- Simplest approach
- No maintenance burden

### Option 2: Custom Model Templates
- Create custom `model.mustache` template
- Fix namespace concatenation
- Regenerate with custom template

**Template fix (conceptual):**
```mustache
namespace {{invokerPackage}}\{{modelPackage}};
```

Should be:
```mustache
namespace {{invokerPackage}}\Models;
```

### Option 3: Post-Processing Fix
- Parse generated model files
- Fix namespace declarations with regex
- Add to Makefile post-processing steps

**Not recommended:** Adds complexity for unused code

## Related Issues

- OpenAPI Generator GitHub issues (search for "psr-4 php-laravel model namespace")
- This is a known template bug affecting multiple users

## Recommendation

✅ **Ignore the warning** - Models are not used in the current architecture, so the PSR-4 compliance issue has no functional impact.

If you need to use model classes in the future, create custom model templates with the corrected namespace logic.

## Related Resources

- Main documentation: [CLAUDE.md](CLAUDE.md)
- All known issues: [KNOWN_ISSUES.md](KNOWN_ISSUES.md)
- Template customization: OpenAPI Generator documentation
