<?php

use Kriptonic\Timing2Jira\DataSources\JsonFileDataSource;
use Kriptonic\Timing2Jira\Jira;

require 'vendor/autoload.php';

if (!file_exists('config.php')) {
    print 'Missing config';
    exit;
}

$config = require 'config.php';

$jira = new Jira($config['jiraUrl']);
$jira->setCredentials(
    $config['credentials']['username'],
    $config['credentials']['password']
);

$worklogSource = new JsonFileDataSource('ExampleTimingExport.json');
$jira->uploadWorklogs($worklogSource);
