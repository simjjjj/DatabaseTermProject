<?php
include 'config.php';
require_once 'functions.php';
?>
<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>건국대학교 청원</title>
    <?php include 'styles.php'; ?>
</head>
<body class="dark-mode">
    <header class="relative pt-20">
        <div class="slideshow-container">
            <img src="a2.jpg" class="slides fade">
            <img src="a2.jpg" class="slides fade">
            <img src="a2.jpg" class="slides fade">
        </div>
        <div class="absolute top-0 left-0 w-full h-full bg-black bg-opacity-30 flex flex-col items-center justify-center text-center text-white">
            <h1 class="text-4xl md:text-6xl font-bold">건국대학교 청원</h1>
        </div>
    </header>
    <nav class="bg-white shadow fixed top-0 left-0 w-full z-50">
        <div class="container mx-auto flex items-center justify-between py-4 px-6">
            <a href="index.php">
                <img src="kulogo.png" alt="Konkuk University Logo" class="h-12">
            </a>
            <ul class="flex space-x-6">
                <li class="dropdown">
                    <a href="index.php" class="hover:text-blue-600">청원 소개</a>
                    <div class="dropdown-content">
                        <a href="petition_site_info.php">청원 사이트 소개</a>
                        <a href="petition_procedure.php">청원 절차</a>
                    </div>
                </li>
                <li><a href="#" class="hover:text-blue-600" onclick="checkLogin('createPetitionModal')">청원 하기</a></li>
                <li class="dropdown">
                    <a href="#" class="hover:text-blue-600">마이페이지</a>
                    <div class="dropdown-content">
                        <a href="#" onclick="checkLoginRedirect('mypage.php')">내가 쓴 청원</a>
                        <a href="#" onclick="checkLoginRedirect('liked_petitions.php')">좋아요 한 청원</a>
                        <a href="#" onclick="checkLoginRedirect('user_info.php')">회원정보</a>
                    </div>
                </li>
                <li><a href="#" class="hover:text-blue-600" onclick="showContactMessage()">문의하기</a></li>
                <?php if (isAdmin()) { ?>
                    <li class="dropdown">
                        <a href="#" class="hover:text-blue-600">관리자 페이지</a>
                        <div class="dropdown-content">
                            <a href="admin_dashboard.php">대시보드</a>
                            <a href="admin_approve.php">관리자 승인</a>
                        </div>
                    </li>
                <?php } ?>
            </ul>
            <div class="flex space-x-4">
                <div class="relative">
                    <form method="GET" action="search.php">
                        <input type="text" name="query" class="border px-4 py-2 rounded" placeholder="검색">
                        <button class="absolute right-2 top-2"><i class="fas fa-search"></i></button>
                    </form>
                </div>
                <?php if (!isset($_SESSION['userid'])) { ?>
                    <button class="border px-4 py-2 rounded hover:bg-gray-100" onclick="openModal('loginModal')">로그인</button>
                    <button class="border px-4 py-2 rounded hover:bg-gray-100" onclick="openModal('registerModal')">회원가입</button>
                <?php } else { ?>
                    <form id="logout-form" method="post" action="index.php">
                        <input type="hidden" name="logout" value="1">
                        <button type="submit" class="border px-4 py-2 rounded hover:bg-gray-100">로그아웃</button>
                    </form>
                <?php } ?>
                <button class="border px-4 py-2 rounded hover:bg-gray-100" onclick="toggleDarkMode()">다크 모드</button>
            </div>
        </div>
    </nav>

    <!-- Modals -->
    <div id="createPetitionModal" class="fixed inset-0 hidden modal flex items-center justify-center">
        <div class="bg-white p-8 rounded shadow-lg w-96 modal-content">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">청원하기</h2>
                <button class="text-gray-500 hover:text-gray-700" onclick="closeModal('createPetitionModal')">&times;</button>
            </div>
            <form method="post" action="create_petition.php" enctype="multipart/form-data">
                <input type="hidden" name="create_petition" value="1">
                <div class="mb-4">
                    <label for="petition-title" class="block text-sm font-medium text-gray-700">제목</label>
                    <input type="text" id="petition-title" name="title" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                </div>
                <div class="mb-4">
                    <label for="petition-content" class="block text-sm font-medium text-gray-700">내용</label>
                    <textarea id="petition-content" name="content" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required></textarea>
                </div>
                <div class="mb-4">
                    <label for="petition-attachment" class="block text-sm font-medium text-gray-700">첨부 파일</label>
                    <div class="flex items-center">
                        <label for="petition-attachment" class="cursor-pointer inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            <i class="fas fa-upload mr-2"></i> 파일 선택
                        </label>
                        <input type="file" id="petition-attachment" name="attachment" class="hidden">
                        <span id="petition-attachment-filename" class="ml-2 text-sm text-gray-600"></span>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="petition-category" class="block text-sm font-medium text-gray-700">카테고리</label>
                    <select id="petition-category" name="category" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                        <option value="" disabled selected>카테고리를 선택하세요</option>
                        <option value="학사 및 교육">학사 및 교육</option>
                        <option value="캠퍼스 시설">캠퍼스 시설</option>
                        <option value="학생 복지">학생 복지</option>
                        <option value="행정 및 정책">행정 및 정책</option>
                    </select>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">청원하기</button>
            </form>
        </div>
    </div>

    <div id="loginModal" class="fixed inset-0 hidden modal flex items-center justify-center">
        <div class="bg-white p-8 rounded shadow-lg w-96 modal-content">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">로그인</h2>
                <button class="text-gray-500 hover:text-gray-700" onclick="closeModal('loginModal')">&times;</button>
            </div>
            <form method="post" action="index.php">
                <input type="hidden" name="login" value="1">
                <div class="mb-4">
                    <label for="login-username" class="block text-sm font-medium text-gray-700">아이디</label>
                    <input type="text" id="login-username" name="username" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <div class="mb-4">
                    <label for="login-password" class="block text-sm font-medium text-gray-700">비밀번호</label>
                    <input type="password" id="login-password" name="password" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">로그인</button>
            </form>
        </div>
    </div>

    <div id="registerModal" class="fixed inset-0 hidden modal flex items-center justify-center">
        <div class="bg-white p-8 rounded shadow-lg w-96 modal-content">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">회원가입</h2>
                <button class="text-gray-500 hover:text-gray-700" onclick="closeModal('registerModal')">&times;</button>
            </div>
            <form method="post" action="register.php" enctype="multipart/form-data">
                <input type="hidden" name="signup" value="1">
                <div class="mb-4">
                    <label for="register-name" class="block text-sm font-medium text-gray-700">이름</label>
                    <input type="text" id="register-name" name="name" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                </div>
                <div class="mb-4">
                    <label for="register-username" class="block text-sm font-medium text-gray-700">아이디</label>
                    <input type="text" id="register-username" name="username" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                </div>
                <div class="mb-4">
                    <label for="register-password" class="block text-sm font-medium text-gray-700">비밀번호</label>
                    <input type="password" id="register-password" name="password" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                </div>
                <div class="mb-4">
                    <label for="register-password-confirm" class="block text-sm font-medium text-gray-700">비밀번호 확인</label>
                    <input type="password" id="register-password-confirm" name="password_confirm" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                </div>
                <div class="mb-4">
                    <label for="register-email" class="block text-sm font-medium text-gray-700">이메일</label>
                    <input type="email" id="register-email" name="email" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
                </div>
                <div class="mb-4">
                    <label for="register-student-id" class="block text-sm font-medium text-gray-700">학생증 인증</label>
                    <div class="flex items-center">
                        <label for="student-id" class="cursor-pointer inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            <i class="fas fa-upload mr-2"></i> 파일 선택
                        </label>
                        <input type="file" id="student-id" name="student_id" class="hidden" required>
                        <span id="student-id-filename" class="ml-2 text-sm text-gray-600"></span>
                    </div>
                </div>
                <div class="mb-4">
                    <label for="register-admin" class="inline-flex items-center">
                        <input type="checkbox" id="register-admin" name="is_admin" class="form-checkbox">
                        <span class="ml-2 text-sm font-medium text-gray-700">관리자 여부</span>
                    </label>
                </div>
                <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">회원가입</button>
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
            <button class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700" onclick="closeModal('messageModal')">확인</button>
        </div>
    </div>

    <div id="contactModal" class="fixed inset-0 hidden modal flex items-center justify-center">
        <div class="bg-white p-8 rounded shadow-lg w-96 modal-content">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold">문의하기</h2>
                <button class="text-gray-500 hover:text-gray-700" onclick="closeModal('contactModal')">&times;</button>
            </div>
            <p class="mb-4">나 방승재다 전화박아라</p>
            <button class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700" onclick="closeModal('contactModal')">확인</button>
        </div>
    </div>
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

function checkLogin(modalId) {
    <?php if (!isset($_SESSION['userid'])) { ?>
        document.getElementById('messageText').innerText = "로그인 후 이용 가능합니다.";
        openModal('messageModal');
    <?php } else { ?>
        openModal(modalId);
    <?php } ?>
}

function checkLoginRedirect(destination) {
    <?php if (!isset($_SESSION['userid'])) { ?>
        document.getElementById('messageText').innerText = "로그인 후 이용 가능합니다.";
        openModal('messageModal');
    <?php } else { ?>
        window.location.href = destination;
    <?php } ?>
}

function showContactMessage() {
    openModal('contactModal');
}

function toggleDarkMode() {
    document.body.classList.toggle('dark-mode');
}

function logout(event) {
    event.preventDefault();
    const formData = new FormData(document.getElementById('logout-form'));
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "index.php", true);
    xhr.onload = function () {
        if (xhr.status === 200) {
            const response = JSON.parse(xhr.responseText);
            document.getElementById('messageText').innerText = response.message;
            openModal('messageModal');
            if (response.status === 'success') {
                setTimeout(() => {
                    window.location.href = "index.php";
                }, 2000);
            }
        }
    };
    xhr.send(formData);
}

window.onload = function() {
    <?php if (isset($_SESSION['message'])) { ?>
        document.getElementById('messageText').innerText = "<?php echo $_SESSION['message']; ?>";
        openModal('messageModal');
        <?php unset($_SESSION['message']); ?>
    <?php } ?>
}
</script>
