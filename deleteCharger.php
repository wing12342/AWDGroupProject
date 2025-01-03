<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: DELETE");
header("Access-Control-Allow-Headers: Content-Type");

$postData = file_get_contents("php://input");
$requestData = json_decode($postData);

// Include your database connection
include 'db.php';

$conn->begin_transaction();

try {
    $sql = 'DELETE FROM ElectricVehicleChargers WHERE LOCATION_EN = ? AND ADDRESS_EN = ?';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param(
        'ss', 
        $requestData->location_en,
        $requestData->address_en
    ); // Assuming shapeid is an integer
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $conn->commit();
        $output = array('result' => 'success', 'message' => 'Record deleted successfully');
    } else {
        $output = array('result' => 'error', 'message' => 'Record not found');
    }

    echo json_encode($output);
} catch (mysqli_sql_exception $exception) {
    $conn->rollback();
    $output = array('result' => 'error', 'message' => $exception->getMessage());
    echo json_encode($output);
    exit;
}

$conn->close();
?>