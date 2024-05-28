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
    <title>청원 절차</title>
    <?php include 'styles.php'; ?>
</head>
<body class="dark-mode">
    <div class="container mx-auto px-6 py-12">
        <h2 class="text-3xl font-bold mb-6 text-center">청원 절차</h2>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md mb-6">
            <h3 class="text-2xl font-bold mb-4">청원 제출 절차</h3>
            <p class="mb-4 text-gray-700 dark:text-gray-300">
                건국대학교 청원 사이트는 학생들이 학교 생활에서 겪는 문제를 해결하고, 학교 정책에 대한 의견을 표현할 수 있도록 청원 제출 절차를 제공합니다. 아래 단계를 통해 청원을 제출할 수 있습니다.
            </p>
        </div>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md mb-6">
            <h3 class="text-2xl font-bold mb-4">청원 작성 단계</h3>
            <ol class="list-decimal pl-6 text-gray-700 dark:text-gray-300">
                <li class="mb-2"><strong>회원가입 및 로그인:</strong> 사이트를 이용하려면 먼저 회원가입을 하고 로그인을 해야 합니다.</li>
                <li class="mb-2"><strong>청원 작성:</strong> '청원 하기' 페이지로 이동하여 청원의 제목, 내용, 카테고리 등을 입력합니다.</li>
                <li class="mb-2"><strong>첨부 파일 업로드 (선택 사항):</strong> 필요한 경우 관련된 파일을 첨부할 수 있습니다.</li>
                <li class="mb-2"><strong>청원 제출:</strong> 작성한 내용을 확인한 후 청원을 제출합니다.</li>
                <li class="mb-2"><strong>청원 검토:</strong> 제출된 청원은 관리자가 검토하여 승인 여부를 결정합니다.</li>
                <li class="mb-2"><strong>청원 공개:</strong> 관리자가 승인한 청원은 사이트에 공개되어 다른 학생들이 열람하고 참여할 수 있습니다.</li>
            </ol>
        </div>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md mb-6">
            <h3 class="text-2xl font-bold mb-4">청원 참여 단계</h3>
            <ol class="list-decimal pl-6 text-gray-700 dark:text-gray-300">
                <li class="mb-2"><strong>청원 검색:</strong> 원하는 주제나 키워드로 청원을 검색할 수 있습니다.</li>
                <li class="mb-2"><strong>청원 열람:</strong> 검색된 청원을 열람하고, 내용을 확인합니다.</li>
                <li class="mb-2"><strong>좋아요 및 댓글:</strong> 마음에 드는 청원에 좋아요를 누르거나 의견을 댓글로 작성할 수 있습니다.</li>
                <li class="mb-2"><strong>청원 공유:</strong> SNS 등을 통해 청원을 다른 사람들과 공유할 수 있습니다.</li>
            </ol>
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