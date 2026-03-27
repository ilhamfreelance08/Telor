<?php
// Partial view ringkasan.
// Variabel dari index.php: $rows, $hargaModal, $kemasan, $ringkasan

extract($ringkasan);
$profitClass  = $keuntungan >= 0 ? 'profit'       : 'loss';
$badgeClass   = $keuntungan >= 0 ? 'badge-profit' : 'badge-loss';
$badgeText    = $keuntungan >= 0 ? 'Untung'       : 'Rugi';
$sisaClass    = $sisaStok  >= 0  ? 'badge-profit' : 'badge-loss';
?>

<div class="card">
  <div class="card-title">Ringkasan</div>

  <!-- Metric cards -->
  <div class="metrics">
    <div class="metric">
      <div class="metric-label">Stok awal</div>
      <div class="metric-value">
        <?= number_format($stokAwal, 2, ',', '.') ?>
        <span style="font-size:14px;font-weight:400;">kg</span>
      </div>
    </div>

    <div class="metric">
      <div class="metric-label">Terjual</div>
      <div class="metric-value">
        <?= number_format($totalBeratKg, 2, ',', '.') ?>
        <span style="font-size:14px;font-weight:400;">kg</span>
      </div>
    </div>

    <div class="metric">
      <div class="metric-label">
        Sisa stok
        <span class="badge <?= $sisaClass ?>"><?= $sisaStok >= 0 ? 'Aman' : 'Lebih jual' ?></span>
      </div>
      <div class="metric-value <?= $sisaStok >= 0 ? 'profit' : 'loss' ?>">
        <?= number_format(abs($sisaStok), 2, ',', '.') ?>
        <span style="font-size:14px;font-weight:400;">kg</span>
      </div>
    </div>

    <div class="metric">
      <div class="metric-label">Pemasukan</div>
      <div class="metric-value"><?= rupiah($totalPemasukan) ?></div>
    </div>

    <div class="metric">
      <div class="metric-label">Modal</div>
      <div class="metric-value"><?= rupiah($totalModal) ?></div>
    </div>

    <div class="metric">
      <div class="metric-label">
        Keuntungan
        <span class="badge <?= $badgeClass ?>"><?= $badgeText ?></span>
      </div>
      <div class="metric-value <?= $profitClass ?>">
        <?= rupiah($keuntungan) ?>
      </div>
    </div>
  </div>

  <hr class="divider">

  <!-- Tabel rekapitulasi per kemasan -->
  <p style="font-size:12px;font-weight:500;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:8px;">
    Rekap per kemasan
  </p>
  <div class="table-wrap" style="margin-bottom:1.25rem;">
    <table>
      <thead>
        <tr>
          <th>Kemasan</th>
          <th class="td-right">Harga jual</th>
          <th class="td-right">Terjual (bungkus)</th>
          <th class="td-right">Berat (kg)</th>
          <th class="td-right">Nilai (Rp)</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($detailKemasan as $key => $dk): ?>
        <?php if ($dk['qty'] > 0): ?>
        <tr>
          <td><span class="badge badge-kemasan"><?= htmlspecialchars($dk['label']) ?></span></td>
          <td class="td-right"><?= rupiah($kemasan[$key]['hargaJual']) ?></td>
          <td class="td-right"><?= number_format($dk['qty'], 0, ',', '.') ?></td>
          <td class="td-right"><?= number_format($dk['beratKg'], 2, ',', '.') ?></td>
          <td class="td-right"><?= rupiah($dk['nilai']) ?></td>
        </tr>
        <?php endif; ?>
        <?php endforeach; ?>

        <tr class="total">
          <td colspan="3">Total</td>
          <td class="td-right"><?= number_format($totalBeratKg, 2, ',', '.') ?> kg</td>
          <td class="td-right"><?= rupiah($totalPemasukan) ?></td>
        </tr>
      </tbody>
    </table>
  </div>

  <hr class="divider">

  <!-- Tabel detail transaksi -->
  <p style="font-size:12px;font-weight:500;color:var(--muted);text-transform:uppercase;letter-spacing:.05em;margin-bottom:8px;">
    Detail transaksi
  </p>
  <div class="table-wrap">
    <table>
      <thead>
        <tr>
          <th>Keterangan</th>
          <th class="td-right">Kemasan</th>
          <th class="td-right">Jumlah</th>
          <th class="td-right">Nilai (Rp)</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $row): ?>
        <?php $k = $kemasan[$row['kemasan']]; ?>
        <tr>
          <td><?= htmlspecialchars($row['label']) ?></td>
          <td class="td-right"><span class="badge badge-kemasan"><?= htmlspecialchars($k['label']) ?></span></td>
          <td class="td-right"><?= number_format($row['qty'], 0, ',', '.') ?> bungkus</td>
          <td class="td-right"><?= rupiah($row['qty'] * $k['hargaJual']) ?></td>
        </tr>
        <?php endforeach; ?>

        <tr class="total">
          <td colspan="3">Total pemasukan</td>
          <td class="td-right"><?= rupiah($totalPemasukan) ?></td>
        </tr>

        <tr>
          <td style="color:var(--muted)">Modal (<?= rupiah($hargaModal) ?>/kg × <?= number_format($totalBeratKg, 2, ',', '.') ?> kg)</td>
          <td colspan="2"></td>
          <td class="td-right" style="color:var(--muted)">- <?= rupiah($totalModal) ?></td>
        </tr>

        <tr class="total">
          <td>Keuntungan bersih</td>
          <td colspan="2" class="td-right">
            <span class="badge <?= $badgeClass ?>">
              <?= number_format(abs($margin), 1, ',', '.') ?>% margin
            </span>
          </td>
          <td class="td-right <?= $profitClass ?>"><?= rupiah($keuntungan) ?></td>
        </tr>
      </tbody>
    </table>
  </div>
</div>