<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");

include 'db.php';

$sql = "SELECT * FROM ElectricVehicleChargers";
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
