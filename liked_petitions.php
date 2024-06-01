<?php
include 'config.php';
include 'header.php';

if (!isset($_SESSION['userid'])) {
    $_SESSION['message'] = "로그인이 필요합니다.";
    header("Location: index.php");
    exit();
}

$userId = $_SESSION['userid'];
$result = $con->query("SELECT p.* FROM petitions p INNER JOIN likes l ON p.id = l.petition_id WHERE l.user_id = $userId");
?>

<div class='container mx-auto px-6 py-12'>
    <h2 class='text-3xl font-bold mb-6'>좋아요 한 청원</h2>
    <div class='grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6'>
        <?php if ($result && $result->num_rows > 0) { 
            while ($row = $result->fetch_assoc()) { ?>
                <div class='bg-white shadow-lg rounded-lg overflow-hidden petition-card'>
                    <div class='p-4'>
                        <h3 class='font-bold text-lg'><?php echo htmlspecialchars($row['title']); ?></h3>
                        <p class='text-sm mt-2 text-gray-700'><?php echo htmlspecialchars($row['content']); ?></p>
                        <div class='mt-4 flex justify-between items-center'>
                            <span class='text-gray-600 text-sm'>청원기간: <?php echo htmlspecialchars($row['created_at']); ?></span>
                            <a href='petition_detail.php?id=<?php echo htmlspecialchars($row['id']); ?>' class='text-blue-600 hover:underline'>자세히 보기</a>
                        </div>
                        <div class='mt-4 flex justify-between items-center'>
                            <span class='text-gray-600 text-sm'><?php echo htmlspecialchars($row['likes']); ?> Likes</span>
                        </div>
                    </div>
                </div>
            <?php } 
        } else { ?>
            <p>좋아요 한 청원이 없습니다.</p>
        <?php } ?>
    </div>
</div>

<?php include 'footer.php'; ?>
