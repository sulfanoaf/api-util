<?php 
namespace Sts\PleafCore\Test;

use PHPUnit\Framework\TestCase;
use Sts\PleafCore\Util\ValidationUtil;
use Sts\PleafCore\CoreException;

/***
 * Create tests for ValidationUtil
 * 
 * @author YangYang
 *
 */
class ValidationUtilTest extends TestCase {
	
	/**
	 * Test val blank or null
	 * 
	 * @group validation-util
	 */
	public function testValBlankOrNull(){
		$dto = [
				"key1" => null,
				"key2" => "",
		];
		try {
			ValidationUtil::valBlankOrNull($dto, "key1");
			$this->fail('valBlankOrNull Seharusnya fail');
		}catch(CoreException $ex){
			$this->assertEquals(VALUE_CANNOT_NULL, $ex->getErrorKey());
		}
		try {
			ValidationUtil::valBlankOrNull($dto, "key2");
			$this->fail('valBlankOrNull Seharusnya fail');
		}catch(CoreException $ex){
			$this->assertEquals(VALUE_CANNOT_NULL, $ex->getErrorKey());
		}
	}

	public function testValDatetime(){
		$dto = [
				"key1" => "20180101000000",
				"key2" => "20180101",
		];
		try {
			ValidationUtil::valDatetime($dto, "key1");
		}catch(CoreException $ex){
			$this->fail('valDatetime Seharusnya success');
		}
		try {
			ValidationUtil::valDatetime($dto, "key2");
			$this->fail('valDatetime Seharusnya fail');
		}catch(CoreException $ex){
			$this->assertEquals(DATETIME_FORMAT_INVALID, $ex->getErrorKey());
		}

	}


	public function testValDate(){
		$dto = [
				"key1" => "20180101",
				"key2" => "20181301",
		];
		try {
			ValidationUtil::valDate($dto, "key1");

		}catch(CoreException $ex){
			$this->fail('valDate Seharusnya success');
		}
		try {
			ValidationUtil::valDate($dto, "key2");
			$this->fail('valDate Seharusnya fail');
		}catch(CoreException $ex){
			$this->assertEquals(DATE_FORMAT_INVALID, $ex->getErrorKey());
		}
	}

	

	public function testValDtoContainsKey(){
		$dto = [
				"tenantId" => null
		];
		try {
			ValidationUtil::valDtoContainsKey($dto, "merchantId");
			$this->fail('valDtoContainsKey Seharusnya fail');
		}catch(CoreException $ex){
			$this->assertEquals(PARAMETER_NOT_SPECIFIED, $ex->getErrorKey());
		}
		try {
			ValidationUtil::valDtoContainsKey($dto, "tenantId");
		}catch(CoreException $ex){
			$this->fail('valDtoContainsKey Seharusnya success');
		}
	}

	public function testValLong(){
		$dto = [
				"key1" => "25",
				"key2" => 25,
		];
		try {
			ValidationUtil::valLong($dto, "key1");
			$this->fail('valLong Seharusnya fail');
		}catch(CoreException $ex){
			$this->assertEquals(VALUE_MUST_NUMERIC, $ex->getErrorKey());
		}
		try {
			ValidationUtil::valLong($dto, "key2");
		}catch(CoreException $ex){
			$this->fail('valLong Seharusnya success');
		}
	}

	public function testValEmail(){
		$dto = [
				"key1" => "test@maila",
				"key2" => "@.com",
				"key3" => "abcdtest.com"
		];
		try {
			ValidationUtil::valEmail($dto, "key1");
			$this->fail('valEmail Seharusnya fail');
		}catch(CoreException $ex){
			$this->assertEquals(EMAIL_FORMAT_INVALID, $ex->getErrorKey());
		}
		try {
			ValidationUtil::valEmail($dto, "key2");
			$this->fail('valEmail Seharusnya fail');
		}catch(CoreException $ex){
			$this->assertEquals(EMAIL_FORMAT_INVALID, $ex->getErrorKey());
		}
		try {
			ValidationUtil::valEmail($dto, "key3");
			$this->fail('valEmail Seharusnya fail');
		}catch(CoreException $ex){
			$this->assertEquals(EMAIL_FORMAT_INVALID, $ex->getErrorKey());
		}
	}

	public function testValNumber(){
		$dto = [
				"key1" => "abc",
				"key2" => "-12",
				"key3" => 0.5
		];
		try {
			ValidationUtil::valNumber($dto, "key1");
			$this->fail('valNumber Seharusnya fail');
		}catch(CoreException $ex){
			$this->assertEquals(VALUE_MUST_NUMBER, $ex->getErrorKey());
		}
		try {
			ValidationUtil::valNumber($dto, "key2");
		}catch(CoreException $ex){
			$this->fail('valNumber Seharusnya success');
		}
		try {
			ValidationUtil::valNumber($dto, "key3");
		}catch(CoreException $ex){
			$this->fail('valNumber Seharusnya success');
		}
	}

	public function testValNumeric(){
		$dto = [
				"key1" => "25",
				"key2" => -25,
				"key3" => 25.5
		];
		try {
			ValidationUtil::valNumeric($dto, "key1");
			$this->fail('valNumeric Seharusnya fail');
		}catch(CoreException $ex){
			$this->assertEquals(VALUE_MUST_NUMERIC, $ex->getErrorKey());
		}
		try {
			ValidationUtil::valNumeric($dto, "key2");
		}catch(CoreException $ex){
			$this->fail('valNumeric Seharusnya success');
		}
		try {
			ValidationUtil::valNumeric($dto, "key3");
			$this->fail('valNumeric Seharusnya fail');
		}catch(CoreException $ex){
			$this->assertEquals(VALUE_MUST_NUMERIC, $ex->getErrorKey());
		}
	}

	public function testValHttpURL(){
		$dto = [
				"key1" => "www.google.com",
				"key2" => "https://www.w3schools.com",
				"key3" => "http://www.w3schools.com"
		];
		try {
			ValidationUtil::valHttpURL($dto, "key1");
			$this->fail('valHttpURL Seharusnya fail');
		}catch(CoreException $ex){
			$this->assertEquals(HTTP_URL_FORMAT_INVALID, $ex->getErrorKey());
		}
		try {
			ValidationUtil::valHttpURL($dto, "key2");
		}catch(CoreException $ex){
			$this->fail('valHttpURL Seharusnya success');
		}
		try {
			ValidationUtil::valHttpURL($dto, "key3");
		}catch(CoreException $ex){
			$this->fail('valHttpURL Seharusnya success');
		}
	}

	public function testValValidIpAdress(){
		$dto = [
				"key1" => "12.12.12.1234",
				"key2" => "256.12.12.12",
				"key3" => "255.255.255.255"
		];
		try {
			ValidationUtil::valValidIpAddress($dto, "key1");
			$this->fail('valValidIpAddress Seharusnya fail');
		}catch(CoreException $ex){
			$this->assertEquals(INVALID_IP_ADDRESS_VALUE, $ex->getErrorKey());
		}
		try {
			ValidationUtil::valValidIpAddress($dto, "key2");
			$this->fail('valValidIpAddress Seharusnya fail');
		}catch(CoreException $ex){
			$this->assertEquals(INVALID_IP_ADDRESS_VALUE, $ex->getErrorKey());
		}
		try {
			ValidationUtil::valValidIpAddress($dto, "key3");
		}catch(CoreException $ex){
			$this->fail('valValidIpAddress Seharusnya success');
		}
	}


	
}
