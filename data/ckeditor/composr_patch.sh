#!/bin/bash

echo "Deleting old files"
rm -rf build-config.js LICENSE.md ckeditor.js plugins skins contents.css styles.js index.html vendor lang

echo 'Making sure data/ckeditor/plugins/{composr|spellchecktoggle|showcomcodeblocks} is preserved'
git checkout -- plugins/composr
git checkout -- plugins/spellchecktoggle
git checkout -- plugins/showcomcodeblocks

echo 'Use builder with our existing build-config.js to create downloadable package, the Optimized version - https://ckeditor.com/cke4/builder'
echo 'Extract'
read -p "Press enter to resume ..."

echo 'Manually download and place moono and moonocolor skins which we like to also bundle, place in skins/'
echo 'https://ckeditor.com/cke4/addon/moonocolor'
echo 'https://ckeditor.com/cke4/addon/moono'
echo 'Extract'
read -p "Press enter to resume ..."

echo "Removing unneeded files"
rm -rf CHANGES.md samples config.js adapters

echo "Converting line endings"
find . -name "*.js" -exec dos2unix {} \;
find . -name "*.css" -exec dos2unix {} \;
find . -name "*.html" -exec dos2unix {} \;
find . -name "*.txt" -exec dos2unix {} \;
find . -name "*.md" -exec dos2unix {} \;

echo "Adding new files to Git"
git add .

echo 'Manually apply the obvious new lines that were put in the old plugins/table/dialogs/table.js'
echo 'https://gitlab.com/composr-foundation/composr/-/blob/v11/data/ckeditor/plugins/table/dialogs/table.js'
read -p "Press enter to resume ..."

echo "Also remember to update the core_form_interfaces addon_registry hook with the new files"
cd ../..
find data/ckeditor -type f -exec echo "            '{}'," \;
read -p "Press enter to resume ..."

echo "Done!"
