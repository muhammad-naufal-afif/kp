<?php
require 'config.php';
requireAuth();

$bulan_id = $_GET['bulan_id'] ?? null;
if (!$bulan_id) {
  http_response_code(400);
  echo "bulan_id required";
  exit;
}

// fetch data
$stmt = $pdo->prepare("SELECT nama,start_date,end_date FROM bulan WHERE id = ?");
$stmt->execute([$bulan_id]);
$bulan_info = $stmt->fetch();
$nama_bulan = $bulan_info['nama'] ?? "Bulan-$bulan_id";

$stmt = $pdo->prepare("SELECT tanggal,keterangan,jumlah,harga,total FROM pendapatan WHERE bulan_id = ? ORDER BY tanggal");
$stmt->execute([$bulan_id]); $pendapatan = $stmt->fetchAll();

$stmt = $pdo->prepare("SELECT tanggal,keterangan,jumlah,harga,total FROM pengeluaran WHERE bulan_id = ? ORDER BY tanggal");
$stmt->execute([$bulan_id]); $pengeluaran = $stmt->fetchAll();

$total_pend = 0.0; foreach($pendapatan as $r) $total_pend += floatval($r['total']);
$total_peng = 0.0; foreach($pengeluaran as $r) $total_peng += floatval($r['total']);
$sisa = $total_pend - $total_peng;

// build HTML report
$html = '<!doctype html><html><head><meta charset="utf-8"><style>
body{font-family:Arial,Helvetica,sans-serif;color:#222}
.header{display:flex;justify-content:space-between;align-items:center}
.h1{font-size:20px;color:#0f9d58}
.table{width:100%;border-collapse:collapse;margin-top:12px}
.table th, .table td{border:1px solid #ddd;padding:8px;font-size:12px}
.summary{margin-top:12px}
.kpi{display:inline-block;padding:8px 12px;border-radius:8px;background:#f7fff7;margin-right:8px}
</style></head><body>';
$html .= '<div class="header"><div><div class="h1">Rekap Bulanan</div><div>'.$nama_bulan.'</div></div><div><small>Generated: '.date('Y-m-d H:i').'</small></div></div>';
$html .= '<div class="summary"><div class="kpi">Total Pendapatan: Rp '.number_format($total_pend,2,',','.').'</div>';
$html .= '<div class="kpi">Total Pengeluaran: Rp '.number_format($total_peng,2,',','.').'</div>';
$html .= '<div class="kpi">Sisa: Rp '.number_format($sisa,2,',','.').'</div></div>';

// pendapatan table
$html .= '<h3>Pendapatan</h3><table class="table"><thead><tr><th>Tanggal</th><th>Keterangan</th><th>Jumlah</th><th>Harga</th><th>Total</th></tr></thead><tbody>';
foreach($pendapatan as $r){
  $html .= '<tr><td>'.$r['tanggal'].'</td><td>'.$r['keterangan'].'</td><td>'.$r['jumlah'].'</td><td>'.number_format($r['harga'],2,',','.').'</td><td>'.number_format($r['total'],2,',','.').'</td></tr>';
}
$html .= '</tbody></table>';

// pengeluaran table
$html .= '<h3>Pengeluaran</h3><table class="table"><thead><tr><th>Tanggal</th><th>Keterangan</th><th>Jumlah</th><th>Harga</th><th>Total</th></tr></thead><tbody>';
foreach($pengeluaran as $r){
  $html .= '<tr><td>'.$r['tanggal'].'</td><td>'.$r['keterangan'].'</td><td>'.$r['jumlah'].'</td><td>'.number_format($r['harga'],2,',','.').'</td><td>'.number_format($r['total'],2,',','.').'</td></tr>';
}
$html .= '</tbody></table>';

$html .= '</body></html>';

// try to generate PDF with Dompdf if available
$canPdf = false;
if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
    require_once __DIR__ . '/../vendor/autoload.php';
    if (class_exists('\\Dompdf\\Dompdf')) {
        $canPdf = true;
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4','portrait');
        $dompdf->render();
        $filename = 'rekap_'.$bulan_id.'_'.date('Ymd').'.pdf';
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="'.$filename.'"');
        echo $dompdf->output();
        exit;
    }
}

// fallback: output HTML (printable) and show notice
header('Content-Type: text/html; charset=utf-8');
echo '<div style="padding:12px;background:#fefefe;border:1px solid #eee;margin:12px;font-family:Arial,Helvetica,sans-serif">';
if (!$canPdf) {
  echo '<p><strong>PDF engine tidak ditemukan.</strong> Untuk menghasilkan file PDF otomatis, install <code>dompdf</code> via Composer:</p>
  <pre>composer require dompdf/dompdf</pre>
  <p>Setelah itu, letakkan folder <code>vendor</code> di root project sehingga <code>vendor/autoload.php</code> dapat ditemukan, lalu panggil kembali endpoint ini.</p>';
}
echo '<p><a href="?bulan_id='.$bulan_id.'&preview=1">Tampilan HTML report (ini)</a></p>';
echo '</div>';
echo $html;
exit;
?>