#!/bin/bash

printf "${COLOR_DEFAULT}Remove unnecessary plugins\n"
wp plugin delete hello
wp plugin delete akismet

if ! wp plugin is-installed glotpress; then
  printf "${COLOR_DEFAULT}Install Glotpress\n"
  wp plugin install glotpress --activate-network
fi

printf "${COLOR_DEFAULT}Activate GP Machine Translate\n"
if [ "$INSTALLER" == 'single-test' ]; then
    wp plugin install ../dist/gp-machine-translate.zip --activate-network
else
    wp plugin activate gp-machine-translate --network
fi
printf "${COLOR_CYAN}Install preset done${COLOR_DEFAULT}\n"
