<?php

//Including the encryted php
include 'file_encryptor.php';

$key = $_POST["keyFile"];
$dKey = "770A8A65DA156D24EE2A093277530142";

// Connecting to the Database
$servername = "mysql-29500-0.cloudclusters.net:29500/";
$username = "root";
$password = "testtest";
$database="project_db";

$conn = mysqli_connect($servername, $username, $password, $database);

if (!$conn){
    die("Sorry we failed to connect: ". mysqli_connect_error());
}

$sql="SELECT file_name FROM file_details where key_file='$key'";
$result = mysqli_query($conn, $sql);
$num=mysqli_num_rows($result);
if($num==0 || $num>1){
    echo "Key does not exist.";
}
else{
    $row=mysqli_fetch_assoc($result);
    $file_url = "uploads/".$row["file_name"];
    if(file_exists($file_url . ".enc")) {
        //Decrypting file
        decryptFile($file_url, $dKey);

        header('Content-Type: application/octet-stream');
        header("Content-Transfer-Encoding: Binary"); 
        header("Content-disposition: attachment; filename=\"" . basename($file_url) . "\""); 
        readfile($file_url);

        //deleting encryted file and plain text file
        $temp = $file_url . ".enc";
        unlink($file_url);
        unlink($temp);

        //deleting from database
        $sql="DELETE FROM file_details where key_file='$key'" ;
        $result = mysqli_query($conn, $sql);
        if(!$result){
            echo "Deletion in the database is unsuccessful ---> ". mysqli_error($conn);
          }
        die();
    }
    else{
        //http_response_code(404);
	    die("File not found or expired.".$file_url);
    }
}
echo '<br><a href="index.html">Go back</a>';
?>