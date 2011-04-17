<?php
require_once dirname(__FILE__) . '/../Serializer/Serializer.php';
require_once dirname(__FILE__) . '/../Serializer/Unserializer.php';
//require_once "Serializer.php";
//require_once "Unserializer.php";

/*
 * SOAP Encoder encodes/decodes the object into/from SOAP string
 * Methods Encode and Decode
 */
class SoapEncoder
{
	
	private static $SOAPFault = "FAULT";
	private static $SOAPBody = "BODY";
	private static $SOAPFaultMessage = "FAULTMESSAGE";

	/*
	 * Encodes the request object into SOAP String
	 */
	public static function Encode($requestObject,$SerializeOption=null)
	{	
		$soap = "";
		
		try
		{
			$writer = new XMLWriter();
			
			$writer->openMemory();
			$writer->startDocument();
			
			$writer->setIndent(4);
			
			$writer->startElement("soap:Envelope");
			$writer->writeAttribute("xmlns:xsi","http://www.w3.org/2001/XMLSchema-instance");
			$writer->writeAttribute("xmlns:xsd","http://www.w3.org/2001/XMLSchema");
			$writer->writeAttribute("xmlns:soap","http://schemas.xmlsoap.org/soap/envelope/");
			$writer->startElement("soap:Body");
							
			$options = array(
			                    XML_SERIALIZER_OPTION_INDENT      => '    ',
			                    XML_SERIALIZER_OPTION_LINEBREAKS  => "\n",
			                    XML_SERIALIZER_OPTION_DEFAULT_TAG => '',
			                    XML_SERIALIZER_OPTION_TYPEHINTS   => false, 
			                    XML_SERIALIZER_OPTION_IGNORE_NULL => true, 
			                    XML_SERIALIZER_OPTION_CLASSNAME_AS_TAGNAME => true,
			                );
			 if(isset($SerializeOption))  
			  $options[XML_SERIALIZER_OPTION_MODE] = $SerializeOption;        
			$serializer = new XML_Serializer($options);
			
			$result = $serializer->serialize($requestObject);
			
			if( $result === true ) {
			    $xml = $serializer->getSerializedData();
				$xml = str_replace('<>','',$xml);
				$xml = str_replace('</>','',$xml);       
			}
			
			$writer->writeRaw($xml);
			$writer->endElement();
			$writer->endElement();
			
			$writer->endDocument();
			$soap = $writer->flush(); 
			
			$soap = str_replace("<?xml version=\"1.0\"?>", "",$soap);
			 
		}	
		catch(Exception $ex)
		{
			throw new Exception("Error occurred while Soap encoding");
		}
		
		return $soap;
	}
	
	/*
	 * Decodes back to object from given SOAP String response
	 */
	public static function Decode($SOAPResponse, &$isSOAPFault)
	{	
		$responseXML = "" ;
		
		try
		{
			if(empty($SOAPResponse))
				throw new Exception("Given Response is not a valid SOAP response.");
			
			$xmlDoc = new XMLReader();
			$res = $xmlDoc->XML($SOAPResponse);
			
			if($res)
			{
			
				while(trim(strtoupper($xmlDoc->localName)) != self::$SOAPBody)
				{
					$isNotEnd = $xmlDoc->read();
					if(!$isNotEnd)
						break;
							
				}
				
				if(!$isNotEnd)
				{
					$isSOAPFault = true;
					$soapFault = new FaultMessage();
					$errorData = new ErrorData();
					$errorData->errorId = 'Given Response is not a valid SOAP response.';
  					$errorData->message = 'Given Response is not a valid SOAP response.';
  					$soapFault->error = $errorData;

  					return $soapFault;
				}
								
				$responseXML = $xmlDoc->readInnerXml();
						
							
				$xmlDOM = new DOMDocument();
				$xmlDOM->loadXML($responseXML);
						
				$count = 0;
				$xmlDoc->read();
				$isSOAPFault = (trim(strtoupper($xmlDoc->localName)) == self::$SOAPFault) ;
				
				if($isSOAPFault)
				{
					while(trim(strtoupper($xmlDoc->localName)) != self::$SOAPFaultMessage)
					{
						$isNotEnd = $xmlDoc->read();
						if(!$isNotEnd)
							break;
					}
					$xmlDOM->loadXML($xmlDoc->readOuterXml());
				}
				
				switch ($xmlDoc->nodeType) 
				{
	            	case XMLReader::ELEMENT:
	            		$nodeName = $xmlDoc->localName;
	            		$prefix = $xmlDoc->prefix;
	            		
						if(class_exists($nodeName))
	            		{
	            			$xmlNodes = $xmlDOM->getElementsByTagName($nodeName);
	            			foreach($xmlNodes as $xmlNode)
	            			{
	            				//$xmlNode->prefix = "";
	            				$xmlNode->setAttribute("_class",$nodeName);
								$xmlNode->setAttribute("_type","object");
	            			}	
	            		}
	            		break;
	            } 
				
				$responseXML = $xmlDOM->saveXML();
									
				$unserializer = new XML_Unserializer();
				
				$unserializer->setOption(XML_UNSERIALIZER_OPTION_COMPLEXTYPE, 'object');
				
				
				$res = $unserializer->unserialize($responseXML, false);
				
				if($res)
				{
					$responseXML = $unserializer->getUnserializedData();
				}
				
				$xmlDoc->close();
			}
			else
			{
				throw new Exception("Given Response is not a valid SOAP response.");
			}
			
		}
		catch(Exception $ex)
		{
			throw new Exception("Error occurred while Soap decoding");
		}
		
		return $responseXML;
	}
}

?>