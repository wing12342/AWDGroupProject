<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Get the raw PUT data
$putData = file_get_contents("php://input");
$requestData = json_decode($putData);

// Check for JSON decoding errors
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode([
        'result' => 'error',
        'ErrorCode' => 'P001',
        'message' => 'Invalid JSON format.'
    ]);
    exit;
}

// Include database connection
include 'db.php';
$conn->begin_transaction();
try {
 
    $sql = 'UPDATE ElectricVehicleChargers SET
        NAME_OF_DISTRICT_COUNCIL_DISTRICT_EN = ?, 
        LOCATION_EN = ?, 
        ADDRESS_EN = ?, 
        NAME_OF_DISTRICT_COUNCIL_DISTRICT_TC = ?, 
        LOCATION_TC = ?, 
        ADDRESS_TC = ?, 
        NAME_OF_DISTRICT_COUNCIL_DISTRICT_SC = ?, 
        LOCATION_SC = ?, 
        ADDRESS_SC = ?, 
        STANDARD_BS1363_no = ?, 
        MEDIUM_IEC62196_no = ?, 
        MEDIUM_SAEJ1772_no = ?, 
        MEDIUM_OTHERS_no = ?, 
        QUICK_CHAdeMO_no = ?, 
        QUICK_CCS_DC_COMBO_no = ?, 
        QUICK_IEC62196_no = ?, 
        QUICK_GB_T20234_3_DC_no = ?, 
        QUICK_OTHERS_no = ?, 
        REMARK_FOR_OTHERS = ?, 
        DATA_PATH = ?, 
        GeometryLongitude = ?, 
        GeometryLatitude = ? 
        WHERE ID= ?';

    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        throw new mysqli_sql_exception('Statement preparation failed: ' . $conn->error);
    }

    // Bind parameters
    $stmt->bind_param('ssssssssssssssssssssddi', 
        $requestData->name_of_district_council_district_en,
        $requestData->location_en,
        $requestData->address_en,
        $requestData->name_of_district_council_district_tc,
        $requestData->location_tc,
        $requestData->address_tc,
        $requestData->name_of_district_council_district_sc,
        $requestData->location_sc,
        $requestData->address_sc,
        $requestData->standard_bs1363_no,
        $requestData->medium_iec62196_no,
        $requestData->medium_saej1772_no,
        $requestData->medium_others_no,
        $requestData->quick_chademo_no,
        $requestData->quick_ccs_dc_combo_no,
        $requestData->quick_iec62196_no,
        $requestData->quick_gb_t20234_3_dc_no,
        $requestData->quick_others_no,
        $requestData->remark_for_others_,
        $requestData->data_path,
        $requestData->geometry_longitude,
        $requestData->geometry_latitude,
        $requestData->id 
    );

    // Execute statement
    if (!$stmt->execute()) {
        throw new mysqli_sql_exception('Execute failed: ' . $stmt->error);
    }

    // Commit the transaction
    if ($stmt->affected_rows > 0) {
        $conn->commit();
        echo json_encode([
            'result' => 'success',
            'message' => 'Record updated successfully'
        ]);
    } else {
        echo json_encode([
            'result' => 'error',
            'ErrorCode' => 'D003',
            'message' => 'Update failed, no changes made.'
        ]);
    }
} catch (mysqli_sql_exception $exception) {
    $conn->rollback();
    error_log('Database error: ' . $exception->getMessage());

    echo json_encode([
        'result' => 'error',
        'ErrorCode' => 'I000',
        'message' => 'A MySQL error occurred. Please try again later.'
    ]);
} finally {
    $conn->close();
}
?>