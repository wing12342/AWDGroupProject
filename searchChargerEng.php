<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");

extract($_GET);

include 'db.php';

/*
$server = 'localhost';
$dbuser = 'root';
$dbpassword = '';
$dbname = 'ElectricVehicleChargers';

// Create database connection
$conn = new mysqli($server, $dbuser, $dbpassword, $dbname);
if ($conn->connect_errno) {
    die('Database connection failed: ' . $conn->connect_error);
}
*/

//echo $module_code.','.$abbrev.','.$module_title.','.$type.','.$effective;


// construct SQL 
$multiple = false;
$query = '';

if ($district!='null' && $district!='') {
	if (!$multiple) {
		$query .= ' WHERE ';
		$multiple = true;
	} else {
		$query .= ' AND ';
	}
	$query .= 'NAME_OF_DISTRICT_COUNCIL_DISTRICT_EN ="'.$district.'" ';
}

if ($location!='null' && $location!='') {
	if (!$multiple) {
		$query .= ' WHERE ';
		$multiple = true;
	} else {
		$query .= ' AND ';
	}
	$query .= 'LOCATION_EN ="'.$location.'" ';
}

if ($address!='null' && $address!='') {
	if (!$multiple) {
		$query .= ' WHERE ';
		$multiple = true;
	} else {
		$query .= ' AND ';
	}
	$query .= 'ADDRESS_EN ="'.$address.'" ';
}


$sql = "SELECT * FROM ElectricVehicleChargers ".$query;
$dbresult = $conn->query($sql);
if ($dbresult) {
	$records = array();
	while ($row = $dbresult->fetch_assoc()) {
		$records[] = $row;
	}
	$output = array();
	$output['result'] = 'success';
	$output['message'] = json_encode($records);
	echo json_encode($output);
} else {
	$output = array();
	$output['result'] = 'error';
	$output['message'] = "Failed to retrieve module records - database error.";
	echo json_encode($output);
	exit;
}

$conn->close();
?>