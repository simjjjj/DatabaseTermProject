-- 데이터베이스 생성
CREATE DATABASE IF NOT EXISTS konkuk_petition;

-- 데이터베이스 사용
USE konkuk_petition;

-- 사용자 테이블 생성
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    username VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    student_id VARCHAR(255),
    is_admin BOOLEAN DEFAULT FALSE
);

-- 청원 테이블 생성
CREATE TABLE IF NOT EXISTS petitions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    content TEXT NOT NULL,
    category VARCHAR(255) NOT NULL,
    likes INT DEFAULT 0,
    is_popular TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    attachment VARCHAR(255),  -- 첨부 파일 경로 저장
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 서명 테이블 생성
CREATE TABLE IF NOT EXISTS signatures (
    id INT AUTO_INCREMENT PRIMARY KEY,
    petition_id INT NOT NULL,
    user_id INT NOT NULL,
    signed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (petition_id) REFERENCES petitions(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 좋아요 테이블 생성
CREATE TABLE IF NOT EXISTS likes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    petition_id INT NOT NULL,
    user_id INT NOT NULL,
    liked_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (petition_id) REFERENCES petitions(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE (petition_id, user_id) -- 중복 좋아요 방지를 위해 UNIQUE 제약 조건 추가
);

-- 댓글 테이블 생성
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    petition_id INT NOT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (petition_id) REFERENCES petitions(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 관리자 요청 테이블 생성
CREATE TABLE IF NOT EXISTS admin_requests (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    username VARCHAR(50) NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) NOT NULL,
    student_id VARCHAR(255),
    request_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- 청원 답변 테이블 생성
CREATE TABLE IF NOT EXISTS petition_responses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    petition_id INT NOT NULL,
    admin_id INT NOT NULL,
    content TEXT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (petition_id) REFERENCES petitions(id) ON DELETE CASCADE,
    FOREIGN KEY (admin_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 초기 관리자 계정 생성
INSERT INTO users (name, username, password, email, student_id, is_admin) 
VALUES ('Admin', 'admin', '$2y$10$snXwsiDL2tVCMI/rpPstc.g5Tp3cKT/STSy37aWkU2m6w5yLrNYIi', 'admin@example.com', '', TRUE)
ON DUPLICATE KEY UPDATE name=VALUES(name), password=VALUES(password), email=VALUES(email), student_id=VALUES(student_id), is_admin=VALUES(is_admin);

-- 테이블 구조 확인
DESCRIBE users;
DESCRIBE petitions;
DESCRIBE signatures;
DESCRIBE likes;
DESCRIBE comments;
DESCRIBE admin_requests;
DESCRIBE petition_responses;

-- 모든 데이터베이스 목록 보기
SHOW DATABASES;

-- 테이블의 모든 데이터 보기
SELECT * FROM users;	
SELECT * FROM petitions;
SELECT * FROM signatures;
SELECT * FROM likes;
SELECT * FROM comments;
SELECT * FROM admin_requests;
SELECT * FROM petition_responses;

SELECT * FROM users WHERE username = 'admin';

-- 데이터 초기화 및 삭제 (참조 무결성 제약 조건을 피하기 위해 순서 주의)
DELETE FROM likes;
DELETE FROM signatures;
DELETE FROM comments;
DELETE FROM petitions;
DELETE FROM users;

-- 데이터베이스 삭제
DROP DATABASE IF EXISTS konkuk_petition;
