<?php
if (isset($_POST["submit"])) {
    $targetDirectory = "uploads/"; // Directory to store uploaded files

    if (!file_exists($targetDirectory)) {
        mkdir($targetDirectory, 0777, true);
    }

    $targetFile = $targetDirectory . basename($_FILES["fileToUpload"]["name"]);
    $uploadOk = 1;

    if (empty($_FILES["fileToUpload"]["name"])) {
        echo "Please select a file to upload.";
        $uploadOk = 0;
    }

    if (file_exists($targetFile)) {
        echo "Sorry, this file already exists.";
        $uploadOk = 0;
    }

    if ($_FILES["fileToUpload"]["size"] > 500000) {
        echo "Sorry, your file is too large.";
        $uploadOk = 0;
    }

    $allowedExtensions = ["jpg", "jpeg", "png", "gif", "pdf"];
    $fileExtension = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

    if (!in_array($fileExtension, $allowedExtensions)) {
        echo "Sorry, only JPG, JPEG, PNG, GIF, and PDF files are allowed.";
        $uploadOk = 0;
    }

    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["fileToUpload"]["tmp_name"], $targetFile)) {
            // File upload successful, now save information in the database

            $db = new mysqli("localhost", "AadityaMalap", "123", "eclasse");
            if ($db->connect_error) {
                die("Connection failed: " . $db->connect_error);
            }

            $fileName = $_FILES["fileToUpload"]["name"];
            $fileSize = $_FILES["fileToUpload"]["size"];
            $fileType = $_FILES["fileToUpload"]["type"];
            $fileContent = file_get_contents($targetFile);

            $sql = "INSERT INTO uploaded_files (file_name, file_size, file_type, file_content) VALUES (?, ?, ?, ?)";
            $stmt = $db->prepare($sql);
            $stmt->bind_param("siss", $fileName, $fileSize, $fileType, $fileContent);

            if ($stmt->execute()) {
                echo "The file " . htmlspecialchars($fileName) . " has been uploaded and saved in the database.";
            } else {
                echo "Sorry, there was an error uploading and saving your file.";
            }

            $stmt->close();
            $db->close();
        } else {
            echo "Sorry, there was an error uploading your file.";
        }
    }
}
?>
