#!/bin/bash
#
# Remove OpenAPI Tags
#
# This script removes all tags from operations in an OpenAPI spec.
# Supports both JSON and YAML formats.
#
# Usage: ./scripts/remove-tags.sh <input-spec> <output-spec>
# Example: ./scripts/remove-tags.sh specs/api.yaml specs/api-no-tags.yaml

set -e

if [ $# -lt 2 ]; then
    echo "Usage: $0 <input-spec> <output-spec>"
    echo "Example: $0 specs/api.yaml specs/api-no-tags.yaml"
    exit 1
fi

INPUT_FILE="$1"
OUTPUT_FILE="$2"

if [ ! -f "$INPUT_FILE" ]; then
    echo "❌ Error: Input file does not exist: $INPUT_FILE"
    exit 1
fi

echo "📋 Reading OpenAPI spec: $INPUT_FILE"

# Detect file format by extension
INPUT_EXT="${INPUT_FILE##*.}"
OUTPUT_EXT="${OUTPUT_FILE##*.}"

# Convert to JSON if needed, remove tags, convert back if needed
if [[ "$INPUT_EXT" == "yaml" || "$INPUT_EXT" == "yml" ]]; then
    # YAML input
    if command -v yq &> /dev/null; then
        echo "   Using yq for YAML processing..."

        # Read YAML, remove tags from operations, output based on extension
        if [[ "$OUTPUT_EXT" == "yaml" || "$OUTPUT_EXT" == "yml" ]]; then
            # YAML to YAML
            yq eval 'del(.paths[][].tags)' "$INPUT_FILE" > "$OUTPUT_FILE"
        else
            # YAML to JSON
            yq eval 'del(.paths[][].tags)' -o=json "$INPUT_FILE" > "$OUTPUT_FILE"
        fi
    else
        echo "❌ Error: yq is not installed. Please install yq to process YAML files."
        echo "   Install: brew install yq (macOS) or see https://github.com/mikefarah/yq"
        exit 1
    fi
else
    # JSON input
    echo "   Using jq for JSON processing..."

    if [[ "$OUTPUT_EXT" == "yaml" || "$OUTPUT_EXT" == "yml" ]]; then
        # JSON to YAML
        if command -v yq &> /dev/null; then
            jq 'walk(if type == "object" and has("tags") and has("operationId") then del(.tags) else . end)' "$INPUT_FILE" | \
                yq eval -P - > "$OUTPUT_FILE"
        else
            echo "❌ Error: yq is required to convert JSON to YAML"
            exit 1
        fi
    else
        # JSON to JSON
        jq 'walk(if type == "object" and has("tags") and has("operationId") then del(.tags) else . end)' "$INPUT_FILE" > "$OUTPUT_FILE"
    fi
fi

echo "✅ Tags removed successfully!"
echo "📁 Output: $OUTPUT_FILE"
