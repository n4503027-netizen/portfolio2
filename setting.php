<?php
session_start();

// تفعيل عرض الأخطاء للتdebugging
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);

require 'connection.php';

// التحقق من كلمة المرور إذا تم إرسال النموذج
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    try {
        // جلب كلمة المرور المشفرة من قاعدة البيانات
        $stmt = $conn->prepare("SELECT password FROM admin_passwords WHERE id = 1");
        $stmt->execute();
        $row = $stmt->fetch();
        
        if (!$row || !password_verify($_POST['password'], $row['password'])) {
            throw new Exception("كلمة المرور غير صحيحة");
        }
        
        $_SESSION['password_verified'] = true;
        header("Location: edit.php");
        exit();
        
    } catch (Exception $e) {
        $password_error = $e->getMessage();
    }
}

// إذا لم يتم التحقق من كلمة المرور بعد، عرض نموذج إدخال كلمة المرور
if (!isset($_SESSION['password_verified']) || $_SESSION['password_verified'] !== true) {
    ?>
    <!DOCTYPE html>
    <html lang="ar" dir="rtl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>التحقق من كلمة المرور</title>
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
                    <h4>التحقق من الهوية</h4>
                </div>
                <div class="card-body">
                    <?php if (isset($password_error)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($password_error) ?></div>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label for="password" class="form-label">كلمة المرور:</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">تأكيد</button>
                    </form>
                </div>
            </div>
        </div>
    </body>
    </html>
    <?php
    exit();
}

// الجزء الأصلي من الكود يبدأ هنا بعد التحقق من كلمة المرور
if (!isset($_SESSION['currentUser'])) {
    header("Location: index.php");
    exit();
}

// ... باقي الكود الأصلي ...
?>
<!DOCTYPE html>
<html lang="ar">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>تعديل البيانات</title>
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
      <span class="page-title">تعديل البيانات</span>
    </h1>
    <form method="POST" action="edit.php">
      <div class="input-container">
        <input type="text" id="editServicecode" name="servicecode" class="page-input" value="<?php echo htmlspecialchars($currentUser['servicecode']); ?>" readonly>
        <input type="text" id="editIdNumber" name="idNumber" class="page-input" value="<?php echo htmlspecialchars($currentUser['idNumber']); ?>" readonly>
      </div>
      <div class="personal-info">
        <div class="personal-item">
          <div class="input-group">
            <span class="personal-label">الاسم:</span>
            <input type="text" id="editName" name="name" class="page-input" value="<?php echo htmlspecialchars($currentUser['name']); ?>">
          </div>
          <div class="input-group">
            <span class="personal-label">تاريخ الإصدار:</span>
            <input type="date" id="editIssueDate" name="issueDate" class="page-input" value="<?php echo htmlspecialchars($currentUser['issueDate']); ?>">
          </div>
          <div class="input-group">
            <span class="personal-label">تبدأ من:</span>
            <input type="date" id="editStartDate" name="startDate" class="page-input" value="<?php echo htmlspecialchars($currentUser['startDate']); ?>">
          </div>
          <div class="input-group">
            <span class="personal-label">وحتى:</span>
            <input type="date" id="editEndDate" name="endDate" class="page-input" value="<?php echo htmlspecialchars($currentUser['endDate']); ?>">
          </div>
          <div class="input-group">
            <span class="personal-label">المدة:</span>
            <input type="number" id="editDuration" name="duration" class="page-input" value="<?php echo htmlspecialchars($currentUser['duration']); ?>">
          </div>
          <div class="input-group">
            <span class="personal-label">اسم الطبيب:</span>
            <input type="text" id="editDoctor" name="doctor" class="page-input" value="<?php echo htmlspecialchars($currentUser['doctor']); ?>">
          </div>
          <div class="input-group">
            <span class="personal-label">المسمى الوظيفي:</span>
            <input type="text" id="editJobTitle" name="jobTitle" class="page-input" value="<?php echo htmlspecialchars($currentUser['jobTitle']); ?>">
          </div>
          <div class="button-container">
            <button type="submit" class="btn btn-primary button">حفظ التغييرات</button>
            <a href="data-display.php" class="btn btn-secondary button">العودة</a>
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