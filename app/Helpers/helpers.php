<?php

if (! function_exists('formatRupiah')) {
    /**
     * Format angka menjadi standar mata uang Rupiah.
     *
     * @param float|int|string $nominal
     * @param bool $denganPrefix Apakah menggunakan 'Rp ' di depan
     * @param int $desimal Jumlah angka di belakang koma (default: 0)
     * @return string
     */
    function formatRupiah($nominal, $denganPrefix = true, $desimal = 0)
    {
        // Pastikan nilai yang masuk adalah angka (jika null, jadikan 0)
        $nominal = is_numeric($nominal) ? (float) $nominal : 0;

        // Gunakan number_format bawaan PHP
        // Format: number_format(angka, jumlah_desimal, pemisah_desimal, pemisah_ribuan)
        $hasil = number_format($nominal, $desimal, ',', '.');

        return $denganPrefix ? 'Rp ' . $hasil : $hasil;
    }
}