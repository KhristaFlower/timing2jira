<?php

namespace Kriptonic\Timing2Jira;

/**
 * Interface DataSource
 *
 * Used to interface with the Jira Worklog uploader.
 *
 * @package Kriptonic\Timing2Jira
 */
interface DataSource
{
    /**
     * Get the worklog objects.
     *
     * @return array|Worklog[]
     */
    function getWorklogs(): array;
}
