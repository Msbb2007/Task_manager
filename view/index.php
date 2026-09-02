<?php
session_start();

$errorMessage = $_SESSION['error_message'] ?? null;
$successMessage = $_SESSION['success_message'] ?? null;

unset($_SESSION['error_message']);
unset($_SESSION['success_message']);
?>

<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ورود و ثبت‌نام | taskManager </title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color:rgb(170, 206, 242); height: 100vh; display: flex; align-items: center; }
        .auth-card { background: #fff; padding: 2rem; border-radius: 1rem; box-shadow: 0 .5rem 1rem rgba(0,0,0,.15); width: 100%; max-width: 450px; margin: auto; }
        .nav-pills .nav-link.active { background-color:rgb(20, 101, 224); }
    </style>
</head>
<body>

<div class="auth-card">
    <div class="text-center mb-4">
        <i class="fa-solid fa-tasks fa-3x text-primary"></i>
        <h3 class="mt-3">taskManager</h3>
    </div>

    <!-- نمایش پیام خطا -->
    <?php if ($errorMessage): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($errorMessage); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- نمایش پیام موفقیت -->
            <?php if ($successMessage): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php echo htmlspecialchars($successMessage); ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

    <ul class="nav nav-pills nav-justified mb-4" id="authTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" data-bs-toggle="pill" data-bs-target="#login" type="button">ورود</button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" data-bs-toggle="pill" data-bs-target="#register" type="button">ثبت‌نام</button>
        </li>
    </ul>

    <div class="tab-content" id="authTabsContent">
        <!-- فرم ورود -->
        <div class="tab-pane fade show active" id="login">
            <form action="process_auth.php" method="POST">
                <input type="hidden" name="action" value="login">
                <div class="mb-3">
                    <label class="form-label">نام کاربری</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">رمز عبور</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">ورود به حساب</button>
            </form>
        </div>

        <!-- فرم ثبت‌نام -->
        <div class="tab-pane fade" id="register">
            <form action="process_auth.php" method="POST">
                <input type="hidden" name="action" value="register">
                <div class="mb-3">
                    <label class="form-label">نام و نام خانوادگی</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label"> ایمیل</label>
                    <input type="text" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">رمز عبور</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-success w-100">تکمیل ثبت‌نام</button>
            </form>
        </div>
    </div>
</div>

<!-- اسکریپت بوت‌استرپ -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
