<?php
$host = 'localhost';
$dbname = '2526_11db';
$username = '2526_11';
$password = '12345678';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
