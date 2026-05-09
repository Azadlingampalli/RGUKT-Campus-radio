-- =========================================
-- CAMPUS RADIO MANAGEMENT SYSTEM DATABASE
-- =========================================

CREATE DATABASE IF NOT EXISTS campus_radio;
USE campus_radio;

-- =========================================
-- STUDENTS / USERS TABLE
-- =========================================

CREATE TABLE students (
    id INT(11) AUTO_INCREMENT PRIMARY KEY,

    name VARCHAR(100) NOT NULL,

    college_id VARCHAR(50) NOT NULL,

    email VARCHAR(150) NOT NULL UNIQUE,

    password VARCHAR(255) DEFAULT NULL,

    role ENUM('student','admin','ass_admin')
    NOT NULL DEFAULT 'student',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    status ENUM('active','blocked')
    DEFAULT 'active'
);

-- =========================================
-- NEWS TABLE
-- =========================================

CREATE TABLE news (

    news_id INT(11) AUTO_INCREMENT PRIMARY KEY,

    title VARCHAR(200) NOT NULL,

    description TEXT NOT NULL,

    category VARCHAR(50) NOT NULL,

    posted_by INT(11),

    status ENUM('pending','approved','rejected')
    DEFAULT 'pending',

    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    pinned TINYINT(1) DEFAULT 0,

    pinned_by VARCHAR(50) DEFAULT NULL,

    pinned_until DATETIME DEFAULT NULL,

    CONSTRAINT fk_news_user
    FOREIGN KEY (posted_by)
    REFERENCES students(id)
    ON DELETE SET NULL
);

-- =========================================
-- NEWS MEDIA TABLE
-- =========================================

CREATE TABLE news_media (

    media_id INT(11) AUTO_INCREMENT PRIMARY KEY,

    news_id INT(11) NOT NULL,

    media_type ENUM('image','audio','video')
    NOT NULL,

    file_path VARCHAR(255) NOT NULL,

    CONSTRAINT fk_media_news
    FOREIGN KEY (news_id)
    REFERENCES news(news_id)
    ON DELETE CASCADE
);


-- =========================================
-- INSERT DEFAULT ADMIN
-- =========================================

INSERT INTO students
(name, college_id, email, password, role, status)
VALUES
(
'Main Admin',
'B2xxxxx',
'admin@gmail.com',
MD5('admin123'),
'admin',
'active'
);

-- =========================================
-- INSERT ASSISTANT ADMIN
-- =========================================

INSERT INTO students
(name, college_id, email, password, role, status)
VALUES
(
'Assistant Admin',
'B2xxxx1',
'assistant@gmail.com',
MD5('assistant123'),
'ass_admin',
'active'
);

-- =========================================
-- INSERT STUDENT
-- =========================================

INSERT INTO students
(name, college_id, email, password, role, status)
VALUES
(
'Student User',
'B220xxx',
'student@gmail.com',
MD5('Password'),
'student',
'active'
);


-- =========================================
-- SAMPLE NEWS
-- =========================================

INSERT INTO news
(title, description, category, posted_by, status)
VALUES
(
'Campus Radio Launch',
'Our new campus radio is now live for all students.',
'Announcement',
1,
'approved'
);

-- =========================================
-- SAMPLE MEDIA
-- =========================================

INSERT INTO news_media
(news_id, media_type, file_path)
VALUES
(
1,
'image',
'uploads/sample.jpg'
);
