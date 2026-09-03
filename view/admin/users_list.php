<?php
require_once __DIR__ . '/../../controller/userController.php';
require_once __DIR__ . '/../../controller/taskController.php';

$users = $userController->getAllUsers() ?? [];
$allTasks = $taskController->getAllTasks() ?? [];

/**
 * تابع کمکی برای دریافت  مقادیر از شیء، Enum، DateTime یا آرایه
 */
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

$search = trim($_GET['search'] ?? '');
if (!empty($search)) {
    $users = array_filter($users, function($user) use ($search) {
        $username = (string) safeGet($user, ['getUsername'], ['username']);
        $email = (string) safeGet($user, ['getEmail'], ['email']);
        return str_contains(mb_strtolower($username), mb_strtolower($search)) || 
               str_contains(mb_strtolower($email), mb_strtolower($search));
    });
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>مدیریت کاربران | Task Manager</title>
    <!-- Bootstrap 5 RTL CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <style>
        body {
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background-color:rgb(170, 206, 242);
        }
        .sidebar {
            min-height: 100vh;
            background-color: #212529;
        }
        .sidebar .nav-link {
            color: #adb5bd;
            margin-bottom: 6px;
            border-radius: 6px;
            transition: all 0.2s;
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
        <!-- Sidebar Menu -->
        <aside class="col-md-3 col-lg-2 p-3 sidebar text-white">
            <div class="text-center py-2 border-bottom border-secondary mb-3">
                <h5 class="fw-bold m-0"><i class="bi bi-kanban me-2"></i>Task Manager</h5>
            </div>
            <ul class="nav nav-pills flex-column">
                <li class="nav-item">
                    <a href="dashbord.php" class="nav-link">
                        <i class="bi bi-speedometer2 me-2"></i> داشبورد
                    </a>
                </li>
                <li class="nav-item">
                    <a href="users_list.php" class="nav-link active">
                        <i class="bi bi-people me-2"></i> مدیریت کاربران
                    </a>
                </li>
                <li class="nav-item">
                    <a href="dashbord.php#tasks-section" class="nav-link">
                        <i class="bi bi-list-check me-2"></i> مدیریت کل تسک‌ها
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#addUserModal">
                        <i class="bi bi-person-plus me-2"></i> افزودن کاربر جدید
                    </a>
                </li>
                <hr class="my-3 border-secondary">
                <li class="nav-item">
                    <a href="../../process_auth.php?action=logout" class="nav-link text-danger">
                        <i class="bi bi-box-arrow-right me-2"></i> خروج
                    </a>
                </li>
            </ul>
        </aside>

        <!-- Main Content Area -->
        <main class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-2 mb-4 border-bottom">
                <h1 class="h3 fw-bold">مدیریت کاربران</h1>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">
                    <i class="bi bi-person-plus-fill me-1"></i> افزودن کاربر جدید
                </button>
            </div>

            <!-- پیام‌های اعلان -->
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php
                        if ($_GET['success'] === 'user_created') echo 'کاربر جدید با موفقیت ایجاد شد.';
                        elseif ($_GET['success'] === 'user_deleted') echo 'کاربر با موفقیت حذف شد.';
                        elseif ($_GET['success'] === 'task_updated') echo 'تسک با موفقیت ویرایش شد.';
                        elseif ($_GET['success'] === 'task_assigned') echo 'تسک با موفقیت تخصیص داده شد.';
                        elseif ($_GET['success'] === 'task_unassigned') echo 'تخصیص تسک با موفقیت لغو شد.';
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php
                        if ($_GET['error'] === 'user_exists') echo 'این نام کاربری قبلاً ثبت شده است.';
                        elseif ($_GET['error'] === 'update_failed') echo 'خطا در ویرایش تسک.';
                        elseif ($_GET['error'] === 'assign_failed') echo 'خطا در تخصیص تسک.';
                        elseif ($_GET['error'] === 'unassign_failed') echo 'خطا در لغو تخصیص تسک.';
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- نوار جستجو -->
            <div class="card border-0 shadow-sm p-3 mb-4">
                <form method="GET" action="users_list.php" class="row g-2 align-items-center">
                    <div class="col-md-5 col-lg-4">
                        <div class="input-group">
                            <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control" placeholder="جستجوی نام کاربری یا ایمیل..." value="<?= htmlspecialchars($search) ?>">
                        </div>
                    </div>
                    <div class="col-auto">
                        <button type="submit" class="btn btn-secondary">جستجو</button>
                        <?php if (!empty($search)): ?>
                            <a href="users_list.php" class="btn btn-outline-danger me-1">پاک‌سازی</a>
                        <?php endif; ?>
                    </div>
                </form>
            </div>

            <!-- Users Table Card -->
            <div class="card border-0 shadow-sm p-3">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>نام کاربری</th>
                                <th>ایمیل</th>
                                <th>نقش</th>
                                <th>عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($users)): ?>
                                <?php foreach ($users as $index => $user): 
                                    $username = (string)(safeGet($user, ['getUsername'], ['username']) ?? '');
                                    $email = (string)(safeGet($user, ['getEmail'], ['email']) ?? '');
                                    $userId = (int) safeGet($user, ['getId'], ['id']);
                                    
                                    $roleObj = safeGet($user, ['getRole'], ['role']) ?? 'user';
                                    $roleValue = is_object($roleObj) ? ($roleObj->value ?? $roleObj->name ?? 'user') : $roleObj;

                                    $userTasksModalId = "userTasksModal_" . md5($username);
                                    $userTasks = $userId > 0 ? $taskController->getTasksByUserId($userId) : [];
                                ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td class="fw-bold"><?= htmlspecialchars($username) ?></td>
                                        <td><?= htmlspecialchars($email) ?></td>
                                        <td>
                                            <span class="badge bg-<?= $roleValue === 'admin' ? 'danger' : 'info' ?>">
                                                <?= $roleValue === 'admin' ? 'مدیر (Admin)' : 'کاربر (member)' ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-sm btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#<?= $userTasksModalId ?>">
                                                <i class="bi bi-list-task me-1"></i> تسک‌ها
                                                <span class="badge bg-primary ms-1"><?= count($userTasks) ?></span>
                                            </button>

                                            <a href="../../controller/userControllser.php?action=delete&username=<?= urlencode($username) ?>" 
                                               class="btn btn-sm btn-outline-danger" 
                                               onclick="return confirm('آیا از حذف کاربر «<?= htmlspecialchars($username) ?>» مطمئن هستید؟')">
                                               <i class="bi bi-trash"></i> حذف
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">هیچ کاربری یافت نشد.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</div>

<?php if (!empty($users)): ?>
    <?php foreach ($users as $user): 
        $username = (string)(safeGet($user, ['getUsername'], ['username']) ?? '');
        $userId = (int) safeGet($user, ['getId'], ['id']);
        $userTasksModalId = "userTasksModal_" . md5($username);
        $userTasks = $userId > 0 ? $taskController->getTasksByUserId($userId) : [];
    ?>
        <div class="modal fade" id="<?= $userTasksModalId ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title fw-bold">
                            <i class="bi bi-person-badge me-2 text-primary"></i>مدیریت و ویرایش تسک‌های: <span class="text-primary"><?= htmlspecialchars($username) ?></span>
                        </h5>
                        <button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <h6 class="fw-bold mb-3"><i class="bi bi-check2-square me-1"></i> تسک‌های اختصاص یافته</h6>
                        
                        <?php if (!empty($userTasks)): ?>
                            <div class="table-responsive mb-4">
                                <table class="table table-bordered align-middle table-hover">
                                    <thead class="table-secondary">
                                        <tr>
                                            <th style="width: 50px;">#</th>
                                            <th>عنوان تسک</th>
                                            <th>توضیحات</th>
                                            <th>وضعیت</th>
                                            <th>تاریخ سررسید</th>
                                            <th class="text-center" style="width: 170px;">عملیات</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $tIdx = 1;
                                        foreach ($userTasks as $utask): 
                                            $utaskId = safeGet($utask, ['getId'], ['id']);
                                            $tTitle = (string)(safeGet($utask, ['getTitle'], ['title']) ?? '');
                                            $tDesc = (string)(safeGet($utask, ['getDescription'], ['description']) ?? '');
                                            
                                            $dueDateVal = safeGet($utask, ['getDueDate', 'get_due_date'], ['due_date', 'dueDate']);
                                            $tDueDate = $dueDateVal !== null ? (string)$dueDateVal : '';

                                            $rawStatus = safeGet($utask, ['getStatus'], ['status']);
                                            if (is_object($rawStatus)) {
                                                $tStatus = $rawStatus->value ?? $rawStatus->name ?? (string)$rawStatus;
                                            } else {
                                                $tStatus = (string)($rawStatus ?? 'not_started');
                                            }

                                            $statusBadge = 'bg-secondary';
                                            $statusText = $tStatus;

                                            if (in_array(mb_strtolower($tStatus), ['done', 'تکمیل شده'])) {
                                                $statusBadge = 'bg-success';
                                                $statusText = 'تکمیل شده';
                                            } elseif (in_array(mb_strtolower($tStatus), ['in_progress', 'در حال انجام'])) {
                                                $statusBadge = 'bg-warning text-dark';
                                                $statusText = 'در حال انجام';
                                            } elseif (in_array(mb_strtolower($tStatus), ['not_started', 'در انتظار'])) {
                                                $statusBadge = 'bg-danger';
                                                $statusText = 'در انتظار';
                                            }

                                            $editFormId = "editTaskForm_" . $utaskId . "_" . $userId;
                                        ?>
                                            <tr>
                                                <td><?= $tIdx++ ?></td>
                                                <td class="fw-bold"><?= htmlspecialchars($tTitle) ?></td>
                                                <td class="text-muted small"><?= htmlspecialchars($tDesc) ?></td>
                                                <td>
                                                    <span class="badge <?= $statusBadge ?>"><?= htmlspecialchars((string)$statusText) ?></span>
                                                </td>
                                                <td><small class="text-secondary"><?= htmlspecialchars($tDueDate ?: '-') ?></small></td>
                                                <td class="text-center">
                                                    <button class="btn btn-sm btn-outline-warning me-1" type="button" data-bs-toggle="collapse" data-bs-target="#<?= $editFormId ?>" aria-expanded="false">
                                                        <i class="bi bi-pencil me-1"></i> ویرایش
                                                    </button>

                                                    <form action="../../controller/taskController.php" method="POST" class="d-inline" onsubmit="return confirm('آیا از لغو تخصیص این تسک از کاربر مطمئن هستید؟');">
                                                        <input type="hidden" name="user_id" value="<?= $userId ?>">
                                                        <input type="hidden" name="task_id" value="<?= $utaskId ?>">
                                                        <button type="submit" name="unassign_task" class="btn btn-sm btn-outline-danger" title="لغو تخصیص">
                                                            <i class="bi bi-x-circle"></i>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>

                                            <tr>
                                                <td colspan="6" class="p-0 border-0">
                                                    <div class="collapse p-3 bg-light border-bottom" id="<?= $editFormId ?>">
                                                        <form action="../../controller/taskController.php" method="POST" class="row g-3">
                                                            <input type="hidden" name="task_id" value="<?= $utaskId ?>">
                                                            <input type="hidden" name="user_id" value="<?= $userId ?>">
                                                            
                                                            <div class="col-md-4">
                                                                <label class="form-label small fw-bold">عنوان تسک</label>
                                                                <input type="text" name="title" class="form-control form-control-sm" value="<?= htmlspecialchars($tTitle) ?>" required>
                                                            </div>

                                                            <div class="col-md-3">
                                                                <label class="form-label small fw-bold">وضعیت</label>
                                                                <select name="status" class="form-select form-select-sm">
                                                                    <option value="pending" <?= in_array(mb_strtolower($tStatus), ['pending', 'todo', 'not_started', 'در انتظار']) ? 'selected' : '' ?>>شروع نشده  </option>
                                                                    <option value="in_progress" <?= in_array(mb_strtolower($tStatus), ['in_progress', 'در حال انجام']) ? 'selected' : '' ?>>در حال انجام </option>
                                                                    <option value="completed" <?= in_array(mb_strtolower($tStatus), ['completed', 'done', 'تکمیل شده']) ? 'selected' : '' ?>>تکمیل شده </option>
                                                                </select>
                                                            </div>

                                                            <div class="col-md-3">
                                                                <label class="form-label small fw-bold">تاریخ سررسید</label>
                                                                <input type="date" name="due_date" class="form-control form-control-sm" value="<?= htmlspecialchars($tDueDate) ?>">
                                                            </div>

                                                            <div class="col-md-12">
                                                                <label class="form-label small fw-bold">توضیحات</label>
                                                                <textarea name="description" class="form-control form-control-sm" rows="2"><?= htmlspecialchars($tDesc) ?></textarea>
                                                            </div>

                                                            <div class="col-md-12 text-end">
                                                                <button type="submit" name="update_task" class="btn btn-sm btn-success">
                                                                    <i class="bi bi-check-lg me-1"></i> ذخیره تغییرات
                                                                </button>
                                                            </div>
                                                        </form>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4 text-muted bg-light rounded mb-4">
                                <i class="bi bi-inbox display-6 d-block mb-2 text-secondary"></i>
                                هیچ تسکی به این کاربر اختصاص داده نشده است.
                            </div>
                        <?php endif; ?>

                        <!-- فرم تخصیص تسک جدید -->
                        <div class="card border p-3 bg-white">
                            <h6 class="fw-bold mb-3 text-primary"><i class="bi bi-plus-circle me-1"></i> تخصیص تسک جدید به <?= htmlspecialchars($username) ?></h6>
                            <form action="../../controller/taskController.php" method="POST" class="row g-2 align-items-center">
                                <input type="hidden" name="user_id" value="<?= $userId ?>">
                                <div class="col-md-8">
                                    <select name="task_id" class="form-select" required>
                                        <option value="" disabled selected>-- انتخاب تسک از دیتابیس --</option>
                                        <?php if (!empty($allTasks)): ?>
                                            <?php foreach ($allTasks as $task): 
                                                $taskId = safeGet($task, ['getId'], ['id']);
                                                $taskTitle = (string)(safeGet($task, ['getTitle'], ['title']) ?? '');
                                            ?>
                                                <option value="<?= $taskId ?>"><?= htmlspecialchars($taskTitle) ?></option>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <option value="" disabled>هیچ تسکی در دیتابیس موجود نیست</option>
                                        <?php endif; ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <button type="submit" name="assign_task" class="btn btn-primary w-100">
                                        <i class="bi bi-plus-lg me-1"></i> تخصیص تسک
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">بستن</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<!-- Modal: Add New User -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="addUserModalLabel">افزودن کاربر جدید</h5>
                <button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="../../controller/userController.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="username" class="form-label">نام کاربری</label>
                        <input type="text" class="form-control" id="username" name="username" required placeholder="مثلاً: ali_dev">
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">ایمیل</label>
                        <input type="email" class="form-control" id="email" name="email" required placeholder="name@example.com">
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">کلمه عبور</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="role_id" class="form-label">نقش کاربر</label>
                        <select class="form-select" id="role_id" name="role_id" required>
                            <option value="admin">مدیر (Admin)</option>
                            <option value="member" selected>کاربر معمولی (member)</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                    <button type="submit" name="add_user" class="btn btn-primary">ثبت کاربر</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>