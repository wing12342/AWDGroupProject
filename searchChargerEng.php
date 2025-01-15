<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");
header("Expires: 0"); // Proxies
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include 'db.php';

// Get parameters and handle multiple values
$district = isset($_GET['district']) ? (array) $_GET['district'] : [];
$location = isset($_GET['location']) ? (array) $_GET['location'] : [];
$addresse = isset($_GET['address']) ? (array) $_GET['address'] : [];
// Construct SQL
$queryParts = [];
$params = [];

// Build query parts based on provided parameters
// Build query parts based on provided parameters
if (!empty($district)) {
    $districtPlaceholders = implode(',', array_fill(0, count($district), '?'));
    $queryParts[] = "NAME_OF_DISTRICT_COUNCIL_DISTRICT_EN LIKE CONCAT('%', ?, '%')";
    $params = array_merge($params, $district);
}

if (!empty($location)) {
    $locationPlaceholders = implode(',', array_fill(0, count($location), '?'));
    $queryParts[] = "LOCATION_EN LIKE CONCAT('%', ?, '%')";
    $params = array_merge($params, $location);
}

if (!empty($addresse)) {
    $addressPlaceholders = implode(',', array_fill(0, count($addresse), '?'));
    $queryParts[] = "ADDRESS_EN LIKE CONCAT('%', ?, '%')";
    $params = array_merge($params, $addresse);
}

// Create final SQL query
$query = '';
if (!empty($queryParts)) {
    $query = ' WHERE ' . implode(' AND ', $queryParts);
}

$sql = "SELECT * FROM ElectricVehicleChargers" . $query;
$stmt = $conn->prepare($sql);

if ($stmt) {
    // Bind the parameters 
    if (!empty($params)) {
        $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    }

    $stmt->execute();
    $dbresult = $stmt->get_result();

    if ($dbresult->num_rows === 0) {
        http_response_code(404); // Not Found
        $output = [
            'result' => 'Error',
            'ErrorCode' => 'D000',
            'message' => 'No records found. please check the parameters.'
        ];
        echo json_encode($output);
        exit; // Stop further execution
    }

    if ($dbresult) {
        $records = [];
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
} else {
    $output = [
        'result' => 'error',
        'message' => "Failed to prepare SQL statement."
    ];
    echo json_encode($output);
}

$stmt->close();
$conn->close();
?>


