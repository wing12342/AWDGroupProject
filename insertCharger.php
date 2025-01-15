<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: Content-Type");
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include your database connection
include 'db.php';

$postData = file_get_contents("php://input");
$requestData = json_decode($postData);

// Ensure the request data is valid
if (!$requestData) {
    echo json_encode(['result' => 'error', 'message' => 'Invalid input data']);
    exit;
}

$conn->begin_transaction();

try {
    // Check if a record with the same primary key exists
    $checkSql = 'SELECT COUNT(*) FROM ElectricVehicleChargers WHERE LOCATION_EN = ? AND ADDRESS_EN = ?';
    $checkStmt = $conn->prepare($checkSql);
    $checkStmt->bind_param('ss', 
        $requestData->location_en,
        $requestData->address_en
    );
    $checkStmt->execute();
    $checkStmt->bind_result($count);
    $checkStmt->fetch();
    $checkStmt->close();

    if ($count > 0) {
        echo json_encode(['result' => 'error', 'ErrorCode' => 'D001', 'message' => 'A record already exists. Please change the primary key']);
        exit;
    }

    // Prepare the insert statement
    $stmt = $conn->prepare("INSERT INTO ElectricVehicleChargers (
        NAME_OF_DISTRICT_COUNCIL_DISTRICT_EN, LOCATION_EN, ADDRESS_EN,
        NAME_OF_DISTRICT_COUNCIL_DISTRICT_TC, LOCATION_TC, ADDRESS_TC,
        NAME_OF_DISTRICT_COUNCIL_DISTRICT_SC, LOCATION_SC, ADDRESS_SC,
        STANDARD_BS1363_no, MEDIUM_IEC62196_no, MEDIUM_SAEJ1772_no, MEDIUM_OTHERS_no,
        QUICK_CHAdeMO_no, QUICK_CCS_DC_COMBO_no, QUICK_IEC62196_no, QUICK_GB_T20234_3_DC_no, QUICK_OTHERS_no,
        REMARK_FOR_OTHERS, DATA_PATH, GeometryLongitude, GeometryLatitude
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

    if ($stmt) {
        // Bind the parameters from the requestData object
        $stmt->bind_param(
            "ssssssssssssssssssssdd",
            $requestData->district_en,
            $requestData->location_en,
            $requestData->address_en,
            $requestData->district_tc,
            $requestData->location_tc,
            $requestData->address_tc,
            $requestData->district_sc,
            $requestData->location_sc,
            $requestData->address_sc,
            $requestData->standard_bs1363_no,
            $requestData->medium_iec62196_no,
            $requestData->medium_sae_j1772_no,
            $requestData->medium_others_no,
            $requestData->quick_chademo_no,
            $requestData->quick_ccs_dc_combo_no,
            $requestData->quick_iec62196_no,
            $requestData->quick_gb_t20234_3_dc_no,
            $requestData->quick_others_no,
            $requestData->remark_for_others,
            $requestData->data_path,
            $requestData->geometry_longitude,
            $requestData->geometry_latitude
        );
        
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $conn->commit();
            echo json_encode(['result' => 'success', 'message' => 'Record inserted successfully']);
        } else {
            echo json_encode(['result' => 'error', 'ErrorCode' => 'D003', 'message' => 'Insertion failed, please try again']);
        }
    } else {
        echo json_encode(['result' => 'error', 'ErrorCode' => 'D004', 'message' => 'Failed to prepare the statement']);
    }

} catch (mysqli_sql_exception $exception) {
    $conn->rollback();
    echo json_encode(['result' => 'error', 'message' => $exception->getMessage()]);
    exit;
}

$conn->close();
?>