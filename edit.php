<?php
// بدء الجلسة (Session)
session_start();

// التحقق من وجود بيانات المستخدم في الجلسة
if (!isset($_SESSION['currentUser'])) {
    header("Location: index.php"); // توجيه المستخدم إلى الصفحة الرئيسية إذا لم يكن مسجل دخوله
    exit();
}

// جلب بيانات المستخدم من الجلسة
$currentUser = json_decode($_SESSION['currentUser'], true);

// الاتصال بقاعدة البيانات
require 'connection.php';

// حفظ التغييرات إذا تم إرسال النموذج
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $servicecode = $_POST['servicecode'];
    $idNumber = $_POST['idNumber'];
    $name = $_POST['name'];
    $issueDate = $_POST['issueDate'];
    $startDate = $_POST['startDate'];
    $endDate = $_POST['endDate'];
    $duration = $_POST['duration'];
    $doctor = $_POST['doctor'];
    $jobTitle = $_POST['jobTitle'];

    try {
        // تحديث البيانات في قاعدة البيانات
        $sql = "UPDATE users SET 
                name = :name, 
                issueDate = :issueDate, 
                startDate = :startDate, 
                endDate = :endDate, 
                duration = :duration, 
                doctor = :doctor, 
                jobTitle = :jobTitle 
                WHERE servicecode = :servicecode AND idNumber = :idNumber";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':issueDate', $issueDate);
        $stmt->bindParam(':startDate', $startDate);
        $stmt->bindParam(':endDate', $endDate);
        $stmt->bindParam(':duration', $duration);
        $stmt->bindParam(':doctor', $doctor);
        $stmt->bindParam(':jobTitle', $jobTitle);
        $stmt->bindParam(':servicecode', $servicecode);
        $stmt->bindParam(':idNumber', $idNumber);
        $stmt->execute();

        // تحديث بيانات الجلسة
        $_SESSION['currentUser'] = json_encode([
            'servicecode' => $servicecode,
            'idNumber' => $idNumber,
            'name' => $name,
            'issueDate' => $issueDate,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'duration' => $duration,
            'doctor' => $doctor,
            'jobTitle' => $jobTitle
        ]);

        echo "<script>alert('تم تحديث البيانات بنجاح.');</script>";
    } catch (PDOException $e) {
        echo "<script>alert('حدث خطأ أثناء تحديث البيانات.');</script>";
    }
}

$conn = null; // إغلاق الاتصال
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