<?php
/****************************************************
AdaptivePayments.php

This file contains client business methods to call
PayPals AdaptivePayments Webservice APIs.

****************************************************/
require_once 'Config/paypal_sdk_clientproperties.php' ;
require_once 'CallerServices.php'  ;
require_once 'Stub/AP/AdaptivePaymentsProxy.php'  ;
require_once 'SOAPEncoder/SOAPEncoder.php'  ;
require_once 'XMLEncoder/XMLEncoder.php'  ;
require_once 'JSONEncoder/JSONEncoder.php'  ;
require_once 'Exceptions/FatalException.php'  ;

class AdaptivePayments extends CallerServices {
   
   function Pay($payRequest, $isRequestString = false) {
   		try {
   			if($isRequestString) {
   				return parent::callWebService($payRequest, 'AdaptivePayments/Pay');
   			}
   			else {
   				return $this->callAPI($payRequest, 'AdaptivePayments/Pay');	
   			}
   				
   		}
   		catch(Exception $ex) {
				  			
   			throw new FatalException('Error occurred in Pay method');
   		}
   		
   }

   
   
   function SetPaymentOptions($SetPaymentOptionsRequest, $isRequestString = false) {
   		try {
   			if($isRequestString) {
   				return parent::callWebService($SetPaymentOptionsRequest, 'AdaptivePayments/SetPaymentOptions');
   			}
   			else {
   				return $this->callAPI($SetPaymentOptionsRequest, 'AdaptivePayments/SetPaymentOptions');	
   			}
   				
   		}
   		catch(Exception $ex) {
				  			
   			throw new FatalException('Error occurred in Pay method');
   		}
   		
   }

   function PaymentDetails($paymentDetailsRequest, $isRequestString = false) {
   		try {
   			if($isRequestString) {
   				return parent::callWebService($paymentDetailsRequest, 'AdaptivePayments/PaymentDetails');
   			}
   			else {
   				return $this->callAPI($paymentDetailsRequest, 'AdaptivePayments/PaymentDetails');	
   			}
   		}
   		catch(Exception $ex) {
				  			
   			throw new FatalException('Error occurred in PaymentDetails method');
   		}
   }	
   function ExecutePayment($executePaymentRequest, $isRequestString = false) {
   		try {
   			if($isRequestString) {
   				return parent::callWebService($executePaymentRequest, 'AdaptivePayments/ExecutePayment');
   			}
   			else {
   				return $this->callAPI($executePaymentRequest, 'AdaptivePayments/ExecutePayment');	
   			}
   		}
   		catch(Exception $ex) {
				  			
   			throw new FatalException('Error occurred in PaymentDetails method');
   		}
   		
   		
   }
   function GetPaymentOptions($getPaymentOptionsRequest, $isRequestString = false) {
   		try {
   			if($isRequestString) {
   				return parent::callWebService($getPaymentOptionsRequest, 'AdaptivePayments/GetPaymentOptions');
   			}
   			else {
   				return $this->callAPI($getPaymentOptionsRequest, 'AdaptivePayments/GetPaymentOptions');	
   			}
   		}
   		catch(Exception $ex) {
				  			
   			throw new FatalException('Error occurred in PaymentDetails method');
   		}
   		
   		
   }
   function Preapproval($preapprovalRequest, $isRequestString = false) {
   		try {
   			
   			if($isRequestString) {
   				return parent::callWebService($preapprovalRequest, 'AdaptivePayments/Preapproval');
   			}
   			else {
   				return $this->callAPI($preapprovalRequest, 'AdaptivePayments/Preapproval');	
   			}
   			
   				
   		}
   		catch(Exception $ex) {
				  			
   			throw new FatalException('Error occurred in Preapproval method');
   		}      
      	   
   }
   function PreapprovalDetails($preapprovalDetailsRequest, $isRequestString = false) {
   		try {
   			if($isRequestString) {
   				return parent::callWebService($preapprovalDetailsRequest, 'AdaptivePayments/PreapprovalDetails');
   			}
   			else {
   				return $this->callAPI($preapprovalDetailsRequest, 'AdaptivePayments/PreapprovalDetails');	
   			}
   				
   		}
   		catch(Exception $ex) {
				  			
   			throw new FatalException('Error occurred in PreapprovalDetails method');
   		}
   		
   }
   function CancelPreapproval($cancelPreapprovalRequest, $isRequestString = false)
   {	
   		try {
   			if($isRequestString) {
   				return parent::callWebService($cancelPreapprovalRequest, 'AdaptivePayments/CancelPreapproval');
   			}
   			else {
   				return $this->callAPI($cancelPreapprovalRequest, 'AdaptivePayments/CancelPreapproval');	
   			}
   		}
   		catch(Exception $ex) {
				  			
   			throw new FatalException('Error occurred in CancelPreapproval method');
   		}
 	  	
   }
   function Refund($refundRequest, $isRequestString = false) {
   		try {
   			if($isRequestString) {
   				return parent::callWebService($refundRequest, 'AdaptivePayments/Refund');
   			}
   			else {
   				return $this->callAPI($refundRequest, 'AdaptivePayments/Refund');	
   			}
   			
   		}
   		catch(Exception $ex) {
				  			
   			throw new FatalException('Error occurred in Refund method');
   		}
   		   		     
   }
   function ConvertCurrency($convertCurrencyRequest,$SerializeOption=null, $isRequestString = false)
   {
   		try {
   			if($isRequestString) {
   				return parent::callWebService($convertCurrencyRequest, 'AdaptivePayments/ConvertCurrency');
   			}
   			else {
   				return $this->callAPI($convertCurrencyRequest, 'AdaptivePayments/ConvertCurrency',$SerializeOption);	
   			}
   			
   				
   		}
   		catch(Exception $ex) {
				  			
   			throw new FatalException('Error occurred in ConvertCurrency method');
   		}
   		
   		
   }

   /*
    * Calls the call method of CallerServices class and returns the response.
    */
   private function callAPI($request, $URL,$SerializeOption=null)
   {
   		$response = null;
		$isError = false;
		$reqObject = $request;
   		try {
			
   		
   			switch(X_PAYPAL_REQUEST_DATA_FORMAT) {
   				case "JSON" :
   						$request = JSONEncoder::Encode($request);
   						$response = parent::callWebService($request, $URL, '');
   					break;
   				case "SOAP11" :
   						$request = SoapEncoder::Encode($request,$SerializeOption);
   						$response = parent::call($request, $URL);
   					break;
   				case "XML" :
   						$request = XMLEncoder::Encode($request,$SerializeOption);
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
			
   			
	      	 if($isError)
	     	  {
	        	$this->isSuccess = 'Failure' ;
	     		$this->setLastError($response) ;
	        	$response = null ;
	     	  }
	      	  else
	      	  {
	   			$this->isSuccess = 'Success' ;
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