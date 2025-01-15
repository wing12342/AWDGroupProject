<?php
    function downloadDataset($url, $savePath) {
        // Check if the URL is valid
        if (!filter_var($url, FILTER_VALIDATE_URL)) {
            echo "Invalid URL.";
            return;
        }

        // Try to get the content
        $content = @file_get_contents($url);
        if ($content !== false) {
            // Try to save the file
            if (@file_put_contents($savePath, $content) !== false) {
                echo "Dataset downloaded successfully and saved as " . $savePath;
            } else {
                echo "Failed to save the dataset to " . $savePath . ". Check file permissions and disk space.";
            }
        } else {
            echo "Failed to download the dataset from " . $url . ". Check the URL and network connection.";
        }
    }
    

    function unzipFile($zipFilePath, $destination) {
        $zip = new ZipArchive;

        // Check if the ZIP file exists
        if (!file_exists($zipFilePath)) {
            echo "ZIP file does not exist.";
            return;
        }

        // Open the ZIP file
        if ($zip->open($zipFilePath) === TRUE) {
            // Extract the ZIP file to the destination directory
            if ($zip->extractTo($destination)) {
                echo "ZIP file extracted successfully to " . $destination;
                unlink($zipFilePath);
            } else {
                echo "Failed to extract ZIP file.";
            }
            $zip->close();
        } else {
            echo "Failed to open ZIP file.";
        }
    }
    function createDatabase($conn, $dbname) {
        $sql = "CREATE DATABASE IF NOT EXISTS $dbname";
        if ($conn->query($sql) === TRUE) {
            echo "<br>Database '$dbname' created successfully.<br>";
        } else {
            echo "Error creating database: " . $conn->error . "<br>";
        }
    }

    function createTable($conn, $dbname) {
        // Select the newly created database
        if (!$conn->select_db($dbname)) {
            echo "Error selecting database: " . $conn->error . "<br>";
            return;
        }
    
        // SQL to create a new table
        $createTableSQL = "CREATE TABLE `ElectricVehicleChargers` (
            `ID` INT NOT NULL AUTO_INCREMENT,
            `NAME_OF_DISTRICT_COUNCIL_DISTRICT_EN` VARCHAR(50) NOT NULL,
            `LOCATION_EN` VARCHAR(200) NOT NULL,
            `ADDRESS_EN` VARCHAR(200) NOT NULL,
            `NAME_OF_DISTRICT_COUNCIL_DISTRICT_TC` VARCHAR(20) NOT NULL,
            `LOCATION_TC` VARCHAR(50) NOT NULL,
            `ADDRESS_TC` VARCHAR(50) NOT NULL,
            `NAME_OF_DISTRICT_COUNCIL_DISTRICT_SC` VARCHAR(20) NOT NULL,
            `LOCATION_SC` VARCHAR(50) NOT NULL,
            `ADDRESS_SC` VARCHAR(50) NOT NULL,
            `STANDARD_BS1363_no` VARCHAR(50) NOT NULL,
            `MEDIUM_IEC62196_no` VARCHAR(5) NOT NULL,
            `MEDIUM_SAEJ1772_no` VARCHAR(5) NOT NULL,
            `MEDIUM_OTHERS_no` VARCHAR(5) NOT NULL,
            `QUICK_CHAdeMO_no` VARCHAR(5) NOT NULL,
            `QUICK_CCS_DC_COMBO_no` VARCHAR(5) NOT NULL,
            `QUICK_IEC62196_no` VARCHAR(5) NOT NULL,
            `QUICK_GB_T20234_3_DC_no` VARCHAR(5) NOT NULL,
            `QUICK_OTHERS_no` VARCHAR(5) NOT NULL,
            `REMARK_FOR_OTHERS` VARCHAR(50) DEFAULT NULL,
            `DATA_PATH` VARCHAR(200) DEFAULT NULL,
            `GeometryLongitude` DOUBLE NOT NULL,
            `GeometryLatitude` DOUBLE NOT NULL,
            PRIMARY KEY (`ID`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;";
    
        // Execute the CREATE TABLE query
        if ($conn->query($createTableSQL) === TRUE) {
            echo "Table 'ElectricVehicleChargers' created successfully.<br>";
        } else {
            echo "Error creating table: " . $conn->error . "<br>";
        }
    }
    function insertRecordFromCsv($conn, $csvFile) {
        if (($handle = fopen($csvFile, "r")) !== FALSE) {
            $row = 0;
            $successCount = 0;
            $errorCount = 0;
            $errorMessages = [];
    
            while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
                if ($row == 0) {
                    $row++;
                    continue; // Skip header row
                }
    
                // Check if the data array has the correct number of elements
                if (count($data) < 22) {
                    $errorCount++;
                    $errorMessages[] = "Row $row has insufficient data: " . implode(", ", $data);
                    $row++;
                    continue; // Skip this row
                }
    
                // Prepare the SQL statement
                $stmt = $conn->prepare("INSERT INTO ElectricVehicleChargers (
                    NAME_OF_DISTRICT_COUNCIL_DISTRICT_EN, LOCATION_EN, ADDRESS_EN,
                    NAME_OF_DISTRICT_COUNCIL_DISTRICT_TC, LOCATION_TC, ADDRESS_TC,
                    NAME_OF_DISTRICT_COUNCIL_DISTRICT_SC, LOCATION_SC, ADDRESS_SC,
                    STANDARD_BS1363_no, MEDIUM_IEC62196_no, MEDIUM_SAEJ1772_no, MEDIUM_OTHERS_no,
                    QUICK_CHAdeMO_no, QUICK_CCS_DC_COMBO_no, QUICK_IEC62196_no, QUICK_GB_T20234_3_DC_no, QUICK_OTHERS_no,
                    REMARK_FOR_OTHERS, DATA_PATH, GeometryLongitude, GeometryLatitude
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
                if ($stmt) {
                    // Bind the parameters (note the removal of the ID parameter)
                    $stmt->bind_param(
                        "ssssssssssssssssssssdd",
                        $data[0],
                        $data[1], $data[2], $data[3],  // Adjust indices based on your CSV structure
                        $data[4], $data[5], $data[6],
                        $data[7], $data[8], $data[9],
                        $data[10], $data[11], $data[12],
                        $data[13], $data[14], $data[15],
                        $data[16], $data[17], $data[18],
                        $data[19], $data[20], $data[21]
                    );
    
                    // Execute the statement and check for errors
                    if (!$stmt->execute()) {
                        $errorCount++;
                        $errorMessages[] = "Execute failed on row $row: " . $stmt->error;
                    } else {
                        $successCount++;
                    }
    
                    $stmt->close();
                } else {
                    // Handle statement preparation failure
                    $errorCount++;
                    $errorMessages[] = "Prepare failed: " . $conn->error;
                }
    
                $row++; // Increment the row counter here
            }
            fclose($handle);
    
            // Generate the import report
            generateImportReport($successCount, $errorCount, $errorMessages);
        } else {
            echo "Error opening CSV file\n";
        }
    }
    
        // Do not close the connection here if you plan to use it later
        // $conn->close();
    
    function generateImportReport($successCount, $errorCount, $errorMessages) {
        echo "<h2>Import Report</h2>";
        echo "<p>Records Successfully Imported: $successCount</p>";
        echo "<p>Records Failed to Import: $errorCount</p>";
    
        if ($errorCount > 0) {
            echo "<h3>Error Details:</h3>";
            echo "<ul>";
            foreach ($errorMessages as $error) {
                echo "<li>$error</li>";
            }
            echo "</ul>";
        }
    }

    function initizationDataBase($csvFile) {
        $server = 'localhost';
        $dbuser = 'root';
        $dbpassword = '';
       // $dbname = 'ElectricVehicleChargers';
    
        
        // Create database connection
        $conn = new mysqli($server, $dbuser, $dbpassword /*, $dbname*/);
        if ($conn->connect_errno) {
            die('Server connection failed: ' . $conn->connect_error);
        }

        $dbname = 'ElectricVehicleChargers';
        createDatabase($conn, $dbname);
        createTable($conn, $dbname);

        $conn = new mysqli($server, $dbuser, $dbpassword , $dbname);
        if ($conn->connect_errno) {
            die('Server connection failed: ' . $conn->connect_error);
        }
        insertRecordFromCsv($conn,$csvFile);

    }
   

    // Download the dataset
    $url = 'https://static.csdi.gov.hk/csdi-webpage/download/62b584a73c535b98bb484441ce1a0a48/csv';
    $savePath = '/Applications/XAMPP/xamppfiles/htdocs/project/dataSet.zip';
    downloadDataset($url, $savePath);

    // Specify the path to your ZIP file and the destination directory
    $zipFilePath = $savePath;
    $destination = '/Applications/XAMPP/xamppfiles/htdocs/project';

    // Call the function to unzip the ZIP file
    unzipFile($zipFilePath, $destination);

    // Path to the CSV file
    $csvFile = '/Applications/XAMPP/xamppfiles/htdocs/project/EV_Charger_converted.csv';
    initizationDataBase($csvFile);
?>