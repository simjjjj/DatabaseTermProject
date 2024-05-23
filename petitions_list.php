<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <title>청원 목록</title>
    <script>
        function likePetition(petitionId) {
            var xhr = new XMLHttpRequest();
            xhr.open("POST", "like_petition.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    alert(xhr.responseText);
                    location.reload();
                }
            };
            xhr.send("petition_id=" + petitionId);
        }
    </script>
</head>
<body>
    <h2>청원 목록</h2>
    <?php
    include 'functions.php';
    $petitions = getPetitions(false);
    foreach ($petitions as $petition) {
        echo "<div class='petition-card'>";
        echo "<h3>" . htmlspecialchars($petition['title']) . "</h3>";
        echo "<p>" . htmlspecialchars($petition['content']) . "</p>";
        echo "<p>Likes: " . $petition['likes'] . "</p>";
        echo "<button onclick='likePetition(" . $petition['id'] . ")'>Like</button>";
        echo "</div><hr>";
    }
    ?>
</body>
</html>
