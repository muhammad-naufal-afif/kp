<?php
require 'config.php';
$action=$_GET['action']??'';
if($action==='login'){
  $d=input();
  $u=$d['username']??'';$p=$d['password']??'';
  $st=$pdo->prepare('SELECT * FROM admin WHERE username=?');$st->execute([$u]);
  $ad=$st->fetch();
  if(!$ad||!password_verify($p,$ad['password_hash']))jsonResponse(['error'=>'Invalid credentials'],401);
  $_SESSION['admin_id']=$ad['id'];$_SESSION['username']=$ad['username'];
  jsonResponse(['success'=>true]);
}
if($action==='logout'){session_unset();session_destroy();jsonResponse(['success'=>true]);}
jsonResponse(['error'=>'Invalid action'],400);
