<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
$DB_HOST='127.0.0.1';$DB_NAME='keuangan_db';$DB_USER='root';$DB_PASS='';
try{$pdo=new PDO("mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4",$DB_USER,$DB_PASS,[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION,PDO::ATTR_DEFAULT_FETCH_MODE=>PDO::FETCH_ASSOC]);}catch(PDOException $e){http_response_code(500);echo json_encode(['error'=>'Database connection failed']);exit;}
function jsonResponse($d,$c=200){http_response_code($c);echo json_encode($d);exit;}
function requireAuth(){if(empty($_SESSION['admin_id']))jsonResponse(['error'=>'Unauthorized'],401);}
function input(){$raw=file_get_contents('php://input');$d=json_decode($raw,true);return $d??$_POST;}
