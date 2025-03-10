#!/usr/bin/env bash

#####################################################################
#                                                                   #
#    Update internal database for                                   #
#    publishing to GitHub.                                          #
#                                                                   #
#    Used only by library maintainers.                              #
#    Usage: ./bin/release.sh [{build_dir}]                          #
#                                                                   #
#    build_dir: optional, dir where database and i18n messages      #
#               prepared, by default is "/tmp/iso-codes-build"      #
#                                                                   #
#####################################################################

CURRENT_DIR=$(dirname $(readlink -f $0))
PROJECT_DIR=$(dirname $CURRENT_DIR)

TMP_BUILD_DIR=${1:-/tmp/iso-codes-build}

# Prepare build dir
if [[ ! -d $TMP_BUILD_DIR ]]; then
    # if not exists, create
    mkdir -p $TMP_BUILD_DIR
fi

if [[ ! -w $TMP_BUILD_DIR ]]; then
    echo -e "[Release] Passed base directory \033[0;31m${TMP_BUILD_DIR}\033[0m is not writable"
    exit 1
fi

echo -e "[Release] Build directory is \033[0;31m${TMP_BUILD_DIR}\033[0m"
echo -e "[Release] Project directory is \033[0;31m${PROJECT_DIR}\033[0m"

# locate composer
cd ${PROJECT_DIR}
COMPOSER_PATH=`which composer`
if [[ $? -eq 1 ]]; then
    COMPOSER_PATH=`which composer.phar`
    if [[ $? -eq 1 ]]; then
        echo -e "[Release]  \033[0;31m\n\nCan not locate composer for testing build.\033[0m\n\n"
        exit 1
    fi
fi

# if required, install composer dependencies
if [[ ! -d ${PROJECT_DIR}/vendor ]]; then
    $COMPOSER_PATH install
fi

# update database
$PROJECT_DIR/vendor/sokil/php-isocodes/bin/update_iso_codes_db.sh db_only $PROJECT_DIR $TMP_BUILD_DIR
if [[ $? -ne 0 ]]; then
  echo "[Release]  Error when updating database"
  exit 1
fi

# test updated database and message files
echo -e "[Release]  \033[0;32mTest updated database\033[0m"
$COMPOSER_PATH test

# message on success test
if [[ $? -ne 0 ]]; then
  echo "[Release]  Unit tests failed. Release process stopped. Please, check test errors"
  exit 1
fi

# update version in README.md
echo -e "Logging of new database version to readme"

cd $TMP_BUILD_DIR
VERSION=`git describe --tags`
if [[ $? != 0 ]]; then
    echo -e "[Release] Can not detect database version"
    exit 1
fi

BUILD_DATE=`date "+%Y-%m-%d %H:%M"`
cd $PROJECT_DIR

sed -i -E "s/Database version: .*/Database version: ${VERSION} from ${BUILD_DATE}/" README.md

# success message
echo -e "\n\n"
echo -e "[Release] \033[0;32mDatabase successfully updated. Now you can verify difference, commit and add new release.\033[0m"
echo -e "[Release] \033[0;32mDatabase version: ${VERSION} from ${BUILD_DATE}.\033[0m\n\n"

