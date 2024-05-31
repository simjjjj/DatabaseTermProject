<?php
include 'config.php';

// 세션 시작
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 비밀번호 변경 처리
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $new_password_confirm = $_POST['new_password_confirm'];

    if ($new_password !== $new_password_confirm) {
        $_SESSION['message'] = "새 비밀번호가 일치하지 않습니다.";
    } else {
        $user_id = $_SESSION['userid'];
        $stmt = $con->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result()->fetch_assoc();

        if (password_verify($current_password, $result['password'])) {
            $new_hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $update_stmt = $con->prepare("UPDATE users SET password = ? WHERE id = ?");
            $update_stmt->bind_param("si", $new_hashed_password, $user_id);
            $update_stmt->execute();
            if ($update_stmt->affected_rows > 0) {
                $_SESSION['message'] = "비밀번호가 성공적으로 변경되었습니다.";
            } else {
                $_SESSION['message'] = "비밀번호 변경에 실패했습니다.";
            }
        } else {
            $_SESSION['message'] = "현재 비밀번호가 정확하지 않습니다.";
        }
    }
    header("Location: user_info.php");
    exit();
}

// 회원 탈퇴 처리
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_account'])) {
    $user_id = $_SESSION['userid'];
    $delete_stmt = $con->prepare("DELETE FROM users WHERE id = ?");
    $delete_stmt->bind_param("i", $user_id);
    $delete_stmt->execute();
    if ($delete_stmt->affected_rows > 0) {
        session_destroy();
        session_start();
        $_SESSION['message'] = "회원 탈퇴 처리가 완료되었습니다.";
        header("Location: index.php");
        exit();
    } else {
        $_SESSION['message'] = "회원 탈퇴에 실패했습니다.";
    }
    header("Location: user_info.php");
    exit();
}

include 'header.php';
include 'styles.php';
?>

<div class="container mx-auto px-6 py-12">
    <h2 class='text-3xl font-bold mb-6'>회원 정보</h2>
    <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
        <!-- 비밀번호 변경 폼 -->
        <form method="POST">
            <div class="mb-4">
                <label for="current_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">현재 비밀번호</label>
                <input type="password" id="current_password" name="current_password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            <div class="mb-4">
                <label for="new_password" class="block text-sm font-medium text-gray-700 dark:text-gray-300">새 비밀번호</label>
                <input type="password" id="new_password" name="new_password" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            <div class="mb-4">
                <label for="new_password_confirm" class="block text-sm font-medium text-gray-700 dark:text-gray-300">새 비밀번호 확인</label>
                <input type="password" id="new_password_confirm" name="new_password_confirm" required class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            <button type="submit" name="change_password" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">비밀번호 변경</button>
        </form>
        <!-- 회원 탈퇴 버튼 -->
        <button onclick="openModal('deleteConfirmationModal')" class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded mt-4">회원 탈퇴</button>
    </div>

    <!-- 회원 탈퇴 확인 모달창 -->
    <div id="deleteConfirmationModal" class="fixed inset-0 hidden modal flex items-center justify-center">
        <div class="bg-white p-8 rounded shadow-lg w-96 modal-content">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">회원 탈퇴</h2>
                <button class="text-gray-500 hover:text-gray-700" onclick="closeModal('deleteConfirmationModal')">&times;</button>
            </div>
            <p class="mb-4">정말로 탈퇴하시겠습니까?</p>
            <div class="flex justify-end">
                <button class="bg-red-600 hover:bg-red-700 text-white font-bold py-2 px-4 rounded mr-2" onclick="confirmDelete()">탈퇴하기</button>
                <button class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded" onclick="closeModal('deleteConfirmationModal')">취소</button>
            </div>
        </div>
    </div>
</div>

<!-- 결과 모달창 -->
<div id="resultModal" class="fixed inset-0 hidden modal flex items-center justify-center">
    <div class="bg-white p-8 rounded shadow-lg w-96 modal-content">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">알림</h2>
            <button class="text-gray-500 hover:text-gray-700" onclick="closeModal('resultModal')">&times;</button>
        </div>
        <p id="resultMessage"></p>
        <div class="flex justify-end">
            <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded" onclick="closeModal('resultModal')">확인</button>
        </div>
    </div>
</div>

<form id="delete-account-form" method="POST" style="display:none;">
    <input type="hidden" name="delete_account" value="1">
</form>

<script>
function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
    document.getElementById(id).style.display = "flex"; // Display flex for centering
}

function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
    document.getElementById(id).style.display = "none";
}

function confirmDelete() {
    document.getElementById('delete-account-form').submit();
}

document.addEventListener("DOMContentLoaded", function() {
    var message = "<?php echo $_SESSION['message'] ?? ''; unset($_SESSION['message']); ?>";
    if (message) {
        document.getElementById('resultMessage').textContent = message;
        openModal('resultModal');
    }
});
</script>

<?php include 'footer.php'; ?>
