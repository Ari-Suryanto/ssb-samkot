<?php
require 'vendor/autoload.php' ;
use PhpOffice\PhpWord\TemplateProcessor ;

// Koneksi ke database
$host = "localhost";
$user = "root";
$pass = "";
$db = "ssb_samkot";

$conn = new mysqli($host, $user, $pass, $db);

// Cek koneksi
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}




// Ambil data dari form
if($_SERVER["REQUEST_METHOD"] == "POST") {
$nama = $_POST['nama'];
$tempatLahir = $_POST['tempatLahir'];
$tanggalLahir = $_POST['tanggalLahir'];
$tanggalFormatted = date('d-m-Y', strtotime($tanggalLahir));
$alamat = $_POST['alamat'];
$namaOrtu = $_POST['namaOrtu'];
$noHp = $_POST['noHp'];
$pekerjaan = $_POST['pekerjaan'];
$posisi = $_POST['posisi'];
$tinggi = $_POST['tinggi'];
$berat = $_POST['berat'];
$penyakit = $_POST['penyakit'];
$cidera = $_POST['cidera'];
$alumni = $_POST['alumni'];
$prestasi = $_POST['prestasi'];

$fotoName = $_FILES ['foto'] ['name'] ;
$fotoTmp = $_FILES ['foto'] ['tmp_name'] ;
$folderUpload = __DIR__. DIRECTORY_SEPARATOR.'/uploads/';
$fotoPath = $folderUpload . $fotoName ;


 if (!is_dir($folderUpload)) {
        mkdir($folderUpload, 0777, true);
    }
move_uploaded_file($fotoTmp, $fotoPath);

}
       
// Simpan ke database
$sql = "INSERT INTO pendaftaran (nama, tempatLahir, tanggalLahir, alamat, namaOrtu, noHp, pekerjaan, posisi, tinggi, berat, penyakit, cidera, alumni, prestasi)
        VALUES ('$nama', '$tempatLahir', '$tanggalLahir', '$alamat', '$namaOrtu', '$noHp', '$pekerjaan', '$posisi', '$tinggi', '$berat', '$penyakit', '$cidera', '$alumni', '$prestasi')";

if ($conn->query($sql) === TRUE) {
$template = new TemplateProcessor ('template-formulir.docx');
$template->setValue ('nama',$nama);
$template->setValue ('tempatLahir',$tempatLahir);
$template->setValue ('tanggalLahir',$tanggalFormatted);
$template->setValue ('alamat',$alamat);
$template->setValue ('namaOrtu',$namaOrtu);
$template->setValue ('noHp',$noHp);
$template->setValue ('pekerjaan',$pekerjaan);
$template->setValue ('posisi',$posisi);
$template->setValue ('tinggi',$tinggi);
$template->setValue ('berat',$berat);
$template->setValue ('penyakit',$penyakit);
$template->setValue ('cidera',$cidera);
$template->setValue ('alumni',$alumni);
$template->setValue ('prestasi',$prestasi);

$outputFile = "Formulir_".$nama. ".docx";
$template->saveAs ($outputFile);

$pdfFile = "Formulir_" . $nama . ".pdf";
$command = "soffice --headless --convert-to pdf --outdir . $outputFile";
exec($command);

$pdfFile = "Formulir_" . $nama . ".pdf";
$command = 'soffice --headless --convert-to pdf --outdir . "' . $outputFile. '"';
exec($command);

// Kirim file PDF ke user
if (file_exists($pdfFile)) {
    header("Content-Disposition: attachment; filename=$pdfFile");
    header("Content-Type: application/pdf");
    readfile($pdfFile);

    // Hapus file sementara
    unlink($outputFile); // hapus file Word
    unlink($pdfFile);    // hapus file PDF setelah dikirim
} else {
    echo "Gagal mengonversi file ke PDF.";
    }
}

$conn->close();
?>