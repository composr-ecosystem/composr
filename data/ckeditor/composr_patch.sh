#!/bin/bash

echo 'Note the build-config.js that can be used to download the latest CKEditor version';

echo 'Make sure data/ckeditor/plugins/{composr|spellchecktoggle|showcomcodeblocks} is preserved'

echo 'Manually download and place moono and moonocolor skins which we like to also bundle, place in skins/';

echo "Removing unneeded files"
rm -rf CHANGES.md samples config.js adapters

echo "Converting line endings (Git would do this anyway)"
find . -name "*.js" -exec dos2unix {} \;
find . -name "*.css" -exec dos2unix {} \;
find . -name "*.html" -exec dos2unix {} \;
find . -name "*.txt" -exec dos2unix {} \;
find . -name "*.md" -exec dos2unix {} \;

echo "Adding new files to Git"
git add .

echo "Also remember to update the core_form_interfaces addon_registry hook with the new files"
cd ../..
find data/ckeditor -type f -exec echo "            '{}'," \;

echo "Done!"
