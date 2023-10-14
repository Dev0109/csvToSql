<?php
// Check if a file was uploaded
if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == UPLOAD_ERR_OK) {
    $file = $_FILES['csv_file']['tmp_name'];

    $csvFile = $file;
    $isFirstRow = true;
    
    if (($handle = fopen($csvFile, "r")) !== false) {        
        while (($data = fgetcsv($handle, 1000, ",")) !== false) {
            echo(count($data));
            die();

            if ($isFirstRow) {
                $isFirstRow = false;

                continue; // Skip the rest of the loop and move to the next row
            }
            
            
            $stmt->close();
        }
        
        // Close the CSV file
        fclose($handle);
    }
}