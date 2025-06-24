<?php
// Lokasi file .htaccess
$original = '/home/nrpegeedu/public_html/.htaccess';
$backup = '/home/nrpegeedu/public_html/.htaccess_bk';

// Periksa apakah file .htaccess ada
if (file_exists($original)) {
    // Coba rename
    if (rename($original, $backup)) {
        echo "File berhasil di-rename menjadi .htaccess_bk.";
    } else {
        echo "Gagal merename file. Periksa izin file/folder.";
    }
} else {
    echo "File .htaccess tidak ditemukan.";
}
?>
