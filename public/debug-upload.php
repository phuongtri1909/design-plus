<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>PHP Configuration</h2>";
echo "PHP Version: " . phpversion() . "<br>";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "<br>";
echo "post_max_size: " . ini_get('post_max_size') . "<br>";
echo "max_execution_time: " . ini_get('max_execution_time') . "<br>";
echo "memory_limit: " . ini_get('memory_limit') . "<br>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h2>Upload Attempt</h2>";
    echo "Content-Length: " . ($_SERVER['CONTENT_LENGTH'] ?? 'N/A') . "<br>";
    echo "Content-Type: " . ($_SERVER['CONTENT_TYPE'] ?? 'N/A') . "<br>";
    
    echo "<h3>POST Data:</h3>";
    echo "<pre>";
    print_r($_POST);
    echo "</pre>";
    
    echo "<h3>FILES Data:</h3>";
    echo "<pre>";
    print_r($_FILES);
    echo "</pre>";
    
    if (!empty($_FILES['file'])) {
        $file = $_FILES['file'];
        echo "<h3>Upload Details:</h3>";
        echo "Name: " . $file['name'] . "<br>";
        echo "Size: " . $file['size'] . " bytes<br>";
        echo "Error Code: " . $file['error'] . "<br>";
        echo "Temp Name: " . $file['tmp_name'] . "<br>";
        
        // Giải thích error code
        $errors = [
            0 => 'UPLOAD_ERR_OK - No error',
            1 => 'UPLOAD_ERR_INI_SIZE - File exceeds upload_max_filesize',
            2 => 'UPLOAD_ERR_FORM_SIZE - File exceeds MAX_FILE_SIZE in form',
            3 => 'UPLOAD_ERR_PARTIAL - File was only partially uploaded',
            4 => 'UPLOAD_ERR_NO_FILE - No file was uploaded',
            6 => 'UPLOAD_ERR_NO_TMP_DIR - Missing temporary folder',
            7 => 'UPLOAD_ERR_CANT_WRITE - Failed to write file to disk',
            8 => 'UPLOAD_ERR_EXTENSION - PHP extension stopped upload'
        ];
        echo "Error Message: " . ($errors[$file['error']] ?? 'Unknown error') . "<br>";
        
        // Thử move file
        if ($file['error'] === 0) {
            $uploadDir = __DIR__ . '/uploads/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0775, true);
            }
            $destination = $uploadDir . basename($file['name']);
            
            if (move_uploaded_file($file['tmp_name'], $destination)) {
                echo "<p style='color: green;'>✓ File uploaded successfully to: $destination</p>";
            } else {
                echo "<p style='color: red;'>✗ Failed to move uploaded file</p>";
            }
        }
    }
} else {
    echo '<h2>Upload Test Form</h2>';
    echo '<form method="POST" enctype="multipart/form-data">';
    echo '<input type="file" name="file" required><br><br>';
    echo '<input type="submit" value="Upload Test">';
    echo '</form>';
}
?>