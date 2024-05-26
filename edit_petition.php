<?php
include 'config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $petitionId = intval($_POST['petition_id']);
    $title = $con->real_escape_string($_POST['title']);
    $content = $con->real_escape_string($_POST['content']);
    $category = $con->real_escape_string($_POST['category']);
    
    $update_query = "UPDATE petitions SET title = '$title', content = '$content', category = '$category' WHERE id = $petitionId";
    if ($con->query($update_query) === TRUE) {
        echo json_encode(["status" => "success", "message" => "청원이 성공적으로 수정되었습니다."]);
    } else {
        echo json_encode(["status" => "error", "message" => "청원 수정 중 오류가 발생했습니다."]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "잘못된 요청입니다."]);
}
?>
