<?php

$config = [
    'host' => "localhost",
    'dbname' => "shop",
    'port' => 3306
];


// $dsn = 'mysql:host=localhost;dbname=shop;port=3306'; 
$dsn = 'mysql:' . http_build_query($config,'',';'); // Data Source Name
$user = 'root';
$pass = 'root';
$options = [
    PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
];

try{
    $conn = new PDO($dsn,$user,$pass,$options);
    $conn->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
}
catch(PDOException $e){
    echo "Failed to connect " . $e->getMessage() . "<br>";
}

