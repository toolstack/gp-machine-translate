#!/bin/bash
# Needed for .htaccess generation that works only on single sites
cp /var/www/install/config/wp-cli.yml  /var/www/html/
# Change owner and permission
chown www-data:www-data /var/www/html/wp-content
chown www-data:www-data /var/www/html/wp-content/plugins
chmod ug+wx /var/www/html/wp-content
chmod ug+wx /var/www/html/wp-content/plugins
# Install WordPress instances
/var/www/install/install.sh
# Start apache
apache2-foreground
