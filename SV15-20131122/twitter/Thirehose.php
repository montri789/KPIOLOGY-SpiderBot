<?php
/**
 * A class that makes it easy to connect to and consume the Twitter stream via the Streaming API.
 * 
 * Note: This is beta software - Please read the following carefully before using: 
 *  - http://code.google.com/p/phirehose/wiki/Introduction
 *  - http://apiwiki.twitter.com/Streaming-API-Documentation 
 * 
 * @author  Fenn Bailey <fenn.bailey@gmail.com>
 * @version 0.2.3 ($Id: Phirehose.php 27 2010-02-26 02:39:56Z fenn.bailey $)
 */
abstract class Thirehose
{
  
  /**
   * Class constants
   */
  const URL_BASE         = 'http://stream.trend.in.th:6080/stream/';
  const FORMAT_JSON      = 'json';
  const FORMAT_XML       = 'xml';
  const METHOD_SAMPLE  = 'sample';
  const METHOD_FIREHOSE  = 'firehose';
  const USER_AGENT       = 'Phirehose/0.2.3 +http://code.google.com/p/phirehose/';
  const FILTER_CHECK_MIN = 5;
  const FILTER_UPD_MIN   = 120;
  const TCP_BACKOFF      = 1;
  const TCP_BACKOFF_MAX  = 16;
  const HTTP_BACKOFF     = 10;
  const HTTP_BACKOFF_MAX = 240;
  const EARTH_RADIUS_KM  = 6371;
  
  
  /**
   * Member Attribs
   */
  protected $username;
  protected $password;
  protected $method;
  protected $format;
  protected $count;
  protected $followIds;
  protected $trackWords;
  protected $locationBoxes;
  protected $conn;
  // State vars
  protected $filterChanged;
  protected $reconnect;
  protected $statusRate;
  protected $lastErrorNo;
  protected $lastErrorMsg;
  // Config type vars - override in subclass if desired
  protected $connectFailuresMax = 60; 
  protected $connectTimeout = 10;
  protected $readTimeout = 10;
  protected $idleReconnectTimeout = 120;
  protected $avgPeriod = 120;

  /**
   * Create a new Phirehose object attached to the appropriate twitter stream method. 
   * Methods are: METHOD_FIREHOSE, METHOD_RETWEET, METHOD_SAMPLE, METHOD_FILTER
   * Formats are: FORMAT_JSON, FORMAT_XML
   * @see Phirehose::METHOD_SAMPLE
   * @see Phirehose::FORMAT_JSON
   * 
   * @param string $username Any twitter username
   * @param string $password Any twitter password
   * @param string $method
   * @param string $format
   */
  public function __construct($username, $password, $method = Phirehose::METHOD_SAMPLE, $format = self::FORMAT_JSON)
  {
    $this->username = $username;
    $this->password = $password;
    $this->method = $method;
    $this->format = $format;
  }
  
  /**
   * Connects to the stream API and consumes the stream. Each status update in the stream will cause a call to the
   * handleStatus() method.
   * 
   * @see handleStatus()
   * @param boolean $reconnect Reconnects as per recommended   
   * @throws ErrorException
   */
  public function consume($reconnect = TRUE)
  {
    // Persist connection?
    $this->reconnect = $reconnect;
	$idlePeriod = $maxIdlePeriod = 0;
    
    // Loop indefinitely based on reconnect
    do {
      
      // (Re)connect
      $this->reconnect();
    
      // Init state
      $statusCount = $filterCheckCount = $enqueueSpent = $filterCheckSpent = $idlePeriod = $maxIdlePeriod = 0;
      $lastAverage = $lastFilterCheck = $lastFilterUpd = $lastStreamActivity = time();
      $buff = '';
      $fdr = array($this->conn);
      $fdw = $fde = NULL;
      
      // We use a blocking-select with timeout, to allow us to continue processing on idle streams
      while ($this->conn !== NULL && !feof($this->conn) && ($numChanged = stream_select($fdr, $fdw, $fde, $this->readTimeout)) !== FALSE) {
        /* Unfortunately, we need to do a safety check for dead twitter streams - This seems to be able to happen where
         * you end up with a valid connection, but NO tweets coming along the wire (or keep alives). The below guards
         * against this.
         */
        if ((time() - $lastStreamActivity) > $this->idleReconnectTimeout) {
          $this->log('Idle timeout: No stream activity for > ' . $this->idleReconnectTimeout . ' seconds. ' . 
           ' Reconnecting.');
          $this->reconnect();
          $fdr = array($this->conn); // Ugly, but required
          $lastStreamActivity = time();
          continue;
        }
        // Process stream/buffer
        $fdr = array($this->conn); // Must reassign for stream_select()
		stream_set_timeout($this->conn, 60);
        $buff .= fread($this->conn, 6); // Small non-blocking to get delimiter text
		
        if (($eol = strpos($buff, "\r\n")) === FALSE) {
          continue; // We need a newline
        }
        // Track maximum idle period
        //$idlePeriod = (time() - $lastStreamActivity);
        //$maxIdlePeriod = ($idlePeriod > $maxIdlePeriod) ? $idlePeriod : $maxIdlePeriod;
        // We got a newline, this is stream activity
        // Read status length delimiter
        $delimiter = substr($buff, 0, $eol);
        $buff = substr($buff, $eol + 2); // consume off buffer, + 2 = "\r\n"
        $statusLength = intval($delimiter);
        if ($statusLength > 0) {
          // Read status bytes and enqueue
          $bytesLeft = $statusLength - strlen($buff);
          while ($bytesLeft > 0 && $this->conn !== NULL && !feof($this->conn) && ($numChanged = stream_select($fdr, $fdw, $fde, 0, 20000)) !== FALSE) {
            $fdr = array($this->conn); // Reassign
            $buff .= fread($this->conn, $bytesLeft); // Read until all bytes are read into buffer
            $bytesLeft = ($statusLength - strlen($buff));
          }
          // Accrue/enqueue and track time spent enqueing
          $statusCount ++;
          //$enqueueStart = microtime(TRUE);
          $this->enqueueStatus($buff);
          //$enqueueSpent += (microtime(TRUE) - $enqueueStart);
        } else {
          // Timeout/no data after readTimeout seconds
          
        }
        // Calc counter averages 
        $lastStreamActivity = time();
        $avgElapsed = $lastStreamActivity - $lastAverage;
        if ($avgElapsed >= $this->avgPeriod) {
          // Calc tweets-per-second
          $this->statusRate = round($statusCount / $avgElapsed, 0);
          // Calc time spent per enqueue in ms
          $enqueueTimeMS = 0; // ($statusCount > 0) ? round($enqueueSpent / $statusCount * 1000, 2) : 0;
          // Calc time spent total in filter predicate checking
          $filterCheckTimeMS = 0; // ($filterCheckCount > 0) ? round($filterCheckSpent / $filterCheckCount * 1000, 2) : 0;
          $this->log(date('Y-m-d H:i:s').' Consume rate: ' . $this->statusRate . ' status/sec (' . $statusCount . ' total), avg ' . 
            'enqueueStatus(): ' . $enqueueTimeMS . 'ms, avg checkFilterPredicates(): ' . $filterCheckTimeMS . 'ms (' . 
            $filterCheckCount . ' total) over ' . $this->avgPeriod . ' seconds, max stream idle period: ' . 
              $maxIdlePeriod . ' seconds.');
          // Reset
          $statusCount = $filterCheckCount = $enqueueSpent = $filterCheckSpent = $idlePeriod = $maxIdlePeriod = 0;
          $lastAverage = $lastStreamActivity;
        }
        // Check if we're ready to check filter predicates
		/*
        if ($this->method == self::METHOD_FILTER && ($lastStreamActivity - $lastFilterCheck) >= self::FILTER_CHECK_MIN) {
          $filterCheckCount ++;
          $lastFilterCheck = time();
          $filterCheckStart = microtime(TRUE);
          $this->checkFilterPredicates(); // This should be implemented in subclass if required
          $filterCheckSpent +=  (microtime(TRUE) - $filterCheckStart);
        }
		*/
        // Check if filter is ready + allowed to be updated (reconnect)
		/*
        if ($this->filterChanged == TRUE && (time() - $lastFilterUpd) >= self::FILTER_UPD_MIN) {
          $this->log('Updating filter predicates (reconnecting).');
          $this->reconnect();
          $fdr = array($this->conn); // Ugly, but required
          $lastFilterUpd = time();
        }
		*/
        
      } // End while-stream-activity

      // Some sort of socket error has occured
      $this->lastErrorNo = is_resource($this->conn) ? @socket_last_error($this->conn) : NULL;
      $this->lastErrorMsg = ($this->lastErrorNo > 0) ? @socket_strerror($this->lastErrorNo) : 'Socket disconnected';
      $this->log('Phirehose connection error occured: ' . $this->lastErrorMsg);
      
      // Reconnect 
    } while ($this->reconnect);

    // Exit
    $this->log('Exiting.');
    
  }
  
  /**
   * Returns the last error message (TCP or HTTP) that occured with the streaming API or client. State is cleared upon
   * successful reconnect
   * @return string
   */
  public function getLastErrorMsg()
  {
    return $this->lastErrorMsg;
  }
  
  /**
   * Returns the last error number that occured with the streaming API or client. Numbers correspond to either the 
   * fsockopen() error states (in the case of TCP errors) or HTTP error codes from Twitter (in the case of HTTP errors).
   * 
   * State is cleared upon successful reconnect.
   * 
   * @return string
   */
  public function getLastErrorNo()
  {
    return $this->lastErrorNo;
  }
  
  
  /**
   * Connects to the stream URL using the configured method.
   * @throws ErrorException
   */
  protected function connect() 
  {

    // Init state
    $connectFailures = 0;
    $tcpRetry = self::TCP_BACKOFF / 2;
    $httpRetry = self::HTTP_BACKOFF / 2;

    // Keep trying until connected (or max connect failures exceeded)
    do {
      
      // Construct URL/HTTP bits
      $url = self::URL_BASE . $this->method . '.' . $this->format;
      $urlParts = parse_url($url);
      $authCredentials = base64_encode($this->username . ':' . $this->password);

      // Debugging is useful
      $this->log('Connecting to twitter stream: ' . $url );
      
      /**
       * Open socket connection to make POST request. It'd be nice to use stream_context_create with the native 
       * HTTP transport but it hides/abstracts too many required bits (like HTTP error responses).
       */
      $errNo = $errStr = NULL;
      $scheme = ($urlParts['scheme'] == 'https') ? 'ssl://' : 'tcp://';
      if (!empty($urlParts['port']))
      	$streamPort = $urlParts['port'];
      elseif ($urlParts['scheme'] == 'https')
      	$streamPort = 443;
      else
      	$streamPort = 80;

      /**
       * We must perform manual host resolution here as Twitter's IP regularly rotates (ie: DNS TTL of 60 seconds) and 
       * PHP appears to cache it the result if in a long running process (as per Phirehose).
       */
      $streamIPs = gethostbynamel($urlParts['host']);
      if (count($streamIPs) == 0) {
        throw new ErrorException("Unable to resolve hostname: '" . $urlParts['host'] . '"');
      }
      
      // Choose one randomly (if more than one)
      $this->log('Resolved host ' . $urlParts['host'] . ' to ' . implode(', ', $streamIPs));
      $streamIP = $streamIPs[rand(0, (count($streamIPs) - 1))];
      $this->log('Connecting to ' . $streamIP);
      
      $this->conn = fsockopen($scheme . $streamIP, $streamPort, $errNo, $errStr, $this->connectTimeout);

      // No go - handle errors/backoff
      if (!$this->conn || !is_resource($this->conn)) {
        $this->lastErrorMsg = $errStr;
        $this->lastErrorNo = $errNo;
        $connectFailures ++;
        if ($connectFailures > $this->connectFailuresMax) {
          $msg = 'TCP failure limit exceeded with ' . $connectFailures . ' failures. Last error: ' . $errStr;
          $this->log($msg);
          throw new ErrorException($msg, $errNo); // Throw an exception for other code to handle
        }
        // Increase retry/backoff up to max
        $tcpRetry = ($tcpRetry < self::TCP_BACKOFF_MAX) ? $tcpRetry * 2 : self::TCP_BACKOFF_MAX;
        $this->log('TCP failure ' . $connectFailures . ' of ' . $this->connectFailuresMax . ' connecting to stream: ' .
          $errStr . ' (' . $errNo . '). Sleeping for ' . $tcpRetry . ' seconds.');
        sleep($tcpRetry);
        continue;
      }
      
      // TCP connect OK, clear last error (if present)
      $this->log('Connection established to ('.$scheme . $streamIP.') ' . $streamIP);
      $this->lastErrorMsg = NULL;
      $this->lastErrorNo = NULL;
      
      // If we have a socket connection, we can attempt a HTTP request - Ensure blocking read for the moment
      stream_set_blocking($this->conn, 1);
  
      // Do it
      fwrite($this->conn, "GET " . $urlParts['path'] . " HTTP/1.0\r\n");
      fwrite($this->conn, "Host: " . $urlParts['host'] . "\r\n");
      fwrite($this->conn, "Accept: */*\r\n");
      fwrite($this->conn, 'Authorization: Basic ' . $authCredentials . "\r\n");
      fwrite($this->conn, 'User-Agent: ' . self::USER_AGENT . "\r\n");
      fwrite($this->conn, "\r\n");
      
      // First line is response
      list($httpVer, $httpCode, $httpMessage) = preg_split('/\s+/', trim(fgets($this->conn, 1024)), 3);
      
      // Response buffers
      $respHeaders = $respBody = '';

      // Consume each header response line until we get to body
      while ($hLine = trim(fgets($this->conn, 4096))) {
        $respHeaders .= $hLine;
      }
      
      // If we got a non-200 response, we need to backoff and retry
      if ($httpCode != 200) {
        $connectFailures ++;
        
        // Twitter will disconnect on error, but we want to consume the rest of the response body (which is useful)
        while ($bLine = trim(fgets($this->conn, 4096))) {
          $respBody .= $bLine;
        }
        
        // Construct error
        $errStr = 'HTTP ERROR ' . $httpCode . ': ' . $httpMessage . ' (' . $respBody . ')'; 
        
        // Set last error state
        $this->lastErrorMsg = $errStr;
        $this->lastErrorNo = $httpCode;
        
        // Have we exceeded maximum failures?
        if ($connectFailures > $this->connectFailuresMax) {
          $msg = 'Connection failure limit exceeded with ' . $connectFailures . ' failures. Last error: ' . $errStr;
          $this->log($msg);
          throw new ErrorException($msg); // We eventually throw an exception for other code to handle          
        }
        // Increase retry/backoff up to max
        $httpRetry = ($httpRetry < self::HTTP_BACKOFF_MAX) ? $httpRetry * 2 : self::HTTP_BACKOFF_MAX;
        $this->log('HTTP failure ' . $connectFailures . ' of ' . $this->connectFailuresMax . ' connecting to stream: ' . 
          $errStr . '. Sleeping for ' . $httpRetry . ' seconds.');
        sleep($httpRetry);
        continue;
        
      } // End if not http 200
      
      // Loop until connected OK
    } while (!is_resource($this->conn) || $httpCode != 200);
    
    // Connected OK, reset connect failures
    $connectFailures = 0;
    $this->lastErrorMsg = NULL;
    $this->lastErrorNo = NULL;
   $this->log('Connection Success :'.date('Y-m-d H:i:s').' Get tweet messages.');
 
    // Switch to non-blocking to consume the stream (important) 
    stream_set_blocking($this->conn, 0);
    
    // Connect always causes the filterChanged status to be cleared
    $this->filterChanged = FALSE;
    
  }
  
  /**
   * Method called as frequently as practical (every 5+ seconds) that is responsible for checking if filter predicates
   * (ie: track words or follow IDs) have changed. If they have, they should be set using the setTrack() and setFollow()
   * methods respectively within the overridden implementation. 
   * 
   * Note that even if predicates are changed every 5 seconds, an actual reconnect will not happen more frequently than
   * every 2 minutes (as per Twitter Streaming API documentation).
   * 
   * Note also that this method is called upon every connect attempt, so if your predicates are causing connection 
   * errors, they should be checked here and corrected.
   * 
   * This should be implemented/overridden in any subclass implementing the FILTER method.
   *
   * @see setTrack()
   * @see setFollow()
   * @see Phirehose::METHOD_FILTER
   */
  protected function checkFilterPredicates()
  {
    // Override in subclass
  }
  
  /**
   * Basic log function that outputs logging to the standard error_log() handler. This should generally be overridden
   * to suit the application environment.
   *
   * @see error_log()
   * @param string $messages
   */
  protected function log($message)
  {
		if (defined('ERRORFILE'))
			error_log('Phirehose: ' . $message."\n", 3, ERRORFILE);
		else
			@error_log('Phirehose: ' . $message, 0);
  }

  /**
   * Performs forcible disconnect from stream (if connected) and cleanup.
   */
  protected function disconnect()
  {
    if (is_resource($this->conn)) {
      $this->log('Closing Phirehose connection.');
      fclose($this->conn);
    }
    $this->conn = NULL;
    $this->reconnect = FALSE;
  }
  
  /**
   * Reconnects as quickly as possible. Should be called whenever a reconnect is required rather that connect/disconnect
   * to preserve streams reconnect state
   */
  private function reconnect()
  {
    $reconnect = $this->reconnect;
    $this->disconnect(); // Implicitly sets reconnect to FALSE
    $this->reconnect = $reconnect; // Restore state to prev
    $this->connect(); 
  }
  
  /**
   * This is the one and only method that must be implemented additionally. As per the streaming API documentation,
   * statuses should NOT be processed within the same process that is performing collection 
   *
   * @param string $status
   */
  abstract public function enqueueStatus($status);
  
} // End of class
