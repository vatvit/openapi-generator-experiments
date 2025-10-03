#!/bin/sh
#
# Post-process script for OpenAPI Generator
# Called by generator with filename as argument when --enable-post-process-file is used
#
# This script merges tag-based controller files into DefaultController.php
# Extracts methods from each file and deduplicates them
#

FILE="$1"

# Function to extract methods from a controller file (skip class declaration and braces)
extract_methods() {
    local file="$1"
    # Skip first line (class declaration) and last line (closing brace)
    # Keep everything in between
    awk '
        BEGIN { found_class = 0; body = "" }
        /^class .* extends Controller$/ { found_class = 1; next }
        found_class && /^}$/ { exit }
        found_class { print }
    ' "$file"
}

# Function to deduplicate methods in a file by function name
deduplicate_methods() {
    local file="$1"
    local temp=$(mktemp)

    awk '
        /^    public function / || /^    protected function / {
            # Extract function name
            match($0, /function ([a-zA-Z0-9_]+)\(/, arr)
            func_name = arr[1]

            # Skip if we have seen this function
            if (func_name in seen) {
                skip = 1
                next
            }
            seen[func_name] = 1
            skip = 0
        }

        !skip { print }

        # Reset skip when we reach end of function (closing brace at indent level 1)
        /^    }$/ { skip = 0 }
    ' "$file" > "$temp"

    mv "$temp" "$file"
}

# Process Controller files
case "$FILE" in
  */Http/Controllers/*Controller.php)
    BASENAME=$(basename "$FILE")
    DIRNAME=$(dirname "$FILE")
    TARGET="$DIRNAME/DefaultController.php"

    if [ "$BASENAME" != "DefaultController.php" ]; then
        echo "Processing controller: $BASENAME" >&2

        if [ -f "$TARGET" ]; then
            # DefaultController exists - merge methods
            echo "  Merging $BASENAME into DefaultController.php" >&2

            # Extract methods from current file
            TEMP_METHODS=$(mktemp)
            extract_methods "$FILE" > "$TEMP_METHODS"

            # Insert methods before the closing brace of DefaultController
            TEMP_OUTPUT=$(mktemp)
            awk -v methods_file="$TEMP_METHODS" '
                /^}$/ && !done {
                    while ((getline line < methods_file) > 0) {
                        print line
                    }
                    close(methods_file)
                    done = 1
                }
                { print }
            ' "$TARGET" > "$TEMP_OUTPUT"

            mv "$TEMP_OUTPUT" "$TARGET"
            rm "$TEMP_METHODS"
            rm "$FILE"

            # Deduplicate methods in the merged file
            deduplicate_methods "$TARGET"
        else
            # First controller - just rename
            echo "  Creating DefaultController.php from $BASENAME" >&2
            mv "$FILE" "$TARGET"
        fi
    fi
    ;;
esac

# Process ApiInterface files
case "$FILE" in
  */Api/*ApiInterface.php)
    BASENAME=$(basename "$FILE")
    DIRNAME=$(dirname "$FILE")
    TARGET="$DIRNAME/DefaultApiInterface.php"

    if [ "$BASENAME" != "DefaultApiInterface.php" ]; then
        echo "Processing interface: $BASENAME" >&2

        if [ -f "$TARGET" ]; then
            # DefaultApiInterface exists - merge methods
            echo "  Merging $BASENAME into DefaultApiInterface.php" >&2

            # Extract methods from current file (between interface declaration and closing brace)
            TEMP_METHODS=$(mktemp)
            awk '/^interface .* \{$/,/^}$/ {
                if (NR > 1 && !/^interface .* \{$/ && !/^}$/) {
                    print
                }
            }' "$FILE" > "$TEMP_METHODS"

            # Insert methods before the closing brace
            TEMP_OUTPUT=$(mktemp)
            awk -v methods_file="$TEMP_METHODS" '
                /^}$/ && !done {
                    while ((getline line < methods_file) > 0) {
                        print line
                    }
                    close(methods_file)
                    done = 1
                }
                { print }
            ' "$TARGET" > "$TEMP_OUTPUT"

            mv "$TEMP_OUTPUT" "$TARGET"
            rm "$TEMP_METHODS"
            rm "$FILE"
        else
            # First interface - just rename
            echo "  Creating DefaultApiInterface.php from $BASENAME" >&2
            mv "$FILE" "$TARGET"
        fi
    fi
    ;;
esac
