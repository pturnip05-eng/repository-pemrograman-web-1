<?php
require_once '../koneksi/connection.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["status" => "error", "message" => "Metode request harus POST"]);
    exit;
}

$id            = $_POST['id'] ?? '';
$kategori      = $_POST['kategori'] ?? '';
$judul_layanan = $_POST['judul_layanan'] ?? '';
$deskripsi     = $_POST['deskripsi'] ?? '';
$harga         = $_POST['harga'] ?? '';
$durasi        = $_POST['durasi'] ?? '';
$kode_akses    = $_POST['kode_akses'] ?? '';

$nama_file_foto = null;
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $folder_tujuan = "../photo/";
    if (!is_dir($folder_tujuan)) {
        mkdir($folder_tujuan, 0777, true);
    }

    $ekstensi = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $nama_file_foto = "layanan_" . time() . "_" . rand(100, 999) . "." . $ekstensi;
    $path_simpan = $folder_tujuan . $nama_file_foto;

    if (!move_uploaded_file($_FILES['foto']['tmp_name'], $path_simpan)) {
        echo json_encode(["status" => "error", "message" => "Gagal mengupload file foto."]);
        exit;
    }
}

try {
    if (!empty($id)) {
        $cek = $pdo->prepare("SELECT foto, kode_akses FROM tb_layanan WHERE id = :id");
        $cek->execute([':id' => $id]);
        $data_lama = $cek->fetch();

        if (!$data_lama) {
            echo json_encode(["status" => "error", "message" => "Data dengan ID $id tidak ditemukan."]);
            exit;
        }

        $foto_final = $nama_file_foto ? $nama_file_foto : $data_lama['foto'];
        $kode_final = !empty($kode_akses) ? sha1($kode_akses) : $data_lama['kode_akses'];

        $sql = "UPDATE tb_layanan SET 
                    kategori = :kategori,
                    judul_layanan = :judul_layanan,
                    deskripsi = :deskripsi,
                    harga = :harga,
                    durasi = :durasi,
                    kode_akses = :kode_akses,
                    foto = :foto
                WHERE id = :id";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':kategori'      => $kategori,
            ':judul_layanan' => $judul_layanan,
            ':deskripsi'     => $deskripsi,
            ':harga'         => $harga,
            ':durasi'        => $durasi,
            ':kode_akses'    => $kode_final,
            ':foto'          => $foto_final,
            ':id'            => $id
        ]);

        echo json_encode([
            "status"  => "success",
            "message" => "Data layanan ID $id berhasil diperbarui (UPDATE)."
        ]);

    } else {
        $foto_final = $nama_file_foto ? $nama_file_foto : "default.jpg";
        $kode_final = !empty($kode_akses) ? sha1($kode_akses) : sha1("12345");

        $sql = "INSERT INTO tb_layanan (kategori, judul_layanan, deskripsi, harga, durasi, kode_akses, foto) 
                VALUES (:kategori, :judul_layanan, :deskripsi, :harga, :durasi, :kode_akses, :foto)";

        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':kategori'      => $kategori,
            ':judul_layanan' => $judul_layanan,
            ':deskripsi'     => $deskripsi,
            ':harga'         => $harga,
            ':durasi'        => $durasi,
            ':kode_akses'    => $kode_final,
            ':foto'          => $foto_final
        ]);

        echo json_encode([
            "status"  => "success",
            "message" => "Data layanan baru berhasil ditambahkan (INSERT).",
            "id_baru" => $pdo->lastInsertId()
        ]);
    }
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode([
        "status"  => "error",
        "message" => "Terjadi kesalahan database: " . $e->getMessage()
    ]);
}
?>