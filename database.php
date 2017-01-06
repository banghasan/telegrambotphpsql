<?php

/*
* Ebook II: Telegram Bot PHP dan Database SQL
* oleh bang Hasan ( @hasanudinhs )
*
* Fungsi Database untuk Diary Bot Telegram
*
*/

// masukkan database framework nya
require_once 'medoo.php';

// koneksikan ke database

// ini contoh menggunakan SQLite
    $database = new medoo([
        'database_type' => 'sqlite',
        'database_file' => 'diary.db',
    ]);

// uncomment ini jika menggunakan mySQL atau mariaDB
// sesuaikan nama database, host, user, dan passwordnya
/*
    $database = new medoo([
        'database_type' => 'mysql',
        'database_name' => 'diary',
        'server' => 'localhost',
        'username' => 'root',
        'password' => 'rahasiakusendiri',
        'charset' => 'utf8'
    ]);
*/

// fungsi untuk menambah diary
function diarytambah($iduser, $pesan)
{
    global $database;
    $last_id = $database->insert('catatan', [
        'id'    => $iduser,
        'waktu' => date('Y-m-d H:i:s').' WIB',
        'pesan' => $pesan,
    ]);

    return $last_id;
}

// fungsi menghapus diary
function diaryhapus($iduser, $idpesan)
{
    global $database;
    $database->delete('catatan', [
        'AND' => [
            'id' => $iduser,
            'no' => $idpesan,
        ],
    ]);

    return '⛔️ telah dilaksanakan..';
}

// fungsi melihat daftar diary user
function diarylist($iduser, $page = 0)
{
    global $database;
    $hasil = '😢 Maaf ya, tidak ada catatan diary di hatiku..';
    $datas = $database->select('catatan', [
        'no',
        'id',
        'waktu',
        'pesan',
    ], [
        'id' => $iduser,
    ]);
    $jml = count($datas);
    if ($jml > 0) {
        $hasil = "✍🏽 *$jml Catatan Diary-mu Kusimpan Rapi Dihati:*\n";
        $n = 0;
        foreach ($datas as $data) {
            $n++;
            $hasil .= "\n$n. ".substr($data['pesan'], 0, 10)."...\n⌛️ `$data[waktu]`\n";
            $hasil .= "\n👀 /view\_$data[no]\n";
        }
    }

    return $hasil;
}

// fungsi melihat isi pesan diary
function diaryview($iduser, $idpesan)
{
    global $database;
    $hasil = "😢 Maaf ya, diarymu yang itu tidak ditemukan dihatiku.\nMungkin saja bukan buatmu..";
    $datas = $database->select('catatan', [
        'no',
        'id',
        'waktu',
        'pesan',
    ], [
        'AND' => [
            'id' => $iduser,
            'no' => $idpesan,
        ],
    ]);
    $jml = count($datas);
    if ($jml > 0) {
        $data = $datas[0];
        $hasil = "✍🏽 Diary nomor $data[no] yang tersimpan dihatiku berisi:\n~~~~~~~~~~~~~~~~~~~~~~~\n";
        $hasil .= "\n$data[pesan]\n\n⌛️ `$data[waktu]`";
        $hasil .= "\n\n📛 Hapus? /hapus\_$data[no]";
    }

    return $hasil;
}

// fungsi mencari pesan di diary
function diarycari($iduser, $pesan)
{
    global $database;
    $hasil = '😢 Maaf ya, apa yang kau cari selama ini tidak ditemukan..';
    $datas = $database->select('catatan', [
        'no',
        'id',
        'waktu',
        'pesan',
    ], [
        'pesan[~]' => $pesan,
    ]);
    $jml = count($datas);
    if ($jml > 0) {
        $hasil = "✍🏽 *$jml Catatan Diary-mu yang kau cari selalu kusimpan di hatiku*\n";
        $n = 0;
        foreach ($datas as $data) {
            $n++;
            $hasil .= "\n$n. ".substr($data['pesan'], 0, 10)."...\n⌛️ `$data[waktu]`\n";
            $hasil .= "\n👀 /view\_$data[no]\n";
        }
    }

    return $hasil;
}
