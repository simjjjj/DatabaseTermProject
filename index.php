<?php
include 'config.php';
include 'functions.php';

$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = $con->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    error_log("로그인 시도: $username");

    $sql = "SELECT id, password, is_admin FROM users WHERE username='$username'";
    $result = $con->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['userid'] = $row['id'];
            $_SESSION['is_admin'] = $row['is_admin'];
            $_SESSION['message'] = "성공적으로 로그인되었습니다.";
            error_log("로그인 성공: $username");
        } else {
            $_SESSION['message'] = "잘못된 아이디 또는 비밀번호입니다.";
            error_log("로그인 실패: $username");
        }
    } else {
        $_SESSION['message'] = "잘못된 아이디 또는 비밀번호입니다.";
        error_log("로그인 쿼리 실패: " . $con->error);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['logout'])) {
    session_destroy();
    session_start();
    $_SESSION['message'] = "성공적으로 로그아웃되었습니다.";
}

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
    echo "<script>
        window.onload = function() {
            document.getElementById('messageText').innerText = '{$message}';
            openModal('messageModal');
        }
    </script>";
}
?>

<?php include 'header.php'; ?>

<section class="bg-gray-100 py-12">
    <div class="container mx-auto px-6">
        <h2 class="text-3xl font-bold mb-6">인기 청원</h2>
        <div id="popular-petition-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php
            $userId = isset($_SESSION['userid']) ? $_SESSION['userid'] : 0;
            $result = $con->query("SELECT p.*, l.user_id IS NOT NULL AS liked FROM petitions p LEFT JOIN likes l ON p.id = l.petition_id AND l.user_id = $userId WHERE p.is_popular = 1");
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='bg-white shadow rounded-lg overflow-hidden petition-card'>";
                    echo "<img src='uploads/" . htmlspecialchars($row['attachment']) . "' alt='Petition image' class='w-full h-48 object-cover'>";
                    echo "<div class='p-4'>";
                    echo "<h3 class='font-bold text-lg'>" . htmlspecialchars($row['title']) . "</h3>";
                    echo "<p class='text-sm mt-2 text-gray-700'>" . htmlspecialchars($row['content']) . "</p>";
                    echo "<div class='mt-4 flex justify-between items-center'>";
                    echo "<span class='text-gray-600 text-sm'>청원기간: " . htmlspecialchars($row['created_at']) . "</span>";
                    echo "<button class='text-blue-600 hover:underline' onclick='openPetitionModal(" . json_encode($row) . ")'>자세히 보기</button>";
                    echo "</div>";
                    echo "<div class='mt-4 flex justify-between items-center'>";
                    echo "<button onclick='likePetition(" . $row['id'] . ")' class='text-gray-600 hover:underline'><i class='" . ($row['liked'] ? "fas text-red-600" : "far") . " fa-heart' id='like-icon-" . $row['id'] . "'></i> 좋아요</button>";
                    echo "<span id='like-count-" . $row['id'] . "' class='text-gray-600 text-sm'>" . htmlspecialchars($row['likes']) . " Likes</span>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<p>인기 청원이 없습니다.</p>";
            }
            ?>
        </div>
    </div>
</section>

<section class="bg-gray-100 py-12">
    <div class="container mx-auto px-6">
        <h2 class="text-3xl font-bold mb-6">청원 안내</h2>
        <div id="petition-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php
            $result = $con->query("SELECT p.*, l.user_id IS NOT NULL AS liked FROM petitions p LEFT JOIN likes l ON p.id = l.petition_id AND l.user_id = $userId WHERE p.is_popular = 0");
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='bg-white shadow rounded-lg overflow-hidden petition-card'>";
                    echo "<div class='p-4'>";
                    echo "<h3 class='font-bold text-lg'>" . htmlspecialchars($row['title']) . "</h3>";
                    echo "<p class='text-sm mt-2 text-gray-700'>" . htmlspecialchars($row['content']) . "</p>";
                    echo "<div class='mt-4 flex justify-between items-center'>";
                    echo "<span class='text-gray-600 text-sm'>청원기간: " . htmlspecialchars($row['created_at']) . "</span>";
                    echo "<button class='text-blue-600 hover:underline' onclick='openPetitionModal(" . json_encode($row) . ")'>자세히 보기</button>";
                    echo "</div>";
                    echo "<div class='mt-4 flex justify-between items-center'>";
                    echo "<button onclick='likePetition(" . $row['id'] . ")' class='text-gray-600 hover:underline'><i class='" . ($row['liked'] ? "fas text-red-600" : "far") . " fa-heart' id='like-icon-" . $row['id'] . "'></i> 좋아요</button>";
                    echo "<span id='like-count-" . $row['id'] . "' class='text-gray-600 text-sm'>" . htmlspecialchars($row['likes']) . " Likes</span>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<p>청원이 없습니다.</p>";
            }
            ?>
        </div>
        <div class="text-center mt-6">
            <button id="loadMore" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700" onclick="loadMore()">더 보기</button>
        </div>
    </div>
</section>

<!-- 청원 자세히 보기 모달 -->
<div id="petitionDetailModal" class="fixed inset-0 hidden modal flex items-center justify-center">
    <div class="bg-white p-8 rounded shadow-lg w-96 modal-content">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold" id="modal-title">청원 상세</h2>
            <button class="text-gray-500 hover:text-gray-700" onclick="closeModal('petitionDetailModal')">&times;</button>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">제목</label>
            <p id="modal-title-content" class="mt-1 block w-full text-gray-900"></p>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">내용</label>
            <p id="modal-content" class="mt-1 block w-full text-gray-900"></p>
        </div>
        <div class="mb-4">
            <label class="block text-sm font-medium text-gray-700">첨부 파일</label>
            <img id="modal-image" class="mt-1 block w-full">
        </div>
    </div>
</div>

<?php include 'footer.php'; ?>
<?php include 'modals.php'; ?>
<?php include 'scripts.php'; ?>

<script>
    function openPetitionModal(petition) {
        document.getElementById('modal-title-content').textContent = petition.title;
        document.getElementById('modal-content').textContent = petition.content;
        if (petition.attachment) {
            document.getElementById('modal-image').src = 'uploads/' + petition.attachment;
            document.getElementById('modal-image').style.display = 'block';
        } else {
            document.getElementById('modal-image').style.display = 'none';
        }
        document.getElementById('petitionDetailModal').classList.remove('hidden');
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }
</script>
