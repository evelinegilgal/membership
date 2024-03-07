<?php
date_default_timezone_set('Asia/Jakarta');
// Koneksi ke database
$host = 'localhost';
$user = 'root'; // Ganti dengan username MySQL Anda
$password = ''; // Ganti dengan password MySQL Anda
$database = 'gilgal';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Ambil data dari form
$nama = $_POST['nama'];
$jenis_kelamin = $_POST['jenis_kelamin'];
$tempat_lahir = $_POST['tempat_lahir'];
$tanggal_lahir = $_POST['tanggal_lahir'];
$nomor_wa = $_POST['negara'] . $_POST['nomor_wa']; // Gabungkan kode negara dengan nomor WA
// Periksa apakah nomor WA hanya berisi angka
if (!preg_match('/^[0-9]+$/', $_POST['nomor_wa'])) {
    echo "Registrasi gagal. Nomor WA hanya boleh berisi angka.";
    exit();
}


$email = $_POST['email'];
$registration_datetime = date('Y-m-d H:i:s');

// Periksa apakah nomor WA sudah terdaftar sebelumnya
$stmt_check = $conn->prepare("SELECT COUNT(*) AS total FROM membership_class WHERE nomor_wa = ?");
$stmt_check->bind_param("s", $nomor_wa);
$stmt_check->execute();
$result = $stmt_check->get_result();
$row = $result->fetch_assoc();
$total_existing = $row['total'];

if ($total_existing > 0) {
    echo "Registrasi gagal. Nomor WA sudah terdaftar.";
    exit(); // Hentikan proses registrasi jika nomor WA sudah terdaftar
}

// Prepared statement untuk mencegah SQL Injection
$stmt = $conn->prepare("INSERT INTO membership_class (nama, jenis_kelamin, tempat_lahir, tanggal_lahir, nomor_wa, email, registration_datetime) VALUES (?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("sssssss", $nama, $jenis_kelamin, $tempat_lahir, $tanggal_lahir, $nomor_wa, $email, $registration_datetime);

if ($stmt->execute()) {
    echo "success"; // Kirim respons "success" jika registrasi berhasil
} else {
    echo "Error: " . $stmt->error;
}

// Tutup koneksi ke database
$stmt->close();
$conn->close();
?>
