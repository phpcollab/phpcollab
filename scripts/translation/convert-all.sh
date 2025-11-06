#!/bin/bash
#
# Batch convert all phpCollab language files to .po format
#

cd "$(dirname "$0")/../.."

echo "=========================================="
echo "  Batch Language Conversion to .po Files"
echo "=========================================="
echo ""

# All supported languages
languages=(ar az pt-br bg ca zh zh-tw cs-iso cs-win1250 da nl en et fr de hu is in it ja ko lv no pl pt ro ru sk-win1250 es tr uk)

total=${#languages[@]}
current=0

echo "Converting ${total} languages..."
echo ""

for lang in "${languages[@]}"; do
    current=$((current + 1))
    echo "[${current}/${total}] Converting ${lang}..."

    # Convert main language file
    if [ -f "languages/lang_${lang}.php" ]; then
        php scripts/translation/convert-to-po.php \
            --input="languages/lang_${lang}.php" \
            --output="translations/messages/messages.${lang}.po" \
            --type=lang
        echo "  ✓ messages.${lang}.po"
    else
        echo "  ✗ lang_${lang}.php not found"
    fi

    # Convert help file
    if [ -f "languages/help_${lang}.php" ]; then
        php scripts/translation/convert-to-po.php \
            --input="languages/help_${lang}.php" \
            --output="translations/help/help.${lang}.po" \
            --type=help
        echo "  ✓ help.${lang}.po"
    else
        echo "  ✗ help_${lang}.php not found"
    fi

    # Convert custom file (if exists)
    if [ -f "languages/custom_${lang}.php" ]; then
        php scripts/translation/convert-to-po.php \
            --input="languages/custom_${lang}.php" \
            --output="translations/custom/custom.${lang}.po" \
            --type=custom
        echo "  ✓ custom.${lang}.po"
    fi

    echo ""
done

echo "=========================================="
echo "  Conversion Complete!"
echo "=========================================="
echo ""
echo "Summary:"
echo "  Messages files: $(ls translations/messages/*.po 2>/dev/null | wc -l)"
echo "  Help files: $(ls translations/help/*.po 2>/dev/null | wc -l)"
echo "  Custom files: $(ls translations/custom/*.po 2>/dev/null | wc -l)"
echo ""
