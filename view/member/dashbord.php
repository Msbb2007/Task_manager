<?php
require_once __DIR__ . '/../../model/mainClasses/role.php';
require_once __DIR__ . '/../../model/mainClasses/users.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../controller/taskController.php';
require_once __DIR__ . '/../../controller/userController.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

$currentUserId = (int)$_SESSION['user_id'];
$currentUsername = $_SESSION['username'] ?? 'کاربر';
$currentEmail = $_SESSION['email'] ?? '';

$search = $_GET['search'] ?? null;
$statusFilter = $_GET['status'] ?? null;
$priorityFilter = $_GET['priority'] ?? null;

$myTasks = $taskController->getTasksByUserIdMember($currentUserId, $search, $statusFilter, $priorityFilter) ?? [];

function safeGet($target, array $methods = [], array $keys = []) {
    $val = null;
    if (is_object($target)) {
        foreach ($methods as $method) {
            if (method_exists($target, $method)) {
                $val = $target->$method();
                break;
            }
        }
        if ($val === null) {
            foreach ($keys as $key) {
                if (property_exists($target, $key) || isset($target->$key)) {
                    $val = $target->$key;
                    break;
                }
            }
        }
    } elseif (is_array($target)) {
        foreach ($keys as $key) {
            if (isset($target[$key])) {
                $val = $target[$key];
                break;
            }
        }
    }

    if ($val instanceof \DateTimeInterface) {
        return $val->format('Y-m-d');
    }

    return $val;
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پنل کاربری | مشاهده تسک‌ها</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, Arial, sans-serif;
            background-color: #f8f9fa;
        }
        .sidebar {
            min-height: 100vh;
            background-color: #212529;
        }
        .sidebar .nav-link {
            color: #adb5bd;
            margin-bottom: 6px;
            border-radius: 6px;
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: #fff;
            background-color: #0d6efd;
        }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <aside class="col-md-3 col-lg-2 p-3 sidebar text-white">
            <div class="text-center py-2 border-bottom border-secondary mb-3">
                <h5 class="fw-bold m-0"><i class="bi bi-person-workspace me-2"></i>پنل کاربری</h5>
            </div>
            <ul class="nav nav-pills flex-column">
                <li class="nav-item">
                    <a href="dashbord.php" class="nav-link active">
                        <i class="bi bi-list-task me-2"></i> تسک‌های من
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                        <i class="bi bi-person-gear me-2"></i> ویرایش پروفایل
                    </a>
                </li>
                <hr class="my-3 border-secondary">
                <li class="nav-item">
                    <a href="../process_auth.php?action=logout" class="nav-link text-danger">
                        <i class="bi bi-box-arrow-right me-2"></i> خروج
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
            
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-2 mb-4 border-bottom">
                <h1 class="h3 fw-bold">خوش آمدید، <span class="text-primary"><?= htmlspecialchars($currentUsername) ?></span></h1>
                <div>
                    <button class="btn btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#editProfileModal">
                        <i class="bi bi-pencil-square me-1"></i> ویرایش حساب
                    </button>
                    <button class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                        <i class="bi bi-trash me-1"></i> حذف حساب
                    </button>
                </div>
            </div>

            <!-- اعلان‌های سیستم -->
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php if ($_GET['success'] === 'profile_updated') echo 'پروفایل شما با موفقیت بروزرسانی شد.'; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php
                        if ($_GET['error'] === 'empty_fields') echo 'لطفاً تمامی فیلدهای ضروری را پر کنید.';
                        elseif ($_GET['error'] === 'update_failed') echo 'خطا در ویرایش اطلاعات پروفایل.';
                        elseif ($_GET['error'] === 'delete_failed') echo 'خطا در حذف حساب کاربری.';
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- کارت جستجو و فیلتر -->
            <div class="card border-0 shadow-sm p-3 mb-4">
                <form method="GET" action="dashbord.php" class="row g-3 align-items-center">
                    <div class="col-md-4">
                        <label class="form-label small fw-bold">جستجو</label>
                        <input type="text" name="search" class="form-control" placeholder="جستجو در عنوان یا توضیحات..." value="<?= htmlspecialchars($search ?? '') ?>">
                    </div>
                    
                    <div class="col-md-3">
                        <label class="form-label small fw-bold">وضعیت</label>
                        <select name="status" class="form-select">
                            <option value="">همه وضعیت‌ها</option>
                            <option value="not_started" <?= $statusFilter === 'not_started' ? 'selected' : '' ?>>در انتظار</option>
                            <option value="in_progress" <?= $statusFilter === 'in_progress' ? 'selected' : '' ?>>در حال انجام</option>
                            <option value="done" <?= $statusFilter === 'done' ? 'selected' : '' ?>>تکمیل شده</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label small fw-bold">اولویت</label>
                        <select name="priority" class="form-select">
                            <option value="">همه اولویت‌ها</option>
                            <option value="low" <?= $priorityFilter === 'low' ? 'selected' : '' ?>>پایین</option>
                            <option value="normal" <?= $priorityFilter === 'normal' ? 'selected' : '' ?>>معمولی</option>
                            <option value="high" <?= $priorityFilter === 'high' ? 'selected' : '' ?>>بالا</option>
                        </select>
                    </div>

                    <div class="col-md-2 d-flex align-items-end gap-2 mt-4">
                        <button type="submit" class="btn btn-primary w-100">فیلتر</button>
                        <?php if (!empty($search) || !empty($statusFilter) || !empty($priorityFilter)): ?>
                            <a href="dashbord.php" class="btn btn-outline-secondary" title="حذف فیلترها">
                                <i class="bi bi-x-lg"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- جدول نمایش تسک‌ها-->
            <div class="card border-0 shadow-sm p-3">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold m-0"><i class="bi bi-check2-circle text-primary me-2"></i>لیست تسک‌های تخصیص داده شده</h5>
                    <span class="badge bg-primary rounded-pill"><?= count($myTasks) ?> تسک</span>
                </div>
                <div class="card-body p-0 mt-3">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>عنوان تسک</th>
                                    <th>توضیحات</th>
                                    <th>اولویت</th>
                                    <th>وضعیت</th>
                                    <th>تاریخ سررسید</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($myTasks)): ?>
                                    <?php foreach ($myTasks as $index => $task): 
                                        $title = (string)(safeGet($task, ['getTitle'], ['title']) ?? '');
                                        $desc = (string)(safeGet($task, ['getDescription'], ['description']) ?? '');
                                        
                                        $dueDateVal = safeGet($task, ['getDueDate', 'get_due_date'], ['due_date', 'dueDate']);
                                        $dueDate = $dueDateVal ? (string)$dueDateVal : '-';

                                        $rawStatus = safeGet($task, ['getStatus'], ['status']);
                                        $tStatus = is_object($rawStatus) ? ($rawStatus->value ?? $rawStatus->name ?? (string)$rawStatus) : (string)($rawStatus ?? 'not_started');

                                        $rawPriority = safeGet($task, ['getPriority'], ['priority']);
                                        $tPriority = is_object($rawPriority) ? ($rawPriority->value ?? $rawPriority->name ?? (string)$rawPriority) : (string)($rawPriority ?? 'normal');

                                    
                                        $statusBadge = 'bg-secondary';
                                        $statusText = $tStatus;
                                        if (in_array(mb_strtolower($tStatus), ['completed', 'done', 'تکمیل شده'])) {
                                            $statusBadge = 'bg-success';
                                            $statusText = 'تکمیل شده';
                                        } elseif (in_array(mb_strtolower($tStatus), ['in_progress', 'در حال انجام'])) {
                                            $statusBadge = 'bg-warning text-dark';
                                            $statusText = 'در حال انجام';
                                        } elseif (in_array(mb_strtolower($tStatus), ['pending', 'todo', 'not_started', 'در انتظار'])) {
                                            $statusBadge = 'bg-danger';
                                            $statusText = 'در انتظار';
                                        }

                                        $priorityBadge = 'bg-info text-dark';
                                        $priorityText = 'معمولی';
                                        if (in_array(mb_strtolower($tPriority), ['high', 'زیاد'])) {
                                            $priorityBadge = 'bg-danger';
                                            $priorityText = 'بالا';
                                        } elseif (in_array(mb_strtolower($tPriority), ['low', 'کم'])) {
                                            $priorityBadge = 'bg-secondary';
                                            $priorityText = 'پایین';
                                        }
                                    ?>
                                        <tr>
                                            <td><?= $index + 1 ?></td>
                                            <td class="fw-bold"><?= htmlspecialchars($title) ?></td>
                                            <td class="text-muted small"><?= htmlspecialchars($desc) ?></td>
                                            <td><span class="badge <?= $priorityBadge ?>"><?= $priorityText ?></span></td>
                                            <td><span class="badge <?= $statusBadge ?>"><?= $statusText ?></span></td>
                                            <td><small class="text-secondary"><?= htmlspecialchars($dueDate) ?></small></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-4">هیچ تسکی با این جستجو و فیلترهای شما یافت نشد.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </main>
    </div>
</div>

<!-- مدال ویرایش پروفایل -->
<div class="modal fade" id="editProfileModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold"><i class="bi bi-person-gear me-2"></i>ویرایش پروفایل</h5>
                <button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="../../controller/userController.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">نام کاربری</label>
                        <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($currentUsername) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ایمیل</label>
                        <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($currentEmail) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">کلمه عبور جدید (در صورت عدم تغییر خالی بگذارید)</label>
                        <input type="password" name="password" class="form-control" placeholder="******">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                    <button type="submit" name="update_profile" class="btn btn-primary">ذخیره تغییرات</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- مدال حذف حساب کاربری -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title fw-bold"><i class="bi bi-exclamation-triangle me-2"></i>تایید حذف حساب کاربری</h5>
                <button type="button" class="btn-close btn-close-white ms-0 me-auto" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                آیا مطمئن هستید که می‌خواهید حساب کاربری خود را حذف کنید؟ این عمل غیرقابل بازگشت است.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                <form action="../../controller/userController.php" method="POST" class="d-inline">
                    <button type="submit" name="delete_self" class="btn btn-danger">بله، حسابم حذف شود</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>