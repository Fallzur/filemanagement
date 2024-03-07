<?php
// connect to the database
require_once("include/connection.php");

// Uploads files
if (isset($_POST['save'])) { // if save button on the form is clicked
    // name of the uploaded file

    $user = $_POST['email'];
    $filename = $_FILES['myfile']['name'];

    // destination of the file on the server
    $destination = '../uploads/' . $filename;

    // the physical file on a temporary uploads directory on the server
    $file = $_FILES['myfile']['tmp_name'];
    $size = $_FILES['myfile']['size'];

    // Allowed file types
    $allowed_extensions = array('docx', 'doc', 'pptx', 'ppt', 'xlsx', 'xls', 'pdf', 'odt', 'jpg', 'jpeg', 'png');
    $file_extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

    if (!in_array($file_extension, $allowed_extensions)) {
        echo "Invalid file type! Allowed file types are: .docx, .doc, .pptx, .ppt, .xlsx, .xls, .pdf, .odt, .jpg, .jpeg, .png";
        echo '<script>document.getElementById("myfile").style.borderColor = "red";</script>'; // Change border color to red
        echo '<script>document.getElementById("save-form").addEventListener("submit", function(event){event.preventDefault();});</script>'; // Prevent form submission
    } elseif ($size > 2000000000) { // file shouldn't be larger than 2 Gigabytes
        echo "File too large!";
    } else {
        $query = mysqli_query($conn,"SELECT * FROM `upload_files` WHERE `name` = '$filename'") or die(mysqli_error($conn));
        $counter = mysqli_num_rows($query);

        if ($counter == 1) {
            echo '
            <script type="text/javascript">
                alert("File already uploaded");
                window.location = "add_document.php";
            </script>';
        } else {
            date_default_timezone_set("Asia/Manila");
            $time = date("M-d-Y h:i A", strtotime("+0 HOURS"));

            // move the uploaded (temporary) file to the specified destination
            if (move_uploaded_file($file, $destination)) {
                $sql = "INSERT INTO upload_files (name, size, download, timers, admin_status, email) VALUES ('$filename', $size, 0, '$time', 'Admin', '$user')";
                if (mysqli_query($conn, $sql)) {
                    echo '
                    <script type="text/javascript">
                        alert("File uploaded successfully!");
                        window.location = "add_document.php";
                    </script>';
                }
            } else {
                echo "Failed to upload files!";
            }
        }
    }
}
?>
