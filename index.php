<?php
include 'config.php';
include 'functions.php';

$message = '';
// 방승재 처리 12
// 로그인 처리12
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
        $_SESSION['message'] = "잘못된 아이디 또는 비밀번호입니다. " . $con->error;
        error_log("로그인 쿼리 실패: " . $con->error);
    }
}

// 로그아웃 처리
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['logout'])) {
    session_destroy();
    session_start();
    $_SESSION['message'] = "성공적으로 로그아웃되었습니다.";
    header("Location: index.php");
    exit();
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
            <!-- 청원 목록 조회 코드 -->
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
            openModal('messageModal');
        <?php } ?>
    }
</script>
</body>
</html>
