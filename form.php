<?php
// Configure your number Prefix and Recipient here
$numberPrefix = '[Contact via website]';
$emailTo = 'your-email@gmail.com';			// change to your email
$errors = array();							// array to hold validation errors
$data = array();							// array to pass back data
if($_SERVER['REQUEST_METHOD'] === 'POST') {
	$name = stripslashes(trim($_POST['name']));
	$number = stripslashes(trim($_POST['number']));

	if (empty($name)) {
		$errors['name'] = 'Name is required.';
	}
	if (empty($number)) {
		$errors['number'] = 'Number is required.';
	}
	// if there are any errors in our errors array, return a success boolean or false
	if (!empty($errors)) {
		$data['success'] = false;
		$data['errors']  = $errors;
	} else {
		$number = "$numberPrefix $number";
		$body = '
			<strong>Name: </strong>'.$name.'<br />
			<strong>Phone number: </strong>'.$number.'<br />
		';
		$headers  = "MIME-Version: 1.1" . PHP_EOL;
		$headers .= "Content-type: text/html; charset=utf-8" . PHP_EOL;
		$headers .= "Content-Transfer-Encoding: 8bit" . PHP_EOL;
		$headers .= "Date: " . date('r', $_SERVER['REQUEST_TIME']) . PHP_EOL;
		$headers .= "Message-ID: <" . $_SERVER['REQUEST_TIME'] . md5($_SERVER['REQUEST_TIME']) . '@' . $_SERVER['SERVER_NAME'] . '>' . PHP_EOL;
		$headers .= "From: " . "=?UTF-8?B?".base64_encode($name)."?=" . PHP_EOL;
		$headers .= "Return-Path: $emailTo" . PHP_EOL;
		$headers .= "X-Mailer: PHP/". phpversion() . PHP_EOL;
		$headers .= "X-Originating-IP: " . $_SERVER['SERVER_ADDR'] . PHP_EOL;
		mail($emailTo, "=?utf-8?B?" . base64_encode($number) . "?=", $body, $headers);
		$data['success'] = true;
		// Change the Success message here
		$data['message'] = 'Thank you, ' . $name . '! Waiting you on our wedding!';
	}
	// return all our data to an AJAX call
	echo json_encode($data);
}