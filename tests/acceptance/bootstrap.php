<?php
/*
 *  Copyright (c) 2021 Borlabs - Benjamin A. Bornschein. All rights reserved.
 *  This file may not be redistributed in whole or significant part.
 *  Content of this file is protected by international copyright laws.
 *
 *  ----------------- Borlabs Cookie IS NOT FREE SOFTWARE -----------------
 *
 *  @copyright Borlabs - Benjamin A. Bornschein, https://borlabs.io
 */

declare(strict_types=1);

namespace {

    // Load .env file and populate $_ENV
    $dotenv = Dotenv\Dotenv::createImmutable(realpath(__DIR__ . '/../../'));
    $dotenv->load();
}
