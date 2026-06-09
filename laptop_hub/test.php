<?php

$conn = mysqli_connect("localhost", "root", "", "laptop_hub");

if($conn){
    echo "Database Connected Successfully";
}else{
    echo "Connection Failed";
}

?>