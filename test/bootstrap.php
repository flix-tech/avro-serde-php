<?php

use Dotenv\Dotenv;
require __DIR__ . '/../vendor/autoload.php';

(new Dotenv(__DIR__ . '/..', '.env.dist'))->load();
