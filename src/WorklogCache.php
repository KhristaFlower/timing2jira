<?php

namespace Kriptonic\Timing2Jira;

/**
 * Class WorklogCache
 *
 * Used to keep track of worklogs that exist on Jira.
 *
 * @package Kriptonic\Timing2Jira
 */
class WorklogCache
{
    /**
     * @var array A Worklog cache.
     */
    private $cache = [];

    /**
     * Clear the current cache.
     */
    public function clear(): void
    {
        $this->cache = [];
    }

    /**
     * Check to see if the provided $cacheKey exists.
     *
     * @param string $cacheKey The cache key to check.
     * @return bool True if the key has been set; false otherwise.
     */
    public function isCacheKeySet(string $cacheKey): bool
    {
        return array_key_exists($cacheKey, $this->cache);
    }

    /**
     * Set a collection of Worklogs at the specified $issueKey.
     *
     * @param string $issueKey The issue key to store worklogs against.
     * @param array|Worklog[] $worklogs The worklogs to store.
     */
    public function set(string $issueKey, array $worklogs): void
    {
        // Filter out any items that might not be worklog objects.
        $actualWorklogs = array_filter($worklogs, function ($worklog) {
            return $worklog instanceof Worklog;
        });

        $this->cache[$issueKey] = $actualWorklogs;
    }

    /**
     * Check to see if the provided $worklog exists in the cache.
     *
     * @param Worklog $worklog A worklog to search for.
     * @return bool True if the worklog exists already; false otherwise.
     */
    public function isUploadedAlready(Worklog $worklog): bool
    {
        if (!array_key_exists($worklog->getIssueKey(), $this->cache)) {
            return false;
        }

        foreach ($this->cache[$worklog->getIssueKey()] as $cachedWorklog) {
            if ($worklog->matches($cachedWorklog)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Add a worklog to the cache.
     *
     * @param Worklog $worklog The worklog to add to the cache.
     */
    public function addWorklog(Worklog $worklog): void
    {
        $this->cache[$worklog->getIssueKey()][] = $worklog;
    }

    /**
     * Add an array of worklogs to the cache.
     *
     * @param array $worklogs The array of worklogs to add.
     */
    public function addWorklogs(array $worklogs): void
    {
        foreach ($worklogs as $worklog) {
            // Ensure we can't add non-worklog entries.
            if ($worklog instanceof Worklog) {
                $this->addWorklog($worklog);
            }
        }
    }
}
