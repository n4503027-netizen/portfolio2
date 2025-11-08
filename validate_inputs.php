<?php
session_start();
require 'connection.php'; // تأكد من تضمين ملف الاتصال بقاعدة البيانات

// تفعيل عرض أخطاء SQL لمساعدتك في اكتشاف الأخطاء
$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

// التحقق من إرسال البيانات عبر النموذج
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // استقبال البيانات مع تنظيفها من المسافات الزائدة
    $idNumber = trim($_POST['idNumber']);
    $servicecode = trim($_POST['servicecode']);

    // طباعة القيم للتحقق من استقبالها
    echo "ID Number: " . htmlspecialchars($idNumber) . "<br>";
    echo "Service Code: " . htmlspecialchars($servicecode) . "<br>";

    try {
        // الاستعلام عن المستخدم في قاعدة البيانات
        $sql = "SELECT * FROM users WHERE idNumber = :idNumber AND servicecode = :servicecode";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':idNumber', $idNumber, PDO::PARAM_STR);
        $stmt->bindParam(':servicecode', $servicecode, PDO::PARAM_STR);
        $stmt->execute();

        // جلب بيانات المستخدم
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // حفظ بيانات المستخدم في الجلسة
            $_SESSION['currentUser'] = json_encode($user);
            echo "تم العثور على المستخدم بنجاح.";
            header("Location: data-display.php");
            exit();
        } else {
            $_SESSION['errorMessage'] = "لايوجد نتائج.";
            echo "لم يتم العثور على المستخدم.";
            header("Location: index.php");
        }
    } catch (PDOException $e) {
        echo "خطأ في الاستعلام: " . $e->getMessage();
    }
} else {
    echo "طلب غير صالح.";
}
$conn = null; // إغلاق الاتصال
?>
