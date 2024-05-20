<?php
include 'config.php';
include 'functions.php';

$message = '';

// 로그인 처리
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = mysqli_real_escape_string($con, $_POST['username']);
    $password = $_POST['password'];

    error_log("로그인 시도: $username");

    $sql = "SELECT id, password, is_admin FROM users WHERE username='$username'";
    $result = mysqli_query($con, $sql);

    if ($result) {
        $row = mysqli_fetch_assoc($result);
        if (mysqli_num_rows($result) == 1 && password_verify($password, $row['password'])) {
            $_SESSION['userid'] = $row['id'];
            $_SESSION['is_admin'] = $row['is_admin'];
            $_SESSION['message'] = "성공적으로 로그인되었습니다.";
            error_log("로그인 성공: $username");
        } else {
            $_SESSION['message'] = "잘못된 아이디 또는 비밀번호입니다.";
            error_log("로그인 실패: $username");
        }
    } else {
        $_SESSION['message'] = "쿼리 실패: " . mysqli_error($con);
        error_log("로그인 쿼리 실패: " . mysqli_error($con));
    }
}

// 로그아웃 처리
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit();
}

// 계정 수정 처리
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_account'])) {
    $userid = $_SESSION['userid'];
    $email = mysqli_real_escape_string($con, $_POST['email']);
    $current_password = $_POST['current_password'];
    $new_password = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    error_log("계정 수정 시도: 사용자 ID $userid");

    $sql = "SELECT password FROM users WHERE id='$userid'";
    $result = mysqli_query($con, $sql);
    $row = mysqli_fetch_assoc($result);

    if (password_verify($current_password, $row['password'])) {
        $sql_update = "UPDATE users SET email='$email', password='$new_password' WHERE id='$userid'";
        if (mysqli_query($con, $sql_update)) {
            $_SESSION['message'] = "계정 정보가 성공적으로 업데이트되었습니다.";
            error_log("계정 정보 업데이트 성공: 사용자 ID $userid");
        } else {
            $_SESSION['message'] = "오류: " . mysqli_error($con);
            error_log("계정 정보 업데이트 오류: " . mysqli_error($con));
        }
    } else {
        $_SESSION['message'] = "현재 비밀번호가 올바르지 않습니다.";
        error_log("현재 비밀번호가 올바르지 않습니다: 사용자 ID $userid");
    }
}

// 계정 삭제 처리
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_account'])) {
    $userid = $_SESSION['userid'];
    error_log("계정 삭제 시도: 사용자 ID $userid");

    $sql_delete = "DELETE FROM users WHERE id='$userid'";

    if (mysqli_query($con, $sql_delete)) {
        $_SESSION['message'] = "계정이 성공적으로 삭제되었습니다.";
        session_destroy();
        error_log("계정 삭제 성공: 사용자 ID $userid");
    } else {
        $_SESSION['message'] = "오류: " . mysqli_error($con);
        error_log("계정 삭제 오류: " . mysqli_error($con));
    }
}

// 청원 생성 처리
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['create_petition'])) {
    $title = mysqli_real_escape_string($con, $_POST['title']);
    $content = mysqli_real_escape_string($con, $_POST['content']);
    $category = mysqli_real_escape_string($con, $_POST['category']);
    $userid = $_SESSION['userid'];

    error_log("청원 생성 시도: 제목 $title, 사용자 ID $userid");

    $sql = "INSERT INTO petitions (title, content, category, user_id) VALUES ('$title', '$content', '$category', '$userid')";

    if (mysqli_query($con, $sql)) {
        $_SESSION['message'] = "청원이 성공적으로 생성되었습니다.";
        error_log("청원 생성 성공: 제목 $title, 사용자 ID $userid");
    } else {
        $_SESSION['message'] = "오류: " . mysqli_error($con);
        error_log("청원 생성 오류: " . mysqli_error($con));
    }
}

// 청원 수정 처리
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_petition'])) {
    $petition_id = mysqli_real_escape_string($con, $_POST['petition_id']);
    $title = mysqli_real_escape_string($con, $_POST['title']);
    $content = mysqli_real_escape_string($con, $_POST['content']);
    $userid = $_SESSION['userid'];

    error_log("청원 수정 시도: 청원 ID $petition_id, 사용자 ID $userid");

    $sql = "UPDATE petitions SET title='$title', content='$content' WHERE id='$petition_id' AND user_id='$userid'";

    if (mysqli_query($con, $sql)) {
        $_SESSION['message'] = "청원이 성공적으로 수정되었습니다.";
        error_log("청원 수정 성공: 청원 ID $petition_id, 사용자 ID $userid");
    } else {
        $_SESSION['message'] = "오류: " . mysqli_error($con);
        error_log("청원 수정 오류: " . mysqli_error($con));
    }
}

// 청원 삭제 처리
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_petition'])) {
    $petition_id = mysqli_real_escape_string($con, $_POST['petition_id']);
    $userid = $_SESSION['userid'];

    error_log("청원 삭제 시도: 청원 ID $petition_id, 사용자 ID $userid");

    $sql = "DELETE FROM petitions WHERE id='$petition_id' AND user_id='$userid'";

    if (mysqli_query($con, $sql)) {
        $_SESSION['message'] = "청원이 성공적으로 삭제되었습니다.";
        error_log("청원 삭제 성공: 청원 ID $petition_id, 사용자 ID $userid");
    } else {
        $_SESSION['message'] = "오류: " . mysqli_error($con);
        error_log("청원 삭제 오류: " . mysqli_error($con));
    }
}

// 청원 서명 처리
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['sign_petition'])) {
    $petition_id = mysqli_real_escape_string($con, $_POST['petition_id']);
    $userid = $_SESSION['userid'];

    error_log("청원 서명 시도: 청원 ID $petition_id, 사용자 ID $userid");

    $check_sign = "SELECT * FROM signatures WHERE petition_id='$petition_id' AND user_id='$userid'";
    $result = mysqli_query($con, $check_sign);

    if (mysqli_num_rows($result) == 0) {
        $sql = "INSERT INTO signatures (petition_id, user_id) VALUES ('$petition_id', '$userid')";

        if (mysqli_query($con, $sql)) {
            $_SESSION['message'] = "청원에 성공적으로 서명하였습니다.";
            error_log("청원 서명 성공: 청원 ID $petition_id, 사용자 ID $userid");
        } else {
            $_SESSION['message'] = "오류: " . mysqli_error($con);
            error_log("청원 서명 오류: " . mysqli_error($con));
        }
    } else {
        $_SESSION['message'] = "이미 서명한 청원입니다.";
        error_log("이미 서명한 청원: 청원 ID $petition_id, 사용자 ID $userid");
    }
}

// 관리자 권한으로 청원 삭제
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['admin_delete_petition'])) {
    requireAdmin();
    $petition_id = mysqli_real_escape_string($con, $_POST['petition_id']);

    error_log("관리자 청원 삭제 시도: 청원 ID $petition_id");

    $sql = "DELETE FROM petitions WHERE id='$petition_id'";

    if (mysqli_query($con, $sql)) {
        $_SESSION['message'] = "청원이 성공적으로 삭제되었습니다.";
        error_log("관리자 청원 삭제 성공: 청원 ID $petition_id");
    } else {
        $_SESSION['message'] = "오류: " . mysqli_error($con);
        error_log("관리자 청원 삭제 오류: " . mysqli_error($con));
    }
}

// 관리자 권한으로 사용자 삭제
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['admin_delete_user'])) {
    requireAdmin();
    $user_id = mysqli_real_escape_string($con, $_POST['user_id']);

    error_log("관리자 사용자 삭제 시도: 사용자 ID $user_id");

    $sql = "DELETE FROM users WHERE id='$user_id'";

    if (mysqli_query($con, $sql)) {
        $_SESSION['message'] = "사용자가 성공적으로 삭제되었습니다.";
        error_log("관리자 사용자 삭제 성공: 사용자 ID $user_id");
    } else {
        $_SESSION['message'] = "오류: " . mysqli_error($con);
        error_log("관리자 사용자 삭제 오류: " . mysqli_error($con));
    }
}

// 메시지가 설정되면 자바스크립트로 전달
if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}
?>

<?php include 'header.php'; ?>

<section class="bg-gray-100 py-12">
    <div class="container mx-auto px-6">
        <h2 class="text-3xl font-bold mb-6">청원 안내</h2>
        <div id="petition-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php
            // 청원 목록 조회
            $sql = "SELECT p.*, (SELECT COUNT(*) FROM likes l WHERE l.petition_id = p.id) AS likes FROM petitions p LIMIT 8";
            $result = mysqli_query($con, $sql);
            while ($row = mysqli_fetch_assoc($result)) {
                echo '<div class="bg-white shadow rounded-lg overflow-hidden petition-card">';
                echo '<img src="https://placehold.co/300x200?text=" alt="Petition image" class="w-full h-48 object-cover">';
                echo '<div class="p-4">';
                echo '<h3 class="font-bold text-lg">' . htmlspecialchars($row['title']) . '</h3>';
                echo '<p class="text-sm mt-2 text-gray-700">' . htmlspecialchars($row['content']) . '</p>';
                echo '<div class="mt-4 flex justify-between items-center">';
                echo '<span class="text-gray-600 text-sm">청원기간: ' . htmlspecialchars($row['created_at']) . '</span>';
                echo '<button class="text-blue-600 hover:underline">자세히 보기</button>';
                echo '</div>';
                echo '<div class="mt-4 flex justify-between items-center">';
                echo '<form method="post">';
                echo '<input type="hidden" name="like_petition" value="1">';
                echo '<input type="hidden" name="petition_id" value="' . $row['id'] . '">';
                if (isset($_SESSION['userid'])) {
                    $check_like = "SELECT id FROM likes WHERE petition_id='{$row['id']}' AND user_id='{$_SESSION['userid']}'";
                    $check_like_result = mysqli_query($con, $check_like);
                    if (mysqli_fetch_assoc($check_like_result)) {
                        echo '<button type="submit" class="text-red-600 hover:underline"><i class="fas fa-heart"></i> 좋아요 취소</button>';
                    } else {
                        echo '<button type="submit" class="text-gray-600 hover:underline"><i class="far fa-heart"></i> 좋아요</button>';
                    }
                }
                echo '</form>';
                echo '<span class="text-gray-600 text-sm">' . $row['likes'] . ' Likes</span>';
                echo '</div>';
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>
        <div class="text-center mt-6">
            <button id="loadMore" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700" onclick="loadMore()">더 보기</button>
        </div>
    </div>
</section>

<?php include 'footer.php'; ?>
<?php include 'modals.php'; ?>
<script>
    let slideIndex = 0;
    showSlides();

    function showSlides() {
        let slides = document.getElementsByClassName("slides");
        for (let i = 0; i < slides.length; i++) {
            slides[i].style.display = "none";
        }
        slideIndex++;
        if (slideIndex > slides.length) {slideIndex = 1}
        slides[slideIndex-1].style.display = "block";
        setTimeout(showSlides, 5000);
    }

    function openModal(id) {
        document.getElementById(id).classList.remove('hidden');
    }

    function closeModal(id) {
        document.getElementById(id).classList.add('hidden');
    }

    function toggleDarkMode() {
        document.body.classList.toggle('dark-mode');
    }

    function loadMore() {
        const container = document.getElementById('petition-container');
        const xhr = new XMLHttpRequest();
        xhr.open('GET', 'load_more_petitions.php', true);
        xhr.onload = function() {
            if (this.status === 200) {
                const petitions = JSON.parse(this.responseText);
                petitions.forEach(petition => {
                    const div = document.createElement('div');
                    div.classList.add('bg-white', 'shadow', 'rounded-lg', 'overflow-hidden', 'petition-card');
                    div.innerHTML = `
                        <img src="https://placehold.co/300x200?text=" alt="Petition image" class="w-full h-48 object-cover">
                        <div class="p-4">
                            <h3 class="font-bold text-lg">${petition.title}</h3>
                            <p class="text-sm mt-2 text-gray-700">${petition.content}</p>
                            <div class="mt-4 flex justify-between items-center">
                                <span class="text-gray-600 text-sm">청원기간: ${petition.created_at}</span>
                                <button class="text-blue-600 hover:underline">자세히 보기</button>
                            </div>
                            <div class="mt-4 flex justify-between items-center">
                                <form method="post">
                                    <input type="hidden" name="like_petition" value="1">
                                    <input type="hidden" name="petition_id" value="${petition.id}">
                                    <button type="submit" class="text-gray-600 hover:underline"><i class="far fa-heart"></i> 좋아요</button>
                                </form>
                                <span class="text-gray-600 text-sm">${petition.likes} Likes</span>
                            </div>
                        </div>
                    `;
                    container.appendChild(div);
                });
            }
        };
        xhr.send();
    }

    window.onload = function() {
        <?php if ($message) { ?>
            document.getElementById('messageText').innerText = "<?php echo $message; ?>";
            openModal('messageModal');
        <?php } ?>
    }
</script>
</body>
</html>
