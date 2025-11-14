<?php
require 'config.php'; requireAuth();
$m=$_SERVER['REQUEST_METHOD'];
if($m==='GET'){echo json_encode($pdo->query('SELECT * FROM bulan')->fetchAll());exit;}
if($m==='POST'){$d=input();$st=$pdo->prepare('INSERT INTO bulan(nama,start_date,end_date)VALUES(?,?,?)');$st->execute([$d['nama'],$d['start_date'],$d['end_date']]);jsonResponse(['success'=>true]);}
if($m==='DELETE'){$d=input();$pdo->prepare('DELETE FROM bulan WHERE id=?')->execute([$d['id']]);jsonResponse(['success'=>true]);}
