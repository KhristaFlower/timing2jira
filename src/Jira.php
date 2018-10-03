<?php

namespace Kriptonic\Timing2Jira;

use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

/**
 * Class Jira
 *
 * Used to handle interactions with Jira.
 *
 * @package Kriptonic\Timing2Jira
 */
class Jira
{
    /**
     * @var Client Guzzle Client
     */
    private $client;

    /**
     * @var array User credentials to send for authorization.
     */
    private $auth;

    /**
     * @var Transformer Used to transform request responses to models.
     */
    private $transformer;

    /**
     * @var WorklogCache Local storage of data from Jira to speed up requests.
     */
    private $cache;

    /**
     * Jira constructor.
     *
     * @param string $jiraUrl The Jira URL eg https://jira.example.com/
     */
    public function __construct(string $jiraUrl)
    {
        $this->client = new Client([
            'base_uri' => $jiraUrl . '/rest/api/2/'
        ]);

        $this->transformer = new Transformer();
        $this->cache = new WorklogCache();
    }

    /**
     * Set the credentials to use when sending requests to Jira.
     *
     * @param string $username Account username.
     * @param string $password Account password.
     */
    public function setCredentials(string $username, string $password): void
    {
        $this->auth = [$username, $password];
    }

    /**
     * Upload Worklogs provided by a DataSource.
     *
     * @param DataSource $dataSource The source of Worklogs.
     */
    public function uploadWorklogs(DataSource $dataSource): void
    {
        // Get the collection of worklogs that need to be uploaded.
        $newWorklogs = $dataSource->getWorklogs();

        foreach ($newWorklogs as $newWorklog) {
            $this->uploadWorklog($newWorklog);
        }
    }

    /**
     * Send the Worklog to Jira.
     *
     * @param Worklog $newWorklog The Worklog to upload.
     */
    public function uploadWorklog(Worklog $newWorklog): void
    {
        $issueKey = $newWorklog->getIssueKey();

        print "\nChecking {$newWorklog->getIssueKey()} {$newWorklog->getDescription()}.\n";

        if (!$this->cache->isCacheKeySet($issueKey)) {
            print "Retrieving worklogs for {$newWorklog->getIssueKey()}.\n";

            // Retrieve the worklogs from Jira.
            $existingWorklogs = $this->getWorklogs($issueKey);

            // Cache the worklogs to speed up future requests.
            $this->cache->set($issueKey, $existingWorklogs);
        }

        // Check to see if this worklog exists on Jira already.
        if ($this->cache->isUploadedAlready($newWorklog)) {
            print "Exists already; skipping.\n";
            return;
        }

        print "Uploading worklog.\n";

        // Send the new worklog to Jira.
        $requestData = [
            'comment' => $newWorklog->getDescription(),
            'started' => date('Y-m-d\TH:i:s.000O', strtotime($newWorklog->getStartTime())),
            'timeSpentSeconds' => $newWorklog->getDuration(),
        ];

        $this->request('POST', "issue/{$newWorklog->getIssueKey()}/worklog", $requestData);

        // Add this worklog to the cache so we can avoid uploading possible duplicates.
        $this->cache->addWorklog($newWorklog);
    }

    /**
     * Retrieve the Worklogs on a Jira Issue.
     *
     * @param string $issueKey The Jira Issue Key.
     * @return array|Worklog[] The worklogs on the issue.
     */
    public function getWorklogs(string $issueKey): array
    {
        $rawWorklogs = $this->request('GET', "issue/{$issueKey}/worklog");

        if (is_array($rawWorklogs) && array_key_exists('worklogs', $rawWorklogs)) {
            return $this->transformer->transformWorklogs($rawWorklogs, $issueKey);
        }

        return [];
    }

    /**
     * Update a worklog.
     *
     * @param string $issueKey The Jira Issue Key.
     * @param int $worklogId The Id of the worklog to update.
     * @param array $data The new data.
     */
    public function updateWorklog(string $issueKey, int $worklogId, array $data): void
    {
        $this->request('PUT', "issue/{$issueKey}/worklog/{$worklogId}", $data);
    }

    /**
     * Delete a worklog.
     *
     * @param string $issueKey The Jira Issue Key.
     * @param int $worklogId The Id of the worklog to delete.
     */
    public function deleteWorklog(string $issueKey, int $worklogId): void
    {
        $this->request('DELETE', "issue/{$issueKey}/worklog/{$worklogId}");
    }

    /**
     * Send a request and retrieve the JSON response.
     *
     * @param string $method The request method.
     * @param string $uri The request URI.
     * @param array $data Data to send as JSON in the request body.
     * @return array The JSON decoded body from the response.
     */
    private function request(string $method, string $uri, array $data = []): array
    {
        try {
            $request = new Request($method, $uri);

            $response = $this->client->send($request, [
                'auth' => $this->auth,
                'json' => $data
            ]);

            return json_decode($response->getBody(), true);
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            return [];
        }
    }
}
