<?php

namespace Kriptonic\Timing2Jira;

/**
 * Class Transformer
 *
 * Responsible for converting raw data into something more useful.
 *
 * @package Kriptonic\Timing2Jira
 */
class Transformer
{
    /**
     * Transform a collection of worklog details into Worklogs.
     *
     * @param array $rawWorklogs The raw worklog details.
     * @param string $issueKey The issue key these worklogs relate to.
     * @return array|Worklog[] The created Worklog objects.
     */
    public function transformWorklogs(array $rawWorklogs, string $issueKey): array
    {
        return array_map(function (array $rawWorklog) use ($issueKey) {
            return $this->transformWorklog($rawWorklog, $issueKey);
        }, $rawWorklogs['worklogs']);
    }

    /**
     * Transform the details for a worklog into a Worklog object.
     *
     * @param array $rawWorklog The raw worklog details.
     * @param string $issueKey The issue key this worklog relates to.
     * @return Worklog The created Worklog object.
     */
    public function transformWorklog(array $rawWorklog, string $issueKey): Worklog
    {
        return new Worklog(
            $issueKey,
            $rawWorklog['started'],
            $rawWorklog['timeSpentSeconds']
        );
    }
}
