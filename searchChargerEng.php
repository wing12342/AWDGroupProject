<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");

include 'db.php';

// Get parameters and handle multiple values
$districts = isset($_GET['district']) ? (array) $_GET['district'] : [];
$locations = isset($_GET['location']) ? (array) $_GET['location'] : [];
$addresses = isset($_GET['address']) ? (array) $_GET['address'] : [];

// Construct SQL
$queryParts = [];
$params = [];

if (!empty($districts)) {
    $districtPlaceholders = implode(',', array_fill(0, count($districts), '?'));
    $queryParts[] = "NAME_OF_DISTRICT_COUNCIL_DISTRICT_EN IN ($districtPlaceholders)";
    $params = array_merge($params, $districts);
}

if (!empty($locations)) {
    $locationPlaceholders = implode(',', array_fill(0, count($locations), '?'));
    $queryParts[] = "LOCATION_EN IN ($locationPlaceholders)";
    $params = array_merge($params, $locations);
}

if (!empty($addresses)) {
    $addressPlaceholders = implode(',', array_fill(0, count($addresses), '?'));
    $queryParts[] = "ADDRESS_EN IN ($addressPlaceholders)";
    $params = array_merge($params, $addresses);
}

$query = $queryParts ? ' WHERE ' . implode(' AND ', $queryParts) : '';

// Prepare and execute the SQL statement
$sql = "SELECT * FROM ElectricVehicleChargers" . $query;
$stmt = $conn->prepare($sql);

if ($query) {
    // Bind the parameters 
    $stmt->bind_param(str_repeat('s', count($params)), ...$params);
}

$stmt->execute();
$dbresult = $stmt->get_result();

if ($dbresult) {
    $records = array();
	while ($row = $dbresult->fetch_assoc()) {
		$records[] = $row;
	}
	$output = [
		'result' => 'success',
		'message' => $records
	];
	echo json_encode($output);
} else {
    $output = [
        'result' => 'error',
        'message' => "Failed to retrieve module records - database error."
    ];
    echo json_encode($output);
}

$stmt->close();
$conn->close();


/*
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
*/
?>

