<?php
defined('BASEPATH') OR exit('No direct script access allowed');

header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE");

class Welcome extends CI_Controller 
{
	public function __construct()
	{
		parent::__construct();
	}

	public function index()
	{
		die("Hey.. theree what's app");
		// $this->load->view('welcome_message');
	}

	public function sendSms()
	{

		$postData = json_decode($this->input->raw_input_stream, true);

		if (empty($postData))
		{
			$postData = $this->input->post();
		}

		$mobileNumber = $postData['mobileNumber'] ?? '';
		$otp = $postData['otp'] ?? '';
		$otp = 1234;

		$response = [
			'otp' => $otp
		];

		$isSuccess = true;
		$message  = null;

		log_message('error', sprintf("SMS API called %s" , json_encode($postData)));


		if (!empty($mobileNumber))
		{
			try
			{
				// Account details
				$apiKey = urlencode('NjU1OTU3NTg2YTUyNzA1MTdhNGI1ODRjNzg1NzY2NDc=');
				
				// Message details
				$numbers = array('91' . $mobileNumber);
				$sender = urlencode('OITAXI');
				$message = rawurlencode('Your OTP is '. $otp);
			
				$numbers = implode(',', $numbers);
			
				// Prepare data for POST request
				$data = array('apikey' => $apiKey, 'numbers' => $numbers, "sender" => $sender, "message" => $message);
			
				// Send the POST request with cURL
				$ch = curl_init('https://api.textlocal.in/send/');
				curl_setopt($ch, CURLOPT_POST, true);
				curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$result = curl_exec($ch);
				curl_close($ch);
			
				// Process your response here
				$response['result'] = json_decode($result, true);
			}
			catch(Exception $e)
			{
				$isSuccess = false;
				$message = $e->getMessage();
			}
		}
		
		responseJson($isSuccess, $message, $response);
	}

	public function sendEmail()
	{
		$postData = json_decode($this->input->raw_input_stream, true);

		if (empty($postData))
		{
			$postData = $this->input->post();
		}

		$name = 'Vishal';
		$mobileNumber = $postData['mobileNumber'] ?? 'Not found';
		$addressFrom = $postData['addressFrom'] ?? 'Address missing';
		$addressTo = $postData['addressTo'] ?? 'Address missing';
		$carName = $postData['carName'] ?? 'Not selected';

		$message = sprintf("<h2>New booking details</h2>
			<p>Mobile: %s</p>
			<p>Address from: %s</p>
			<p>Address to: %s</p>
			<p>Car Name: %s</p>
			<p>Datetime: %s</p>
		",  $mobileNumber, $addressFrom, $addressTo, $carName, Date('Y-m-d H:i A'));

		$subject = 'OiTaxi New Booking ' . Date('Y-m-d H:i A');

		log_message('error', sprintf("Email API called %s" , json_encode($postData)));

		$isMailSent = send_mail('vishalkumar8507@gmail.com', $subject, $message, 'vishalkumar750372@gmail.com', 'OiTaxi');
		if ($isMailSent)
		{
			$response = "Yes";
		}
		else
		{
			$response = "No";
		}

		responseJson(true, null, $response);
	}
}
