# Issue: Tag Duplication in Generated Controllers

## Problem Description

When an OpenAPI operation has **multiple tags**, OpenAPI Generator creates **duplicate methods** across multiple controller files.

## Example

**OpenAPI Spec:**
```yaml
paths:
  /board:
    get:
      tags: ["Tic Tac", "Gameplay"]  # Multiple tags
      operationId: "getBoard"
      summary: "Get the whole board"
```

**Generated Output (without post-processing):**
```
lib/Http/Controllers/
├── TicTacController.php      # Contains: getBoard() method
└── GameplayController.php    # Contains: getBoard() method (DUPLICATE!)
```

Both controllers have identical `getBoard()` methods, causing conflicts.

## Root Cause

**OpenAPI Generator's Architecture:**
1. **Parse OpenAPI Specification** - Reads the spec and builds internal model
2. **Group Operations by Tag** - Each operation with multiple tags is **duplicated** into multiple tag groups
3. **Generate Files Per Tag** - Creates one controller file for each tag group
4. **Apply Templates** - Uses Mustache templates to generate code for each tag group independently

The duplication happens at **Step 2** in the core generator logic, before templates are even applied.

## Why This Happens

- OpenAPI Generator was originally designed for **client libraries**
- For client SDKs, having operations in multiple API classes (grouped by tag) is acceptable and useful for organization
- For **server-side code**, this creates a fundamental problem: you can't implement the same operation in multiple controllers

## Template Limitations

**Why custom templates can't fix this:**
- Templates receive already-grouped operations from the generator core
- By the time templates execute, operations have already been duplicated into multiple tag groups
- Templates process each tag group independently with no visibility into other tags
- No template variable or conditional can "merge" operations across tags

## OpenAPI Generator Upstream Issues

This is documented OpenAPI Generator behavior:
- **Issue #2844**: Multi-tag operations create duplicate code in server generators
- **Issue #11843**: Request to support single controller for multiple tags

**OpenAPI Generator team's stance:**
- Multi-tag support is primarily designed for client libraries
- Server-side duplication is a known limitation
- Post-processing is the recommended workaround

## Solution: Post-Processing ✅

**Approach:** Generate code normally, then merge duplicate controllers with a post-processing script.

See [TAG_DUPLICATION_SOLUTION.md](TAG_DUPLICATION_SOLUTION.md) for complete solution details.

### Quick Summary

```bash
# Step 1: Generate (creates duplicate controllers)
docker run ... openapitools/openapi-generator-cli generate ...

# Step 2: Merge duplicates into single DefaultController
php scripts/merge-controllers-simple.php \
  laravel-api/generated-v2/tictactoe/lib/Http/Controllers \
  laravel-api/generated-v2/tictactoe/lib/Http/Controllers/DefaultController.php
```

**Result:**
```
lib/Http/Controllers/
└── DefaultController.php     # All unique methods (no duplicates)
```

### Integration with Build Pipeline

The Makefile automatically runs post-processing:

```bash
make generate-tictactoe-v2  # Generates AND merges automatically
```

## Alternative Solutions (Not Recommended)

### ❌ Solution 1: Modify OpenAPI Spec

Remove multi-tag assignments, use only one tag per operation.

**Why not recommended:**
- Loses documentation organization capabilities
- Modifies source of truth
- Limits flexibility of API documentation

### ❌ Solution 2: Custom Java Generator

Extend OpenAPI Generator's Java codebase.

**Why not recommended:**
- Requires modifying core generator logic (complex)
- Maintenance burden (must track OpenAPI Generator updates)
- Fighting against framework's design patterns
- Java development environment and build complexity

## Recommendations

For **simple cases** (no need for multi-tag documentation):
- Use **one tag per operation** in the spec
- No post-processing needed

For **complex cases** (need multi-tag organization):
- Use **post-processing script** to merge controllers
- Keep spec as source of truth
- Automated via Makefile

## Related Resources

- **Complete analysis**: [TAG_DUPLICATION_SOLUTION.md](TAG_DUPLICATION_SOLUTION.md)
- **Solutions comparison**: [SOLUTIONS_COMPARISON.md](SOLUTIONS_COMPARISON.md)
- **Main documentation**: [CLAUDE.md](CLAUDE.md)
- **All known issues**: [KNOWN_ISSUES.md](KNOWN_ISSUES.md)
