<?php

/**
 * Format angka ke format Rupiah
 */
function rupiah(float $n): string {
    return 'Rp ' . number_format($n, 0, ',', '.');
}

/**
 * Harga jual default per kemasan
 */
function defaultHargaKemasan(): array {
    return [
        '1kg'    => 30000,
        '0.5kg'  => 16000,
        '0.25kg' => 9000,
    ];
}

/**
 * Bangun daftar kemasan — hargaJual bisa di-override lewat array $hargaOverride
 */
function daftarKemasan(array $hargaOverride = []): array {
    $d = defaultHargaKemasan();
    return [
        '1kg'    => ['label' => '1 kg', 'berat' => 1.00,  'hargaJual' => max(0, floatval($hargaOverride['1kg']    ?? $d['1kg']))],
        '0.5kg'  => ['label' => '½ kg', 'berat' => 0.50,  'hargaJual' => max(0, floatval($hargaOverride['0.5kg']  ?? $d['0.5kg']))],
        '0.25kg' => ['label' => '¼ kg', 'berat' => 0.25,  'hargaJual' => max(0, floatval($hargaOverride['0.25kg'] ?? $d['0.25kg']))],
    ];
}

/**
 * Proses input POST dan kembalikan data terstruktur
 */
function prosesForm(): array {
    $hargaModal = 25000;
    $stokAwal   = 10;
    $rows       = defaultRows();
    $kemasan    = daftarKemasan();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $hargaModal = floatval($_POST['hargaModal'] ?? 25000);
        $stokAwal   = floatval($_POST['stokAwal']   ?? 10);

        // Harga jual per kemasan bisa diubah
        $hargaJualPost = $_POST['hargaJual'] ?? [];
        $kemasan = daftarKemasan($hargaJualPost);

        $labels = $_POST['label']   ?? [];
        $qtys   = $_POST['qty']     ?? [];
        $sizes  = $_POST['kemasan'] ?? [];

        $rows = [];
        foreach ($labels as $i => $label) {
            $size   = isset($kemasan[$sizes[$i]]) ? $sizes[$i] : '1kg';
            $rows[] = [
                'label'   => htmlspecialchars(trim($label)),
                'qty'     => floatval($qtys[$i] ?? 0),
                'kemasan' => $size,
            ];
        }

        $action = $_POST['action'] ?? '';

        if ($action === 'tambah') {
            $rows[] = [
                'label'   => 'Transaksi ' . (count($rows) + 1),
                'qty'     => 0,
                'kemasan' => '1kg',
            ];
        }

        if (strpos($action, 'hapus_') === 0) {
            $idx = intval(substr($action, 6));
            array_splice($rows, $idx, 1);
        }
    }

    return compact('hargaModal', 'stokAwal', 'rows', 'kemasan');
}

/**
 * Hitung ringkasan dari data penjualan per kemasan
 */
function hitungRingkasan(array $rows, float $hargaModal, array $kemasan, float $stokAwal = 10): array {
    $totalPemasukan = 0;
    $totalBeratKg   = 0;

    $detailKemasan = [];
    foreach ($kemasan as $key => $k) {
        $detailKemasan[$key] = [
            'label'   => $k['label'],
            'qty'     => 0,
            'nilai'   => 0,
            'beratKg' => 0,
        ];
    }

    foreach ($rows as $row) {
        $key   = $row['kemasan'];
        $k     = $kemasan[$key];
        $nilai = $row['qty'] * $k['hargaJual'];
        $berat = $row['qty'] * $k['berat'];

        $totalPemasukan += $nilai;
        $totalBeratKg   += $berat;

        $detailKemasan[$key]['qty']     += $row['qty'];
        $detailKemasan[$key]['nilai']   += $nilai;
        $detailKemasan[$key]['beratKg'] += $berat;
    }

    $totalModal = $totalBeratKg * $hargaModal;
    $keuntungan = $totalPemasukan - $totalModal;
    $margin     = $totalPemasukan > 0 ? ($keuntungan / $totalPemasukan * 100) : 0;
    $sisaStok   = $stokAwal - $totalBeratKg;

    return compact(
        'totalPemasukan', 'totalBeratKg',
        'totalModal', 'keuntungan', 'margin',
        'detailKemasan', 'stokAwal', 'sisaStok'
    );
}

/**
 * Data default saat pertama kali dibuka
 */
function defaultRows(): array {
    return [
        ['label' => 'Pagi',  'qty' => 10, 'kemasan' => '1kg'],
        ['label' => 'Siang', 'qty' => 5,  'kemasan' => '0.5kg'],
        ['label' => 'Sore',  'qty' => 4,  'kemasan' => '0.25kg'],
    ];
}