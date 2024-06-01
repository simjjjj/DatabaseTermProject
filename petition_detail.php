<?php
include 'config.php';
include 'header.php';

$petition_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$stmt = $con->prepare("SELECT * FROM petitions WHERE id = ?");
$stmt->bind_param("i", $petition_id);
$stmt->execute();
$result = $stmt->get_result();
$petition = $result->fetch_assoc();

$comments = [];
if ($petition) {
    $stmt = $con->prepare("SELECT * FROM comments WHERE petition_id = ? ORDER BY created_at ASC");
    $stmt->bind_param("i", $petition_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $comments[] = $row;
    }

    // 관리자 답변 가져오기
    $responses = [];
    $stmt = $con->prepare("SELECT pr.*, u.username AS admin_name FROM petition_responses pr JOIN users u ON pr.admin_id = u.id WHERE pr.petition_id = ?");
    $stmt->bind_param("i", $petition_id);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $responses[] = $row;
    }
}
?>

<div class="container mx-auto px-6 py-12">
  <?php if ($petition): ?>
    <h2 class="text-3xl font-bold mb-6 text-white-900 dark:text-white-200"><?php echo htmlspecialchars($petition['title']); ?></h2>
    <p class="mb-4 text-gray-700 dark:text-gray-300"><?php echo nl2br(htmlspecialchars($petition['content'])); ?></p>
    <p class="text-gray-600 dark:text-gray-400 text-sm">청원기간: <?php echo htmlspecialchars($petition['created_at']); ?></p>
    <p class="text-gray-600 dark:text-gray-400 text-sm mb-6"><?php echo htmlspecialchars($petition['likes']); ?> Likes</p>
    
    <h3 class="text-2xl font-bold mb-4 text-white-900 dark:text-white-200">청원 처리 결과</h3>
    <?php if ($responses): ?>
      <?php foreach ($responses as $response): ?>
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden mb-4">
          <div class="p-4">
            <p class="text-sm text-gray-700 dark:text-gray-300"><?php echo htmlspecialchars($response['content']); ?></p>
            <p class="text-gray-600 dark:text-gray-400 text-sm">작성자: <?php echo htmlspecialchars($response['admin_name']); ?>, 작성일: <?php echo htmlspecialchars($response['created_at']); ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-gray-700 dark:text-gray-300">답변이 없습니다.</p>
    <?php endif; ?>

    <h3 class="text-2xl font-bold mb-4 text-white-900 dark:text-white-200">댓글</h3>
    <?php if ($comments): ?>
      <?php foreach ($comments as $comment): ?>
        <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden mb-4">
          <div class="p-4">
            <p class="text-sm text-gray-700 dark:text-gray-300"><?php echo htmlspecialchars($comment['content']); ?></p>
            <p class="text-gray-600 dark:text-gray-400 text-sm">작성자: <?php echo htmlspecialchars($comment['author']); ?>, 작성일: <?php echo htmlspecialchars($comment['created_at']); ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p class="text-gray-700 dark:text-gray-300">댓글이 없습니다.</p>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['userid'])): ?>
      <form method="POST" action="add_comment.php" class="mt-6">
        <input type="hidden" name="petition_id" value="<?php echo $petition_id; ?>">
        <textarea name="content" class="border px-4 py-2 rounded w-full text-gray-900 dark:text-gray-300 bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600" placeholder="댓글을 입력하세요" required></textarea>
        <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded mt-4">댓글 달기</button>
      </form>
    <?php else: ?>
      <p class="mt-4 text-gray-700 dark:text-gray-300">댓글을 달려면 <a href="#" onclick="openModal('loginModal')" class="text-blue-600 hover:underline">로그인</a>하세요.</p>
    <?php endif; ?>
  <?php else: ?>
    <p class="text-gray-700 dark:text-gray-300">청원이 존재하지 않습니다.</p>
  <?php endif; ?>
</div>

<?php include 'footer.php'; ?>
