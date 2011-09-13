<?php
/****************************************************
wshandler.php

This file contains methods to make calls to Sermepa webservice

Called by /library/tpv.php

****************************************************/
require_once 'library/paypal/stub.php'; // sí, uso el stub de paypal
require_once 'library/paypal/log.php'; // sí, uso el log de paypal

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
   	function callWebService($request) {
		
		$response = null;
		
		try {
			
		    $response = tpvcall($request, TPV_WEBSERVICE_URL);
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
//   no tendria que dar un fault soap
//              $response = SoapEncoder::Decode($response, $isFault);
				if($isFault)
		        {
		        	$this->isSuccess = 'Failure' ;
		        	$this->LastError = $response ;
		        	$response = null ;
		        }
	        }
		}
		catch(Exception $ex) {
			die('Error occurred in call method');
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

function tpvcall($MsgStr, $endpoint)
{

    //setting the curl parameters.
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$endpoint);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); //no se exactamente para que es, está en los ejemplos
    /// pasamos el xml como 'entrada' con urlencoded
    curl_setopt($ch,CURLOPT_POSTFIELDS, 'entrada='. rawurlencode($MsgStr));
    

    //setting the MsgStr as POST FIELD to curl
    $conf = array('mode' => 0600, 'timeFormat' => '%X %x');
    $logger = &Log::singleton('file', 'logs/'.date('Ymd').'_invest.log', 'caller', $conf);

    $logger->log('##### TPV call '.date('d/m/Y').' #####');
    
    $logger->log("endpoint: $endpoint");
    $logger->log("request: $MsgStr");

    
    if(isset($_SESSION['curl_error_no'])) {
	    unset($_SESSION['curl_error_no']);
    }
    if(isset($_SESSION['curl_error_msg'])) {
	    unset($_SESSION['curl_error_msg']);
    }
    
   
    //getting response from server
    $response = curl_exec($ch);
    $logger->log("response: ".trim($response));
    $logger->log('##### END TPV call '.date('d/m/Y').' #####');
    $logger->close();
    
    if (curl_errno($ch)) {
        // moving to display page to display curl errors
        die('curl_error: ' . curl_errno($ch) . '<br />' . curl_error($ch));
     } else {
         //closing the curl
            curl_close($ch);
      }

    return $response;
}
?>