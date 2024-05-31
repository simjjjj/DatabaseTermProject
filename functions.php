<?php
function isAdmin() {
    return isset($_SESSION['userid']) && isset($_SESSION['is_admin']) && $_SESSION['is_admin'];
}

function requireAdmin() {
    if (!isAdmin()) {
        $_SESSION['message'] = "관리자 권한이 필요합니다.";
        header("Location: index.php");
        exit();
    }
}

function getPetitions($con, $is_popular = false) {
    $stmt = $con->prepare("SELECT * FROM petitions WHERE is_popular = ?");
    $stmt->bind_param("i", $is_popular);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function getPetitionById($con, $id) {
    $stmt = $con->prepare("SELECT * FROM petitions WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc();
}

function addComment($con, $petition_id, $user_id, $author, $content) {
    $stmt = $con->prepare("INSERT INTO comments (petition_id, user_id, author, content, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("iiss", $petition_id, $user_id, $author, $content);
    return $stmt->execute();
}

function getCommentsByPetitionId($con, $petition_id) {
    $stmt = $con->prepare("SELECT * FROM comments WHERE petition_id = ? ORDER BY created_at ASC");
    $stmt->bind_param("i", $petition_id);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

function isLoggedIn() {
    return isset($_SESSION['userid']);
}

function requireLogin() {
    if (!isLoggedIn()) {
        $_SESSION['message'] = "로그인이 필요합니다.";
        header("Location: index.php");
        exit();
    }
}
?>
