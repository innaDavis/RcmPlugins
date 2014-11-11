<?php

namespace RcmJira;

use RcmJira\Exception\JiraListenerException;
use RcmJira\Exception\JiraLoggerException;
use Zend\Log\Logger;
use Zend\Log\LoggerInterface;


/**
 * Class JiraLogger
 *
 * LongDescHere
 *
 * PHP version 5
 *
 * @category  Reliv
 * @package   RcmJira
 * @author    James Jervis <jjervis@relivinc.com>
 * @copyright 2014 Reliv International
 * @license   License.txt New BSD License
 * @version   Release: <package_version>
 * @link      https://github.com/reliv
 */
class JiraLogger implements LoggerInterface
{
    /**
     * @var array $priorities
     */
    protected $priorities
        = array(
            Logger::EMERG => 'EMERG',
            Logger::ALERT => 'ALERT',
            Logger::CRIT => 'CRIT',
            Logger::ERR => 'ERR',
            Logger::WARN => 'WARN',
            Logger::NOTICE => 'NOTICE',
            Logger::INFO => 'INFO',
            Logger::DEBUG => 'DEBUG',
        );

    /**
     * array(
     * 'endpoint' => 'https://jira.example.com',
     * 'username' => 'myUsername',
     * 'password' => 'myPassword',
     * 'projectKey' => 'REF',
     * 'issueType' => 1, // Bug
     * 'enterIssueIfNotStatus' => array(
     *   'closed',
     *   'resolved',
     *  ),
     * ),
     *
     * @var array $options
     */
    protected $options = array();

    /**
     * @var \chobie\Jira\Api|null $api
     */
    protected $api = null;

    /**
     * @param \chobie\Jira\Api $api
     * @param array            $jiraOptions
     */
    public function __construct(\chobie\Jira\Api $api, $jiraOptions = array())
    {
        $this->api = $api;
        $this->options = $jiraOptions;
    }

    /**
     * getOption
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return null
     */
    protected function getOption($key, $default = null)
    {
        if (isset($this->options[$key])) {

            return $this->options[$key];
        }

        return $default;
    }

    /**
     * @param string            $message
     * @param array|Traversable $extra
     *
     * @return LoggerInterface
     */
    public function emerg($message, $extra = array())
    {
        $this->log(Logger::EMERG, $message, $extra);
    }

    /**
     * @param string            $message
     * @param array|Traversable $extra
     *
     * @return LoggerInterface
     */
    public function alert($message, $extra = array())
    {
        $this->log(Logger::ALERT, $message, $extra);
    }

    /**
     * @param string            $message
     * @param array|Traversable $extra
     *
     * @return LoggerInterface
     */
    public function crit($message, $extra = array())
    {
        $this->log(Logger::CRIT, $message, $extra);
    }

    /**
     * @param string            $message
     * @param array|Traversable $extra
     *
     * @return LoggerInterface
     */
    public function err($message, $extra = array())
    {
        $this->log(Logger::ERR, $message, $extra);
    }

    /**
     * @param string            $message
     * @param array|Traversable $extra
     *
     * @return LoggerInterface
     */
    public function warn($message, $extra = array())
    {
        $this->log(Logger::WARN, $message, $extra);
    }

    /**
     * @param string            $message
     * @param array|Traversable $extra
     *
     * @return LoggerInterface
     */
    public function notice($message, $extra = array())
    {
        $this->log(Logger::NOTICE, $message, $extra);
    }

    /**
     * @param string            $message
     * @param array|Traversable $extra
     *
     * @return LoggerInterface
     */
    public function info($message, $extra = array())
    {
        $this->log(Logger::INFO, $message, $extra);
    }

    /**
     * @param string            $message
     * @param array|Traversable $extra
     *
     * @return LoggerInterface
     */
    public function debug($message, $extra = array())
    {
        $this->log(Logger::DEBUG, $message, $extra);
    }

    /**
     * Add a message as a log entry
     *
     * @param  int               $priority
     * @param  mixed             $message
     * @param  array|Traversable $extra
     *
     * @return Logger
     */
    public function log($priority, $message, $extra = array())
    {
        $summary = $this->prepareSummary($this->getPriorityString($priority) . ': ' . $message);

        $existingIssueKey = $this->getIssueKey($summary);

        if ($existingIssueKey) {
            // Add comment
            $this->addComment($existingIssueKey, $summary, $extra);

        } else {
            // create issue
            $this->createIssue($summary, $extra);
        }

        return $this;
    }

    /**
     * getPriorityString
     *
     * @param $priority
     *
     * @return string
     */
    public function getPriorityString($priority)
    {
        if (isset($this->priorities[$priority])) {
            $priorityString = $this->priorities[$priority];
        } else {
            $priorityString = $this->priorities[Logger::INFO];
        }
        return $priorityString;
    }

    /**
     * getPriorityFromErrorNumber
     *
     * @param $errno
     *
     * @return int
     */
    public function getPriorityFromErrorNumber($errno)
    {
        if (isset(Logger::$errorPriorityMap[$errno])) {
            $priority = Logger::$errorPriorityMap[$errno];
        } else {
            $priority = Logger::INFO;
        }
        return $priority;
    }

    /**
     * getApi
     *
     * @return \chobie\Jira\Api|null
     */
    protected function getApi()
    {
        return $this->api;
    }

    /**
     * getIssueKey
     *
     * @param $summary
     *
     * @return null
     * @throws JiraLoggerException
     */
    protected function getIssueKey($summary)
    {
        $jql = $this->getIssueJql($summary);

        $result = $this->getApi()->search($jql, 0, 1);

        if ($this->hasApiError($result)) {

            $message = 'An error occured while talking to JIRA (search): ' .
                implode(' ', $result->getResult()['errorMessages']) . ' '.
                implode(' ', $result->getResult()['errors']);

            throw new JiraLoggerException($message);
        }

        if ($result->getIssuesCount() > 0) {

            $issue = $result->getIssues()[0];
            return $issue->getKey();
        }

        return null;
    }

    /**
     * getIssueJql
     *
     * @param $summary
     *
     * @return string
     * @throws JiraListenerException
     */
    protected function getIssueJql($summary)
    {
        $closedJql = $this->getStatusQuery();

        $projectsJql = $this->getProjectQuery();

        if(empty($projectsJql)){
            throw new JiraListenerException(
                'No project key has been defined, JQL not valid.'
            );
        }

        $jql = $projectsJql .
        $closedJql .
        'AND (Summary ~ "\"' . $this->jqlEscape($summary). '\"") ' .
        "ORDER BY createdDate DESC";

        return $jql;
    }

    /**
     * getProjectQuery
     *
     * @return string
     */
    protected function getProjectQuery()
    {
        $projectKey = $this->getOption('projectKey', 'REF');

        if(empty($projectKey)){

            return '';
        }

        $projects = $this->getOption('projectsToCheckForIssues', null);

        $jql = "project = '" . $this->jqlEscape($projectKey) . "' ";

        if(empty($projects)){

            return $jql;
        }

        $jqlArr = array();

        foreach ($projects as $project) {

            $jqlArr[] = "project = '" . $this->jqlEscape($project) . "'";
        }

        $jql = "(" . $jql ." OR " . implode(' OR ', $jqlArr) . ") ";

        return $jql;
    }

    /**
     * getStatusQuery
     *
     * @return string
     */
    protected function getStatusQuery()
    {
        $statuses = $this->getOption('enterIssueIfNotStatus', null);

        if(empty($statuses)){

            return '';
        }

        foreach ($statuses as &$status) {

            $status = "status != '" . $this->jqlEscape($status) . "'";
        }

        $closedJql = 'AND (' . implode(' and ', $statuses) . ') ';

        return $closedJql;
    }

    /**
     * addComment
     *
     * @param string $issueKey
     * @param string $summary
     * @param array  $extra
     *
     * @return void
     * @throws JiraLoggerException
     */
    protected function addComment($issueKey, $summary, $extra = array())
    {
        $result =  $this->getApi()->addComment($issueKey, 'Error occured again: ' . $summary);

        if ($this->hasApiError($result)) {

            $message = 'An error occured while talking to JIRA (addComment): ' .
                implode(' ', $result->getResult()['errorMessages']) . ' '.
                implode(' ', $result->getResult()['errors']);

            throw new JiraLoggerException($message);
        }
    }

    /**
     * createIssue
     *
     * @param string $summary
     * @param array  $extra
     *
     * @return void
     * @throws JiraLoggerException
     */
    protected function createIssue($summary, $extra = array())
    {
        $options = array(
            "description" => $this->getDescription($extra)
        );

        $projectKey = $this->getOption('projectKey', 'REF');

        $issueType = (int) $this->getOption('issueType', 1);

        $result = $this->getApi()->createIssue(
            $projectKey,
            $summary,
            $issueType,
            $options
        );

        if ($this->hasApiError($result)) {

            $message = 'An error occured while talking to JIRA (createIssue): ' .
                implode(' ', $result->getResult()['errorMessages']) . ' '.
                implode(' ', $result->getResult()['errors']);

            throw new JiraLoggerException($message);
        }
    }

    /**
     * getDescription
     *
     * @param array $extra
     *
     * @return void
     */
    protected function getDescription($extra = array()) {

        $description = '';

        if(isset($extra['description'])){
            $description .= $extra['description'];
        }

        if(isset($_SERVER['REQUEST_URI'])){
            $description .= "\n URL: " . $_SERVER['REQUEST_URI'];
        }

        if(isset($extra['file'])){
            $description .= "\n File: " . $extra['file'];
        }

        if(isset($extra['line'])){
            $description .= "\n Line: " . $extra['line'];
        }

        if(isset($extra['message'])){
            $description .= "\n Message: " . $extra['message'];
        }

        if(isset($extra['trace'])){
            $description .= "\n Stack trace: \n" . $extra['trace'];
        }

        return $description;
    }

    /**
     * jqlEscape
     *
     * @param $param
     *
     * @return string
     */
    protected function jqlEscape($param)
    {
        return addslashes($param);
    }

    protected function prepareSummary($summary){

        $summary = substr($summary, 0, 255);

        $summary = str_replace(array("\r", "\n"), '', $summary);

        return $summary;
    }

    /**
     * hasApiError
     *
     * @param $result
     *
     * @return bool
     */
    protected function hasApiError($result)
    {
        $apiResult = $result->getResult();

        if (isset($apiResult['errors'])) {
            return true;
        }

        return false;
    }
} 