<?php
include 'config.php';

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['like_petition'])) {
    $petitionId = $_POST['petition_id'];
    $userId = $_SESSION['userid'];

    if (!$userId) {
        echo json_encode(["message" => "로그인이 필요합니다.", "status" => "error"]);
        exit();
    }

    $check_like_query = "SELECT * FROM likes WHERE user_id = $userId AND petition_id = $petitionId";
    $result = $con->query($check_like_query);

    if ($result->num_rows == 0) {
        $con->query("INSERT INTO likes (user_id, petition_id) VALUES ($userId, $petitionId)");

        $result = $con->query("SELECT COUNT(*) as like_count FROM likes WHERE petition_id = $petitionId");
        $row = $result->fetch_assoc();
        $like_count = $row['like_count'];

        $con->query("UPDATE petitions SET likes = $like_count WHERE id = $petitionId");

        if ($like_count >= 10) {
            $con->query("UPDATE petitions SET is_popular = 1 WHERE id = $petitionId");
        }

        echo json_encode(["message" => "좋아요가 업데이트되었습니다.", "like_count" => $like_count]);
    } else {
        echo json_encode(["message" => "이미 좋아요를 누르셨습니다."]);
    }
} else {
    echo json_encode(["message" => "잘못된 요청입니다."]);
}
?>