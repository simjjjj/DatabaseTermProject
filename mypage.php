<?php
include 'config.php';
include 'header.php';

if (!isset($_SESSION['userid'])) {
    $_SESSION['message'] = "로그인이 필요합니다.";
    header("Location: index.php");
    exit();
}

$userId = $_SESSION['userid'];
$result = $con->query("SELECT * FROM petitions WHERE user_id = $userId");
?>

<div class='container mx-auto px-6 py-12'>
    <h2 class='text-3xl font-bold mb-6'>내가 쓴 청원</h2>
    <div class='grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6'>
        <?php if ($result && $result->num_rows > 0) { ?>
            <?php while ($row = $result->fetch_assoc()) { ?>
                <div id="petition-<?php echo $row['id']; ?>" class='bg-white shadow-lg rounded-lg overflow-hidden petition-card'>
                    <div class='p-4'>
                        <h3 class='font-bold text-lg'><?php echo htmlspecialchars($row['title']); ?></h3>
                        <p class='text-sm mt-2 text-gray-700'><?php echo htmlspecialchars($row['content']); ?></p>
                        <div class='mt-4 flex justify-between items-center'>
                            <span class='text-gray-600 text-sm'>청원기간: <?php echo htmlspecialchars($row['created_at']); ?></span>
                        </div>
                        <div class='mt-4 flex justify-between items-center'>
                            <span class='text-gray-600 text-sm'><?php echo htmlspecialchars($row['likes']); ?> Likes</span>
                        </div>
                        <div class='mt-4 flex justify-between items-center'>
                            <button class='text-blue-600 hover:text-blue-800 font-semibold mr-4' onclick="openEditModal(<?php echo $row['id']; ?>, '<?php echo addslashes(htmlspecialchars($row['title'])); ?>', '<?php echo addslashes(htmlspecialchars($row['content'])); ?>', '<?php echo addslashes(htmlspecialchars($row['category'])); ?>')">
                                <i class="fas fa-edit mr-2"></i>수정
                            </button>
                            <button class="text-red-600 hover:text-red-800 font-semibold" onclick="confirmDeletion(<?php echo $row['id']; ?>)">
                                <i class="fas fa-trash-alt mr-2"></i>삭제
                            </button>
                        </div>
                    </div>
                </div>
            <?php } ?>
        <?php } else { ?>
            <p>내가 쓴 청원이 없습니다.</p>
        <?php } ?>
    </div>
</div>

<div id="delete-confirmation-modal" class="fixed inset-0 hidden modal flex items-center justify-center">
    <div class="bg-white p-8 rounded shadow-lg w-96 modal-content">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">청원 삭제</h2>
            <button class="text-gray-500 hover:text-gray-700" onclick="closeModal('delete-confirmation-modal')">&times;</button>
        </div>
        <p>정말로 삭제하시겠습니까?</p>
        <div class="mt-4 flex justify-end">
            <button type="button" class="mr-4 bg-gray-500 text-white py-2 px-4 rounded hover:bg-gray-600" onclick="closeModal('delete-confirmation-modal')">취소</button>
            <button type="button" class="bg-red-600 text-white py-2 px-4 rounded hover:bg-red-700" onclick="deletePetition()">삭제</button>
        </div>
    </div>
</div>

<div id="delete-success-modal" class="fixed inset-0 hidden modal flex items-center justify-center">
    <div class="bg-white p-8 rounded shadow-lg w-96 modal-content">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">알림</h2>
            <button class="text-gray-500 hover:text-gray-700" onclick="closeModal('delete-success-modal')">&times;</button>
        </div>
        <p id="delete-success-message"></p>
        <div class="mt-4 flex justify-end">
            <button type="button" class="bg-blue-600 text-white py-2 px-4 rounded hover:bg-blue-700" onclick="closeModal('delete-success-modal')">확인</button>
        </div>
    </div>
</div>

<!-- 청원 수정 모달 -->
<div id="edit-petition-modal" class="fixed inset-0 hidden modal flex items-center justify-center">
    <div class="bg-white p-8 rounded shadow-lg w-96 modal-content">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold">청원 수정</h2>
            <button class="text-gray-500 hover:text-gray-700" onclick="closeModal('edit-petition-modal')">&times;</button>
        </div>
        <form id="edit-petition-form" method="post">
            <input type="hidden" name="petition_id" id="edit-petition-id">
            <div class="mb-4">
                <label for="edit-petition-title" class="block text-sm font-medium text-gray-700">제목</label>
                <input type="text" id="edit-petition-title" name="title" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
            </div>
            <div class="mb-4">
                <label for="edit-petition-content" class="block text-sm font-medium text-gray-700">내용</label>
                <textarea id="edit-petition-content" name="content" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required></textarea>
            </div>
            <div class="mb-4">
                <label for="edit-petition-category" class="block text-sm font-medium text-gray-700">카테고리</label>
                <input type="text" id="edit-petition-category" name="category" class="mt-1 block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">수정하기</button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>

<script>
let petitionIdToDelete;

function confirmDeletion(petitionId) {
    petitionIdToDelete = petitionId;
    document.getElementById('delete-confirmation-modal').classList.remove('hidden');
}

function closeModal(id) {
    document.getElementById(id).classList.add('hidden');
}

function deletePetition() {
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "delete_petition.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            const response = JSON.parse(xhr.responseText);
            document.getElementById('delete-success-message').innerText = response.message;
            if (response.status === "success") {
                document.getElementById(`petition-${petitionIdToDelete}`).remove();
            }
            closeModal('delete-confirmation-modal');
            document.getElementById('delete-success-modal').classList.remove('hidden');
        }
    };
    xhr.send(`delete_petition=1&petition_id=${petitionIdToDelete}`);
}

function openModal(id) {
    document.getElementById(id).classList.remove('hidden');
}

function openEditModal(id, title, content, category) {
    document.getElementById('edit-petition-id').value = id;
    document.getElementById('edit-petition-title').value = title;
    document.getElementById('edit-petition-content').value = content;
    document.getElementById('edit-petition-category').value = category;
    openModal('edit-petition-modal');
}

document.getElementById('edit-petition-form').addEventListener('submit', function(event) {
    event.preventDefault();
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "edit_petition.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            const response = JSON.parse(xhr.responseText);
            if (response.status === "success") {
                location.reload();
            } else {
                alert("청원 수정 중 오류가 발생했습니다.");
            }
        }
    };
    const data = new FormData(event.target);
    const params = new URLSearchParams(data).toString();
    xhr.send(params);
});
</script>
