<?php
include 'config.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

header('Content-Type: application/json');

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_petition'])) {
    $petitionId = intval($_POST['petition_id']);
    $userId = $_SESSION['userid'];

    $check_query = "SELECT * FROM petitions WHERE id = $petitionId AND user_id = $userId";
    $result = $con->query($check_query);

    if ($result->num_rows > 0) {
        $delete_query = "DELETE FROM petitions WHERE id = $petitionId";
        if ($con->query($delete_query) === TRUE) {
            echo json_encode(["status" => "success", "message" => "청원이 성공적으로 삭제되었습니다."]);
        } else {
            echo json_encode(["status" => "error", "message" => "청원 삭제 중 오류가 발생했습니다."]);
        }
    } else {
        echo json_encode(["status" => "error", "message" => "권한이 없습니다."]);
    }
    exit();
} else {
    echo json_encode(["status" => "error", "message" => "잘못된 요청입니다."]);
}
?>
