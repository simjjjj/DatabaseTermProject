<?php
include 'config.php';
require_once 'functions.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_petition'])) {
    $petition_id = intval($_POST['petition_id']);

    // 삭제할 청원의 댓글 삭제
    $stmt = $con->prepare("DELETE FROM comments WHERE petition_id = ?");
    $stmt->bind_param("i", $petition_id);
    $stmt->execute();

    // 삭제할 청원의 좋아요 삭제
    $stmt = $con->prepare("DELETE FROM likes WHERE petition_id = ?");
    $stmt->bind_param("i", $petition_id);
    $stmt->execute();

    // 삭제할 청원의 서명 삭제
    $stmt = $con->prepare("DELETE FROM signatures WHERE petition_id = ?");
    $stmt->bind_param("i", $petition_id);
    $stmt->execute();

    // 청원 삭제
    $stmt = $con->prepare("DELETE FROM petitions WHERE id = ?");
    $stmt->bind_param("i", $petition_id);
    $stmt->execute();

    $_SESSION['message'] = "청원이 성공적으로 삭제되었습니다.";
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_user'])) {
    $user_id = intval($_POST['user_id']);

    // 삭제할 회원의 청원 삭제
    $stmt = $con->prepare("DELETE FROM petitions WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    // 삭제할 회원의 댓글 삭제
    $stmt = $con->prepare("DELETE FROM comments WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    // 삭제할 회원의 좋아요 삭제
    $stmt = $con->prepare("DELETE FROM likes WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    // 삭제할 회원의 서명 삭제
    $stmt = $con->prepare("DELETE FROM signatures WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    // 회원 삭제
    $stmt = $con->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    $_SESSION['message'] = "회원이 성공적으로 삭제되었습니다.";
}

$petitions = $con->query("SELECT * FROM petitions");
$users = $con->query("SELECT * FROM users");
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>관리자 대시보드</title>
    <?php include 'styles.php'; ?>
    <style>
        body.dark-mode {
            background-color: #121212;
            color: #E0E0E0;
        }
        .table-custom {
            width: 100%;
            border-collapse: collapse;
        }
        .table-custom th,
        .table-custom td {
            border: 1px solid #ddd;
            padding: 8px;
        }
        .table-custom th {
            padding-top: 12px;
            padding-bottom: 12px;
            background-color: #f2f2f2;
            color: #333;
        }
        .dark-mode .table-custom th {
            background-color: #333;
            color: #f2f2f2;
        }
        .dark-mode .table-custom td {
            border-color: #555;
        }
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 500px;
            text-align: center;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body class="dark-mode">
    <?php include 'header.php'; ?>
    <div class="container mx-auto px-6 py-12">
        <h2 class="text-3xl font-bold mb-6">관리자 대시보드</h2>
        <?php
        if (isset($_SESSION['message'])) {
            echo "<script>window.onload = function() { openModal('messageModal'); }</script>";
            echo "<div id='messageModal' class='modal'>
                    <div class='modal-content'>
                        <span class='close' onclick='closeModal(\"messageModal\")'>&times;</span>
                        <p>" . $_SESSION['message'] . "</p>
                    </div>
                  </div>";
            unset($_SESSION['message']);
        }
        ?>
        <h3 class="text-2xl font-bold mb-4">청원 목록</h3>
        <table class="table-custom">
            <thead>
                <tr>
                    <th>제목</th>
                    <th>작성자</th>
                    <th>카테고리</th>
                    <th>좋아요 수</th>
                    <th>관리</th>
                    <th>삭제</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($petition = $petitions->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($petition['title']); ?></td>
                    <td><?php echo htmlspecialchars($petition['user_id']); ?></td>
                    <td><?php echo htmlspecialchars($petition['category']); ?></td>
                    <td><?php echo htmlspecialchars($petition['likes']); ?></td>
                    <td>
                        <a href="admin_petition_detail.php?id=<?php echo $petition['id']; ?>" class="text-blue-600 hover:underline">자세히 보기</a>
                    </td>
                    <td>
                        <button class="text-red-600 hover:underline" onclick="openDeleteModal(<?php echo $petition['id']; ?>, 'petition')">삭제</button>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>

        <h3 class="text-2xl font-bold mb-4 mt-6">회원 목록</h3>
        <table class="table-custom">
            <thead>
                <tr>
                    <th>이름</th>
                    <th>아이디</th>
                    <th>이메일</th>
                    <th>관리자 여부</th>
                    <th>삭제</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($user = $users->fetch_assoc()) { ?>
                <tr>
                    <td><?php echo htmlspecialchars($user['name']); ?></td>
                    <td><?php echo htmlspecialchars($user['username']); ?></td>
                    <td><?php echo htmlspecialchars($user['email']); ?></td>
                    <td><?php echo $user['is_admin'] ? '예' : '아니오'; ?></td>
                    <td>
                        <button class="text-red-600 hover:underline" onclick="openDeleteModal(<?php echo $user['id']; ?>, 'user')">삭제</button>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
    </div>
    <?php include 'footer.php'; ?>

    <div id="deleteModal" class="modal">
        <div class="bg-white dark:bg-gray-800 p-8 rounded shadow-lg w-96 modal-content">
            <span class="close" onclick="closeModal('deleteModal')">&times;</span>
            <p id="deleteModalText"></p>
            <form id="deleteForm" method="post" action="admin_dashboard.php">
                <input type="hidden" id="deleteId" name="delete_id" value="">
                <button type="submit" id="deleteButton" name="delete_action" class="bg-red-600 text-white py-2 px-4 rounded mt-4">삭제</button>
                <button type="button" class="bg-gray-600 text-white py-2 px-4 rounded mt-4" onclick="closeModal('deleteModal')">취소</button>
            </form>
        </div>
    </div>

    <script>
        function openModal(modalId) {
            document.getElementById(modalId).style.display = "flex";
        }

        function closeModal(modalId) {
            document.getElementById(modalId).style.display = "none";
        }

        function openDeleteModal(id, type) {
            document.getElementById('deleteId').value = id;
            if (type === 'petition') {
                document.getElementById('deleteModalText').innerText = "정말 이 청원을 삭제하시겠습니까?";
                document.getElementById('deleteButton').name = 'delete_petition';
            } else if (type === 'user') {
                document.getElementById('deleteModalText').innerText = "정말 이 회원을 삭제하시겠습니까?";
                document.getElementById('deleteButton').name = 'delete_user';
            }
            openModal('deleteModal');
        }

        window.onclick = function(event) {
            var modal = document.getElementById('deleteModal');
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
    </script>
</body>
</html>
