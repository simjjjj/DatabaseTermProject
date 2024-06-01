<?php
include 'config.php';
include 'header.php';

$query = isset($_GET['query']) ? $con->real_escape_string($_GET['query']) : '';
$category = isset($_GET['category']) ? $con->real_escape_string($_GET['category']) : '';
$start_date = isset($_GET['start_date']) ? $con->real_escape_string($_GET['start_date']) : '';
$end_date = isset($_GET['end_date']) ? $con->real_escape_string($_GET['end_date']) : '';

$results = [];
if ($query || $category || ($start_date && $end_date)) {
    $sql = "SELECT * FROM petitions WHERE 1=1";

    if ($query) {
        $sql .= " AND (title LIKE '%$query%' OR content LIKE '%$query%')";
    }

    if ($category) {
        $sql .= " AND category='$category'";
    }

    if ($start_date && $end_date) {
        $sql .= " AND DATE(created_at) BETWEEN '$start_date' AND '$end_date'";
    }

    $stmt = $con->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();
    while ($row = $result->fetch_assoc()) {
        $results[] = $row;
    }
}
?>

<div class="container mx-auto px-6 py-12">
  <h2 class="text-3xl font-bold mb-6">검색 결과</h2>
  <form method="GET" action="search.php" class="mb-6">
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      <div>
        <input type="text" name="query" placeholder="키워드 검색" class="w-full px-3 py-2 border rounded" value="<?php echo htmlspecialchars($query); ?>">
      </div>
      <div>
        <select name="category" class="w-full px-3 py-2 border rounded">
          <option value="">카테고리 선택</option>
          <option value="학사 및 교육" <?php echo $category == '학사 및 교육' ? 'selected' : ''; ?>>학사 및 교육</option>
          <option value="캠퍼스 시설" <?php echo $category == '캠퍼스 시설' ? 'selected' : ''; ?>>캠퍼스 시설</option>
          <option value="학생 복지" <?php echo $category == '학생 복지' ? 'selected' : ''; ?>>학생 복지</option>
          <option value="행정 및 정책" <?php echo $category == '행정 및 정책' ? 'selected' : ''; ?>>행정 및 정책</option>
        </select>
      </div>
      <div class="flex items-center">
        <input type="date" name="start_date" class="px-3 py-2 border rounded" value="<?php echo htmlspecialchars($start_date); ?>">
        <span class="mx-2">-</span>
        <input type="date" name="end_date" class="px-3 py-2 border rounded" value="<?php echo htmlspecialchars($end_date); ?>">
      </div>
      <div>
        <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded">검색</button>
      </div>
    </div>
  </form>
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