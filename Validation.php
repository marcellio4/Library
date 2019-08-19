<?php

/**
 * All validation that can be implement for example user or login class
 * Common validation already implemented
 */
abstract class Validation
{

	/**
	 * @param [string] $mail email address
	 * @return [boolean]
	 */
	public function mailCheck($mail)
	{
		return (filter_var($mail, FILTER_VALIDATE_EMAIL));
	}

	/**
	 * @param [string] $phone telephone number
	 * @param [string] $country short country code example(GBP, USD) use what is set in config
	 * @return [boolean]
	 */
	public function telNumber($phone, $country)
	{ // Allow +, - and . in phone number
		$filtered_phone_number = filter_var($phone, FILTER_SANITIZE_NUMBER_INT);
		// Remove "-" from number
		$phone_to_check = str_replace("-", "", $filtered_phone_number);
		// Check the lenght of number
		$countries = array(
			'USD' => 10,
			'GBP' => 10
		);
		return ! (strlen($phone_to_check) < $countries[$country] || strlen($phone_to_check) > 14);
	}

	/**
	 * @param [string] $postcode postcode or zip code
	 * @param [string] $country short country code example(GBP, USD) use what is set in config
	 * @return [boolean]
	 */
	public function postcode($postcode, $country)
	{
		$countries = array(
			'USD' => '/^[0-9]{5}(-[0-9]{4})?$/',
			'GBP' => '/^\s*(([A-Z]{1,2})[0-9][0-9A-Z]?)\s*(([0-9])[A-Z]{2})\s*$/'
		);
		return preg_match($countries[$country], strtoupper($postcode));
	}

	/**
	 * Check one value againts other if they are identical
	 * Can be used againts integers or string
	 * @param [string] $value
	 * @param [string] $param
	 * @return [boolean]
	 */
	public function match_values($value, $param)
	{
		return ($value === $param);
	}

	/**
	 * Check if is alphabetical
	 * @param [string] $name
	 * @return [boolean]
	 */
	public function name($name)
	{
		return (preg_match("/^[a-zA-Z ]*$/", $name));
	}

	/**
	 * @param [string] $test_date date that we want to validate
	 * @return [boolean]
	 */
	public function dateCheck($test_date)
	{
		DateTime::createFromFormat('Y-m-d', $test_date);
		$date_errors = DateTime::getLastErrors();
		return ($date_errors['warning_count'] + $date_errors['error_count'] === 0);
	}
}
