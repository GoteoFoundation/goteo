<?php

if (!class_exists("BaseAddress", false)) {
/**
 * BaseAddress
 */
class BaseAddress {
	/**
	 * @access public
	 * @var xsstring
	 */
	public $line1;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $line2;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $city;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $state;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $postalCode;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $countryCode;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $type;
}}

if (!class_exists("ClientDetailsType", false)) {
/**
 * ClientDetailsType
 */
class ClientDetailsType {
	/**
	 * @access public
	 * @var xsstring
	 */
	public $ipAddress;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $deviceId;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $applicationId;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $model;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $geoLocation;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $customerType;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $partnerName;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $customerId;
}}

if (!class_exists("currencyType", false)) {
/**
 * CurrencyType
 */
class currencyType {
	/**
	 * @access public
	 * @var xsstring
	 */
	public $code;
	/**
	 * @access public
	 * @var xsdecimal
	 */
	public $amount;
}}

if (!class_exists("ErrorData", false)) {
/**
 * ErrorData
 */
class ErrorData {
	/**
	 * @access public
	 * @var xslong
	 */
	public $errorId;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $domain;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $subdomain;
	/**
	 * @access public
	 * @var commonErrorSeverity
	 */
	public $severity;
	/**
	 * @access public
	 * @var commonErrorCategory
	 */
	public $category;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $message;
	/**
	 * @access public
	 * @var xstoken
	 */
	public $exceptionId;
	/**
	 * @access public
	 * @var commonErrorParameter
	 */
	public $parameter;
}}

if (!class_exists("ErrorParameter", false)) {
/**
 * ErrorParameter
 */
class ErrorParameter {
}}

if (!class_exists("FaultMessage", false)) {
/**
 * FaultMessage
 */
class FaultMessage {
	/**
	 * @access public
	 * @var commonResponseEnvelope
	 */
	public $responseEnvelope;
	/**
	 * @access public
	 * @var commonErrorData
	 */
	public $error;
}}

if (!class_exists("PhoneNumberType", false)) {
/**
 * PhoneNumberType
 */
class PhoneNumberType {
	/**
	 * @access public
	 * @var xsstring
	 */
	public $countryCode;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $phoneNumber;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $extension;
}}

if (!class_exists("RequestEnvelope", false)) {
/**
 * RequestEnvelope
 */
class RequestEnvelope {
	/**
	 * @access public
	 * @var commonDetailLevelCode
	 */
	public $detailLevel;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $errorLanguage;
}}

if (!class_exists("ResponseEnvelope", false)) {
/**
 * ResponseEnvelope
 */
class ResponseEnvelope {
	/**
	 * @access public
	 * @var xsdateTime
	 */
	public $timestamp;
	/**
	 * @access public
	 * @var commonAckCode
	 */
	public $ack;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $correlationId;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $build;
}}

if (!class_exists("Address", false)) {
/**
 * Address
 */
class Address {
	/**
	 * @access public
	 * @var xsstring
	 */
	public $addresseeName;
	/**
	 * @access public
	 * @var commonBaseAddress
	 */
	public $baseAddress;
}}

if (!class_exists("AddressList", false)) {
/**
 * AddressList
 */
class AddressList {
	/**
	 * @access public
	 * @var apAddress
	 */
	public $address;
}}

if (!class_exists("CurrencyCodeList", false)) {
/**
 * CurrencyCodeList
 */
class CurrencyCodeList {
	/**
	 * @access public
	 * @var xsstring
	 */
	public $currencyCode;
}}

if (!class_exists("CurrencyConversionList", false)) {
/**
 * CurrencyConversionList
 */
class CurrencyConversionList {
	/**
	 * @access public
	 * @var commonCurrencyType
	 */
	public $baseAmount;
	/**
	 * @access public
	 * @var apCurrencyList
	 */
	public $currencyList;
}}

if (!class_exists("CurrencyConversionTable", false)) {
/**
 * CurrencyConversionTable
 */
class CurrencyConversionTable {
	/**
	 * @access public
	 * @var apCurrencyConversionList
	 */
	public $currencyConversionList;
}}

if (!class_exists("CurrencyList", false)) {
/**
 * CurrencyList
 */
class CurrencyList {
	/**
	 * @access public
	 * @var commonCurrencyType
	 */
	public $currency;
}}

if (!class_exists("DisplayOptions", false)) {
/**
 * DisplayOptions
 */
class DisplayOptions {
	/**
	 * @access public
	 * @var xsstring
	 */
	public $emailHeaderImageUrl;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $emailMarketingImageUrl;
}}

if (!class_exists("ErrorList", false)) {
/**
 * ErrorList
 */
class ErrorList {
	/**
	 * @access public
	 * @var commonErrorData
	 */
	public $error;
}}

if (!class_exists("FundingConstraint", false)) {
/**
 * FundingConstraint
 */
class FundingConstraint {
	/**
	 * @access public
	 * @var apFundingTypeList
	 */
	public $allowedFundingType;
}}

if (!class_exists("fundingTypeInfo", false)) {
/**
 * FundingTypeInfo
 */
class fundingTypeInfo {
	/**
	 * @access public
	 * @var xsstring
	 */
	public $fundingType;
}}

if (!class_exists("FundingTypeList", false)) {
/**
 * FundingTypeList
 */
class FundingTypeList {
	/**
	 * @access public
	 * @var apFundingTypeInfo
	 */
	public $fundingTypeInfo;
}}

if (!class_exists("InitiatingEntity", false)) {
/**
 * InitiatingEntity
 */
class InitiatingEntity {
	/**
	 * @access public
	 * @var apInstitutionCustomer
	 */
	public $institutionCustomer;
}}

if (!class_exists("InstitutionCustomer", false)) {
/**
 * InstitutionCustomer
 */
class InstitutionCustomer {
	/**
	 * @access public
	 * @var xsstring
	 */
	public $institutionId;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $firstName;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $lastName;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $displayName;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $institutionCustomerId;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $countryCode;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $email;
}}

if (!class_exists("PayError", false)) {
/**
 * PayError
 */
class PayError {
	/**
	 * @access public
	 * @var apReceiver
	 */
	public $receiver;
	/**
	 * @access public
	 * @var commonErrorData
	 */
	public $error;
}}

if (!class_exists("PayErrorList", false)) {
/**
 * PayErrorList
 */
class PayErrorList {
	/**
	 * @access public
	 * @var apPayError
	 */
	public $payError;
}}

if (!class_exists("PaymentInfo", false)) {
/**
 * PaymentInfo
 */
class PaymentInfo {
	/**
	 * @access public
	 * @var xsstring
	 */
	public $transactionId;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $transactionStatus;
	/**
	 * @access public
	 * @var apReceiver
	 */
	public $receiver;
	/**
	 * @access public
	 * @var xsdecimal
	 */
	public $refundedAmount;
	/**
	 * @access public
	 * @var xsboolean
	 */
	public $pendingRefund;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $senderTransactionId;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $senderTransactionStatus;
}}

if (!class_exists("PaymentInfoList", false)) {
/**
 * PaymentInfoList
 */
class PaymentInfoList {
	/**
	 * @access public
	 * @var apPaymentInfo
	 */
	public $paymentInfo;
}}

if (!class_exists("receiver", false)) {
/**
 * Receiver
 */
class receiver {
	/**
	 * @access public
	 * @var xsdecimal
	 */
	public $amount;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $email;
	/**
	 * @access public
	 * @var commonPhoneNumberType
	 */
	public $phone;
	/**
	 * @access public
	 * @var xsboolean
	 */
	public $primary;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $invoiceId;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $paymentType;
}}

if (!class_exists("ReceiverList", false)) {
/**
 * ReceiverList
 */
class ReceiverList {
	/**
	 * @access public
	 * @var apReceiver
	 */
	public $receiver;
}}

if (!class_exists("RefundInfo", false)) {
/**
 * RefundInfo
 */
class RefundInfo {
	/**
	 * @access public
	 * @var apReceiver
	 */
	public $receiver;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $refundStatus;
	/**
	 * @access public
	 * @var xsdecimal
	 */
	public $refundNetAmount;
	/**
	 * @access public
	 * @var xsdecimal
	 */
	public $refundFeeAmount;
	/**
	 * @access public
	 * @var xsdecimal
	 */
	public $refundGrossAmount;
	/**
	 * @access public
	 * @var xsdecimal
	 */
	public $totalOfAllRefunds;
	/**
	 * @access public
	 * @var xsboolean
	 */
	public $refundHasBecomeFull;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $encryptedRefundTransactionId;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $refundTransactionStatus;
	/**
	 * @access public
	 * @var apErrorList
	 */
	public $errorList;
}}

if (!class_exists("RefundInfoList", false)) {
/**
 * RefundInfoList
 */
class RefundInfoList {
	/**
	 * @access public
	 * @var apRefundInfo
	 */
	public $refundInfo;
}}

if (!class_exists("CancelPreapprovalRequest", false)) {
/**
 * CancelPreapprovalRequest
 */
class CancelPreapprovalRequest {
	/**
	 * @access public
	 * @var commonRequestEnvelope
	 */
	public $requestEnvelope;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $preapprovalKey;
}}

if (!class_exists("CancelPreapprovalResponse", false)) {
/**
 * CancelPreapprovalResponse
 */
class CancelPreapprovalResponse {
	/**
	 * @access public
	 * @var commonResponseEnvelope
	 */
	public $responseEnvelope;
}}

if (!class_exists("ConvertCurrencyRequest", false)) {
/**
 * ConvertCurrencyRequest
 */
class ConvertCurrencyRequest {
	/**
	 * @access public
	 * @var commonRequestEnvelope
	 */
	public $requestEnvelope;
	/**
	 * @access public
	 * @var apCurrencyList
	 */
	public $baseAmountList;
	/**
	 * @access public
	 * @var apCurrencyCodeList
	 */
	public $convertToCurrencyList;
}}

if (!class_exists("ConvertCurrencyResponse", false)) {
/**
 * ConvertCurrencyResponse
 */
class ConvertCurrencyResponse {
	/**
	 * @access public
	 * @var commonResponseEnvelope
	 */
	public $responseEnvelope;
	/**
	 * @access public
	 * @var apCurrencyConversionTable
	 */
	public $estimatedAmountTable;
}}

if (!class_exists("ExecutePaymentRequest", false)) {
/**
 * ExecutePaymentRequest
 */
class ExecutePaymentRequest {
	/**
	 * @access public
	 * @var commonRequestEnvelope
	 */
	public $requestEnvelope;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $payKey;
}}

if (!class_exists("ExecutePaymentResponse", false)) {
/**
 * ExecutePaymentResponse
 */
class ExecutePaymentResponse {
	/**
	 * @access public
	 * @var commonResponseEnvelope
	 */
	public $responseEnvelope;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $paymentExecStatus;
	/**
	 * @access public
	 * @var apPayErrorList
	 */
	public $payErrorList;
}}

if (!class_exists("GetPaymentOptionsRequest", false)) {
/**
 * GetPaymentOptionsRequest
 */
class GetPaymentOptionsRequest {
	/**
	 * @access public
	 * @var commonRequestEnvelope
	 */
	public $requestEnvelope;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $payKey;
}}

if (!class_exists("GetPaymentOptionsResponse", false)) {
/**
 * GetPaymentOptionsResponse
 */
class GetPaymentOptionsResponse {
	/**
	 * @access public
	 * @var commonResponseEnvelope
	 */
	public $responseEnvelope;
	/**
	 * @access public
	 * @var apInitiatingEntity
	 */
	public $initiatingEntity;
	/**
	 * @access public
	 * @var apDisplayOptions
	 */
	public $displayOptions;
}}

if (!class_exists("PaymentDetailsRequest", false)) {
/**
 * PaymentDetailsRequest
 */
class PaymentDetailsRequest {
	/**
	 * @access public
	 * @var commonRequestEnvelope
	 */
	public $requestEnvelope;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $payKey;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $transactionId;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $trackingId;
}}

if (!class_exists("PaymentDetailsResponse", false)) {
/**
 * PaymentDetailsResponse
 */
class PaymentDetailsResponse {
	/**
	 * @access public
	 * @var commonResponseEnvelope
	 */
	public $responseEnvelope;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $cancelUrl;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $currencyCode;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $ipnNotificationUrl;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $memo;
	/**
	 * @access public
	 * @var apPaymentInfoList
	 */
	public $paymentInfoList;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $returnUrl;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $senderEmail;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $status;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $trackingId;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $payKey;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $actionType;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $feesPayer;
	/**
	 * @access public
	 * @var xsboolean
	 */
	public $reverseAllParallelPaymentsOnError;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $preapprovalKey;
	/**
	 * @access public
	 * @var apFundingConstraint
	 */
	public $fundingConstraint;
}}

if (!class_exists("PayRequest", false)) {
/**
 * PayRequest
 */
class PayRequest {
	/**
	 * @access public
	 * @var commonRequestEnvelope
	 */
	public $requestEnvelope;
	/**
	 * @access public
	 * @var commonClientDetailsType
	 */
	public $clientDetails;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $actionType;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $cancelUrl;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $currencyCode;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $feesPayer;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $ipnNotificationUrl;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $memo;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $pin;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $preapprovalKey;
	/**
	 * @access public
	 * @var apReceiverList
	 */
	public $receiverList;
	/**
	 * @access public
	 * @var xsboolean
	 */
	public $reverseAllParallelPaymentsOnError;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $senderEmail;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $returnUrl;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $trackingId;
	/**
	 * @access public
	 * @var apFundingConstraint
	 */
	public $fundingConstraint;
}}

if (!class_exists("PayResponse", false)) {
/**
 * PayResponse
 */
class PayResponse {
	/**
	 * @access public
	 * @var commonResponseEnvelope
	 */
	public $responseEnvelope;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $payKey;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $paymentExecStatus;
	/**
	 * @access public
	 * @var apPayErrorList
	 */
	public $payErrorList;
}}

if (!class_exists("PreapprovalDetailsRequest", false)) {
/**
 * PreapprovalDetailsRequest
 */
class PreapprovalDetailsRequest {
	/**
	 * @access public
	 * @var commonRequestEnvelope
	 */
	public $requestEnvelope;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $preapprovalKey;
	/**
	 * @access public
	 * @var xsboolean
	 */
	public $getBillingAddress;
}}

if (!class_exists("PreapprovalDetailsResponse", false)) {
/**
 * PreapprovalDetailsResponse
 */
class PreapprovalDetailsResponse {
	/**
	 * @access public
	 * @var commonResponseEnvelope
	 */
	public $responseEnvelope;
	/**
	 * @access public
	 * @var xsboolean
	 */
	public $approved;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $cancelUrl;
	/**
	 * @access public
	 * @var xslong
	 */
	public $curPayments;
	/**
	 * @access public
	 * @var xsdecimal
	 */
	public $curPaymentsAmount;
	/**
	 * @access public
	 * @var xslong
	 */
	public $curPeriodAttempts;
	/**
	 * @access public
	 * @var xsdateTime
	 */
	public $curPeriodEndingDate;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $currencyCode;
	/**
	 * @access public
	 * @var xsint
	 */
	public $dateOfMonth;
	/**
	 * @access public
	 * @var commonDayOfWeek
	 */
	public $dayOfWeek;
	/**
	 * @access public
	 * @var xsdateTime
	 */
	public $endingDate;
	/**
	 * @access public
	 * @var xsdecimal
	 */
	public $maxAmountPerPayment;
	/**
	 * @access public
	 * @var xsint
	 */
	public $maxNumberOfPayments;
	/**
	 * @access public
	 * @var xsint
	 */
	public $maxNumberOfPaymentsPerPeriod;
	/**
	 * @access public
	 * @var xsdecimal
	 */
	public $maxTotalAmountOfAllPayments;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $paymentPeriod;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $pinType;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $returnUrl;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $senderEmail;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $memo;
	/**
	 * @access public
	 * @var xsdateTime
	 */
	public $startingDate;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $status;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $ipnNotificationUrl;
	/**
	 * @access public
	 * @var apAddressList
	 */
	public $addressList;
}}

if (!class_exists("PreapprovalRequest", false)) {
/**
 * PreapprovalRequest
 */
class PreapprovalRequest {
	/**
	 * @access public
	 * @var commonRequestEnvelope
	 */
	public $requestEnvelope;
	/**
	 * @access public
	 * @var commonClientDetailsType
	 */
	public $clientDetails;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $cancelUrl;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $currencyCode;
	/**
	 * @access public
	 * @var xsint
	 */
	public $dateOfMonth;
	/**
	 * @access public
	 * @var commonDayOfWeek
	 */
	public $dayOfWeek;
	/**
	 * @access public
	 * @var xsdateTime
	 */
	public $endingDate;
	/**
	 * @access public
	 * @var xsdecimal
	 */
	public $maxAmountPerPayment;
	/**
	 * @access public
	 * @var xsint
	 */
	public $maxNumberOfPayments;
	/**
	 * @access public
	 * @var xsint
	 */
	public $maxNumberOfPaymentsPerPeriod;
	/**
	 * @access public
	 * @var xsdecimal
	 */
	public $maxTotalAmountOfAllPayments;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $paymentPeriod;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $returnUrl;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $memo;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $ipnNotificationUrl;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $senderEmail;
	/**
	 * @access public
	 * @var xsdateTime
	 */
	public $startingDate;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $pinType;
}}

if (!class_exists("PreapprovalResponse", false)) {
/**
 * PreapprovalResponse
 */
class PreapprovalResponse {
	/**
	 * @access public
	 * @var commonResponseEnvelope
	 */
	public $responseEnvelope;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $preapprovalKey;
}}

if (!class_exists("RefundRequest", false)) {
/**
 * RefundRequest
 */
class RefundRequest {
	/**
	 * @access public
	 * @var commonRequestEnvelope
	 */
	public $requestEnvelope;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $currencyCode;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $payKey;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $transactionId;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $trackingId;
	/**
	 * @access public
	 * @var apReceiverList
	 */
	public $receiverList;
}}

if (!class_exists("RefundResponse", false)) {
/**
 * RefundResponse
 */
class RefundResponse {
	/**
	 * @access public
	 * @var commonResponseEnvelope
	 */
	public $responseEnvelope;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $currencyCode;
	/**
	 * @access public
	 * @var apRefundInfoList
	 */
	public $refundInfoList;
}}

if (!class_exists("SetPaymentOptionsRequest", false)) {
/**
 * SetPaymentOptionsRequest
 */
class SetPaymentOptionsRequest {
	/**
	 * @access public
	 * @var commonRequestEnvelope
	 */
	public $requestEnvelope;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $payKey;
	/**
	 * @access public
	 * @var apInitiatingEntity
	 */
	public $initiatingEntity;
	/**
	 * @access public
	 * @var apDisplayOptions
	 */
	public $displayOptions;
}}

if (!class_exists("SetPaymentOptionsResponse", false)) {
/**
 * SetPaymentOptionsResponse
 */
class SetPaymentOptionsResponse {
	/**
	 * @access public
	 * @var commonResponseEnvelope
	 */
	public $responseEnvelope;
}}

?>
