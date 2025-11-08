<?php
// بدء الجلسة
session_start();

// تدمير جميع بيانات الجلسة
$_SESSION = array();

// إذا كنت تريد حذف الكوكي تماماً
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), 
        '', 
        time() - 42000,
        $params["path"], 
        $params["domain"],
        $params["secure"], 
        $params["httponly"]
    );
}

// تدمير الجلسة
session_destroy();

// التوجيه إلى الصفحة الرئيسية
header("Location: index.php");
exit();
?>