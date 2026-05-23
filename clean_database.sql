CREATE DATABASE IF NOT EXISTS digital_certificate_db;
USE digital_certificate_db;

CREATE TABLE admin (
    Admin_ID INT PRIMARY KEY AUTO_INCREMENT,
    Username VARCHAR(50),
    Password VARCHAR(50),
    Email VARCHAR(100)
);

INSERT INTO admin (Username, Password, Email)
VALUES ('admin', 'admin123', 'admin@gmail.com');

CREATE TABLE student (
    Student_ID INT PRIMARY KEY AUTO_INCREMENT,
    Student_Name VARCHAR(100),
    Register_Number VARCHAR(50),
    Course VARCHAR(50),
    Year VARCHAR(10),
    Institution VARCHAR(100)
);

INSERT INTO student (Student_Name, Register_Number, Course, Year, Institution) VALUES
('YOGITHA', '23C01074', 'BCA', '2026', 'Presidency College'),
('BHAGYA', '22C010800', 'BBA', '2022', 'Bangalore University'),
('KEER', '2344', 'BCA', '2023', 'Christ University');

CREATE TABLE certificate (
    Certificate_ID INT PRIMARY KEY AUTO_INCREMENT,
    Student_ID INT,
    Certificate_Type VARCHAR(500),
    Issue_Date DATE,
    Certificate_File_Path VARCHAR(200),
    Status VARCHAR(20),
    File_Hash VARCHAR(256),
    FOREIGN KEY (Student_ID) REFERENCES student(Student_ID)
);

INSERT INTO certificate
(Student_ID, Certificate_Type, Issue_Date, Certificate_File_Path, Status, File_Hash)
VALUES
(1, 'Programming Skills during Coding Bootcamp', '2026-03-17', 'certificates/CERT-2026-050.pdf', 'Valid', '18441448837ecb48670a0d187d4ff688e2aec0a1b0c5f537f4740115dd02b702'),

(2, 'Machine Learning Basics during AI Workshop', '2026-04-09', 'certificates/CERT-2026-056.pdf', 'Revoked', '15f06adf333763226c0981a0bbb65e9a5b5b6c1f33ef6ebe559ff36c59806fa8'),

(3, 'Web Technologies Excellence during Development Workshop', '2026-04-10', 'certificates/CERT-2026-057.pdf', 'Tampered', '53eb3ce51c22b7f8208b14be28fa3a663c2fcd36a173f24a81dcd8215817f8d0');

CREATE TABLE qr_code (
    QR_ID INT PRIMARY KEY AUTO_INCREMENT,
    Certificate_ID INT,
    QR_Image VARCHAR(200),
    Verification_URL VARCHAR(200),
    FOREIGN KEY (Certificate_ID) REFERENCES certificate(Certificate_ID)
);

INSERT INTO qr_code
(Certificate_ID, QR_Image, Verification_URL)
VALUES
(1, 'qrcodes/CERT-2026-050.png', 'http://localhost/digital_certificates/verify.php?id=1'),

(2, 'qrcodes/CERT-2026-056.png', 'http://localhost/digital_certificates/verify.php?id=2'),

(3, 'qrcodes/CERT-2026-057.png', 'http://localhost/digital_certificates/verify.php?id=3');

CREATE TABLE verification (
    Verification_ID INT PRIMARY KEY AUTO_INCREMENT,
    Certificate_ID INT,
    Scan_Date DATETIME,
    Verification_Result VARCHAR(20),
    FOREIGN KEY (Certificate_ID) REFERENCES certificate(Certificate_ID)
);

INSERT INTO verification
(Certificate_ID, Scan_Date, Verification_Result)
VALUES
(1, '2026-04-10 10:16:49', 'Valid'),
(2, '2026-04-09 20:49:36', 'Invalid'),
(3, '2026-03-08 18:16:55', 'Valid');