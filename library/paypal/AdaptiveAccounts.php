<?php
/****************************************************
AdaptiveAccounts.php  
This file contains client business methods to call
PayPals AdaptiveAccounts Webservice APIs.    
****************************************************/
require_once 'Config/paypal_sdk_clientproperties.php' ;
require_once 'CallerServices.php'  ;
require_once 'Stub/AA/AdaptiveAccountsProxy.php'  ;
require_once 'SOAPEncoder/SOAPEncoder.php'  ;
require_once 'XMLEncoder/XMLEncoder.php'  ;
require_once 'JSONEncoder/JSONEncoder.php'  ;
require_once 'Exceptions/FatalException.php'  ;

class AdaptiveAccounts extends CallerServices {     

   function CreateAccount($createAccountRequest, $isRequestString = false) {
   		try {
   			if($isRequestString) {
   				return parent::callWebService($createAccountRequest, 'AdaptiveAccounts/CreateAccount');
   			}
   			else {
   				return $this->callAPI($createAccountRequest, 'AdaptiveAccounts/CreateAccount');	
   			}
   		}
   		catch(Exception $ex) {
				  			
   			throw new FatalException('Error occurred in CreateAccount method');
   		}
   } 		
   function AddBankAccount($FSRequest, $isRequestString = false) {
   		try {
   			if($isRequestString) {
   				return parent::callWebService($FSRequest, 'AdaptiveAccounts/AddBankAccount');
   			}
   			else {
   				return $this->callAPI($FSRequest, 'AdaptiveAccounts/AddBankAccount');	
   			}
   		}
   		catch(Exception $ex) {
				  			
   			throw new FatalException('Error occurred in AddBankAccount method');
   		}
   }
   
 function AddPaymentCard($FSRequest, $isRequestString = false) {
   		try {
   			if($isRequestString) {
   				return parent::callWebService($FSRequest, 'AdaptiveAccounts/AddPaymentCard');
   			}
   			else {
   				return $this->callAPI($FSRequest, 'AdaptiveAccounts/AddPaymentCard');	
   			}
   		}
   		catch(Exception $ex) {
				  			
   			throw new FatalException('Error occurred in AddPaymentCard method');
   		}
   }
   
function SetFundingSourceConfirmed($FSRequest, $isRequestString = false) {
   		try {
   			if($isRequestString) {
   				return parent::callWebService($FSRequest, 'AdaptiveAccounts/SetFundingSourceConfirmed');
   			}
   			else {
   				return $this->callAPI($FSRequest, 'AdaptiveAccounts/SetFundingSourceConfirmed');	
   			}
   		}
   		catch(Exception $ex) {
				  			
   			throw new FatalException('Error occurred in SetFundingSourceConfirmed method');
   		}
   }  
   
   function SetFundingSourcePrimary($FSRequest, $isRequestString = false) {
   		try {
   			if($isRequestString) {
   				return parent::callWebService($FSRequest, 'AdaptiveAccounts/SetFundingSourcePrimary');
   			}
   			else {
   				return $this->callAPI($FSRequest, 'AdaptiveAccounts/SetFundingSourcePrimary');	
   			}
   		}
   		catch(Exception $ex) {
				  			
   			throw new FatalException('Error occurred in SetFundingSourceConfirm method');
   		}
   }  
  function GetVerifiedStatus ($VstatusRequest, $isRequestString = false) {
   		try {
   			if($isRequestString) {
   				return parent::callWebService($VstatusRequest, 'AdaptiveAccounts/GetVerifiedStatus');
   			}
   			else {
   				return $this->callAPI($VstatusRequest, 'AdaptiveAccounts/GetVerifiedStatus');	
   			}
   		}
   		catch(Exception $ex) {
				  			
   			throw new FatalException('Error occurred in GetVerifiedStatus method');
   		}
   }
   /*
    * Calls the call method of CallerServices class and returns the response.
    */
   private function callAPI($request, $URL)
   {
   $response = null;
		$isError = false;
		$reqObject = $request;
   		try {
			
   			switch(X_PAYPAL_REQUEST_DATA_FORMAT) {
   				case "JSON" :
   						$request = JSONEncoder::Encode($request);
   						$response = parent::callWebService($request, $URL);
   					break;
   				case "SOAP11" :
   						$request = SoapEncoder::Encode($request);
   						$response = parent::call($request, $URL);
   					break;
   				case "XML" :
   						$request = XMLEncoder::Encode($request);
   						$response = parent::callWebService($request, $URL);
   						
   					break;
   				
   			}
   			if((X_PAYPAL_RESPONSE_DATA_FORMAT == 'XML')||(X_PAYPAL_RESPONSE_DATA_FORMAT == 'JSON'))
   			{
   			switch(X_PAYPAL_RESPONSE_DATA_FORMAT) {
   				case "JSON" :
   						$strObjName = get_class($reqObject);
        				$strObjName = str_replace('Request', 'Response', $strObjName);
        				$response = JSONEncoder::Decode($response,$isError, $strObjName); 
   					break;
   				case "XML" :
   						$response = XMLEncoder::Decode($response, $isError);
   					break;
   				
   			}  			
			
   			$this->result='Success';
	   		$this->isSuccess = 'Success' ;
	        if($isError)
	        {
	        	$this->isSuccess = 'Failure' ;
	        	$this->setLastError($response) ;
	        	$response = null ;
	        }
   		  }
   		}
   		catch(Exception $ex) {
   			throw new FatalException('Error occurred in callAPI method');
   		}
        return $response;
   }
                              
}
?>