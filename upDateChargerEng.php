<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: *");
header("Access-Control-Allow-Headers: *");

$postData = file_get_contents("php://input");
$requestData = json_decode($postData);

include 'db.php';
$conn->begin_transaction();

try {
    
    // Update other details
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
    QUICK_GB_T20234_3_DC__no = ?, 
    QUICK_OTHERS_no = ?, 
    REMARK_FOR__OTHERS_ = ?, 
    DATA_PATH = ?, 
    GeometryLongitude = ?, 
    GeometryLatitude = ? 
    WHERE LOCATION_EN = ? AND ADDRESS_EN = ?';



    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ssssssssssssssssssssddss', 
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
        $requestData->location_en,  // Last two parameters for WHERE clause
        $requestData->address_en
        );
    $stmt->execute();
    
    // Commit the transaction
    $conn->commit();


	$output = array();
	$output['result'] = 'success';
	$output['message'] = 'New status record created successfully';
	echo json_encode($output);	

}
catch (mysqli_sql_exception $exception) {
	$conn->rollback();
	$output = array();
	$output['result'] = 'error';
	$output['message'] = $exception->getMessage();
	echo json_encode($output);
	exit;
}

$conn->close();
?>