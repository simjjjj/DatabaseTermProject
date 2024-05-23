<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>인기 청원</title>
    <?php include 'styles.php'; ?>
</head>
<body>
    <?php include 'header.php'; ?>
    <section class="bg-gray-100 py-12">
        <div class="container mx-auto px-6">
            <h2 class="text-3xl font-bold mb-6">인기 청원</h2>
            <div id="popular-petition-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php
                // 인기 청원 목록 조회
                $result = $con->query("SELECT * FROM petitions WHERE is_popular = 1");
                if ($result) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<div class='bg-white shadow rounded-lg overflow-hidden petition-card'>";
                        echo "<img src='https://placehold.co/300x200?text=' alt='Petition image' class='w-full h-48 object-cover'>";
                        echo "<div class='p-4'>";
                        echo "<h3 class='font-bold text-lg'>" . htmlspecialchars($row['title']) . "</h3>";
                        echo "<p class='text-sm mt-2 text-gray-700'>" . htmlspecialchars($row['content']) . "</p>";
                        echo "<div class='mt-4 flex justify-between items-center'>";
                        echo "<span class='text-gray-600 text-sm'>청원기간: " . htmlspecialchars($row['created_at']) . "</span>";
                        echo "<button class='text-blue-600 hover:underline'>자세히 보기</button>";
                        echo "</div>";
                        echo "<div class='mt-4 flex justify-between items-center'>";
                        echo "<span class='text-gray-600 text-sm'>" . htmlspecialchars($row['likes']) . " Likes</span>";
                        echo "</div>";
                        echo "</div>";
                        echo "</div>";
                    }
                } else {
                    echo "<p>인기 청원이 없습니다.</p>";
                }
                ?>
            </div>
        </div>
    </section>
    <?php include 'footer.php'; ?>
</body>
</html>
