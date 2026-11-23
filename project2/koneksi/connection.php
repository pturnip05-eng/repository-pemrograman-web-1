<?php
header("Content-Type: application/json; charset=UTF-8");

$host     = "localhost";
$dbname   = "db_nexatech";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    if (basename($_SERVER['PHP_SELF']) === 'connection.php') {
        echo json_encode([
            "status"  => "success",
            "message" => "Koneksi ke database db_nexatech berhasil!"
        ]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "status"  => "error",
        "message" => "Koneksi database gagal: " . $e->getMessage()
    ]);
    exit;
}
?>