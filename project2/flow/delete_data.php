<?php
require_once '../koneksi/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Metode request harus POST"]);
    exit;
}

$id = $_POST['id'] ?? '';

if (empty($id)) {
    echo json_encode(["status" => "error", "message" => "Parameter ID wajib diisi!"]);
    exit;
}

try {
    $cek = $pdo->prepare("SELECT foto FROM tb_layanan WHERE id = :id");
    $cek->execute([':id' => $id]);
    $data = $cek->fetch();

    if (!$data) {
        echo json_encode(["status" => "error", "message" => "Data dengan ID $id tidak ditemukan."]);
        exit;
    }

    if (!empty($data['foto']) && $data['foto'] !== 'default.jpg') {
        $path_foto = "../photo/" . $data['foto'];
        if (file_exists($path_foto)) {
            unlink($path_foto);
        }
    }

    $stmt = $pdo->prepare("DELETE FROM tb_layanan WHERE id = :id");
    $stmt->execute([':id' => $id]);

    echo json_encode([
        "status"  => "success",
        "message" => "Data layanan dengan ID $id berhasil dihapus (DELETE)."
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "status"  => "error",
        "message" => "Gagal menghapus data: " . $e->getMessage()
    ]);
}
?>