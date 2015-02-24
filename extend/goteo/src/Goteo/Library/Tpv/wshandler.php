<?php
/****************************************************
wshandler.php

This file contains methods to make calls to Ceca "webservice"

Called by /library/tpv.php

****************************************************/
require_once __DIR__ . '/../../../../../../src/Goteo/Library/paypal/stub.php'; // sí, uso el stub de paypal
require_once __DIR__ . '/../../../../../../src/Goteo/Library/paypal/log.php'; // sí, uso el log de paypal

class WSHandler {

	/*
	 * public variables
	 */



    /*
     * Error ID
     */
    public $error_id = '';

    /*
     * Error Message
     */
    public $error_message = '';

    /*
     * Result FAILURE or SUCCESS
     */
    public $isSuccess;

    /*
     * Last Error
     */
    private $LastError;


   	/*
   	 * Calls the actual WEB Service and returns the response.
   	 */
   	function callWebService($data, $url) {

		$response = null;

		try {

		    $response = tpvcall($data, $url);
		    $isFault = false;
			if(empty($response) || trim($response) == '')
	   		{
	   			$isFault = true;
				$fault = new FaultMessage();
				$errorData = new ErrorData();
				$errorData->errorId = 'API Error' ;
		  		$errorData->message = 'response is empty.' ;
		  		$fault->error = $errorData;

		  		$this->isSuccess = 'Failure' ;
		  		$this->LastError = $fault;
		        $response = null;

	   		}
	   		else
	   		{
		   		$isFault = false;

		   		$this->isSuccess = 'Success' ;
				if($isFault)
		        {
		        	$this->isSuccess = 'Failure' ;
		        	$this->LastError = $response ;
		        	$response = null ;
		        }
	        }
		}
		catch(Exception $ex) {
			return null;
		}
	   return $response;
	}

    /*
     * Returns Error ID
     */
    function getErrorId() {
		$errorId  = '';
		if($this->LastError != null) {

	     	if(is_array($this->LastError->error))
	        {
	        	$errorId  = $this->LastError->error[0]->errorId ;
	        }
	        else
	        {
	        	$errorId  = $this->LastError->error->errorId ;
	        }
		}
        return $errorId ;

    }

    /*
     * Returns Error Message
     */
    function getErrorMessage() {
    	$errorMessage = '';
    	if($this->LastError != null) {

    		if(is_array($this->LastError->error))
	        {
	        	$errorMessage = $this->LastError->error[0]->message ;
	        }
	        else
	        {
	        	$errorMessage = $this->LastError->error->message ;
	        }
    	}
        return $errorMessage ;

    }

    /*
     * Returns Last error
     */
	public function getLastError()
   	{
   		return $this->LastError;
   	}
/*
     * Sets the Last error
     */
	public function setLastError($error)
   	{
   		$this->LastError = $error;
   	}
}

/**
  * call: Function to perform the a call to sermepa webservice
  * @methodName is name of API  method.
  * @a is  String
  * $serviceName is String
  * returns an associtive array containing the response from the server.
*/

function tpvcall($data, $endpoint)
{
    //setting the curl parameters.
    $ch = curl_init();
    //For Debugging
//    curl_setopt($ch, CURLOPT_HEADER, true);
//    curl_setopt($ch, CURLINFO_HEADER_OUT, true);
//    curl_setopt($ch, CURLOPT_VERBOSE, true);
    curl_setopt($ch, CURLOPT_SSLVERSION, 4);
    curl_setopt($ch, CURLOPT_URL,$endpoint);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); //no se exactamente para que es, está en los ejemplos
    curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");

    // tiene que ser    application/x-www-form-urlencoded
    $the_data = array();
    foreach ($data as $key=>$value) {
        $the_data[] = $key.'='.$value;
    }
    curl_setopt($ch, CURLOPT_POSTFIELDS, implode('&', $the_data)); // datos clave=>valor del POST


    if(isset($_SESSION['curl_error_no'])) {
	    unset($_SESSION['curl_error_no']);
    }
    if(isset($_SESSION['curl_error_msg'])) {
	    unset($_SESSION['curl_error_msg']);
    }


    //getting response from server
    $response = curl_exec($ch);

    // LOGGER
    \Goteo\Library\Feed::logger('tpv call', 'invest', \substr($the_data['Num_operacion'], 0, -4), 'Data:'. implode(' ', $the_data).'<br /> Curl response: '.trim(htmlentities($response)), $endpoint);

//    curl_getinfo($ch);

    if (curl_errno($ch)) {
        @mail(\GOTEO_FAIL_MAIL,
            'Ha fallado el handler de tpv en ' . SITE_URL,
            'curl_error: ' . curl_errno($ch) . '<br />' . curl_error($ch) . '<hr /><pre>'.print_r($data, true).'</pre>');
        return null;
     } else {
         //closing the curl
            curl_close($ch);
      }

    return $response;
}
