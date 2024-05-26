<?php
include 'config.php';
include 'header.php';

$query = isset($_GET['query']) ? $con->real_escape_string($_GET['query']) : '';

$results = [];
if ($query) {
    $stmt = $con->prepare("SELECT * FROM petitions WHERE title LIKE CONCAT('%', ?, '%') OR category LIKE CONCAT('%', ?, '%')");
    $stmt->bind_param("ss", $query, $query);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $results[] = $row;
    }
}
?>

<div class="container mx-auto px-6 py-12">
  <h2 class="text-3xl font-bold mb-6">검색 결과</h2>
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <?php if ($results): ?>
      <?php foreach ($results as $row): ?>
        <div class="bg-white shadow-lg rounded-lg overflow-hidden petition-card">
          <img src="https://placehold.co/300x200?text=" alt="Petition image" class="w-full h-48 object-cover">
          <div class="p-4">
            <h3 class="font-bold text-lg"><a href="petition_detail.php?id=<?php echo $row['id']; ?>"><?php echo htmlspecialchars($row['title']); ?></a></h3>
            <p class="text-sm mt-2 text-gray-700"><?php echo htmlspecialchars($row['content']); ?></p>
            <div class="mt-4 flex justify-between items-center">
              <span class="text-gray-600 text-sm">청원기간: <?php echo htmlspecialchars($row['created_at']); ?></span>
              <button class="text-blue-600 hover:underline">자세히 보기</button>
            </div>
            <div class="mt-4 flex justify-between items-center">
              <span class="text-gray-600 text-sm"><?php echo htmlspecialchars($row['likes']); ?> Likes</span>
            </div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>검색 결과가 없습니다.</p>
    <?php endif; ?>
  </div>
</div>

<?php include 'footer.php'; ?>
