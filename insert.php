<?php
session_start();

// الاتصال بقاعدة البيانات
require 'connection.php';

// الرمز السري - غيره ليكون خاصًا بك (يجب أن يكون معقدًا)
define('SECRET_ACCESS_CODE', 'hazemkha');

// التحقق من الصلاحية قبل تنفيذ أي شيء
if (!isset($_SESSION['authenticated']) || $_SESSION['authenticated'] !== true) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['access_code'])) {
        if ($_POST['access_code'] === SECRET_ACCESS_CODE) {
            $_SESSION['authenticated'] = true;
            $_SESSION['last_activity'] = time();
            header("Location: insert.php");
            exit();
        } elseif ($_POST['access_code'] === '123') {
            // إذا كان الرمز المدخل هو 123، توجيه إلى صفحة تغيير كلمة السر
            header("Location: change_password.php");
            exit();
        } else {
            $error = "⚠️ رمز الدخول غير صحيح";
        }
    }
    
    // عرض واجهة تسجيل الدخول
    ?>
    <!DOCTYPE html>
    <html lang="ar" dir="rtl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>نظام الدخول الآمن</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            body { background-color: #f8f9fa; }
            .login-container { max-width: 400px; margin: 100px auto; }
            .card { border-radius: 15px; box-shadow: 0 6px 15px rgba(0,0,0,0.1); }
            .card-header { background-color: #306db5; color: white; border-radius: 15px 15px 0 0 !important; }
        </style>
    </head>
    <body>
        <div class="container login-container">
            <div class="card">
                <div class="card-header text-center">
                    <h4>الدخول للنظام</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label for="access_code" class="form-label">الرمز السري:</label>
                            <input type="password" class="form-control" id="access_code" name="access_code" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">تسجيل الدخول</button>
                    </form>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit();
}

// التحقق من وقت النشاط (جلسة تنتهي بعد 30 دقيقة)
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 1800)) {
    session_unset();
    session_destroy();
    header("Location: insert.php");
    exit();
}
$_SESSION['last_activity'] = time();

// ============= الكود الأصلي للاتصال والإدراج =============
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['servicecode'])) {
    try {
        // تنظيف المدخلات
        $inputs = [
            'servicecode' => htmlspecialchars($_POST['servicecode']),
            'idNumber' => htmlspecialchars($_POST['idNumber']),
            'name' => htmlspecialchars($_POST['name']),
            'issueDate' => htmlspecialchars($_POST['issueDate']),
            'startDate' => htmlspecialchars($_POST['startDate']),
            'endDate' => htmlspecialchars($_POST['endDate']),
            'duration' => htmlspecialchars($_POST['duration']),
            'doctor' => htmlspecialchars($_POST['doctor']),
            'jobTitle' => htmlspecialchars($_POST['jobTitle'])
        ];

        // إدخال البيانات مع Prepared Statements
        $sql = "INSERT INTO users (servicecode, idNumber, name, issueDate, startDate, endDate, duration, doctor, jobTitle)
                VALUES (:servicecode, :idNumber, :name, :issueDate, :startDate, :endDate, :duration, :doctor, :jobTitle)";

        $stmt = $conn->prepare($sql);
        $stmt->execute($inputs);

        echo '<div class="alert alert-success mt-4">تم إضافة البيانات بنجاح</div>';
    } catch (PDOException $e) {
        error_log("Database Error: " . $e->getMessage());
        echo '<div class="alert alert-danger mt-4">حدث خطأ تقني، يرجى المحاولة لاحقًا</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>إضافة بيانات جديدة</title>
  <link rel="icon" href="/images/tr.png" type="image/png"> 
  <link rel="icon" href="/images/tr.png" type="image/x-icon">
  <link rel="stylesheet" href="css/normalize.css">
  <link rel="stylesheet" href="css/all.min.css">
  <link rel="stylesheet" href="css/social_links.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
  <div class="header">
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
      <div class="container-fluid">
        <a class="navbar-brand" href="index.php"><img src="./images/se.png" alt="logo" height="40px"></a>
        <button class="navbar-toggler" type="button" style="border: none; outline: none; box-shadow: none;" data-bs-toggle="collapse" data-bs-target="#navbarNavAltMarkup" aria-controls="navbarNavAltMarkup" aria-expanded="false" aria-label="Toggle navigation">
          <i class="icon fa-solid fa-bars"></i>
        </button>
        <div class="collapse navbar-collapse" id="navbarNavAltMarkup">
          <div class="navbar-nav text-center">
            <a class="nav-link active" aria-current="page" href="#">الخدمات</a>
            <a class="nav-link" href="#">الاستعلامات</a>
            <a class="nav-link" href="#">انشاء حساب</a>
            <a class="nav-link" href="#">تسجيل دخول</a>
          </div>
        </div>
      </div>
    </nav>
  </div>
  <div class="container">
    <h1 class="h1">
      <span class="page-title">إضافة بيانات جديدة</span>
    </h1>
    <form method="POST" action="insert.php">
      <div class="input-container">
        <input type="text" id="servicecode" name="servicecode" class="page-input" placeholder="رقم الخدمة" required>
        <input type="text" id="idNumber" name="idNumber" class="page-input" placeholder="رقم الهوية / الإقامة" required>
      </div>
      <div class="personal-info">
        <div class="personal-item">
          <div class="input-group">
            <span class="personal-label">الاسم:</span>
            <input type="text" id="name" name="name" class="page-input" placeholder="الاسم" required>
          </div>
          <div class="input-group">
            <span class="personal-label">تاريخ الإصدار:</span>
            <input type="date" id="issueDate" name="issueDate" class="page-input" required>
          </div>
          <div class="input-group">
            <span class="personal-label">تبدأ من:</span>
            <input type="date" id="startDate" name="startDate" class="page-input" required>
          </div>
          <div class="input-group">
            <span class="personal-label">وحتى:</span>
            <input type="date" id="endDate" name="endDate" class="page-input" required>
          </div>
          <div class="input-group">
            <span class="personal-label">المدة:</span>
            <input type="number" id="duration" name="duration" class="page-input" placeholder="المدة بالأيام" required>
          </div>
          <div class="input-group">
            <span class="personal-label">اسم الطبيب:</span>
            <input type="text" id="doctor" name="doctor" class="page-input" placeholder="اسم الطبيب" required>
          </div>
          <div class="input-group">
            <span class="personal-label">المسمى الوظيفي:</span>
            <input type="text" id="jobTitle" name="jobTitle" class="page-input" placeholder="المسمى الوظيفي" required>
          </div>
          <div class="button-container">
            <button type="submit" class="btn btn-primary button">إضافة البيانات</button>
            <button type="reset" class="btn btn-secondary button">مسح الحقول</button>
          </div>
        </div>
      </div>
    </form>
  </div>
  <!-- Start Footer -->
  <footer class="footer">
    <div class="footer-container">
      <img src="./images/lean.png" alt="Lean logo" class="image-1">
      <div class="vertical-line"></div>
      <img src="./images/MOF.png" alt="MOF logo" class="image-2">
    </div>
    <p class="footer-para">منصة صحة معتمدة من قبل وزارة الصحة © 2025</p>
    <div class="footer-links">
      <a href="https://www.seha.sa/files/T_Cs_v3.pdf" class="footer-link" target="_blank" rel="noopener noreferrer">سياسة الخصوصية وشروط الإستخدام</a>
      <span>|</span>
      <a href="https://seha.sa/Content/LandingPages/UserManual.pdf" class="footer-link" target="_blank" rel="noopener noreferrer">دليل الاستخدام</a>
    </div>
    <div class="footer-social">
      <div class="social-icons">
        <a href="https://wa.me/+966545909461" target="_blank" rel="noopener noreferrer">
          <img src="./images/you.png" alt="WhatsApp" class="social-icon">
        </a>
        <a href="https://twitter.com/seha_services" target="_blank" rel="noopener noreferrer">
          <img src="./images/T.png" alt="Twitter" class="social-icon">
        </a>
        <a href="https://www.youtube.com/channel/UCb9ZrS2YcriYqIPIHNp9wcQ" target="_blank" rel="noopener noreferrer">
          <img src="./images/wh.png" alt="YouTube" class="social-icon">
        </a>
      </div>
      <div class="contact-info">
        <span>920002005</span>
        <span>|</span>
        <span>support@seha.sa</span>
      </div>
    </div>
  </footer>
  <!-- End Footer -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>