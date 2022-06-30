<?php
declare(strict_types = 1);

$config = [];

if (version_compare(PHP_VERSION, '8.1') < 0) {
    $config['parameters']['excludePaths'] = [
        'analyseAndScan' => [
            'src/Objects/Schema/Generation/Attributes/',
            'src/Objects/Schema/Generation/AttributeReader.php'
        ],
    ];
}

return $config;
