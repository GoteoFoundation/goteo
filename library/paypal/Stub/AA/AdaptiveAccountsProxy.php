<?php

if (!class_exists("ErrorData")) {
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

if (!class_exists("ErrorParameter")) {
/**
 * ErrorParameter
 */
class ErrorParameter  {
}}

if (!class_exists("RequestEnvelope")) {
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

if (!class_exists("ResponseEnvelope")) {
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

if (!class_exists("ClientDetailsType")) {
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

if (!class_exists("FaultMessage")) {
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

if (!class_exists("CreateAccountRequest")) {
/**
 * CreateAccountRequest
 */
class CreateAccountRequest {
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
	public $accountType;
	/**
	 * @access public
	 * @var aaNameType
	 */
	public $name;
	/**
	 * @access public
	 * @var xsdate
	 */
	public $dateOfBirth;
	/**
	 * @access public
	 * @var aaAddressType
	 */
	public $address;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $contactPhoneNumber;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $homePhoneNumber;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $mobilePhoneNumber;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $currencyCode;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $citizenshipCountryCode;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $preferredLanguageCode;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $notificationURL;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $emailAddress;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $registrationType;
	/**
	 * @access public
	 * @var aaCreateAccountWebOptionsType
	 */
	public $createAccountWebOptions;
	/**
	 * @access public
	 * @var xsboolean
	 */
	public $suppressWelcomeEmail;
	/**
	 * @access public
	 * @var xsboolean
	 */
	public $performExtraVettingOnThisAccount;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $partnerField1;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $partnerField2;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $partnerField3;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $partnerField4;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $partnerField5;
	/**
	 * @access public
	 * @var aaBusinessInfoType
	 */
	public $businessInfo;
}}

if (!class_exists("CreateAccountResponse")) {
/**
 * CreateAccountResponse
 */
class CreateAccountResponse {
	/**
	 * @access public
	 * @var commonResponseEnvelope
	 */
	public $responseEnvelope;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $createAccountKey;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $execStatus;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $redirectURL;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $accountId;
}}

if (!class_exists("GetUserAgreementRequest")) {
/**
 * GetUserAgreementRequest
 */
class GetUserAgreementRequest {
	/**
	 * @access public
	 * @var commonRequestEnvelope
	 */
	public $requestEnvelope;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $createAccountKey;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $countryCode;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $languageCode;
}}

if (!class_exists("GetUserAgreementResponse")) {
/**
 * GetUserAgreementResponse
 */
class GetUserAgreementResponse {
	/**
	 * @access public
	 * @var commonResponseEnvelope
	 */
	public $responseEnvelope;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $agreement;
}}

if (!class_exists("GetVerifiedStatusRequest")) {
/**
 * GetVerifiedStatusRequest
 */
class GetVerifiedStatusRequest {
	/**
	 * @access public
	 * @var commonRequestEnvelope
	 */
	public $requestEnvelope;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $emailAddress;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $matchCriteria;
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
}}

if (!class_exists("GetVerifiedStatusResponse")) {
/**
 * GetVerifiedStatusResponse
 */
class GetVerifiedStatusResponse {
	/**
	 * @access public
	 * @var commonResponseEnvelope
	 */
	public $responseEnvelope;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $accountStatus;
}}

if (!class_exists("AddBankAccountRequest")) {
/**
 * AddBankAccountRequest
 */
class AddBankAccountRequest {
	/**
	 * @access public
	 * @var commonRequestEnvelope
	 */
	public $requestEnvelope;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $emailAddress;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $accountId;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $createAccountKey;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $bankCountryCode;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $bankName;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $routingNumber;
	/**
	 * @access public
	 * @var aaBankAccountType
	 */
	public $bankAccountType;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $bankAccountNumber;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $iban;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $clabe;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $bsbNumber;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $branchLocation;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $sortCode;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $bankTransitNumber;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $institutionNumber;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $branchCode;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $agencyNumber;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $bankCode;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $ribKey;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $controlDigit;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $taxIdType;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $taxIdNumber;
	/**
	 * @access public
	 * @var xsdate
	 */
	public $accountHolderDateOfBirth;
	/**
	 * @access public
	 * @var aaConfirmationType
	 */
	public $confirmationType;
	/**
	 * @access public
	 * @var aaWebOptionsType
	 */
	public $webOptions;
}}

if (!class_exists("AddBankAccountResponse")) {
/**
 * AddBankAccountResponse
 */
class AddBankAccountResponse {
	/**
	 * @access public
	 * @var commonResponseEnvelope
	 */
	public $responseEnvelope;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $execStatus;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $redirectURL;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $fundingSourceKey;
}}

if (!class_exists("AddPaymentCardRequest")) {
/**
 * AddPaymentCardRequest
 */
class AddPaymentCardRequest {
	/**
	 * @access public
	 * @var commonRequestEnvelope
	 */
	public $requestEnvelope;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $emailAddress;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $accountId;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $createAccountKey;
	/**
	 * @access public
	 * @var aaNameType
	 */
	public $nameOnCard;
	/**
	 * @access public
	 * @var aaAddressType
	 */
	public $billingAddress;
	/**
	 * @access public
	 * @var xsdate
	 */
	public $cardOwnerDateOfBirth;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $cardNumber;
	/**
	 * @access public
	 * @var aaCardTypeType
	 */
	public $cardType;
	/**
	 * @access public
	 * @var aaCardDateType
	 */
	public $expirationDate;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $cardVerificationNumber;
	/**
	 * @access public
	 * @var aaCardDateType
	 */
	public $startDate;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $issueNumber;
	/**
	 * @access public
	 * @var aaConfirmationType
	 */
	public $confirmationType;
	/**
	 * @access public
	 * @var aaWebOptionsType
	 */
	public $webOptions;
}}

if (!class_exists("AddPaymentCardResponse")) {
/**
 * AddPaymentCardResponse
 */
class AddPaymentCardResponse {
	/**
	 * @access public
	 * @var commonResponseEnvelope
	 */
	public $responseEnvelope;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $execStatus;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $redirectURL;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $fundingSourceKey;
}}

if (!class_exists("SetFundingSourceConfirmedRequest")) {
/**
 * SetFundingSourceConfirmedRequest
 */
class SetFundingSourceConfirmedRequest {
	/**
	 * @access public
	 * @var commonRequestEnvelope
	 */
	public $requestEnvelope;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $emailAddress;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $accountId;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $fundingSourceKey;
}}

if (!class_exists("SetFundingSourceConfirmedResponse")) {
/**
 * SetFundingSourceConfirmedResponse
 */
class SetFundingSourceConfirmedResponse {
	/**
	 * @access public
	 * @var commonResponseEnvelope
	 */
	public $responseEnvelope;
}}

if (!class_exists("NameType")) {
/**
 * NameType
 */
class NameType {
	/**
	 * @access public
	 * @var xsstring
	 */
	public $salutation;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $firstName;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $middleName;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $lastName;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $suffix;
}}

if (!class_exists("AddressType")) {
/**
 * AddressType
 */
class AddressType {
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
}}

if (!class_exists("CreateAccountWebOptionsType")) {
/**
 * CreateAccountWebOptionsType
 */
class CreateAccountWebOptionsType {
	/**
	 * @access public
	 * @var xsstring
	 */
	public $returnUrl;
	/**
	 * @access public
	 * @var xsboolean
	 */
	public $showAddCreditCard;
	/**
	 * @access public
	 * @var xsboolean
	 */
	public $showMobileConfirm;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $returnUrlDescription;
	/**
	 * @access public
	 * @var xsboolean
	 */
	public $useMiniBrowser;
}}

if (!class_exists("BusinessInfoType")) {
/**
 * BusinessInfoType
 */
class BusinessInfoType {
	/**
	 * @access public
	 * @var xsstring
	 */
	public $businessName;
	/**
	 * @access public
	 * @var aaAddressType
	 */
	public $businessAddress;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $workPhone;
	/**
	 * @access public
	 * @var xsinteger
	 */
	public $category;
	/**
	 * @access public
	 * @var xsinteger
	 */
	public $subCategory;
	/**
	 * @access public
	 * @var xsinteger
	 */
	public $merchantCategoryCode;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $doingBusinessAs;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $customerServicePhone;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $customerServiceEmail;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $disputeEmail;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $webSite;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $companyId;
	/**
	 * @access public
	 * @var xsdate
	 */
	public $dateOfEstablishment;
	/**
	 * @access public
	 * @var aaBusinessType
	 */
	public $businessType;
	/**
	 * @access public
	 * @var aaBusinessSubtypeType
	 */
	public $businessSubtype;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $incorporationId;
	/**
	 * @access public
	 * @var xsdecimal
	 */
	public $averagePrice;
	/**
	 * @access public
	 * @var xsdecimal
	 */
	public $averageMonthlyVolume;
	/**
	 * @access public
	 * @var xsinteger
	 */
	public $percentageRevenueFromOnline;
	/**
	 * @access public
	 * @var aaSalesVenueType
	 */
	public $salesVenue;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $salesVenueDesc;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $vatId;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $vatCountryCode;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $commercialRegistrationLocation;
	/**
	 * @access public
	 * @var aaAddressType
	 */
	public $principalPlaceOfBusinessAddress;
	/**
	 * @access public
	 * @var aaAddressType
	 */
	public $registeredOfficeAddress;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $establishmentCountryCode;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $establishmentState;
	/**
	 * @access public
	 * @var aaBusinessStakeholderType
	 */
	public $businessStakeholder;
}}

if (!class_exists("AccountValidationInfoType")) {
/**
 * AccountValidationInfoType
 */
class AccountValidationInfoType {
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
	public $addressLine1;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $postalCode;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $phoneNumber;
}}

if (!class_exists("BusinessStakeholderType")) {
/**
 * BusinessStakeholderType
 */
class BusinessStakeholderType {
	/**
	 * @access public
	 * @var aaStakeholderRoleType
	 */
	public $role;
	/**
	 * @access public
	 * @var aaNameType
	 */
	public $name;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $fullLegalName;
	/**
	 * @access public
	 * @var aaAddressType
	 */
	public $address;
	/**
	 * @access public
	 * @var xsdate
	 */
	public $dateOfBirth;
}}

if (!class_exists("WebOptionsType")) {
/**
 * WebOptionsType
 */
class WebOptionsType {
	/**
	 * @access public
	 * @var xsstring
	 */
	public $returnUrl;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $cancelUrl;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $returnUrlDescription;
	/**
	 * @access public
	 * @var xsstring
	 */
	public $cancelUrlDescription;
}}

if (!class_exists("CardDateType")) {
/**
 * CardDateType
 */
class CardDateType {
	/**
	 * @access public
	 * @var xsinteger
	 */
	public $month;
	/**
	 * @access public
	 * @var xsinteger
	 */
	public $year;
}}



?>