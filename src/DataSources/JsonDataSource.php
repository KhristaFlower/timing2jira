<?php

namespace Kriptonic\Timing2Jira\DataSources;

use Kriptonic\Timing2Jira\Worklog;
use Kriptonic\Timing2Jira\DataSource;

/**
 * Class JsonDataSource
 *
 * Load worklogs from a JSON string data source.
 *
 * @package Kriptonic\Timing2Jira\DataSources
 */
class JsonDataSource implements DataSource
{
    /**
     * @var array A list of Worklogs created from the source.
     */
    private $worklogs = [];

    /**
     * JsonDataSource constructor.
     *
     * @param string $jsonData The string of JSON data.
     */
    public function __construct(string $jsonData)
    {
        $data = json_decode($jsonData, true);

        foreach ($data as $datum) {
            // Attempt to find the issue key.
            preg_match('/^([A-Z]{1,5}\-\d{1,5}) (.+)$/', $datum['taskActivityTitle'], $matches);

            if (!count($matches)) {
                // Skip this entry if we can't determine the Jira Issue.
                continue;
            }

            $worklog = new Worklog($matches[1], $datum['startDate'], $datum['duration']);
            $worklog->setDescription($matches[2]);

            $this->worklogs[] = $worklog;
        }
    }

    /**
     * Get the worklog objects.
     *
     * @return array|Worklog[] An array of worklogs created from the data source.
     */
    function getWorklogs(): array
    {
        return $this->worklogs;
    }
}
