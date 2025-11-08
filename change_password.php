<?php
session_start();

// تفعيل عرض الأخطاء للتdebugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require 'connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_password'], $_POST['confirm_password'])) {
    try {
        // التحقق من تطابق كلمتي السر
        if ($_POST['new_password'] !== $_POST['confirm_password']) {
            throw new Exception("كلمتا السر غير متطابقتين");
        }

        $new_password = $_POST['new_password'];
        
        // التحقق من قوة كلمة السر (اختياري)
        if (strlen($new_password) < 8) {
            throw new Exception("كلمة السر يجب أن تكون 8 أحرف على الأقل");
        }

        // تحديث كلمة السر في قاعدة البيانات
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE admin_passwords SET password = ? WHERE id = 1";
        $stmt = $conn->prepare($sql);
        $stmt->execute([$hashed_password]);
        
        // تحديث ملف insert.php بطريقة أكثر أماناً
        $file_path = __DIR__ . '/insert.php';
        if (!file_exists($file_path)) {
            throw new Exception("ملف insert.php غير موجود");
        }

        $file_content = file_get_contents($file_path);
        $new_content = preg_replace(
            "/define\('SECRET_ACCESS_CODE',\s*'.*?'\)/",
            "define('SECRET_ACCESS_CODE', '" . $new_password . "')",
            $file_content
        );

        if (file_put_contents($file_path, $new_content) === false) {
            throw new Exception("تعذر تحديث ملف insert.php");
        }
        
        $_SESSION['password_changed'] = true;
        header("Location: insert.php");
        exit();

    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تغيير كلمة السر</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; font-family: 'Tahoma', Arial, sans-serif; }
        .login-container { max-width: 400px; margin: 50px auto; padding: 20px; }
        .card { border-radius: 10px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); border: none; }
        .card-header { 
            background: linear-gradient(135deg, #306db5 0%, #1e4b8f 100%);
            color: white;
            border-radius: 10px 10px 0 0 !important;
            padding: 15px;
            text-align: center;
            font-weight: bold;
        }
        .form-control:focus { border-color: #306db5; box-shadow: 0 0 0 0.25rem rgba(48, 109, 181, 0.25); }
        .btn-primary { 
            background-color: #306db5;
            border: none;
            padding: 10px;
            font-weight: bold;
        }
        .btn-primary:hover { background-color: #1e4b8f; }
        .alert { margin-top: 20px; }
    </style>
</head>
<body>
    <div class="container login-container">
        <div class="card">
            <div class="card-header">
                <h4>تغيير كلمة المرور</h4>
            </div>
            <div class="card-body">
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <form method="POST" onsubmit="return validatePassword()">
                    <div class="mb-3">
                        <label for="new_password" class="form-label">كلمة المرور الجديدة:</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" required minlength="8">
                        <div class="invalid-feedback">يجب أن تكون كلمة المرور 8 أحرف على الأقل</div>
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">تأكيد كلمة المرور:</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        <div class="invalid-feedback">كلمتا المرور غير متطابقتين</div>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">تغيير كلمة المرور</button>
                </form>
            </div>
        </div>
    </div>

    <script>
        function validatePassword() {
            const password = document.getElementById('new_password').value;
            const confirm = document.getElementById('confirm_password').value;
            
            if (password !== confirm) {
                alert('كلمتا المرور غير متطابقتين');
                return false;
            }
            
            if (password.length < 8) {
                alert('يجب أن تكون كلمة المرور 8 أحرف على الأقل');
                return false;
            }
            
            return true;
        }
    </script>
</body>
</html>