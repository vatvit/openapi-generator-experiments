#!/bin/bash
#
# Set Operation-Specific Tags
#
# This script removes existing tags and sets each operation's tag to its operationId.
# This ensures one unique tag per operation, enabling per-operation file generation.
#
# Since each spec generates to its own directory with its own namespace,
# operationId uniqueness within the spec is sufficient - no global prefix needed.
#
# Usage: ./scripts/set-operation-tags.sh <input-spec> <output-spec>
# Example: ./scripts/set-operation-tags.sh petshop/petshop-extended.yaml petshop/processed.yaml
# Example: ./scripts/set-operation-tags.sh tictactoe/tictactoe.json tictactoe/processed.json

set -e

if [ $# -lt 2 ]; then
    echo "Usage: $0 <input-spec> <output-spec>"
    echo ""
    echo "Arguments:"
    echo "  input-spec   - Path to input OpenAPI specification file"
    echo "  output-spec  - Path to output file (with unique tags)"
    echo ""
    echo "The script extracts info.title and info.version from the spec"
    echo "to create unique tags in the format: {title}_{version}_{operationId}"
    echo ""
    echo "Example: $0 specs/api.yaml specs/api-tagged.yaml"
    exit 1
fi

INPUT_FILE="$1"
OUTPUT_FILE="$2"

if [ ! -f "$INPUT_FILE" ]; then
    echo "❌ Error: Input file does not exist: $INPUT_FILE"
    exit 1
fi

echo "📋 Processing OpenAPI spec: $INPUT_FILE"

# Detect file format by extension
INPUT_EXT="${INPUT_FILE##*.}"
OUTPUT_EXT="${OUTPUT_FILE##*.}"

# JQ filter to set tag to operationId (one tag per operation)
JQ_FILTER='
  walk(
    if type == "object" and has("operationId") then
      . + {
        "tags": [.operationId]
      }
    else
      .
    end
  )
'

# Process based on input/output format
if [[ "$INPUT_EXT" == "yaml" || "$INPUT_EXT" == "yml" ]]; then
    # YAML input
    if ! command -v yq &> /dev/null; then
        echo "❌ Error: yq is not installed. Please install yq to process YAML files."
        echo "   Install: brew install yq (macOS) or see https://github.com/mikefarah/yq"
        exit 1
    fi

    echo "   Using yq for YAML processing..."

    if [[ "$OUTPUT_EXT" == "yaml" || "$OUTPUT_EXT" == "yml" ]]; then
        # YAML to YAML
        yq eval -o=json '.' "$INPUT_FILE" | \
            jq "$JQ_FILTER" | \
            yq eval -P - > "$OUTPUT_FILE"
    else
        # YAML to JSON
        yq eval -o=json '.' "$INPUT_FILE" | \
            jq "$JQ_FILTER" > "$OUTPUT_FILE"
    fi
else
    # JSON input
    echo "   Using jq for JSON processing..."

    if [[ "$OUTPUT_EXT" == "yaml" || "$OUTPUT_EXT" == "yml" ]]; then
        # JSON to YAML
        if ! command -v yq &> /dev/null; then
            echo "❌ Error: yq is required to convert JSON to YAML"
            exit 1
        fi
        jq "$JQ_FILTER" "$INPUT_FILE" | \
            yq eval -P - > "$OUTPUT_FILE"
    else
        # JSON to JSON
        jq "$JQ_FILTER" "$INPUT_FILE" > "$OUTPUT_FILE"
    fi
fi

# Count how many operations were tagged (convert to JSON first if needed)
if [[ "$OUTPUT_EXT" == "yaml" || "$OUTPUT_EXT" == "yml" ]]; then
    OPERATION_COUNT=$(yq eval -o=json '.' "$OUTPUT_FILE" | jq '[.. | objects | select(has("operationId"))] | length' 2>/dev/null || echo "0")
else
    OPERATION_COUNT=$(jq '[.. | objects | select(has("operationId"))] | length' "$OUTPUT_FILE" 2>/dev/null || echo "0")
fi

echo "✅ Tags set successfully!"
echo "   Operations tagged: $OPERATION_COUNT"
echo "   Tag format: tags=[operationId]"
echo "📁 Output: $OUTPUT_FILE"
