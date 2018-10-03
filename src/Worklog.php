<?php

namespace Kriptonic\Timing2Jira;

/**
 * Class Worklog
 *
 * Details about a Worklog.
 *
 * @package Kriptonic\Timing2Jira
 */
class Worklog
{
    /**
     * @var string The Issue Key this Worklog links to.
     */
    private $issueKey;

    /**
     * @var string The Author name.
     */
    private $author;

    /**
     * @var string The Start Time.
     */
    private $startTime;

    /**
     * @var int The Duration in seconds.
     */
    private $duration;

    /**
     * @var string The Description for the Worklog.
     */
    private $description;

    /**
     * Worklog constructor.
     *
     * @param string $issueKey The Jira Issue for this worklog.
     * @param string $startTime The start time of the worklog.
     * @param int $duration The duration in seconds of the worklog.
     */
    public function __construct(string $issueKey, string $startTime, int $duration)
    {
        $this->issueKey = $issueKey;
        $this->startTime = $startTime;
        $this->duration = $duration;
    }

    /**
     * Check to see if this worklog matches another.
     *
     * @param Worklog $other The Worklog to compare against.
     * @return bool True if the worklogs match; false otherwise.
     */
    public function matches(Worklog $other): bool
    {
        return (
            $this->issueKey == $other->getIssueKey() &&
            strtotime($this->startTime) == strtotime($other->getStartTime())
        );
    }

    /**
     * Set the Issue Key.
     *
     * @param string $issueKey The Issue Key.
     * @return Worklog
     */
    public function setIssueKey(string $issueKey): Worklog
    {
        $this->issueKey = $issueKey;
        return $this;
    }

    /**
     * Set the issue Author.
     *
     * @param mixed $author The Author.
     * @return Worklog
     */
    public function setAuthor($author): Worklog
    {
        $this->author = $author;
        return $this;
    }

    /**
     * Set the Start Time.
     *
     * @param string $startTime The Start Time.
     * @return Worklog
     */
    public function setStartTime(string $startTime): Worklog
    {
        $this->startTime = $startTime;
        return $this;
    }

    /**
     * Set the Duration.
     *
     * @param int $duration The Duration in seconds.
     * @return Worklog
     */
    public function setDuration(int $duration): Worklog
    {
        $this->duration = $duration;
        return $this;
    }

    /**
     * Set the Description.
     *
     * @param mixed $description The Description.
     * @return Worklog
     */
    public function setDescription($description): Worklog
    {
        $this->description = $description;
        return $this;
    }

    /**
     * Get the Issue Key.
     *
     * @return string The Issue Key.
     */
    public function getIssueKey(): string
    {
        return $this->issueKey;
    }

    /**
     * Get the Author.
     *
     * @return string The Author.
     */
    public function getAuthor(): string
    {
        return $this->author;
    }

    /**
     * Get the Start Time.
     *
     * @return string The Start Time.
     */
    public function getStartTime(): string
    {
        return $this->startTime;
    }

    /**
     * Get the Duration in seconds.
     *
     * @return int The Duration.
     */
    public function getDuration(): int
    {
        return $this->duration;
    }

    /**
     * Get the Description.
     *
     * @return string The Description.
     */
    public function getDescription(): string
    {
        return $this->description;
    }
}
