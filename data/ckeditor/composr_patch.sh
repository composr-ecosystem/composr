#!/bin/bash

echo 'Use builder with our existing build-config.js to create downloadable package, the Optimized version';

echo 'Make sure data/ckeditor/plugins/{composr|imagepaste|spellchecktoggle|showcomcodeblocks} is preserved'

echo 'Manually download and place moono and moonocolor skins which we like to also bundle, place in skins/';

echo 'Manually download and extract codemirror, dialogadvtab and emojione plugins';

echo 'Manually apply the obvious new lines that were put in the old plugins/table/dialogs/table.js';

echo 'Manually replace "http://" with (window.location.protocol+"//") in link.js and ckeditor.js';

echo "Removing unneeded files"
rm -rf CHANGES.md samples _source ckeditor.pack config.js adapters

echo "Converting line endings"
find . -name "*.js" -exec dos2unix {} \;
find . -name "*.css" -exec dos2unix {} \;
find . -name "*.html" -exec dos2unix {} \;
find . -name "*.txt" -exec dos2unix {} \;
find . -name "*.md" -exec dos2unix {} \;
dos2unix .htaccess

echo "Adding byte order marks (need https://code.google.com/archive/p/utf-bom-utils/downloads)"
find . -name "*.js" -exec bom_add {} \;

echo "Done!"
