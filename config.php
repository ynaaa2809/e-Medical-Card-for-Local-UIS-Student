<?php

$conn = mysqli_connect(
    "localhost",
    "root",
    "",
    "emcs"
);

if(!$conn)
{
    die("Connection Failed : " . mysqli_connect_error());
}

?>