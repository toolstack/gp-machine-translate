#!/bin/bash
##############################
# BASIC VARS
##############################
INSTALL_PATH="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"
TARGET_PATH="$( cd "$( dirname $INSTALL_PATH )/html" && pwd )"

COLOR_DEFAULT="\033[0m"
COLOR_RED="\033[1;31m"
COLOR_GREEN="\033[1;32m"
COLOR_YELLOW="\033[1;93m"
COLOR_PURPLE="\033[1;35m"
COLOR_PINK="\033[1;95m"
COLOR_BLUE="\033[1;94m"
COLOR_CYAN="\033[1;96m"
############ END ############

# Check if the $INSTALLER variable is present and if not, cancel the operation
if [ -z $INSTALLER ]; then
    printf "${COLOR_RED}No \$INSTALLER configured!${COLOR_DEFAULT}\n"
    exit 0;
fi

# Set default variables for the installer
if [ "$INSTALLER" == 'single' ]; then
    dbname=$WORDPRESS_DATABASE_SINGLE
    dbname_wpunit="${WORDPRESS_DATABASE_SINGLE}_wpunit"
    url=$WORDPRESS_HOST_SINGLE
elif [ "$INSTALLER" == 'single-test' ]; then
    dbname=$WORDPRESS_DATABASE_SINGLE_TEST
    dbname_wpunit="${WORDPRESS_DATABASE_SINGLE_TEST}_wpunit"
    url=$WORDPRESS_HOST_SINGLE_TEST
elif [ "$INSTALLER" == 'multi' ]; then
    dbname=$WORDPRESS_DATABASE_MULTI
    dbname_wpunit="${WORDPRESS_DATABASE_MULTI}_wpunit"
    url=$WORDPRESS_HOST_MULTI
elif [ "$INSTALLER" == 'multi-test' ]; then
    dbname=$WORDPRESS_DATABASE_MULTI_TEST
    dbname_wpunit="${WORDPRESS_DATABASE_MULTI_TEST}_wpunit"
    url=$WORDPRESS_HOST_MULTI_TEST
fi

############ START OF DATABASE SETUP ############

# Wait until database container is available
echo -n "Waiting till MySQL service is available ..."
while ! (mysqladmin ping --host=${MYSQL_HOST} --port=${MYSQL_PORT} --user="${MYSQL_ROOT_USER}" --password="${MYSQL_ROOT_PASSWORD}" >/dev/null 2>&1); do
    echo -n '.'
    sleep 3
done
echo -e " ${COLOR_GREEN} done!${COLOR_DEFAULT}"
# Default database setup query
SQL=$(cat << EOF
    DROP DATABASE IF EXISTS $dbname_wpunit;
    CREATE DATABASE IF NOT EXISTS $dbname;
    CREATE DATABASE IF NOT EXISTS $dbname_wpunit;
    GRANT ALL PRIVILEGES ON $dbname.* TO '$MYSQL_USER'@'%';
    GRANT ALL PRIVILEGES ON $dbname_wpunit.* TO '$MYSQL_USER'@'%';
EOF
);
# Delete test database, if available
if [ "$INSTALLER" == 'single-test' ] || [ "$INSTALLER" == 'multi-test' ]; then
    SQL="DROP DATABASE IF EXISTS $dbname; ${SQL}";
fi

# Execute query
mysql --user="${MYSQL_ROOT_USER}" --password="${MYSQL_ROOT_PASSWORD}" --host=${MYSQL_HOST} --port=${MYSQL_PORT} --execute="${SQL}"

############ END OF DATABASE SETUP ############

# Check if target is writable and if not, cancel the operation
if [ ! -w "${TARGET_PATH}" ]; then
  printf "${COLOR_RED}No permission for: ${TARGET_PATH}${COLOR_DEFAULT}\n"
  exit 0
fi
# Go to target folder
cd ${TARGET_PATH}
# Start install routine
printf "${COLOR_DEFAULT}Setting up: ${INSTALLER}\n"
# Check if WordPress is downloaded
if [ ! -f "${TARGET_PATH}/wp-config-sample.php" ]; then
  printf "${COLOR_DEFAULT}Downloading WordPress${COLOR_CYAN}\n"
  wp core download
fi
# Create wp-config.php if missing
if [ ! -f "${TARGET_PATH}/wp-config.php" ]; then
    printf "${COLOR_DEFAULT}Creating wp-config.php${COLOR_CYAN}\n"
    wp config create \
        --dbname=$dbname \
        --dbuser=$MYSQL_USER \
        --dbpass=$MYSQL_PASSWORD \
        --dbhost=$MYSQL_HOST:$MYSQL_PORT \
        --dbprefix=$WORDPRESS_TABLE_PREFIX

    cat - ${INSTALL_PATH}/config/wp-config-prefix.php | cat - ${TARGET_PATH}/wp-config.php > ${INSTALL_PATH}/temp && mv ${INSTALL_PATH}/temp ${TARGET_PATH}/wp-config.php
fi
# Install WordPress if not installed
if ! wp core is-installed; then
    printf "${COLOR_DEFAULT}Installing WordPress in ${TARGET_PATH}\n"
    # Install WordPress
    wp core install \
        --url="https://${url}/" \
        --title="${INSTALLER}" \
        --admin_user="${WORDPRESS_ADMIN_USERNAME}" \
        --admin_password="${WORDPRESS_ADMIN_PASSWORD}" \
        --admin_email="${WORDPRESS_ADMIN_EMAIL}" \
        --skip-email
    # Setup permalink structure
    wp rewrite structure '/%postname%/'
    # This will generate the .htaccess file on single sites
    wp rewrite flush --hard
    # Special setup routine for multisite network
    if [ "$INSTALLER" == 'multi' ] || [ "$INSTALLER" == 'multi-test' ]; then
        printf "${COLOR_DEFAULT}Convert WordPress in a multisite network\n"
        # Convert single site into a multisite network
        wp core multisite-convert
        # Move prepared .htaccess file, because wp-cli cannot create the file for a network for no fucking reason.
        mv /var/www/install/config/multisite-htaccess  /var/www/html/.htaccess
        # Install language packs
        wp language core install de_DE
        wp language core install fr_FR
        # Create DE site
        wp site create \
            --slug="lang-de"
        wp site switch-language de_DE \
            --url=http://${url}/lang-de/
        wp rewrite structure '/%postname%/' \
            --url=http://${url}/lang-de/
        # Create FR site
        wp site create \
            --slug="lang-fr"
        wp site switch-language fr_FR \
            --url=http://${url}/lang-fr/
        wp rewrite structure '/%postname%/' \
            --url=http://${url}/lang-fr/
    fi
    # Fallback if no preset package is defined.
    if [ -z $PRESET ]; then
        PRESET="default-single"
    fi
    # Install preset package
    if [ -f "${INSTALL_PATH}/presets/${PRESET}.sh" ]; then
        printf "${COLOR_CYAN}Install preset: ${PRESET}${COLOR_DEFAULT}\n"
        source "${INSTALL_PATH}/presets/${PRESET}.sh"
    else
        printf "${COLOR_RED}Preset not available: ${PRESET}${COLOR_DEFAULT}\n"
    fi
    printf "${COLOR_CYAN}Installation complete: ${INSTALLER}${COLOR_DEFAULT}\n"
fi
printf "Create SQL dump for tests\n"
# Define SQL dump name
dump_filename="dump-${INSTALLER}.sql"
# Create SQL dump for WPUnit tests
mysqldump --opt --skip-lock-tables -h$MYSQL_HOST -P$MYSQL_PORT -u$MYSQL_USER -p$MYSQL_PASSWORD $dbname > ${dump_filename}
# Move dump
printf "Move SQL dump to test folder\n"
mv ${dump_filename} ${INSTALL_PATH}/../tests/_data/${dump_filename}
# Check if a SQL dump has been created by trying to move it.
if [ -f "${INSTALL_PATH}/../tests/_data/${dump_filename}" ]; then
    printf "${COLOR_CYAN}Moved SQL dump to test folder${COLOR_DEFAULT}\n"
else
    printf "${COLOR_RED}Could not move SQL dump to test folder${COLOR_DEFAULT}\n"
fi
# Instance is ready
printf "${COLOR_GREEN}Instance is ready: ${INSTALLER}${COLOR_DEFAULT}\n"
