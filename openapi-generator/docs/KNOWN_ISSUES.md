# Known Issues and Limitations

This document provides a quick reference to all known issues. Each issue has a detailed dedicated file.

## Issues Summary

### 1. Monolithic API Interface File

**Problem:** Generated `DefaultApiInterface.php` can be 1000+ lines for complex APIs.

**Status:** Not fixable with templates or post-processing; fundamental generator design.

**Recommendation:** Keep as-is; works correctly despite size.

**Details:** [ISSUE_MONOLITHIC_API_INTERFACE.md](ISSUE_MONOLITHIC_API_INTERFACE.md)

---

### 2. PSR-4 Compliance Errors in Models

**Problem:** Model classes show PSR-4 warnings due to duplicate namespace.

**Impact:** Cosmetic only; models not used in current architecture.

**Status:** Template bug in OpenAPI Generator.

**Recommendation:** Ignore warnings; create custom templates if models needed.

**Details:** [ISSUE_PSR4_COMPLIANCE.md](ISSUE_PSR4_COMPLIANCE.md)

---

### 3. Empty README Template Causes Errors

**Problem:** Generation fails if `README.mustache` is empty.

**Solution:** Ensure all template files have content.

**Prevention:** Never leave templates empty; add minimal content.

**Details:** [ISSUE_EMPTY_README_TEMPLATE.md](ISSUE_EMPTY_README_TEMPLATE.md)

---

### 4. Tag Duplication in Controllers

**Problem:** Operations with multiple tags create duplicate methods across controllers.

**Solution:** ✅ Post-processing script merges duplicates (automated in Makefile).

**Alternative:** Use one tag per operation (simpler but less flexible).

**Details:** [ISSUE_TAG_DUPLICATION.md](ISSUE_TAG_DUPLICATION.md)

**Complete analysis:** [TAG_DUPLICATION_SOLUTION.md](TAG_DUPLICATION_SOLUTION.md)

---

## Issue Categories

### Generator Limitations
- Monolithic API Interface File
- Tag Duplication

### Template Bugs
- PSR-4 Compliance Errors in Models
- Empty README Template

### Solutions Implemented
- ✅ Tag Duplication: Post-processing merger
- ✅ Empty Template: Documentation and examples

### No Solution Needed
- Monolithic file: Works fine as-is
- PSR-4 models: Models not used

## Related Documentation

- [TROUBLESHOOTING.md](TROUBLESHOOTING.md) - Common runtime issues and fixes
- [CLAUDE.md](CLAUDE.md) - Main documentation
- [TAG_DUPLICATION_SOLUTION.md](TAG_DUPLICATION_SOLUTION.md) - Complete tag duplication analysis
- [SOLUTIONS_COMPARISON.md](SOLUTIONS_COMPARISON.md) - V1 vs V2 comparison
