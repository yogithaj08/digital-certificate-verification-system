CREATE DATABASE digital_certificate_db;
USE digital_certificate_db;

CREATE TABLE ADMIN (
    Admin_ID INT AUTO_INCREMENT PRIMARY KEY,
    Username VARCHAR(50) NOT NULL,
    Password VARCHAR(100) NOT NULL
);

INSERT INTO ADMIN (Username, Password)
VALUES ('admin','admin123');

CREATE TABLE STUDENT (
    Student_ID INT AUTO_INCREMENT PRIMARY KEY,
    Student_Name VARCHAR(100),
    Register_Number VARCHAR(50) UNIQUE,
    Course VARCHAR(100),
    Year VARCHAR(20),
    Institution VARCHAR(150)
);

CREATE TABLE CERTIFICATE (
    Certificate_ID INT AUTO_INCREMENT PRIMARY KEY,
    Student_ID INT,
    Certificate_Type VARCHAR(200),
    Issue_Date DATE,
    Certificate_File_Path VARCHAR(255),
    Status VARCHAR(20),
    File_Hash VARCHAR(256),
    FOREIGN KEY (Student_ID)
    REFERENCES STUDENT(Student_ID)
    ON DELETE CASCADE
);

CREATE TABLE QR_CODE (
    QR_ID INT AUTO_INCREMENT PRIMARY KEY,
    Certificate_ID INT,
    QR_Image VARCHAR(255),
    Verification_URL VARCHAR(255),
    FOREIGN KEY (Certificate_ID)
    REFERENCES CERTIFICATE(Certificate_ID)
    ON DELETE CASCADE
);

CREATE TABLE VERIFICATION (
    Verification_ID INT AUTO_INCREMENT PRIMARY KEY,
    Certificate_ID INT,
    Scan_Date DATETIME,
    Verification_Result VARCHAR(50),
    FOREIGN KEY (Certificate_ID)
    REFERENCES CERTIFICATE(Certificate_ID)
    ON DELETE CASCADE
);

CREATE TABLE INSTITUTION (
    Institution_ID INT AUTO_INCREMENT PRIMARY KEY,
    Institution_Name VARCHAR(150)
);

INSERT INTO INSTITUTION (Institution_Name) VALUES
('Presidency College'),
('Christ University'),
('Jain University'),
('Bangalore University');