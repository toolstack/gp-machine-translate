#!/bin/bash

##############################
# SETUP VARS
##############################
PLUGIN_FOLDER_NAME="gp-machine-translate"
PLUGIN_PACKAGE_NAME="gp-machine-translate.zip"
DESTINATION_PATH="../dist"
SOURCE_PATH="../src"
BUILD_PATH=$(pwd)

# Excluded files and folders
RSYNC_EXCLUDE=(--exclude "node_modules" --exclude "resources" --exclude "vendor")

##############################
# BASIC VARS
##############################
COLOR_DEFAULT="\033[0m"

COLOR_RED="\033[1;31m"
COLOR_GREEN="\033[1;32m"
COLOR_CYAN="\033[1;96m"
############ END ############

if [ ! -d "${DESTINATION_PATH}" ] ; then
    mkdir "${DESTINATION_PATH}"
    if [ ! -d "${DESTINATION_PATH}" ] ; then
        printf "${COLOR_RED}Destination folder does not exist.${COLOR_DEFAULT}\n"
        exit 1;
    fi
fi

if [ ! -d "${SOURCE_PATH}" ] ; then
    printf "${COLOR_RED}Source folder does not exist.${COLOR_DEFAULT}\n"
    exit 1;
fi

# Empty destination folder
rm -rf ${DESTINATION_PATH:?}/*

# Create plugin folder
mkdir "${DESTINATION_PATH}/${PLUGIN_FOLDER_NAME}" || exit 1;

# Build parameters for rsync
RSYNC_PARAMS=(-aq "${SOURCE_PATH}/" "${DESTINATION_PATH}/${PLUGIN_FOLDER_NAME}")
RSYNC_PARAMS+=("${RSYNC_EXCLUDE[@]}")
# Copy all files to destination folder
rsync "${RSYNC_PARAMS[@]}"

# Go to dist folder
cd "${DESTINATION_PATH}/${PLUGIN_FOLDER_NAME}" || exit 1;

# Install npm
npm install --only=production --silent

# Install composer
composer install --no-dev --quiet

# Go back
cd "$BUILD_PATH" || exit 1;

# Remove config files
find ${DESTINATION_PATH}/${PLUGIN_FOLDER_NAME}/* -maxdepth 0 -name "*.js" -print0 | xargs -0 rm

# Remove all hidden files except .htaccess
find "${DESTINATION_PATH}/" -name ".*" \! -name ".htaccess" -print0 | xargs -0 rm -rf

# Remove all 0 byte files
find "${DESTINATION_PATH}/${PLUGIN_FOLDER_NAME}/" -name "*" -size 0 -print0 | xargs -0 rm

# Remove .DS_Store files
find "${DESTINATION_PATH}/${PLUGIN_FOLDER_NAME}/" -name ".DS_Store" -print0 | xargs -0 rm

# Remove .lock files
find "${DESTINATION_PATH}/${PLUGIN_FOLDER_NAME}/" -name "*.lock" -print0 | xargs -0 rm

# Remove .json files
find "${DESTINATION_PATH}/${PLUGIN_FOLDER_NAME}/" -name "*.json" -print0 | xargs -0 rm

# Remove .ts files
find "${DESTINATION_PATH}/${PLUGIN_FOLDER_NAME}/" -name "*.ts" -print0 | xargs -0 rm

# Remove empty folders (node_modules)
find "${DESTINATION_PATH}/${PLUGIN_FOLDER_NAME}/" -type d -empty -print0 | xargs -0 rm -d

# Build .zip file without __MACOSX folder
cd "${DESTINATION_PATH}/" || exit 1;
zip -r "${PLUGIN_PACKAGE_NAME}" "${PLUGIN_FOLDER_NAME}" -q -x ".*" -x "__MACOSX"

# Done.
printf "\n${COLOR_GREEN}Building ${COLOR_CYAN}${PLUGIN_PACKAGE_NAME}${COLOR_GREEN} package completed.${COLOR_DEFAULT}\n\n"

exit 0;
