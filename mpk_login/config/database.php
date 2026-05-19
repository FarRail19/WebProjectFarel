<?php
$host = 'sql113.byetcluster.com';
$dbname = 'if0_41942872_mpk_db';
$username = 'if0_41942872';
$password = 'gilaak999';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
