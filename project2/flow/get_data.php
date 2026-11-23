<?php
require_once '../koneksi/connection.php';

try {
    if (isset($_GET['id']) && !empty($_GET['id'])) {
        $stmt = $pdo->prepare("SELECT * FROM tb_layanan WHERE id = :id");
        $stmt->execute([':id' => $_GET['id']]);
        $data = $stmt->fetch();
    } else {
        $stmt = $pdo->prepare("SELECT * FROM tb_layanan ORDER BY id DESC");
        $stmt->execute();
        $data = $stmt->fetchAll();
    }

    echo json_encode([
        "status"  => "success",
        "total"   => is_array($data) ? (isset($data['id']) ? 1 : count($data)) : 0,
        "data"    => $data
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "status"  => "error",
        "message" => "Gagal mengambil data: " . $e->getMessage()
    ]);
}
?>