<?php
// اسم ملف قاعدة البيانات
$dbname = 'seha.sqlite';

try {
    // الاتصال بقاعدة بيانات SQLite باستخدام PDO
    $conn = new PDO("sqlite:$dbname");
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // تعيين الترميز إلى UTF-8
    $conn->exec('PRAGMA encoding = "UTF-8";');

} catch (PDOException $e) {
    die("فشل الاتصال بقاعدة البيانات: " . $e->getMessage());
}
?>