<?php

$conn = new mysqli(
    "localhost",
    "root",
    "",
    "mathsworld_new"
);

if ($conn->connect_error) {
    die("Database connection failed");
}

$conn->set_charset("utf8mb4");

?>