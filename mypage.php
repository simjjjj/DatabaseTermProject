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
                    <img src='https://placehold.co/300x200?text=' alt='Petition image' class='w-full h-48 object-cover'>
                    <div class='p-4'>
                        <h3 class='font-bold text-lg'><?php echo htmlspecialchars($row['title']); ?></h3>
                        <p class='text-sm mt-2 text-gray-700'><?php echo htmlspecialchars($row['content']); ?></p>
                        <div class='mt-4 flex justify-between items-center'>
                            <span class='text-gray-600 text-sm'>청원기간: <?php echo htmlspecialchars($row['created_at']); ?></span>
                            <button class='text-blue-600 hover:underline'>자세히 보기</button>
                        </div>
                        <div class='mt-4 flex justify-between items-center'>
                            <span class='text-gray-600 text-sm'><?php echo htmlspecialchars($row['likes']); ?> Likes</span>
                        </div>
                        <div class='mt-4 flex justify-between items-center'>
                            <button onclick="confirmDeletion(<?php echo $row['id']; ?>)" class="text-red-600 hover:underline">삭제</button>
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
            <button type="button" class="mr-4 bg-gray-500 text-white py-2 px-4 rounded" onclick="closeModal('delete-confirmation-modal')">취소</button>
            <button type="button" class="bg-red-600 text-white py-2 px-4 rounded" onclick="deletePetition()">삭제</button>
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
            <button type="button" class="bg-blue-600 text-white py-2 px-4 rounded" onclick="closeModal('delete-success-modal')">확인</button>
        </div>
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
    console.log("Deleting petition with ID:", petitionIdToDelete); // 디버깅을 위한 로그
    const xhr = new XMLHttpRequest();
    xhr.open("POST", "delete_petition.php", true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4) {
            console.log("AJAX response status:", xhr.status); // 디버깅을 위한 로그
            if (xhr.status == 200) {
                const response = JSON.parse(xhr.responseText);
                console.log("AJAX response:", response); // 디버깅을 위한 로그
                document.getElementById('delete-success-message').innerText = response.message;
                if (response.status === "success") {
                    document.getElementById(`petition-${petitionIdToDelete}`).remove();
                }
                closeModal('delete-confirmation-modal');
                document.getElementById('delete-success-modal').classList.remove('hidden');
            } else {
                console.error("AJAX error:", xhr.statusText); // 에러 로그
            }
        }
    };
    xhr.send(`delete_petition=1&petition_id=${petitionIdToDelete}`);
}
</script>
