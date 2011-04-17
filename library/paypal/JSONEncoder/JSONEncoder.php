<?php
require_once "JSON.php";

/*
 * JSON Encoder encodes/decodes the object into/from JSON string
 * Methods Encode and Decode
 */
class JSONEncoder
{
	private static $FAULT = 'FaultMessage';
	/*
	 * Encodes the request object into JSON String
	 */
	public static function Encode($requestObject)
	{	
		$JSON = "";
		
		try
		{
			$toEncode = array(
						get_class($requestObject) => $requestObject);
			$encoder = new Services_JSON();			
			$JSON = $encoder->encode($toEncode);
			$JSON = str_replace('"":', '', $JSON);
		}	
		catch(Exception $ex)
		{
			throw new Exception("Error occurred while JSON encoding");
		}
		return $JSON;
	}
	
	/*
	 * Decodes back to object from given JSON String response
	 */
	public static function Decode($JSONResponse, &$isFault, $objectName = '')
	{	
		$responseJSONObject = null;
		
		try
		{
			if(empty($JSONResponse))
				throw new Exception("Given Response is not a valid JSON response.");
				
			if(strlen($JSONResponse) != strlen(str_replace('"error":','',$JSONResponse))) {
				$isFault = true;
				$objectName = self::$FAULT;
				
			}else {
				$isFault = false;
			}
				
			$encoder = new Services_JSON();		
			$responseJSONObject = $encoder->decode($JSONResponse,$objectName);	
			
		}
		catch(Exception $ex)
		{
			throw new Exception("Error occurred while JSON decoding. " . $ex->getMessage());
		}
		
		return $responseJSONObject;
	}
}

?>