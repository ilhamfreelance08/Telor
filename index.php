<?php
require_once 'functions.php';

$data       = prosesForm();
$hargaModal = $data['hargaModal'];
$stokAwal   = $data['stokAwal'];
$rows       = $data['rows'];
$kemasan    = $data['kemasan'];

$ringkasan = hitungRingkasan($rows, $hargaModal, $kemasan, $stokAwal);

// Helper: render hidden inputs untuk mempertahankan state rows
function hiddenRows(array $rows): void {
    foreach ($rows as $i => $row) {
        echo '<input type="hidden" name="label[]"   value="' . htmlspecialchars($row['label'])   . '">';
        echo '<input type="hidden" name="qty[]"     value="' . $row['qty']                       . '">';
        echo '<input type="hidden" name="kemasan[]" value="' . htmlspecialchars($row['kemasan']) . '">';
    }
}

// Helper: render hidden inputs harga kemasan
function hiddenHargaKemasan(array $kemasan): void {
    foreach ($kemasan as $key => $k) {
        echo '<input type="hidden" name="hargaJual[' . $key . ']" value="' . $k['hargaJual'] . '">';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kalkulator Penjualan Telur</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="container">

  <header>
    <h1>Kalkulator Penjualan Telur</h1>
    <p>Penjualan satuan kg — tersedia kemasan 1 kg, ½ kg, dan ¼ kg</p>
  </header>

  <!-- ═══ FORM PENGATURAN (harga kemasan, modal, stok) ═══ -->
  <form method="POST">
    <?php hiddenRows($rows); ?>
    <input type="hidden" name="action" value="hitung">

    <div class="card">
      <div class="card-title">Pengaturan Harga</div>

      <!-- Harga jual per kemasan — bisa diedit -->
      <div class="kemasan-grid">
        <?php foreach ($kemasan as $key => $k): ?>
        <div class="kemasan-card">
          <div class="k-label"><?= htmlspecialchars($k['label']) ?></div>
          <div class="k-sub"><?= $k['berat'] * 1000 ?> gram</div>
          <div class="k-input-wrap">
            <span class="k-rp">Rp</span>
            <input type="number"
                   name="hargaJual[<?= $key ?>]"
                   value="<?= $k['hargaJual'] ?>"
                   min="0" step="500"
                   class="k-input"
                   onchange="this.form.submit()">
          </div>
        </div>
        <?php endforeach; ?>
      </div>

      <!-- Harga modal per kg -->
      <div class="field-row">
        <label>Harga modal per kg</label>
        <input type="number" name="hargaModal" value="<?= $hargaModal ?>" min="0" step="any"
               onchange="this.form.submit()">
        <span class="unit-text">Rp</span>
      </div>

      <!-- Stok awal -->
      <div class="field-row">
        <label>Total stok telur (kg)</label>
        <input type="number" name="stokAwal" value="<?= $stokAwal ?>" min="0" step="0.25"
               onchange="this.form.submit()">
        <span class="unit-text">kg</span>
      </div>
    </div>
  </form>

  <!-- ═══ FORM TRANSAKSI ═══ -->
  <form method="POST">
    <!-- Pertahankan pengaturan harga -->
    <input type="hidden" name="hargaModal" value="<?= $hargaModal ?>">
    <input type="hidden" name="stokAwal"   value="<?= $stokAwal ?>">
    <?php hiddenHargaKemasan($kemasan); ?>

    <div class="card">
      <div class="card-title">Rincian Transaksi</div>

      <div class="row-header">
        <span>Keterangan</span>
        <span>Kemasan</span>
        <span>Jumlah</span>
        <span></span>
      </div>

      <?php foreach ($rows as $i => $row): ?>
      <div class="transaksi-row">
        <input type="text"   name="label[]"   value="<?= htmlspecialchars($row['label']) ?>" placeholder="Keterangan">
        <select name="kemasan[]">
          <?php foreach ($kemasan as $key => $k): ?>
          <option value="<?= $key ?>" <?= $row['kemasan'] === $key ? 'selected' : '' ?>>
            <?= htmlspecialchars($k['label']) ?> — <?= rupiah($k['hargaJual']) ?>
          </option>
          <?php endforeach; ?>
        </select>
        <input type="number" name="qty[]" value="<?= $row['qty'] ?>" min="0" step="1" placeholder="0">
        <button type="submit" class="btn-hapus" name="action" value="hapus_<?= $i ?>" title="Hapus">&#10005;</button>
      </div>
      <?php endforeach; ?>

      <button type="submit" class="btn-tambah" name="action" value="tambah">
        + Tambah transaksi
      </button>
    </div>

    <div class="btn-wrap">
      <button type="submit" class="btn-hitung" name="action" value="hitung">Hitung</button>
    </div>
  </form>

  <?php require 'ringkasan.php'; ?>

</div>
</body>
</html>