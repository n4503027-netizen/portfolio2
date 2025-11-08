<?php
// بدء الجلسة مع إعدادات أمنية

// التحقق من الصلاحيات (يجب أن يكون المستخدم مسجلاً دخوله)

// الاتصال بقاعدة البيانات
require 'connection.php';

// حذف مستخدم إذا تم إرسال طلب حذف
if (isset($_GET['delete'])) {
    $servicecode = $_GET['servicecode'];
    $idNumber = $_GET['idNumber'];

    try {
        $sql = "DELETE FROM users WHERE servicecode = :servicecode AND idNumber = :idNumber";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':servicecode', $servicecode);
        $stmt->bindParam(':idNumber', $idNumber);
        $stmt->execute();

        echo "<script>alert('تم حذف المستخدم بنجاح.');</script>";
    } catch (PDOException $e) {
        echo "<script>alert('حدث خطأ أثناء حذف المستخدم.');</script>";
    }
}
// جلب جميع البيانات من قاعدة البيانات
try {
    $sql = "SELECT * FROM users ORDER BY id DESC"; // الترتيب حسب الأحدث
    $stmt = $conn->query($sql);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $totalUsers = count($users); // عدد المستخدمين
} catch (PDOException $e) {
    die("حدث خطأ أثناء جلب البيانات: " . $e->getMessage());
}

$conn = null; // إغلاق الاتصال
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>عرض جميع البيانات</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css">
  <style>
    body {
      font-family: 'Tahoma', Arial, sans-serif;
    }
    .table-container {
      margin-top: 20px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    .count-badge {
      font-size: 1.2rem;
      background-color: #306db5;
    }
    .serial-column {
      width: 50px;
    }
    .action-column {
      width: 100px;
    }
    .delete-btn {
      background-color: #dc3545;
      color: white;
      border: none;
      padding: 5px 10px;
      border-radius: 4px;
      transition: all 0.3s;
    }
    .delete-btn:hover {
      background-color: #bb2d3b;
      transform: scale(1.05);
    }
  </style>
</head>
<body>
  <div class="container py-4">
    <h1 class="text-center mb-4">عرض جميع البيانات</h1>
    
    <!-- عرض رسائل النجاح/الخطأ -->
    <?php if (isset($_SESSION['success_message'])): ?>
      <div class="alert alert-success alert-dismissible fade show">
        <?php echo $_SESSION['success_message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php unset($_SESSION['success_message']); ?>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error_message'])): ?>
      <div class="alert alert-danger alert-dismissible fade show">
        <?php echo $_SESSION['error_message']; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
      <?php unset($_SESSION['error_message']); ?>
    <?php endif; ?>
    
    <div class="d-flex justify-content-between align-items-center mb-3">
      <span class="badge count-badge">
        عدد السجلات: <?php echo $totalUsers; ?>
      </span>
      <a href="add_user.php" class="btn btn-primary">إضافة مستخدم جديد</a>
    </div>
    
    <div class="table-responsive table-container">
      <table class="table table-bordered table-hover">
        <thead class="table-light">
          <tr>
            <th class="serial-column">#</th>
            <th>رقم الخدمة</th>
            <th>رقم الهوية/الإقامة</th>
            <th>الاسم</th>
            <th>تاريخ الإصدار</th>
            <th>تبدأ من</th>
            <th>وحتى</th>
            <th>المدة (أيام)</th>
            <th>اسم الطبيب</th>
            <th>المسمى الوظيفي</th>
            <th class="action-column">إجراءات</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($users as $index => $user): ?>
            <tr>
              <td><?php echo $index + 1; ?></td>
              <td><?php echo htmlspecialchars($user['servicecode']); ?></td>
              <td><?php echo htmlspecialchars($user['idNumber']); ?></td>
              <td><?php echo htmlspecialchars($user['name']); ?></td>
              <td><?php echo htmlspecialchars($user['issueDate']); ?></td>
              <td><?php echo htmlspecialchars($user['startDate']); ?></td>
              <td><?php echo htmlspecialchars($user['endDate']); ?></td>
              <td><?php echo htmlspecialchars($user['duration']); ?></td>
              <td><?php echo htmlspecialchars($user['doctor']); ?></td>
              <td><?php echo htmlspecialchars($user['jobTitle']); ?></td>
              <td>
                <button class="delete-btn" 
                        onclick="confirmDelete('<?php echo htmlspecialchars($user['servicecode']); ?>', '<?php echo htmlspecialchars($user['idNumber']); ?>')">
                  حذف
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // دالة لتأكيد الحذف مع CSRF Token
    function confirmDelete(servicecode, idNumber) {
      if (confirm('هل أنت متأكد من حذف هذا المستخدم؟')) {
        const csrfToken = "<?php echo $_SESSION['csrf_token']; ?>";
        window.location.href = `all-data.php?delete=true&servicecode=${servicecode}&idNumber=${idNumber}&csrf_token=${csrfToken}`;
      }
    }
  </script>
</body>
</html>