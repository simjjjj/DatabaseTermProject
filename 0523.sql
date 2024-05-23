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
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- 테이블 구조 확인 및 초기 데이터 관리
DESCRIBE users;
DESCRIBE petitions;
DESCRIBE signatures;
DESCRIBE likes;

-- 모든 데이터베이스 목록 보기
SHOW DATABASES;

-- 테이블의 모든 데이터 보기
SELECT * FROM users;
SELECT * FROM petitions;
SELECT * FROM signatures;
SELECT * FROM likes;

-- 데이터 초기화
TRUNCATE TABLE users;
TRUNCATE TABLE petitions;
TRUNCATE TABLE signatures;
TRUNCATE TABLE likes;

-- 모든 데이터 삭제
DELETE FROM users;
DELETE FROM petitions;
DELETE FROM signatures;
DELETE FROM likes;

-- 데이터베이스 삭제
DROP DATABASE IF EXISTS konkuk_petition;
