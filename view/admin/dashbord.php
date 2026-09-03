<?php
require_once __DIR__ . '/../../controller/userController.php';
require_once __DIR__ . '/../../controller/taskController.php';

$users = $userController->getAllUsers();
$tasks = $taskController->getAllTasks();

$totalUsersCount = count($users);
$totalTasksCount = count($tasks);

$completedTasksCount = 0;
$inProgressTasksCount = 0;

foreach ($tasks as $task) {
    $statusObj = method_exists($task, 'getStatus') ? $task->getStatus() : ($task['status'] ?? '');
    $statusVal = is_object($statusObj) ? $statusObj->value : $statusObj;

    if ($statusVal === 'done' || $statusVal == 3) {
        $completedTasksCount++;
    } elseif($statusVal === 'in_progress' || $statusVal == 2) {
        $inProgressTasksCount++;
    }
}
?>
<!DOCTYPE html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پنل مدیریت | Task Manager</title>
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
                    <a href="dashbord.php" class="nav-link active">
                        <i class="bi bi-speedometer2 me-2"></i> داشبورد
                    </a>
                </li>
                <li class="nav-item">
                    <a href="users_list.php" class="nav-link">
                        <i class="bi bi-people me-2"></i> مدیریت کاربران
                    </a>
                </li>
                <li class="nav-item">
                    <a href="dashbord.php" class="nav-link">
                        <i class="bi bi-list-check me-2"></i> مدیریت تسک‌ها
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" class="nav-link" data-bs-toggle="modal" data-bs-target="#createTaskModal">
                        <i class="bi bi-plus-square me-2"></i> ایجاد تسک جدید
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

        <!-- Main Content Area -->
        <main class="col-md-9 col-lg-10 ms-sm-auto px-md-4 py-4">
            <!-- Header -->
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-2 mb-4 border-bottom">
                <h1 class="h3 fw-bold">پنل مدیریت سیستم</h1>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#createTaskModal">
                    <i class="bi bi-plus-lg me-1"></i> تسک جدید
                </button>
            </div>

            <!-- پیام‌های اعلان -->
            <?php if (isset($_GET['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php
                        if ($_GET['success'] === 'task_created') echo 'تسک جدید با موفقیت ثبت شد.';
                        elseif ($_GET['success'] === 'task_updated') echo 'تسک با موفقیت بروزرسانی شد.';
                        elseif ($_GET['success'] === 'task_deleted') echo 'تسک با موفقیت حذف شد.';
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php
                        if ($_GET['error'] === 'empty_title') echo 'عنوان تسک نمی‌تواند خالی باشد.';
                        elseif ($_GET['error'] === 'task_create_failed') echo 'خطا در ثبت تسک جدید.';
                        elseif ($_GET['error'] === 'task_update_failed') echo 'خطا در بروزرسانی تسک.';
                        elseif ($_GET['error'] === 'delete_failed') echo 'خطا در حذف تسک.';
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Stats Overview Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="card bg-primary text-white border-0 shadow-sm p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">تعداد کاربران</h6>
                                <h3 class="fw-bold mb-0"><?= $totalUsersCount ?></h3>
                            </div>
                            <i class="bi bi-people fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-success text-white border-0 shadow-sm p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">تسک‌های انجام شده</h6>
                                <h3 class="fw-bold mb-0"><?= $completedTasksCount ?></h3>
                            </div>
                            <i class="bi bi-check-circle fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card bg-warning text-dark border-0 shadow-sm p-3">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">تسک‌های در حال انجام </h6>
                                <h3 class="fw-bold mb-0"><?= $inProgressTasksCount ?></h3>
                            </div>
                            <i class="bi bi-clock-history fs-1 opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Tasks Table Section -->
            <section id="tasks-section" class="card border-0 shadow-sm p-3">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title fw-bold mb-0">مدیریت تسک‌ها</h5>
                    <span class="badge bg-secondary">کل تسک‌ها: <?= $totalTasksCount ?></span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>#</th>
                                <th>عنوان تسک</th>
                                <th>اولویت</th>
                                <th>وضعیت</th>
                                <th>عملیات</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($tasks)): ?>
                                <?php foreach ($tasks as $index => $task): 
                                    $taskId = method_exists($task, 'getId') ? $task->getId() : $task['id'];
                                    $title = method_exists($task, 'getTitle') ? $task->getTitle() : $task['title'];
                                    $description = method_exists($task, 'getDescription') ? $task->getDescription() : ($task['description'] ?? '');
                                    
                                    // مهلت انجام
                                    $dueDate = method_exists($task, 'getDueDate') ? $task->getDueDate() : ($task['due_date'] ?? date('Y-m-d'));
                                    if ($dueDate instanceof \DateTimeInterface) {
                                        $dueDate = $dueDate->format('Y-m-d');
                                    }

                                    // اولویت
                                    $priorityObj = method_exists($task, 'getPriority') ? $task->getPriority() : ($task['priority'] ?? 'normal');
                                    $priorityVal = is_object($priorityObj) ? $priorityObj->value : $priorityObj;

                                    // وضعیت
                                    $statusObj = method_exists($task, 'getStatus') ? $task->getStatus() : ($task['status'] ?? 'not_started');
                                    $statusVal = is_object($statusObj) ? $statusObj->value : $statusObj;

                                    $editModalId = "editTaskModal_" . $taskId;
                                ?>
                                    <tr>
                                        <td><?= $index + 1 ?></td>
                                        <td class="fw-bold"><?= htmlspecialchars($title) ?></td>
                                        <td>
                                            <?php if ($priorityVal === 'high' || $priorityVal == 3): ?>
                                                <span class="badge bg-danger">بالا</span>
                                            <?php elseif ($priorityVal === 'normal' || $priorityVal === 'medium' || $priorityVal == 2): ?>
                                                <span class="badge bg-warning text-dark">متوسط</span>
                                            <?php else: ?>
                                                <span class="badge bg-info text-dark">کم</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($statusVal === 'done' || $statusVal == 3): ?>
                                                <span class="badge bg-success">انجام شده</span>
                                            <?php elseif ($statusVal === 'in_progress' || $statusVal == 2): ?>
                                                <span class="badge bg-warning text-dark">در حال انجام</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary">شروع نشده</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <!-- دکمه ویرایش -->
                                            <button type="button" class="btn btn-sm btn-outline-warning me-1" data-bs-toggle="modal" data-bs-target="#<?= $editModalId ?>">
                                                <i class="bi bi-pencil"></i> ویرایش
                                            </button>

                                            <!-- دکمه حذف -->
                                            <a href="../../controller/taskController.php?action=delete_task&id=<?= $taskId ?>" 
                                               class="btn btn-sm btn-outline-danger" 
                                               onclick="return confirm('آیا از حذف این تسک مطمئن هستید؟')">
                                                <i class="bi bi-trash"></i> حذف
                                            </a>
                                        </td>
                                    </tr>

                                    <!-- Modal: ویرایش تسک -->
                                    <div class="modal fade" id="<?= $editModalId ?>" tabindex="-1" aria-hidden="true">
                                        <div class="modal-dialog modal-dialog-centered">
                                            <div class="modal-content">
                                                <div class="modal-header">
                                                    <h5 class="modal-title fw-bold">ویرایش تسک: <?= htmlspecialchars($title) ?></h5>
                                                    <button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="modal" aria-label="Close"></button>
                                                </div>
                                                <form action="../../controller/taskController.php" method="POST">
                                                    <input type="hidden" name="task_id" value="<?= $taskId ?>">
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">عنوان تسک</label>
                                                            <input type="text" class="form-control" name="title" value="<?= htmlspecialchars($title) ?>" required>
                                                        </div>
                                                        <div class="row mb-3">
                                                            <div class="col-md-6">
                                                                <label class="form-label fw-bold">اولویت</label>
                                                                <select class="form-select" name="priority_id" required>
                                                                    <option value="low" <?= $priorityVal === 'low' ? 'selected' : '' ?>>کم</option>
                                                                    <option value="normal" <?= ($priorityVal === 'normal' || $priorityVal === 'medium') ? 'selected' : '' ?>>متوسط</option>
                                                                    <option value="high" <?= $priorityVal === 'high' ? 'selected' : '' ?>>بالا</option>
                                                                </select>
                                                            </div>
                                                            <div class="col-md-6">
                                                                <label class="form-label fw-bold">وضعیت</label>
                                                                <select class="form-select" name="status_id" required>
                                                                    <option value="not_started" <?= $statusVal === 'not_started' ? 'selected' : '' ?>>شروع نشده</option>
                                                                    <option value="in_progress" <?= $statusVal === 'in_progress' ? 'selected' : '' ?>>در حال انجام</option>
                                                                    <option value="done" <?= $statusVal === 'done' ? 'selected' : '' ?>>انجام شده</option>
                                                                </select>
                                                            </div>
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">مهلت انجام</label>
                                                            <input type="date" class="form-control" name="due_date" value="<?= htmlspecialchars($dueDate) ?>">
                                                        </div>
                                                        <div class="mb-3">
                                                            <label class="form-label fw-bold">توضیحات</label>
                                                            <textarea class="form-control" name="description" rows="3"><?= htmlspecialchars($description) ?></textarea>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                                                        <button type="submit" name="update_task" class="btn btn-warning">بروزرسانی تسک</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">هیچ تسکی ثبت نشده است.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</div>

<!-- Modal: Create Task -->
<div class="modal fade" id="createTaskModal" tabindex="-1" aria-labelledby="createTaskModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="createTaskModalLabel">ایجاد تسک جدید</h5>
                <button type="button" class="btn-close ms-0 me-auto" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="../../controller/taskController.php" method="POST">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="title" class="form-label fw-bold">عنوان تسک</label>
                        <input type="text" class="form-control" id="title" name="title" required placeholder="مثلاً: طراحی فرم ورود">
                    </div>
                    <div class="mb-3">
                        <label for="user" class="form-label fw-bold">مسئول انجام (اختیاری)</label>
                        <select class="form-select" id="user" name="user_id">
                            <option value="" selected>-- انتخاب کاربر مسئول --</option>
                            <?php foreach ($users as $user): 
                                $uId = method_exists($user, 'getId') ? $user->getId() : $user['id'];
                                $uName = method_exists($user, 'getUsername') ? $user->getUsername() : $user['username'];
                            ?>
                                <option value="<?= $uId ?>"><?= htmlspecialchars($uName) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="priority" class="form-label fw-bold">اولویت</label>
                            <select class="form-select" id="priority" name="priority_id" required>
                                <option value="low">کم</option>
                                <option value="normal" selected>متوسط</option>
                                <option value="high">بالا</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="due_date" class="form-label fw-bold">مهلت انجام</label>
                            <input type="date" class="form-control" id="due_date" name="due_date" value="<?= date('Y-m-d') ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label fw-bold">توضیحات</label>
                        <textarea class="form-control" id="description" name="description" rows="3" placeholder="توضیحات مربوط به تسک..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">انصراف</button>
                    <button type="submit" name="create_task" class="btn btn-primary">ثبت تسک</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>