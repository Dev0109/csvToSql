<?php
// Check if a file was uploaded
if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == UPLOAD_ERR_OK) {
    $file = $_FILES['csv_file']['tmp_name'];

    $hostname = "localhost";
    $username = "root";
    $password = "";
    $database = "aca1_db1";

    // Create a database connection
    $mysqli = new mysqli($hostname, $username, $password, $database);

    // Check the connection
    if ($mysqli->connect_error) {
        die("Connection failed: " . $mysqli->connect_error);
    }

    $tableName = "p004_NMI_Detail";

    $csvFile = $file;
    $columnsName = [];
    $isFirstRow = true;

    if (!tableExists($mysqli, $tableName)) {
        createTable($mysqli, $tableName);
    }

    if (($handle = fopen($csvFile, "r")) !== false) {
        while (($data = fgetcsv($handle, 1000, ",")) !== false) {
            $len = count($data);

            if ($isFirstRow) {
                $isFirstRow = false;
                for ($i = 0; $i < $len; $i++) {
                    $data_value[$i] = str_replace(' ', '_', $data[$i]);
                    $columnsName[$i] = $data_value[$i];
                    echo $data_value[$i];

                    if (!columnExists($mysqli, $tableName, $data_value[$i])) {
                        // Generate ALTER TABLE statements to add these columns to the table
                        $sql = "ALTER TABLE `$tableName` ADD `$data_value[$i]` VARCHAR(255)";
                        
                        if ($mysqli->query($sql) === TRUE) {
                            echo "Column `$data_value[$i]` added successfully.";
                        } else {
                            echo "Error adding column `$data_value[$i]`: " . $mysqli->error;
                        }
                    }
                }
            } else {
                $insertData = array_combine($columnsName, $data);
                insertDataIntoTable($mysqli, $tableName, $insertData);
            }
        }

        fclose($handle);
    }

    // Close the database connection
    $mysqli->close();
}

function tableExists($mysqli, $tableName) {
    $query = "SELECT 1 FROM information_schema.tables WHERE table_name = '$tableName' LIMIT 1";
    $result = $mysqli->query($query);
    return $result && $result->num_rows > 0;
}

function createTable($mysqli, $tableName) {
    $createTableSQL = "CREATE TABLE IF NOT EXISTS `$tableName` (
        id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";
    $mysqli->query($createTableSQL);
}

function columnExists($mysqli, $tableName, $columnName) {
    $query = "SELECT 1 FROM information_schema.columns WHERE table_name = '$tableName' AND column_name = '$columnName' LIMIT 1";
    $result = $mysqli->query($query);
    return $result && $result->num_rows > 0;
}

function insertDataIntoTable($mysqli, $tableName, $data) {
    $columns = implode("`, `", array_keys($data));
    $values = implode("', '", $data);
    $sql = "INSERT INTO `$tableName` (`$columns`) VALUES ('$values')";
    
    if ($mysqli->query($sql) === TRUE) {
        echo "Row inserted successfully.<br>";
    } else {
        echo "Error inserting row: " . $mysqli->error . "<br>";
    }
}
?>
