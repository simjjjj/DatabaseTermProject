<?php
include 'config.php';
require_once 'functions.php';

if (!isset($_SESSION['userid'])) {
    header("Location: index.php");
    exit();
}

$user_id = $_SESSION['userid'];
$user = null;
$message = '';

// 사용자 정보 가져오기
$stmt = prepareAndBind($con, "SELECT * FROM users WHERE id = ?", "i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
}

// 비밀번호 확인 후 정보 수정
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['verify_password'])) {
    $password = $_POST['password'];
    if (password_verify($password, $user['password'])) {
        $_SESSION['password_verified'] = true;
        $message = "비밀번호가 확인되었습니다.";
    } else {
        $message = "비밀번호가 일치하지 않습니다.";
    }
}

// 사용자 정보 수정
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_info'])) {
    $name = $_POST['name'];
    $username = $_POST['username'];
    $email = $_POST['email'];

    $stmt = prepareAndBind($con, "UPDATE users SET name = ?, username = ?, email = ? WHERE id = ?", "sssi", $name, $username, $email, $user_id);
    if ($stmt->execute()) {
        $message = "사용자 정보가 업데이트되었습니다.";
        // 사용자 정보를 다시 가져오기
        $stmt = prepareAndBind($con, "SELECT * FROM users WHERE id = ?", "i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows == 1) {
            $user = $result->fetch_assoc();
        }
    } else {
        $message = "사용자 정보 업데이트 중 오류가 발생했습니다.";
    }
}

// 비밀번호 변경
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (password_verify($current_password, $user['password'])) {
        if ($new_password === $confirm_password) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = prepareAndBind($con, "UPDATE users SET password = ? WHERE id = ?", "si", $hashed_password, $user_id);
            if ($stmt->execute()) {
                $message = "비밀번호가 성공적으로 변경되었습니다.";
            } else {
                $message = "비밀번호 변경 중 오류가 발생했습니다.";
            }
        } else {
            $message = "새 비밀번호와 비밀번호 확인이 일치하지 않습니다.";
        }
    } else {
        $message = "현재 비밀번호가 일치하지 않습니다.";
    }
}

// 회원 탈퇴
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_account_confirm'])) {
    $stmt = prepareAndBind($con, "DELETE FROM users WHERE id = ?", "i", $user_id);
    if ($stmt->execute()) {
        session_destroy();
        header("Location: index.php");
        exit();
    } else {
        $message = "회원 탈퇴 중 오류가 발생했습니다.";
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>회원 정보 수정</title>
    <?php include 'styles.php'; ?>
    <style>
        .narrow-input {
            width: 50%;
        }
    </style>
</head>
<body class="dark-mode">
    <?php include 'header.php'; ?>
    <div class="container mx-auto px-6 py-12">
        <h2 class="text-3xl font-bold mb-6">회원 정보 수정</h2>
        <?php if ($message): ?>
            <script>
                window.onload = function() {
                    document.getElementById('messageText').innerText = "<?php echo $message; ?>";
                    openModal('messageModal');
                }
            </script>
        <?php endif; ?>

        <?php if (!isset($_SESSION['password_verified'])): ?>
            <button class="bg-blue-600 text-white py-2 px-4 rounded mb-6" onclick="openModal('verifyPasswordModal')">회원정보 수정</button>
        <?php else: ?>
            <form method="POST" action="user_info.php" class="mb-6">
                <div class="mb-4">
                    <label for="name" class="block text-sm font-medium text-gray-700">이름</label>
                    <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($user['name']); ?>" class="mt-1 block px-3 py-2 border rounded narrow-input" required>
                </div>
                <div class="mb-4">
                    <label for="username" class="block text-sm font-medium text-gray-700">아이디</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" class="mt-1 block px-3 py-2 border rounded narrow-input" required>
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">이메일</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" class="mt-1 block px-3 py-2 border rounded narrow-input" required>
                </div>
                <button type="submit" name="update_info" class="bg-blue-600 text-white py-2 px-4 rounded">정보 수정</button>
            </form>

            <button class="bg-blue-600 text-white py-2 px-4 rounded mb-4" onclick="openModal('passwordModal')">비밀번호 변경</button>

            <button class="bg-red-600 text-white py-2 px-4 rounded" onclick="openModal('deleteAccountModal')">회원 탈퇴</button>
        <?php endif; ?>
    </div>

    <div id="verifyPasswordModal" class="fixed inset-0 hidden modal flex items-center justify-center">
        <div class="bg-white p-8 rounded shadow-lg w-96 modal-content">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">비밀번호 확인</h2>
                <button class="text-gray-500 hover:text-gray-700" onclick="closeModal('verifyPasswordModal')">&times;</button>
            </div>
            <form method="POST" action="user_info.php" class="mb-6">
                <div class="mb-4">
                    <label for="password" class="block text-sm font-medium text-gray-700">비밀번호</label>
                    <input type="password" id="password" name="password" class="mt-1 block px-3 py-2 border rounded narrow-input" required>
                </div>
                <button type="submit" name="verify_password" class="bg-blue-600 text-white py-2 px-4 rounded">확인</button>
            </form>
        </div>
    </div>

    <div id="passwordModal" class="fixed inset-0 hidden modal flex items-center justify-center">
        <div class="bg-white p-8 rounded shadow-lg w-96 modal-content">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">비밀번호 변경</h2>
                <button class="text-gray-500 hover:text-gray-700" onclick="closeModal('passwordModal')">&times;</button>
            </div>
            <form method="POST" action="user_info.php" class="mb-6">
                <div class="mb-4">
                    <label for="current_password" class="block text-sm font-medium text-gray-700">현재 비밀번호</label>
                    <input type="password" id="current_password" name="current_password" class="mt-1 block px-3 py-2 border rounded narrow-input" required>
                </div>
                <div class="mb-4">
                    <label for="new_password" class="block text-sm font-medium text-gray-700">새 비밀번호</label>
                    <input type="password" id="new_password" name="new_password" class="mt-1 block px-3 py-2 border rounded narrow-input" required>
                </div>
                <div class="mb-4">
                    <label for="confirm_password" class="block text-sm font-medium text-gray-700">비밀번호 확인</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="mt-1 block px-3 py-2 border rounded narrow-input" required>
                </div>
                <button type="submit" name="change_password" class="bg-blue-600 text-white py-2 px-4 rounded">비밀번호 변경</button>
            </form>
        </div>
    </div>

    <div id="deleteAccountModal" class="fixed inset-0 hidden modal flex items-center justify-center">
        <div class="bg-white p-8 rounded shadow-lg w-96 modal-content">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">회원 탈퇴</h2>
                <button class="text-gray-500 hover:text-gray-700" onclick="closeModal('deleteAccountModal')">&times;</button>
            </div>
            <form method="POST" action="user_info.php" class="mb-6">
                <p class="mb-4">정말로 회원 탈퇴하시겠습니까?</p>
                <button type="submit" name="delete_account_confirm" class="bg-red-600 text-white py-2 px-4 rounded">회원 탈퇴</button>
                <button type="button" class="bg-gray-600 text-white py-2 px-4 rounded" onclick="closeModal('deleteAccountModal')">취소</button>
            </form>
        </div>
    </div>

    <div id="messageModal" class="fixed inset-0 hidden modal flex items-center justify-center">
        <div class="bg-white p-8 rounded shadow-lg w-96 modal-content">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">알림</h2>
                <button class="text-gray-500 hover:text-gray-700" onclick="closeModal('messageModal')">&times;</button>
            </div>
            <p id="messageText" class="mb-4"></p>
            <button class="w-full bg-blue-600 text-white py-2 px-4 rounded" onclick="closeModal('messageModal')">확인</button>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>

<script>
function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
    document.getElementById(id).style.display = "flex"; // Display flex for centering
}

function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
    document.getElementById(id).style.display = "none";
}

window.onload = function() {
    <?php if ($message): ?>
        document.getElementById('messageText').innerText = "<?php echo $message; ?>";
        openModal('messageModal');
    <?php endif; ?>
}
</script>
