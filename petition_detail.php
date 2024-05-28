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
}
?>

<div class="container mx-auto px-6 py-12">
  <?php if ($petition): ?>
    <h2 class="text-3xl font-bold mb-6"><?php echo htmlspecialchars($petition['title']); ?></h2>
    <p class="mb-4"><?php echo htmlspecialchars($petition['content']); ?></p>
    <p class="text-gray-600 text-sm">청원기간: <?php echo htmlspecialchars($petition['created_at']); ?></p>
    <p class="text-gray-600 text-sm mb-6"><?php echo htmlspecialchars($petition['likes']); ?> Likes</p>
    
    <h3 class="text-2xl font-bold mb-4">댓글</h3>
    <?php if ($comments): ?>
      <?php foreach ($comments as $comment): ?>
        <div class="bg-white shadow-lg rounded-lg overflow-hidden mb-4">
          <div class="p-4">
            <p class="text-sm text-gray-700"><?php echo htmlspecialchars($comment['content']); ?></p>
            <p class="text-gray-600 text-sm">작성자: <?php echo htmlspecialchars($comment['author']); ?>, 작성일: <?php echo htmlspecialchars($comment['created_at']); ?></p>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>댓글이 없습니다.</p>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['userid'])): ?>
      <form method="POST" action="add_comment.php" class="mt-6">
        <input type="hidden" name="petition_id" value="<?php echo $petition_id; ?>">
        <textarea name="content" class="border px-4 py-2 rounded w-full" placeholder="댓글을 입력하세요" required></textarea>
        <button type="submit" class="bg-blue-600 text-white py-2 px-4 rounded mt-4">댓글 달기</button>
      </form>
    <?php else: ?>
      <p class="mt-4">댓글을 달려면 <a href="#" onclick="openModal('loginModal')" class="text-blue-600 hover:underline">로그인</a>하세요.</p>
    <?php endif; ?>
  <?php else: ?>
    <p>청원이 존재하지 않습니다.</p>
  <?php endif; ?>
</div>

<?php include 'footer.php'; ?>