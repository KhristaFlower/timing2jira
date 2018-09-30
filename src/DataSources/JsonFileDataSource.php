<?php

namespace Kriptonic\Timing2Jira\DataSources;

/**
 * Class JsonFileDataSource
 *
 * Load worklogs from a JSON File data source.
 *
 * @package Kriptonic\Timing2Jira\DataSources
 */
class JsonFileDataSource extends JsonDataSource
{
    /**
     * JsonFileDataSource constructor.
     *
     * @param string $jsonFilePath The path to a JSON file.
     */
    public function __construct(string $jsonFilePath)
    {
        $jsonData = file_get_contents($jsonFilePath);

        parent::__construct($jsonData);
    }
}
