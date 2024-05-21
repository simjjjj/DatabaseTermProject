<div id="loginModal" class="fixed inset-0 hidden modal flex items-center justify-center">
    <div class="bg-white p-8 rounded shadow-lg w-96 modal-content">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">로그인</h2>
            <button class="text-gray-500 hover:text-gray-700" onclick="closeModal('loginModal')">&times;</button>
        </div>
        <form method="post">
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
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="signup" value="1">
            <div class="mb-4">
                <label for="register-name" class="block text-sm font-medium text-gray-700">이름</label>
                <input type="text" id="register-name" name="name" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            <div class="mb-4">
                <label for="register-username" class="block text-sm font-medium text-gray-700">아이디</label>
                <input type="text" id="register-username" name="username" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            <div class="mb-4">
                <label for="register-password" class="block text-sm font-medium text-gray-700">비밀번호</label>
                <input type="password" id="register-password" name="password" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            <div class="mb-4">
                <label for="register-password-confirm" class="block text-sm font-medium text-gray-700">비밀번호 확인</label>
                <input type="password" id="register-password-confirm" name="password_confirm" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            <div class="mb-4">
                <label for="register-email" class="block text-sm font-medium text-gray-700">이메일</label>
                <input type="email" id="register-email" name="email" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
            </div>
            <div class="mb-4">
                <label for="register-student-id" class="block text-sm font-medium text-gray-700">학생증 인증</label>
                <div class="flex items-center">
                    <label for="student-id" class="cursor-pointer inline-flex items-center bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                        <i class="fas fa-upload mr-2"></i> 파일 선택
                    </label>
                    <input type="file" id="student-id" name="student_id" class="hidden">
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

<script>
document.getElementById('student-id').addEventListener('change', function() {
    const fileName = this.files[0] ? this.files[0].name : '선택된 파일 없음';
    document.getElementById('student-id-filename').innerText = fileName;
});
</script>
