<?php
include 'config.php';
require_once 'functions.php';
include 'header.php';
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>청원 사이트 소개</title>
    <?php include 'styles.php'; ?>
</head>
<body class="dark-mode">
    <div class="container mx-auto px-6 py-12">
        <h2 class="text-3xl font-bold mb-6 text-center">청원 사이트 소개</h2>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md mb-6">
            <h3 class="text-2xl font-bold mb-4">사이트 목적</h3>
            <p class="mb-4 text-gray-700 dark:text-gray-300">
                건국대학교 청원 사이트는 학생들이 학교 생활에서 겪는 다양한 문제들을 해결하고, 학교 정책에 대한 의견을 자유롭게 표명할 수 있도록 돕기 위해 만들어졌습니다.
            </p>
        </div>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md mb-6">
            <h3 class="text-2xl font-bold mb-4">주요 기능</h3>
            <ul class="list-disc pl-6 text-gray-700 dark:text-gray-300">
                <li class="mb-2">청원 작성: 학생들이 원하는 주제로 청원을 작성할 수 있습니다.</li>
                <li class="mb-2">청원 검색: 특정 주제나 키워드로 청원을 검색할 수 있습니다.</li>
                <li class="mb-2">좋아요 기능: 마음에 드는 청원에 좋아요를 눌러 의견을 표시할 수 있습니다.</li>
                <li class="mb-2">댓글 기능: 청원에 대한 의견을 댓글로 남길 수 있습니다.</li>
                <li class="mb-2">관리자 기능: 관리자는 청원을 관리하고, 사이트 운영을 할 수 있습니다.</li>
            </ul>
        </div>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md mb-6">
            <h3 class="text-2xl font-bold mb-4">사용 방법</h3>
            <p class="mb-4 text-gray-700 dark:text-gray-300">
                <strong>1. 회원가입 및 로그인:</strong> 사이트 이용을 위해 회원가입을 하고 로그인을 합니다.<br>
                <strong>2. 청원 작성:</strong> 원하는 주제로 청원을 작성하고 제출합니다.<br>
                <strong>3. 청원 검색 및 참여:</strong> 다른 학생들이 작성한 청원을 검색하고, 좋아요를 누르거나 댓글을 작성하여 참여합니다.<br>
                <strong>4. 청원 진행 상황 확인:</strong> 작성한 청원의 진행 상황을 마이페이지에서 확인할 수 있습니다.
            </p>
        </div>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md">
            <p class="text-gray-700 dark:text-gray-300">
                건국대학교 청원 사이트는 학생들의 목소리를 듣고, 학교 정책에 반영하기 위해 최선을 다하고 있습니다. 많은 참여 부탁드립니다.
            </p>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
