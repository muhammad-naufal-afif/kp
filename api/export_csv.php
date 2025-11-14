<?php
require 'config.php'; requireAuth();
$bulan_id = $_GET['bulan_id'] ?? null;
if(!$bulan_id){http_response_code(400);echo "bulan_id required";exit;}
$stmt=$pdo->prepare("SELECT tanggal,keterangan,jumlah,harga,total FROM pendapatan WHERE bulan_id=?");
$stmt->execute([$bulan_id]);$pend=$stmt->fetchAll();
$stmt=$pdo->prepare("SELECT tanggal,keterangan,jumlah,harga,total FROM pengeluaran WHERE bulan_id=?");
$stmt->execute([$bulan_id]);$peng=$stmt->fetchAll();
$tp=array_reduce($pend,fn($s,$r)=>$s+$r['total'],0);
$tg=array_reduce($peng,fn($s,$r)=>$s+$r['total'],0);
header("Content-Type: text/csv; charset=UTF-8");
header("Content-Disposition: attachment; filename=rekap_$bulan_id.csv");
echo chr(0xEF).chr(0xBB).chr(0xBF);
$o=fopen('php://output','w');
fputcsv($o,['Total Pendapatan',$tp]);fputcsv($o,['Total Pengeluaran',$tg]);fputcsv($o,['Sisa',$tp-$tg]);
fputcsv($o,[]);fputcsv($o,['PENDAPATAN']);fputcsv($o,['Tanggal','Ket','Jumlah','Harga','Total']);
foreach($pend as $r)fputcsv($o,$r);
fputcsv($o,[]);fputcsv($o,['PENGELUARAN']);fputcsv($o,['Tanggal','Ket','Jumlah','Harga','Total']);
foreach($peng as $r)fputcsv($o,$r);
fclose($o);exit;
