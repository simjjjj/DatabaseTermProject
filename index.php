<?php
include 'config.php';
include 'functions.php';

$message = '';

// 로그인 및 로그아웃 처리
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $username = $con->real_escape_string($_POST['username']);
    $password = $_POST['password'];

    error_log("로그인 시도: $username");

    $sql = "SELECT id, password, is_admin FROM users WHERE username='$username'";
    $result = $con->query($sql);

    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
        if (password_verify($password, $row['password'])) {
            $_SESSION['userid'] = $row['id'];
            $_SESSION['is_admin'] = $row['is_admin'];
            $_SESSION['message'] = "성공적으로 로그인되었습니다.";
            error_log("로그인 성공: $username");
        } else {
            $_SESSION['message'] = "잘못된 아이디 또는 비밀번호입니다.";
            error_log("로그인 실패: $username");
        }
    } else {
        $_SESSION['message'] = "잘못된 아이디 또는 비밀번호입니다.";
        error_log("로그인 쿼리 실패: " . $con->error);
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['logout'])) {
    session_destroy();
    session_start();
    $_SESSION['message'] = "성공적으로 로그아웃되었습니다.";
}

if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
    echo "<script>
        window.onload = function() {
            document.getElementById('messageText').innerText = '{$message}';
            openModal('messageModal');
        }
    </script>";
}

// 카테고리별로 청원을 가져오기 위한 쿼리
$categories = ["학사 및 교육", "캠퍼스 시설", "학생 복지", "행정 및 정책"];

$category_petitions = [];
foreach ($categories as $category) {
    $stmt = $con->prepare("
        SELECT p.*, l.user_id IS NOT NULL AS liked, 
        (SELECT COUNT(*) FROM petition_responses pr WHERE pr.petition_id = p.id) AS response_count 
        FROM petitions p 
        LEFT JOIN likes l ON p.id = l.petition_id AND l.user_id = ? 
        WHERE p.category = ?
    ");
    $userId = isset($_SESSION['userid']) ? $_SESSION['userid'] : 0;
    $stmt->bind_param("is", $userId, $category);
    $stmt->execute();
    $result = $stmt->get_result();
    $category_petitions[$category] = $result->fetch_all(MYSQLI_ASSOC);
}

include 'header.php';
?>

<style>
    .section-divider {
        border-bottom: 2px solid #ccc;
        margin: 40px 0;
    }
    .section-title {
        text-align: center;
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 20px;
    }
    .petition-card img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }
</style>

<section class="bg-gray-100 py-12">
    <div class="container mx-auto px-6">
        <h2 class="section-title">인기 청원</h2>
        <div id="popular-petition-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            <?php
            $userId = isset($_SESSION['userid']) ? $_SESSION['userid'] : 0;
            $result = $con->query("
                SELECT p.*, l.user_id IS NOT NULL AS liked, 
                (SELECT COUNT(*) FROM petition_responses pr WHERE pr.petition_id = p.id) AS response_count 
                FROM petitions p 
                LEFT JOIN likes l ON p.id = l.petition_id AND l.user_id = $userId 
                WHERE p.is_popular = 1
            ");
            if ($result) {
                while ($row = $result->fetch_assoc()) {
                    echo "<div class='bg-white shadow-lg rounded-lg overflow-hidden petition-card'>";
                    echo "<div class='p-4'>";
                    echo "<div class='flex justify-between items-center'>";
                    echo "<h3 class='font-bold text-lg'><a href='petition_detail.php?id=" . htmlspecialchars($row['id']) . "'>" . htmlspecialchars($row['title']) . "</a></h3>";
                    if ($row['response_count'] > 0) {
                        echo "<p class='text-red-600 font-bold ml-4'>답변 완료</p>";
                    }
                    echo "</div>";
                    echo "<p class='text-sm mt-2 text-gray-700'>" . htmlspecialchars($row['content']) . "</p>";
                    echo "<div class='mt-4 flex justify-between items-center'>";
                    echo "<span class='text-gray-600 text-sm'>청원기간: " . htmlspecialchars($row['created_at']) . "</span>";
                    echo "<a href='petition_detail.php?id=" . htmlspecialchars($row['id']) . "' class='text-blue-600 hover:underline'>자세히 보기</a>";
                    echo "</div>";
                    echo "<div class='mt-4 flex justify-between items-center'>";
                    echo "<button onclick='likePetition(" . htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') . ")' class='text-gray-600 hover:underline'><i class='" . htmlspecialchars($row['liked'] ? 'fas text-red-600' : 'far', ENT_QUOTES, 'UTF-8') . " fa-heart' id='like-icon-" . htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') . "'></i> 좋아요</button>";
                    echo "<span id='like-count-" . htmlspecialchars($row['id'], ENT_QUOTES, 'UTF-8') . "' class='text-gray-600 text-sm'>" . htmlspecialchars($row['likes']) . " Likes</span>";
                    echo "</div>";
                    echo "</div>";
                    echo "</div>";
                }
            } else {
                echo "<p>인기 청원이 없습니다.</p>";
            }
            ?>
        </div>
        <div class="section-divider"></div>
    </div>
</section>

<!-- 추가된 카테고리별 청원 섹션 -->
<section class="bg-gray-100 py-12">
    <div class="container mx-auto px-6">
        <h2 class="section-title">청원 안내</h2>
        
        <?php foreach ($categories as $category): ?>
            <div class="mb-8">
                <h3 class="text-2xl font-bold mb-4"><?php echo $category; ?></h3>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <?php if (!empty($category_petitions[$category])): ?>
                        <?php foreach ($category_petitions[$category] as $petition): ?>
                            <div class="bg-white shadow-lg rounded-lg overflow-hidden petition-card">
                                <div class="p-4">
                                    <div class="flex justify-between items-center">
                                        <h3 class="font-bold text-lg"><?php echo htmlspecialchars($petition['title']); ?></h3>
                                        <?php if ($petition['response_count'] > 0): ?>
                                            <p class="text-red-600 font-bold ml-4">답변 완료</p>
                                        <?php endif; ?>
                                    </div>
                                    <p class="text-sm mt-2 text-gray-700"><?php echo htmlspecialchars($petition['content']); ?></p>
                                    <div class="mt-4 flex justify-between items-center">
                                        <span class="text-gray-600 text-sm">청원기간: <?php echo htmlspecialchars($petition['created_at']); ?></span>
                                        <a href="petition_detail.php?id=<?php echo htmlspecialchars($petition['id']); ?>" class="text-blue-600 hover:underline">자세히 보기</a>
                                    </div>
                                    <div class="mt-4 flex justify-between items-center">
                                        <button onclick="likePetition(<?php echo htmlspecialchars($petition['id'], ENT_QUOTES, 'UTF-8'); ?>)" class="text-gray-600 hover:underline"><i class="<?php echo htmlspecialchars($petition['liked'] ? 'fas text-red-600' : 'far', ENT_QUOTES, 'UTF-8'); ?> fa-heart" id="like-icon-<?php echo htmlspecialchars($petition['id'], ENT_QUOTES, 'UTF-8'); ?>"></i> 좋아요</button>
                                        <span id="like-count-<?php echo htmlspecialchars($petition['id'], ENT_QUOTES, 'UTF-8'); ?>" class="text-gray-600 text-sm"><?php echo htmlspecialchars($petition['likes']); ?> Likes</span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>해당 카테고리에 청원이 없습니다.</p>
                    <?php endif; ?>
                </div>
                <div class="section-divider"></div>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<?php include 'footer.php'; ?>
<?php include 'modals.php'; ?>
<?php include 'scripts.php'; ?>

<script>
    function likePetition(petitionId) {
        var xhr = new XMLHttpRequest();
        xhr.open("POST", "like_petitions.php", true);
        xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                var response = JSON.parse(xhr.responseText);
                document.getElementById('messageText').innerText = response.message;
                openModal('messageModal');
                if (response.like_count !== undefined) {
                    document.getElementById(`like-count-${petitionId}`).innerText = response.like_count + " Likes";
                    document.getElementById(`like-icon-${petitionId}`).classList.remove('far');
                    document.getElementById(`like-icon-${petitionId}`).classList.add('fas', 'text-red-600');
                }
            }
        };
        xhr.send("like_petition=1&petition_id=" + petitionId);
    }

    window.onload = function() {
        <?php if ($message) { ?>
            document.getElementById('messageText').innerText = "<?php echo $message; ?>";
            openModal('messageModal');
        <?php } ?>
    }
</script>
