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
    $createTableSQL = "CREATE TABLE IF NOT EXISTS $tableName (
        id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY
    )";
    $mysqli->query($createTableSQL);

    if (($handle = fopen($csvFile, "r")) !== false) {
        while (($data = fgetcsv($handle, 1000, ",")) !== false) {
            $len = count($data);

            if ($isFirstRow) {
                $isFirstRow = false;
                for ($i = 0; $i < $len; $i++) {
                    // Collect the column names from the CSV file
                    $data_value[$i] = str_replace(' ', '_', $data[$i]);
                    $columnsName[$i] = $data_value[$i];

                    // Generate ALTER TABLE statements to add these columns to the table
                    $sql = "ALTER TABLE $tableName ADD {$data_value[$i]} VARCHAR(255)";
                    
                    if ($mysqli->query($sql) === TRUE) {
                        echo "Column '{$data_value[$i]}' added successfully.";
                    } else {
                        echo "Error adding column '{$data_value[$i]}': " . $mysqli->error;
                    }
                }
            }
        }

        fclose($handle);
    }

    // Close the database connection
    $mysqli->close();
}
?>
