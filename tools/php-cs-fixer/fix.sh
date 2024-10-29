#!/bin/sh

#
#  Copyright (c) 2022 Borlabs - Benjamin A. Bornschein. All rights reserved.
#  This file may not be redistributed in whole or significant part.
#  Content of this file is protected by international copyright laws.
#
#  ----------------- Borlabs Cookie IS NOT FREE SOFTWARE -----------------
#
#  @copyright Borlabs - Benjamin A. Bornschein, https://borlabs.io
#

TOOL_PATH="$(dirname "$(realpath "$0")")"

php -d memory_limit=2000M ${TOOL_PATH}/vendor/bin/php-cs-fixer fix --allow-risky=yes --config="${TOOL_PATH}/.php-cs-fixer.php"
