-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 11, 2025 at 02:14 AM
-- Server version: 9.1.0
-- PHP Version: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pulsescheduler`
--

-- --------------------------------------------------------

--
-- Table structure for table `appointment`
--

DROP TABLE IF EXISTS `appointment`;
CREATE TABLE IF NOT EXISTS `appointment` (
  `appointment_ID` int NOT NULL AUTO_INCREMENT,
  `user_ID` int DEFAULT NULL,
  `doctor_ID` int DEFAULT NULL,
  `compounder_ID` int DEFAULT NULL,
  `chamber_ID` int DEFAULT NULL,
  `serial_no` int DEFAULT NULL,
  `appointment_date` date DEFAULT NULL,
  `appointment_start_time` time DEFAULT NULL,
  `appointment_end_time` time DEFAULT NULL,
  `status` enum('Scheduled','Completed','Cancelled') DEFAULT NULL,
  `remark` text,
  `sch_id` int DEFAULT NULL,
  PRIMARY KEY (`appointment_ID`),
  KEY `user_ID` (`user_ID`),
  KEY `doctor_ID` (`doctor_ID`),
  KEY `compounder_ID` (`compounder_ID`),
  KEY `chamber_ID` (`chamber_ID`),
  KEY `fk_schid` (`sch_id`)
) ENGINE=InnoDB AUTO_INCREMENT=65 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `appointment`
--

INSERT INTO `appointment` (`appointment_ID`, `user_ID`, `doctor_ID`, `compounder_ID`, `chamber_ID`, `serial_no`, `appointment_date`, `appointment_start_time`, `appointment_end_time`, `status`, `remark`, `sch_id`) VALUES
(34, 33, 30, 43, 11, 1, '2025-05-26', '09:00:00', '09:30:00', 'Completed', 'N/A', 28),
(35, 33, 30, 43, 11, 1, '2025-05-21', '19:00:00', '19:30:00', 'Completed', 'N/A', 29),
(36, 47, 37, NULL, 14, 1, '2025-05-05', '14:00:00', '14:30:00', 'Scheduled', NULL, 32),
(37, 34, 38, NULL, 13, 1, '2025-05-27', '15:00:00', '15:30:00', 'Scheduled', NULL, 44),
(38, 33, 38, NULL, 13, 2, '2025-05-27', '15:30:00', '16:00:00', 'Scheduled', NULL, 44),
(39, 33, 38, NULL, 13, 3, '2025-05-27', '16:00:00', '16:30:00', 'Scheduled', NULL, 44),
(40, 32, 30, 43, 11, 1, '2025-05-02', '07:00:00', '07:30:00', 'Scheduled', 'N/A', 30),
(41, 36, 30, 43, 11, 2, '2025-05-02', '07:30:00', '08:00:00', 'Scheduled', 'N/A', 30),
(42, 35, 30, 43, 11, 3, '2025-05-02', '08:00:00', '08:30:00', 'Completed', 'N/A', 30),
(43, 34, 30, 43, 11, 4, '2025-05-02', '08:30:00', '09:00:00', 'Completed', 'N/A', 30),
(44, 31, 41, 46, 13, 1, '2025-05-20', '07:00:00', '07:30:00', 'Scheduled', 'N/A', 51),
(45, 47, 41, 46, 13, 2, '2025-05-20', '07:30:00', '08:00:00', 'Scheduled', 'N/A', 51),
(46, 45, 41, 46, 13, 3, '2025-05-20', '08:00:00', '08:30:00', 'Completed', 'N/A', 51),
(48, 34, 38, NULL, 13, 1, '2025-05-05', '15:00:00', '15:30:00', 'Scheduled', NULL, 43),
(49, 34, 38, NULL, 13, 2, '2025-05-05', '15:30:00', '16:00:00', 'Scheduled', NULL, 43),
(50, 34, 26, 43, 11, 1, '2025-05-04', '07:00:00', '07:30:00', 'Scheduled', 'N/A', 26),
(51, 51, 41, NULL, 13, 1, '2025-05-05', '07:00:00', '07:30:00', 'Scheduled', NULL, 50),
(52, 51, 26, NULL, 11, 2, '2025-05-04', '07:30:00', '08:00:00', 'Scheduled', NULL, 26),
(53, 50, 26, 43, 11, 3, '2025-05-04', '08:00:00', '08:30:00', 'Completed', 'Pays Late', 26),
(54, 30, 37, NULL, 14, 1, '2025-05-04', '14:00:00', '14:30:00', 'Scheduled', NULL, 38),
(55, 34, 37, NULL, 14, 2, '2025-05-04', '07:30:00', '08:00:00', 'Scheduled', NULL, 37),
(56, 51, 37, NULL, 14, 1, '2025-05-06', '14:00:00', '14:30:00', 'Scheduled', NULL, 34),
(57, 30, 37, NULL, 14, 2, '2025-05-05', '14:30:00', '15:00:00', 'Scheduled', NULL, 32),
(58, 36, 38, 46, 13, 1, '2025-05-06', '15:00:00', '15:30:00', 'Scheduled', 'N/A', 44),
(59, 28, 26, NULL, 11, 4, '2025-05-04', '08:30:00', '09:00:00', 'Scheduled', NULL, 26),
(60, 26, 30, NULL, 11, 1, '2025-05-09', '07:00:00', '07:30:00', 'Scheduled', NULL, 30),
(61, 29, 26, NULL, 11, 5, '2025-05-04', '09:00:00', '09:30:00', 'Scheduled', NULL, 26),
(62, 30, 37, NULL, 14, 1, '2025-05-03', '14:00:00', '14:30:00', 'Scheduled', 'Please Come in time', 36),
(63, 33, 30, NULL, 11, 1, '2025-05-12', '09:00:00', '09:30:00', 'Scheduled', NULL, 28),
(64, 33, 30, NULL, 11, 1, '2025-05-19', '09:00:00', '09:30:00', 'Scheduled', NULL, 28);

-- --------------------------------------------------------

--
-- Table structure for table `billing`
--

DROP TABLE IF EXISTS `billing`;
CREATE TABLE IF NOT EXISTS `billing` (
  `appointment_ID` int NOT NULL,
  `compounder_ID` int DEFAULT NULL,
  `discount` decimal(10,2) DEFAULT '0.00',
  `payment_date` date DEFAULT NULL,
  `payment_status` enum('Paid','Free','Pending') DEFAULT 'Pending',
  `additional_fees` decimal(10,2) DEFAULT '0.00',
  `remark` text,
  PRIMARY KEY (`appointment_ID`),
  KEY `compounder_ID` (`compounder_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `billing`
--

INSERT INTO `billing` (`appointment_ID`, `compounder_ID`, `discount`, `payment_date`, `payment_status`, `additional_fees`, `remark`) VALUES
(34, 43, 5.00, '2025-05-02', 'Paid', 1000.00, 'N/A'),
(35, 43, 10.00, '2025-05-02', 'Paid', 500.00, 'N/A'),
(42, 43, 0.00, '2025-05-02', 'Pending', 0.00, 'N/A'),
(43, 43, 10.00, '2025-05-02', 'Pending', 500.00, 'N/A'),
(46, 46, 0.00, '2025-05-02', 'Pending', 0.00, 'N/A'),
(53, 43, 5.00, '2025-05-02', 'Paid', 2000.00, 'N/A');

-- --------------------------------------------------------

--
-- Table structure for table `chamber`
--

DROP TABLE IF EXISTS `chamber`;
CREATE TABLE IF NOT EXISTS `chamber` (
  `chamber_ID` int NOT NULL AUTO_INCREMENT,
  `chamber_name` varchar(250) DEFAULT NULL,
  `opening_time` time DEFAULT NULL,
  `closing_time` time DEFAULT NULL,
  `working_days` json DEFAULT NULL,
  PRIMARY KEY (`chamber_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `chamber`
--

INSERT INTO `chamber` (`chamber_ID`, `chamber_name`, `opening_time`, `closing_time`, `working_days`) VALUES
(11, 'Prime Diagnostic Lab & Imaging ', '10:55:00', '10:54:00', '[\"Saturday\", \"Sunday\", \"Monday\", \"Tuesday\", \"Wednesday\", \"Thursday\", \"Friday\"]'),
(12, 'TrustPath Diagnostic Center', '06:00:00', '00:00:00', '[\"Saturday\", \"Sunday\", \"Monday\", \"Tuesday\", \"Wednesday\", \"Thursday\", \"Friday\"]'),
(13, 'North Star Diagnostic ', '07:00:00', '00:00:00', '[\"Saturday\", \"Sunday\", \"Monday\", \"Tuesday\", \"Wednesday\", \"Thursday\"]'),
(14, 'PeaceMind Psychiatric Services', '07:00:00', '22:00:00', '[\"Saturday\", \"Sunday\", \"Monday\", \"Tuesday\", \"Wednesday\", \"Thursday\"]'),
(16, 'Kazi\'s Care', '07:00:00', '22:00:00', '[\"Saturday\", \"Sunday\", \"Monday\", \"Friday\"]'),
(17, 'Rezaul\'s City Diabetes & Hormone Center', '08:00:00', '20:00:00', '[\"Sunday\", \"Monday\", \"Tuesday\", \"Wednesday\", \"Thursday\"]');

-- --------------------------------------------------------

--
-- Table structure for table `chamber_address`
--

DROP TABLE IF EXISTS `chamber_address`;
CREATE TABLE IF NOT EXISTS `chamber_address` (
  `address_ID` int NOT NULL AUTO_INCREMENT,
  `chamber_ID` int NOT NULL,
  `house_no` varchar(150) DEFAULT NULL,
  `road` varchar(150) DEFAULT NULL,
  `area` varchar(200) DEFAULT NULL,
  `thana` varchar(200) DEFAULT NULL,
  `district` varchar(200) DEFAULT NULL,
  `division` varchar(200) DEFAULT NULL,
  `postal_code` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`address_ID`),
  KEY `fk_chamber_address_chamber` (`chamber_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `chamber_address`
--

INSERT INTO `chamber_address` (`address_ID`, `chamber_ID`, `house_no`, `road`, `area`, `thana`, `district`, `division`, `postal_code`) VALUES
(15, 11, '32', 'Majid Saroni', ' Sonadanga', ' Sonadanga', 'Khulna', 'Khulna', '9000'),
(16, 12, '21', '49 KDA AVE', ' Sonadanga', ' Sonadanga', 'Khulna', 'Khulna', '9000'),
(17, 13, '120', 'M A Bari St', ' Boyra', ' Boyra', 'Khulna', 'Khulna', '9000'),
(18, 14, '453', '12 KDA Ave', 'Shib Bari', 'Sonadanga', 'Khulna', 'Khulna', '9100'),
(20, 16, '65', '345', 'Nirala Residential Area', 'Khulna Sader', 'Khulna', 'Khulna', '12345'),
(21, 17, '98', '203', 'Shiromoni', 'Khan Jahan Ali', 'Khulna', 'Khulna', '9300');

-- --------------------------------------------------------

--
-- Table structure for table `chamber_email`
--

DROP TABLE IF EXISTS `chamber_email`;
CREATE TABLE IF NOT EXISTS `chamber_email` (
  `email_ID` int NOT NULL AUTO_INCREMENT,
  `email` varchar(250) DEFAULT NULL,
  `chamber_ID` int DEFAULT NULL,
  PRIMARY KEY (`email_ID`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_email_ch` (`chamber_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `chamber_email`
--

INSERT INTO `chamber_email` (`email_ID`, `email`, `chamber_ID`) VALUES
(9, 'pdli@gmail.com', 11),
(10, 'TDC@gmail.com', 12),
(11, 'nsd@gmail.com', 13),
(12, 'pmps@gmail.com', 14),
(14, 'kazi@gmail.com', 16),
(15, 'rahatCDhc@gmail.com', 17);

-- --------------------------------------------------------

--
-- Table structure for table `chamber_phone`
--

DROP TABLE IF EXISTS `chamber_phone`;
CREATE TABLE IF NOT EXISTS `chamber_phone` (
  `phone_ID` int NOT NULL AUTO_INCREMENT,
  `phone` varchar(11) DEFAULT NULL,
  `chamber_ID` int DEFAULT NULL,
  PRIMARY KEY (`phone_ID`),
  KEY `fk_phone_ch` (`chamber_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=17 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `chamber_phone`
--

INSERT INTO `chamber_phone` (`phone_ID`, `phone`, `chamber_ID`) VALUES
(9, '01677889900', 11),
(10, '01677889911', 12),
(11, '01811111111', 13),
(12, '01566778844', 14),
(15, '01566778824', 16),
(16, '01723415678', 17);

-- --------------------------------------------------------

--
-- Table structure for table `compounder`
--

DROP TABLE IF EXISTS `compounder`;
CREATE TABLE IF NOT EXISTS `compounder` (
  `user_ID` int NOT NULL,
  `qualification` text,
  `chamber_ID` int DEFAULT NULL,
  PRIMARY KEY (`user_ID`),
  KEY `fk_chamber` (`chamber_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `compounder`
--

INSERT INTO `compounder` (`user_ID`, `qualification`, `chamber_ID`) VALUES
(43, 'Diploma in Medical Assistant Training – State Medical Faculty of Bangladesh\r\nCertificate in Pharmacy Dispensing – Bangladesh Pharmacy Council\r\nFirst Aid & Emergency Care Certification – BRAC Health Program', 11),
(44, 'Diploma in Medical Assistant – Institute of Health Technology (IHT), Rajshahi\r\nCommunity Health & Vaccination Training – DGHS\r\nInfection Prevention & Patient Safety – BRAC Health Program', 12),
(46, 'Diploma in Pharmacy from Dhaka Institute of Health Sciences, completed in 2022. Specialized in patient care and medication management. 2 years of experience assisting doctors in outpatient departments.', 13),
(48, 'Qualification: Diploma in Medical Assistant from Khulna Medical Institute (2016).\r\nTraining: Certified in First Aid, Injection Administration, and Prescription Handling.\r\nExperience: 5 years at Noor Clinic assisting in patient care and medicine dispensing.', 16);

-- --------------------------------------------------------

--
-- Table structure for table `doctor`
--

DROP TABLE IF EXISTS `doctor`;
CREATE TABLE IF NOT EXISTS `doctor` (
  `user_ID` int NOT NULL,
  `bmdc_registration_number` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `specialization` varchar(250) DEFAULT NULL,
  `hospital_affiliation` varchar(250) DEFAULT NULL,
  `experience_years` int DEFAULT NULL,
  `bio` text,
  `total_rating` int DEFAULT '0',
  `rating_count` int DEFAULT '0',
  `max_consultation_duration` int DEFAULT NULL,
  `qualifications` text,
  `types_of_treatments` text,
  PRIMARY KEY (`user_ID`),
  UNIQUE KEY `registration_number` (`bmdc_registration_number`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `doctor`
--

INSERT INTO `doctor` (`user_ID`, `bmdc_registration_number`, `specialization`, `hospital_affiliation`, `experience_years`, `bio`, `total_rating`, `rating_count`, `max_consultation_duration`, `qualifications`, `types_of_treatments`) VALUES
(26, '14561', 'General Physician', 'Khulna Medical College, Khulna', 6, 'I am Dr. Musfiqur Rahman, a general physician with 6 years of experience providing comprehensive healthcare to individuals of all ages. I specialize in diagnosing and treating a wide range of conditions, from common illnesses to chronic diseases. My approach focuses on preventive care, health education, and personalized treatment plans for each patient. I am affiliated with KMC, where I offer compassionate care and work closely with my patients to maintain their health.', 5, 1, 30, 'MBBS, DFM (Diploma in Family Medicine)', 'Preventive health check-ups.\r\nManagement of common illnesses (fever, cough, cold, etc.).\r\nChronic disease management (hypertension, diabetes, etc.).\r\nHealth screenings and vaccinations.\r\nFamily planning and health counseling.'),
(30, '123456', 'Cardiology', 'Khulna Medical College, Khulna', 5, 'I’m Dr. Rifat, a cardiologist committed to helping patients prevent and manage heart disease through evidence-based care and compassionate communication. With over 5 years in the field, I focus on hypertension, coronary artery disease, and heart failure management. I work closely with my patients to develop personalized care plans and emphasize the importance of lifestyle modification alongside medical treatment.', 5, 1, 30, 'MBBS, MD (Internal Medicine), DM (Cardiology), FACC', 'ECG, Echocardiography, Stress Testing\r\nCoronary Angiography & Angioplasty\r\nHeart Failure Management\r\nBlood Pressure & Cholesterol Control\r\nLifestyle & Dietary Counseling'),
(37, '123452', 'Psychiatry', ' National Institute of Mental Health (NIMH)', 5, 'I’m Dr. Rakib, a psychiatrist dedicated to helping individuals across Bangladesh overcome mental health challenges with care, dignity, and professionalism. Over the past 4 years, I’ve treated patients with a wide range of conditions including depression, anxiety, bipolar disorder, schizophrenia, and stress-related issues. I strive to create a non-judgmental, supportive environment where patients feel truly heard. Mental health is just as important as physical health, and I aim to raise awareness while providing accessible, evidence-based treatment.', 0, 0, 30, 'BSc (Hons) in Psychology – University of Dhaka\r\nMS in Clinical Psychology – Department of Psychology, University of Dhaka\r\nInternship in Psychosocial Therapy – National Institute of Mental Health, Dhaka', 'Psychiatric Consultation & Diagnosis\r\nMedication Management (Antidepressants, Antipsychotics, Mood Stabilizers)\r\nCognitive Behavioral Therapy (CBT)\r\nAnxiety & Depression Counseling\r\nSleep Disorder Management\r\nFamily Counseling & Psychoeducation\r\nSupport for Substance Abuse and Addiction Recovery'),
(38, '5453434', 'Dermatology', 'Bangladesh Specialized Hospital, Dhaka', 10, 'I am a dermatologist with over 10 years of experience in diagnosing and treating a wide range of skin, hair, and nail conditions. My approach combines clinical expertise with modern cosmetic techniques to help my patients not only heal but also feel confident in their appearance. I believe that every person’s skin is unique, so I focus on providing personalized care tailored to individual needs. I regularly attend both national and international dermatology conferences to stay updated with the latest advancements in skin health and aesthetic medicine. For me, there’s no greater satisfaction than seeing my patients regain their confidence through healthy, glowing skin.', 0, 0, 30, 'MBBS, Khulna Medical College\r\nDiploma in Dermatology & Venereology (DDV), BSMMU\r\nLaser & Cosmetic Dermatology Training – Thailand', 'Acne, eczema, psoriasis\r\nFungal & bacterial skin infections\r\nHair loss and scalp issues\r\nSkin allergy and pigmentation\r\nSkin rejuvenation, laser therapy, chemical peels'),
(41, '9450999534', 'Obstetrics & Gynecology', 'Dhaka Medical College Hospital', 5, 'As a gynecologist with over 5 years of experience, I’ve had the privilege of supporting women through every stage of life — from adolescence to motherhood and beyond. I specialize in managing high-risk pregnancies, menstrual disorders, and infertility, and I’m deeply committed to providing respectful, informed, and compassionate care. My goal is to make sure every woman I treat feels heard, safe, and confident in her healthcare choices. I believe in combining clinical excellence with clear communication and patient education. It brings me great joy to guide women through some of the most important and sensitive moments in their lives.', 5, 1, 30, 'MBBS – Chittagong Medical College\r\nFCPS (Gynecology & Obstetrics) – Bangladesh College of Physicians and Surgeons (BCPS)\r\nMS (Gynecology & Obstetrics) – Bangabandhu Sheikh Mujib Medical University (BSMMU)\r\nAdvanced Training in Laparoscopic Gynecology – India\r\nFellow, International Federation of Gynecology and Obstetrics (FIGO)', 'High-risk pregnancy management\r\nInfertility treatments (IUI, IVF)\r\nMenstrual disorders\r\nHormonal imbalance (PCOS, thyroid)\r\nLaparoscopic surgery (fibroids, cysts)\r\nC-Section & normal delivery\r\nPostpartum care\r\nMenopause management (HRT)\r\nGynecological cancer screening\r\nContraceptive counseling'),
(42, '394829344', 'Medical Oncology, Chemotherapy, Targeted Therapy', 'National Institute of Cancer Research & Hospital, Dhaka', 10, 'I am a medical oncologist with over 10 years of experience in diagnosing and treating various types of cancer. My focus is on providing personalized care to cancer patients through a combination of chemotherapy, targeted therapy, and immunotherapy. I work closely with my patients to develop treatment plans that fit their unique needs and circumstances. My goal is not only to treat cancer but also to improve the quality of life for those going through this difficult journey. I am dedicated to offering compassionate care, support, and the latest treatment options available.', 0, 0, 30, '1. MBBS – Dhaka Medical College\r\n2. MD in Oncology – Bangladesh Medical University (BMU)\r\n3. FRCP (Fellow of the Royal College of Physicians) – London\r\n4. Fellowship in Medical Oncology – Cancer Research Institute, USA', '1. Chemotherapy & immunotherapy\r\n2. Targeted therapy for cancer treatment\r\n3. Radiation therapy coordination\r\n4. Management of breast, lung, and colon cancer\r\n5. Early-stage cancer screening & diagnosis\r\n6. Palliative care and pain management\r\n7. Supportive care for cancer patients\r\n8. Post-cancer treatment follow-up and monitoring\r\n9. Cancer surgery referrals and coordination\r\n10. Genetic counseling for cancer risk'),
(49, '37482911', 'Neurology', ' National Institute of Neurosciences & Hospital, Sher-E-Bangla Nagar, Dhaka', 15, 'Hi, I’m Dr. Ayesha Siddiqua. I specialize in neurology, focusing on diagnosing and treating complex neurological disorders. My passion lies in improving the quality of life for patients with chronic brain and nerve conditions. With over 15 years of experience, I strive to combine clinical precision with empathy, and I continuously update my knowledge to stay at the forefront of neurological care.', 0, 0, 15, 'MBBS – Dhaka Medical College, University of Dhaka (2006)\r\nMD (Neurology) – Bangladesh Medical University (BMU), Dhaka (2011)\r\nFellowship in Epilepsy & Neurophysiology – All India Institute of Medical Sciences (AIIMS), Delhi (2015)\r\nMember – Bangladesh Society of Neurologists', 'Epilepsy and Seizure Disorders\r\nStroke Management and Recovery\r\nMigraine and Chronic Headache Treatment\r\nParkinson’s Disease and Movement Disorders\r\nNeuropathy and Nerve Pain\r\nMultiple Sclerosis (MS)\r\nMemory Disorders and Dementia\r\nSleep Disorders (e.g., insomnia, narcolepsy)'),
(50, '1234562', 'Endocrinology and Metabolism', 'Square Hospital, Khulna. Central Hospital, Taltoli', 13, 'I\'m Dr. Rahat, an endocrinologist dedicated to managing chronic hormonal conditions like diabetes and thyroid disorders. I understand how hormones affect your overall health, and I take a comprehensive, lifestyle-friendly approach to treatment. My mission is to help you live better, not just medicate symptoms.', 0, 0, 20, 'MBBS, Shaheed Suhrawardy Medical College (2010)\r\nMD in Endocrinology, BMU (2017)', 'Diabetes and thyroid disorder management\r\nObesity and hormonal disorder diagnosis\r\nInsulin therapy planning\r\nHormonal test interpretation'),
(52, '1234', NULL, NULL, NULL, NULL, 0, 0, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `doctor_bmdc_verification`
--

DROP TABLE IF EXISTS `doctor_bmdc_verification`;
CREATE TABLE IF NOT EXISTS `doctor_bmdc_verification` (
  `request_ID` int NOT NULL AUTO_INCREMENT,
  `user_ID` int NOT NULL,
  `bmdc_number` varchar(250) NOT NULL,
  `status` enum('Pending','Verified','Rejected') DEFAULT 'Pending',
  `admin_ID` int DEFAULT NULL,
  `request_date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`request_ID`),
  KEY `user_ID` (`user_ID`),
  KEY `admin_ID` (`admin_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `doctor_bmdc_verification`
--

INSERT INTO `doctor_bmdc_verification` (`request_ID`, `user_ID`, `bmdc_number`, `status`, `admin_ID`, `request_date`) VALUES
(6, 26, '14561', 'Verified', 39, '2025-04-29 11:27:34'),
(7, 30, '123456', 'Verified', 29, '2025-04-29 12:10:51'),
(8, 37, '123452', 'Verified', 40, '2025-05-01 07:34:31'),
(9, 38, '5453434', 'Verified', 40, '2025-05-01 07:35:38'),
(10, 41, '9450999534', 'Verified', 39, '2025-05-01 07:41:03'),
(11, 42, '394829344', 'Verified', 29, '2025-05-01 07:41:56'),
(12, 49, '37482911', 'Verified', 39, '2025-05-02 07:02:37'),
(13, 50, '1234562', 'Verified', 29, '2025-05-02 11:35:16'),
(14, 52, '1234', 'Verified', 29, '2025-05-03 12:06:50');

-- --------------------------------------------------------

--
-- Table structure for table `doctor_schedule_compounder`
--

DROP TABLE IF EXISTS `doctor_schedule_compounder`;
CREATE TABLE IF NOT EXISTS `doctor_schedule_compounder` (
  `schedule_id` int NOT NULL AUTO_INCREMENT,
  `doctor_id` int DEFAULT NULL,
  `compounder_id` int DEFAULT NULL,
  `day_of_week` enum('Saturday','Sunday','Monday','Tuesday','Wednesday','Thursday','Friday') DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  PRIMARY KEY (`schedule_id`),
  KEY `fk_doctor_schedule_doctor` (`doctor_id`),
  KEY `fk_doctor_schedule_compounder` (`compounder_id`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `doctor_schedule_compounder`
--

INSERT INTO `doctor_schedule_compounder` (`schedule_id`, `doctor_id`, `compounder_id`, `day_of_week`, `start_time`, `end_time`) VALUES
(47, 26, 43, 'Sunday', '07:00:00', '10:00:00'),
(48, 26, 43, 'Monday', '00:00:00', '04:00:00'),
(49, 26, 44, 'Sunday', '12:00:00', '16:00:00'),
(50, 30, 43, 'Monday', '09:00:00', '02:00:00'),
(51, 30, 43, 'Wednesday', '19:00:00', '00:00:00'),
(52, 30, 43, 'Friday', '19:00:00', '00:00:00'),
(53, 38, 46, 'Monday', '15:00:00', '22:00:00'),
(54, 38, 46, 'Tuesday', '15:00:00', '22:00:00'),
(55, 41, 46, 'Monday', '07:00:00', '14:00:00'),
(56, 41, 46, 'Tuesday', '07:00:00', '14:00:00'),
(57, 41, 46, 'Wednesday', '07:00:00', '12:00:00'),
(58, 42, 43, 'Tuesday', '19:00:00', '00:00:00'),
(59, 42, 43, 'Wednesday', '19:00:00', '00:00:00'),
(60, 42, 46, 'Wednesday', '07:00:00', '12:00:00');

-- --------------------------------------------------------

--
-- Stand-in structure for view `doctor_with_rating`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `doctor_with_rating`;
CREATE TABLE IF NOT EXISTS `doctor_with_rating` (
`user_ID` int
,`total_rating` int
,`rating_count` int
,`average_rating` decimal(14,4)
);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset`
--

DROP TABLE IF EXISTS `password_reset`;
CREATE TABLE IF NOT EXISTS `password_reset` (
  `request_id` int NOT NULL AUTO_INCREMENT,
  `birth_certificate_number` char(17) NOT NULL,
  `phone` varchar(11) NOT NULL,
  `request_date` datetime DEFAULT CURRENT_TIMESTAMP,
  `status` enum('Pending','Sent','Rejected') DEFAULT 'Pending',
  `matched_user_ID` int DEFAULT NULL,
  `approved_by` int DEFAULT NULL,
  PRIMARY KEY (`request_id`),
  KEY `approved_by` (`approved_by`),
  KEY `matched_user_ID` (`matched_user_ID`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `password_reset`
--

INSERT INTO `password_reset` (`request_id`, `birth_certificate_number`, `phone`, `request_date`, `status`, `matched_user_ID`, `approved_by`) VALUES
(6, '12345678901234561', '01566778847', '2025-05-02 20:21:12', 'Sent', 45, 29),
(7, '12345678901234561', '01566778847', '2025-05-20 07:26:51', 'Sent', 45, 29),
(8, '12345678901234561', '01566778847', '2025-05-20 07:28:08', 'Pending', 45, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `schedule_doctor`
--

DROP TABLE IF EXISTS `schedule_doctor`;
CREATE TABLE IF NOT EXISTS `schedule_doctor` (
  `schedule_id` int NOT NULL AUTO_INCREMENT,
  `doctor_id` int DEFAULT NULL,
  `chamber_id` int DEFAULT NULL,
  `day_of_week` enum('Saturday','Sunday','Monday','Tuesday','Wednesday','Thursday','Friday') DEFAULT NULL,
  `start_time` time DEFAULT NULL,
  `end_time` time DEFAULT NULL,
  `max_patients` int DEFAULT NULL,
  `consultation_fee` decimal(10,2) DEFAULT NULL,
  `room_number` varchar(250) DEFAULT '0000',
  PRIMARY KEY (`schedule_id`),
  KEY `fk_schedule_doctor_chamber` (`doctor_id`,`chamber_id`)
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `schedule_doctor`
--

INSERT INTO `schedule_doctor` (`schedule_id`, `doctor_id`, `chamber_id`, `day_of_week`, `start_time`, `end_time`, `max_patients`, `consultation_fee`, `room_number`) VALUES
(25, 26, 12, 'Sunday', '12:00:00', '16:00:00', 30, 1500.00, '503'),
(26, 26, 11, 'Sunday', '07:00:00', '10:00:00', 20, 1000.00, '105'),
(27, 26, 11, 'Monday', '00:00:00', '04:00:00', 30, 1500.00, '106'),
(28, 30, 11, 'Monday', '09:00:00', '02:00:00', 30, 1500.00, '403'),
(29, 30, 11, 'Wednesday', '19:00:00', '00:00:00', 30, 1500.00, '405'),
(30, 30, 11, 'Friday', '07:00:00', '12:00:00', 30, 1500.00, '406'),
(31, 37, 14, 'Monday', '07:00:00', '12:00:00', 30, 1000.00, 'A'),
(32, 37, 14, 'Monday', '14:00:00', '22:00:00', 40, 1000.00, 'A'),
(33, 37, 14, 'Tuesday', '07:00:00', '12:00:00', 30, 1000.00, 'A'),
(34, 37, 14, 'Tuesday', '14:00:00', '22:00:00', 40, 1000.00, 'A'),
(35, 37, 14, 'Saturday', '07:00:00', '12:00:00', 30, 1000.00, 'A'),
(36, 37, 14, 'Saturday', '14:00:00', '22:00:00', 40, 1000.00, 'A'),
(37, 37, 14, 'Sunday', '07:00:00', '12:00:00', 30, 1000.00, 'A'),
(38, 37, 14, 'Sunday', '14:00:00', '22:00:00', 40, 1000.00, 'A'),
(39, 37, 14, 'Wednesday', '07:00:00', '12:00:00', 30, 1000.00, 'A'),
(40, 37, 14, 'Wednesday', '14:00:00', '22:00:00', 40, 1000.00, 'A'),
(41, 37, 14, 'Thursday', '07:00:00', '12:00:00', 30, 1000.00, 'A'),
(42, 37, 14, 'Thursday', '14:00:00', '22:00:00', 40, 1000.00, 'A'),
(43, 38, 13, 'Monday', '15:00:00', '22:00:00', 40, 800.00, '108'),
(44, 38, 13, 'Tuesday', '15:00:00', '22:00:00', 40, 800.00, '108'),
(47, 42, 11, 'Tuesday', '19:00:00', '00:00:00', 40, 1500.00, '1234'),
(48, 42, 13, 'Wednesday', '07:00:00', '12:00:00', 40, 1500.00, '543'),
(49, 42, 11, 'Wednesday', '19:00:00', '00:00:00', 40, 1500.00, '1234'),
(50, 41, 13, 'Monday', '07:00:00', '14:00:00', 40, 1000.00, '1241'),
(51, 41, 13, 'Tuesday', '07:00:00', '14:00:00', 40, 1000.00, '1241'),
(52, 41, 13, 'Wednesday', '07:00:00', '12:00:00', 40, 1000.00, '1234'),
(53, 49, 13, 'Sunday', '07:00:00', '12:00:00', 20, 2000.00, '109'),
(54, 49, 13, 'Monday', '07:00:00', '12:00:00', 20, 2000.00, '109'),
(56, 30, 16, 'Sunday', '07:30:00', '21:30:00', 50, 700.00, '103'),
(57, 50, 17, 'Sunday', '08:00:00', '12:30:00', 10, 1000.00, '101'),
(58, 50, 17, 'Sunday', '15:00:00', '20:00:00', 20, 1000.00, '101');

-- --------------------------------------------------------

--
-- Table structure for table `systemuser`
--

DROP TABLE IF EXISTS `systemuser`;
CREATE TABLE IF NOT EXISTS `systemuser` (
  `user_ID` int NOT NULL AUTO_INCREMENT,
  `user_name` varchar(250) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `user_password` varchar(250) NOT NULL,
  `full_name` varchar(250) DEFAULT NULL,
  `birth_certificate_number` char(17) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL,
  `gender` enum('Male','Female','Other') DEFAULT NULL,
  `photo` text,
  `birth_day` date DEFAULT NULL,
  `blood_group` enum('A+','A-','B+','B-','O+','O-','AB+','AB-','Other') DEFAULT NULL,
  `medical_history` text,
  `role` enum('User','Admin','Head') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT 'User',
  PRIMARY KEY (`user_ID`),
  UNIQUE KEY `birth_cirtificate_number` (`birth_certificate_number`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `systemuser`
--

INSERT INTO `systemuser` (`user_ID`, `user_name`, `user_password`, `full_name`, `birth_certificate_number`, `gender`, `photo`, `birth_day`, `blood_group`, `medical_history`, `role`) VALUES
(26, 'musfi', '$2y$10$wD33.iwparsg2RBbtYVuee1WSg/ToGRAshrtI3tEWpdS5pQgdoG2a', 'Musfiqur Rahman', '12345678901234567', 'Male', 'uploads/profile_pics/user_26_1746076812.jpg', '1991-10-30', 'B+', 'Chief Complaint: Frequent urination and fatigue\r\nHistory: Symptoms ongoing for 3 months, increased thirst\r\nPast History: Newly diagnosed type 2 diabetes\r\nFamily History: Brother has diabetes', 'User'),
(27, 'summa', '$2y$10$r69NaxDHLZvnWfx7K38x9.fwnS0TiDBV5rHpXRq9HPbQr93fSlyLy', 'Sumiya Afrin', '12983467129803452', 'Female', 'uploads/profile_pics/user_27_1746069701.jpg', '2004-06-05', 'B+', 'Chief Complaint: Recurrent headaches\r\nHistory: Throbbing headache, photophobia, nausea\r\nPast History: Diagnosed with migraine at 18\r\nMeds: Uses paracetamol', 'Head'),
(28, 'shamima', '$2y$10$7LDHChcLMQghBDWO27EbBecDnX5JSVdi.ech0mbSYTyjhlOHjmT7m', 'Shamima Aktar', '34671209834126753', 'Female', 'uploads/profile_pics/user_28_1746064696.jpg', '1982-03-12', 'B-', 'Type 2 Diabetes Mellitus\r\nHypertension\r\nIschemic Heart Disease\r\nDiabetic Retinopathy – mild non-proliferative stage\r\nMedications: Metformin, Amlodipine, Atorvastatin, Aspirin\r\nFamily History: Father had diabetes and died of stroke at 65', 'User'),
(29, 'yashin', '$2y$10$mFwFXSZzCUuv4hv0o1zpUua5OK7i99Xu0vxgGHZ7oatlmRfra.xWy', 'Yashin Hossain', '12098734561298345', 'Male', 'uploads/profile_pics/user_29_1746064877.jpg', '2000-04-12', 'AB+', 'Asthma – diagnosed at age 10, uses an inhaler as needed\r\nFamily History: Father has asthma, mother has hypertension', 'Admin'),
(30, 'kazi', '$2y$10$XxJgRdrvhciViDr6w/lGT./g5eLJLwtQrydF0Da4cRFLCCj07pHQK', 'Kazi Rifat', '12348956120983541', 'Male', 'uploads/profile_pics/user_30_1746084986.jpg', '2004-08-16', 'O+', 'Allergic Rhinitis – symptoms include sneezing, nasal congestion, itchy throat; triggered by dust and vehicle emissions\r\nMild Asthma – occasional wheezing during allergy season (spring and pre-monsoon)\r\nAllergy Triggers: House dust, air pollution, strong perfume', 'User'),
(31, 'mohi', '$2y$10$amlkdwj4VAM6CNruX1caJ.jsCfNHg67bBIGSJ8QE377wSB7mP4q36', 'Mohima Mohi', '12903456128765239', 'Female', 'uploads/profile_pics/user_31_1746065411.jpg', '2004-04-12', 'O+', 'Migraine – recurrent episodes since teenage years, usually triggered by stress or lack of sleep\r\nHypoglycemia – occasional episodes of dizziness and weakness if meals are skipped', 'User'),
(32, 'munia', '$2y$10$44Flc.s/WijFEhE77zDbvOviPg0Ueq21UFmGnLbMq3wyZ9tPwevxK', 'Mahjabin Munia', '12367340912367342', 'Female', 'uploads/profile_pics/user_32_1746069991.jpg', '2004-05-13', 'O-', 'Iron-deficiency anemia, generalized anxiety disorder, and mild depression.\r\nSymptoms: fatigue, dizziness, low mood, and poor concentration.', 'Head'),
(33, 'tuly', '$2y$10$p3kTbBpX7QGoLSgPWZ0cBewDCqsaEaa8llHgB5mAISDAk7joVrDVS', 'Sohana Rahman Tuly', '12345678901234560', 'Female', 'uploads/profile_pics/user_33_1746085222.jpg', '2004-01-09', 'B+', 'Dust Allergy – frequent sneezing and itchy eyes while working in closed spaces\r\nEye Allergy – redness and tearing due to prolonged screen exposure + environmental irritants\r\nMedications: Antihistamine eye drops, nasal spray (Fluticasone), Levocetirizine tablets\r\nAllergy Triggers: Dust, pollution, electronic screen time', 'Head'),
(34, 'seam', '$2y$10$zf13Y02uDo4HMx5rrLbDj.r81rJ/tgtYfO1K.0.NXp//jnlfnjfje', 'Ibnul Abrar Shahriar Seam', '12345678901230061', 'Male', 'uploads/profile_pics/user_34_1746085147.jpg', '2004-02-29', 'O+', 'Dust Allergy – triggered in classrooms, old hostels, and libraries\r\nMild Intermittent Asthma – occasional wheezing when exposed to dust\r\nMedications: Montelukast, Levocetirizine, Salbutamol inhaler (as needed)\r\nAllergy Triggers: Book dust, room dust, dry air', 'User'),
(35, 'siam', '$2y$10$wnI3bLDZhtXw8qxCVM85fO4lqBuIfO.EoTtejheAiCPDzDUXFxu.i', 'MD. SIAM AHMED', '12345678901234511', 'Male', 'uploads/profile_pics/user_35_1746068362.jpg', '2004-05-12', 'B+', 'Generalized Anxiety Disorder (GAD) – excessive worry about academic performance and future job prospects\r\nSymptoms: Restlessness, poor concentration, frequent headaches, stomach discomfort\r\nMedications: Clonazepam (as needed, short-term), Propranolol (low dose for physical symptoms)', 'User'),
(36, 'toma', '$2y$10$2a0AokCxjCOiOo0M7TjPdu0.GV4eMzXKfWWgJDkDEnHEcMwrkN3r6', 'Toma Rani', '12345678901234111', 'Female', 'uploads/profile_pics/user_36_1746068556.jpg', '2004-07-22', 'B+', 'Condition: Scalp Dermatitis & Hair Fall\r\nSymptoms: Itchy, flaky scalp; gradual hair thinning over 6 months\r\nDiagnosis: Seborrheic dermatitis with telogen effluvium', 'User'),
(37, 'rakib', '$2y$10$oGstzFER2L.z3gLer2BQ4ucKlKQ7/uT99GZ2nEWFb.NdlN6N3XcAe', 'Md. Rakib Hossain', '12341118901234561', 'Male', 'uploads/profile_pics/user_37_1746068868.jpg', '1991-11-23', 'B+', 'Condition: Chronic Lower Back Pain\r\nSymptoms: Dull aching in the lower back, worsens after sitting for long lectures or using laptop in bed\r\nDiagnosis: Postural back strain', 'User'),
(38, 'tanvir', '$2y$10$g5w1ToU1.BVeF30iGFWvLOYXj7xSg3AHtAKthDM12TC4hKSdalurC', 'Tanvir Ahmed', '12345678901234544', 'Male', 'uploads/profile_pics/user_38_1746069025.jpg', '1987-12-27', 'A+', 'Migraine – Recurrent throbbing headache, mostly on the right side, often preceded by light sensitivity and nausea. Triggered by lack of sleep and exam stress.\r\nUpper Back Pain – Aching and tightness in shoulder and neck region, worsens during long study hours', 'User'),
(39, 'anwar', '$2y$10$zblGf9ZbDvx2o0ppJbLFhOx3xAmCi6oyhffPUljeuGqPZDLuIgDYK', 'Anwar Hossain', '12345678901234991', 'Male', 'uploads/profile_pics/user_39_1746069151.jpg', '1991-12-15', 'AB+', 'Language Problem: Mild stammering (stuttering), especially during stress or speaking to strangers\r\nCalcium Deficiency:\r\nSymptoms: Leg cramps at night, occasional numbness in fingers, brittle nails\r\nLab Report: Serum calcium slightly below normal (7.8 mg/dL)', 'Admin'),
(40, 'saiful', '$2y$10$wUYd.rbnEj1NST964CLMnO32ZDkIbGbUyt7c4jIv9cW.kfWuULnRq', 'Saiful Alam', '12345678901234522', 'Male', 'uploads/profile_pics/user_40_1746069295.jpg', '2000-09-16', 'AB-', 'Kidney Problem: Renal Calculi (Kidney Stones)\r\nSymptoms: Sudden sharp lower back/flank pain, blood in urine, nausea\r\nDiagnosis: 5 mm stone in left ureter seen on ultrasound', 'Admin'),
(41, 'nasrin', '$2y$10$2QYpbkHwRCl420nDjdm0qOExx/mOzdSw/.b4MTL.WGWBwHNXxYMp.', 'Nasrin Sultana', '12345342167543890', 'Female', 'uploads/profile_pics/user_41_1746069564.jpg', '1995-08-04', 'O+', 'Eczema (Atopic Dermatitis)\r\nSymptoms: Itchy, dry patches on arms and neck, worsens in winter and after using detergent\r\nDiagnosis: Mild chronic eczema', 'User'),
(42, 'farzana', '$2y$10$6Z0shxoVE.QKCNtTfT.Ys.hLvpmcWT4g3Q0Ibs9DY3XJ8am0480GO', 'Farzana Akter', '12345678901334561', 'Female', 'uploads/profile_pics/user_42_1746069426.jpg', '1987-06-20', 'AB+', 'Iron Deficiency Anemia\r\nSymptoms: Fatigue, dizziness, pale skin, shortness of breath during light activity\r\nLab Results:\r\nHemoglobin: 9.2 g/dL\r\nMCV: Low (microcytic anemia)\r\nSerum Ferritin: Low', 'User'),
(43, 'kamal', '$2y$10$RQ4QYRfdK857H0.UdNYLGuaA6f2bYECpOxTvivHTuNIllnYDeUaaC', 'Kamal Uddin', '10012312312002120', 'Male', 'uploads/profile_pics/user_43_1746158911.jpg', '1999-12-27', 'B+', NULL, 'User'),
(44, 'rana', '$2y$10$GismSexP7jqnL7T7.eo29.k6uxom6wY7S2UpYPIVjY.FmUnmxHi2i', 'Masud Rana', '34343434343434343', 'Male', 'uploads/profile_pics/user_44_1746083424.gif', '1991-12-15', 'AB+', NULL, 'User'),
(45, 'nurbanu', '$2y$10$saY.widOm1fKgFbmgHtg5.sUv8Z1gKYRLv80NK9c.3gv6BXd8QjCe', 'Nur Banu Khanom', '12345678901234561', 'Female', 'uploads/profile_pics/user_45_1746149149.jpg', '1991-07-12', 'AB+', 'Hypertension: Diagnosed in 2017; managed with lifestyle modifications and medication.\r\nMild Hypothyroidism: Diagnosed in 2019; controlled with Levothyroxine.', 'User'),
(46, 'monir', '$2y$10$YcIRrxM1HFZQmlUX.N1FfODa/elCQvPa4f0v96IG8pObWcLomz8TG', 'Monir Mia', '12445678901234560', 'Male', 'uploads/profile_pics/user_46_1746084163.jpg', '1991-12-15', 'O-', NULL, 'User'),
(47, 'jamila', '$2y$10$xaFC/UYstBU70H9GW.E9Qe96s99Yqy3k4DGGJTpvGWY.o4MvGKara', 'Jamila Khatun', '34343434343434399', 'Female', 'uploads/profile_pics/user_47_1746090752.jpg', '1991-11-23', 'AB+', NULL, 'User'),
(48, 'rahim', '$2y$10$fRF9NFC/vF84RisiUSAWyO4J.YpEHYqahc5Nh66GONerzd9GXo5Je', 'Md. Rahim Uddin', '19900315123456789', 'Male', 'uploads/profile_pics/user_48_1746147309.jpg', '1991-03-15', 'B-', 'Penicillin – Causes skin rash and itching\r\nDust/Pollen – Triggers sneezing, runny nose, and watery eyes (seasonal allergy)\r\n\r\nLifestyle Notes:\r\nSmoker: No\r\nAlcohol: No\r\nDiet: Moderate carbohydrate intake, advised low-sodium diet\r\nPhysical Activity: Light walking daily (30 minutes)', 'User'),
(49, 'asu', '$2y$10$B0pzUFbXFFmwAidyKv.Zl.fQUIcYeXC/Y3HdOPt.JZIKwr7.f3woS', 'Ayesha Siddiqua', '19820610123456789', 'Female', 'uploads/profile_pics/user_49_1746148049.jpg', '1982-06-10', 'AB+', 'Iron-deficiency Anemia (managed through diet and supplements)\r\nMild Hypothyroidism (Diagnosed in 2019, controlled with medication)\r\nLatex (mild skin irritation from gloves)', 'User'),
(50, 'rahat', '$2y$10$1dHDqP9RRsZjiJKua7mv5uMcroCCM0E.SyjhI3E4TT2QQburL188u', 'Mahmud Hasan Rahat', '19841207000045987', 'Male', 'uploads/profile_pics/user_50_1746193798.jpg', '1987-02-05', 'A+', 'Allergic Rhinitis – Diagnosed in early adulthood; controlled with seasonal antihistamines.\r\nLumbar Disc Herniation (L4-L5) – Diagnosed in 2016; managed through physiotherapy and posture correction.\r\nHypertension (Stage 1) – Diagnosed in 2020; managed with low-dose medication and lifestyle changes.\r\nCOVID-19 (Moderate) – Recovered in 2021; no long-term complications observed.', 'User'),
(51, 'laboni', '$2y$10$Lz7dL9H3IGERvn.YaAze1uMy6oTdkcnXJd6CJJyxau.Ev.TMy4Y6K', 'Laboni Akther', '12342409854324524', 'Female', 'uploads/profile_pics/user_51_1746191608.jpg', '2004-04-19', 'A+', 'Diagnosed with mild anemia (Hb: 10.2 g/dL)\r\nReceived HPV vaccine at school\r\nOccasional migraines\r\nNo hospitalizations or surgeries.', 'User'),
(52, 'rashi', '$2y$10$sCUKw9CHuikYpDbLuqgR3Ova4qIHeBkbT7OoRZg8APBik61PwZqr2', 'Rashi Khanom', '12345678901234599', 'Female', 'uploads/profile_pics/user_52_1746252598.jpg', NULL, 'AB+', NULL, 'User');

-- --------------------------------------------------------

--
-- Table structure for table `unavailability`
--

DROP TABLE IF EXISTS `unavailability`;
CREATE TABLE IF NOT EXISTS `unavailability` (
  `unavailability_id` int NOT NULL AUTO_INCREMENT,
  `doctor_id` int NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `reason` text,
  PRIMARY KEY (`unavailability_id`),
  KEY `fk_unavailability_doctor` (`doctor_id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `unavailability`
--

INSERT INTO `unavailability` (`unavailability_id`, `doctor_id`, `start_date`, `end_date`, `reason`) VALUES
(4, 30, '2025-05-05', '2025-05-06', 'Conference'),
(5, 30, '2025-03-05', '2025-05-05', 'Personal leave');

-- --------------------------------------------------------

--
-- Table structure for table `user_address`
--

DROP TABLE IF EXISTS `user_address`;
CREATE TABLE IF NOT EXISTS `user_address` (
  `address_ID` int NOT NULL AUTO_INCREMENT,
  `user_ID` int NOT NULL,
  `house_no` varchar(200) DEFAULT NULL,
  `road` varchar(200) DEFAULT NULL,
  `area` varchar(200) DEFAULT NULL,
  `thana` varchar(200) DEFAULT NULL,
  `district` varchar(200) DEFAULT NULL,
  `division` varchar(200) DEFAULT NULL,
  `postal_code` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`address_ID`),
  KEY `fk_user_address_user` (`user_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_address`
--

INSERT INTO `user_address` (`address_ID`, `user_ID`, `house_no`, `road`, `area`, `thana`, `district`, `division`, `postal_code`) VALUES
(19, 26, '21', '4', ' Sonadanga', ' Sonadanga', 'Khulna', 'Khulna', '9000'),
(20, 27, ' 22/B', 'Shah Shirin Road', 'Gollamari', 'Khulna Sader', 'Khulna', 'Khulna', '33233'),
(21, 28, '34', '11', 'Khalishpur Industrial Area', 'Khalishpur', 'Khulna', 'Khulna', '2322222'),
(22, 29, '4', '2', 'Fultola', 'Fultola', 'Khulna', 'Khulna', '3443'),
(23, 30, '23', '5', 'Nirala Residential Area', 'Khulna Sadar', 'Khulna', 'Khulna', '534343'),
(24, 31, '3', 'Shah Shirin Road', 'Gollamari', 'Khulna Sader', 'Khulna', 'Khulna', '2323'),
(25, 33, '3', 'Gobor Chaka Main Road', 'Sonadanga', 'Sonadanga', 'Khulna', 'Khulna', '1212'),
(26, 34, '45', 'Hall Road', 'Gollamari', 'Khulna Sader', 'Khulna', 'Khulna', '2121'),
(27, 35, '45', 'Hall Road', 'Gollamari', 'Khulna Sader', 'Khulna', 'Khulna', '2121'),
(28, 36, '45', 'Hall Road', 'Gollamari', 'Khulna Sader', 'Khulna', 'Khulna', '2121'),
(29, 37, '45', '45', ' Daulatpur', ' Daulatpur', 'Khulna', 'Khulna', '2121'),
(30, 38, '45', '102', ' Moylapota', 'Khulna Sadar', 'Khulna', 'Khulna', '2121'),
(31, 39, '232', '103', ' Moylapota', 'Khulna Sadar', 'Khulna', 'Khulna', '2121'),
(32, 40, '23', 'Nobi Nagar Road', 'Gobor Chaka', 'Sonadanga', 'Khulna', 'Khulna', '2121'),
(33, 42, '15', 'Majid Sarani', 'Sonadanga', 'Sonadanga', 'Khulna', 'Khulna', '2121'),
(34, 41, '15', '21', 'Shiromoni', 'Khan Jahan Ali', 'Khulna', 'Khulna', '2121'),
(35, 32, '2', 'Mujgunni', 'Boyra', 'Boyra', 'Khulna', 'Khulna', '12233'),
(36, 43, '', '', 'Sonadanga 2nd phase', 'Sonadanga', 'Khulna', 'Khulna', '1234'),
(37, 44, '', 'Shah Shirin Road', 'Gollamari', 'Khulna Sader', 'Khulna', 'Khulna', '1234'),
(38, 46, '', 'Majid Sarani', 'Sonadanga', 'Sonadanga', 'Khulna', 'Khulna', '1234'),
(39, 47, '', '123', 'Sonadanga', 'Sonadanga', 'Khulna', 'Khulna', '1231'),
(40, 48, '32', 'Majid Saroni', 'Sonadanga', 'Sonadanga', 'Khulna', 'Khulna', '9000'),
(41, 49, '45', '124', 'Boyra', 'Boyra', 'Khulna', 'Khulna', '9400'),
(42, 45, '23', '67', 'Sonadanga', 'Sonadanga', 'Khulna', 'Khulna', '9100'),
(43, 50, '11', '2334', 'Moylapota', 'Khulna Sader', 'Khulna', 'Khulna', '345453'),
(44, 51, '11', '2334', 'Moylapota', 'Khulna Sader', 'Khulna', 'Khulna', '345453'),
(45, 52, '', '', '', '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `user_email`
--

DROP TABLE IF EXISTS `user_email`;
CREATE TABLE IF NOT EXISTS `user_email` (
  `email_ID` int NOT NULL AUTO_INCREMENT,
  `email` varchar(250) DEFAULT NULL,
  `user_ID` int DEFAULT NULL,
  PRIMARY KEY (`email_ID`),
  KEY `fk_email` (`user_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_email`
--

INSERT INTO `user_email` (`email_ID`, `email`, `user_ID`) VALUES
(26, 'musfiq@gmail.com', 26),
(27, 'summa@gmail.com', 27),
(28, 'shamima@gmail.com', 28),
(29, 'yashin@gmail.com', 29),
(30, 'kazi@gmail.com', 30),
(31, 'mohi@gmail.com', 31),
(32, 'munia234@gmail.com', 32),
(33, 'tuly67@gmail.com', 33),
(34, 'seam@gmail.com', 34),
(35, 'siam@gmail.com', 35),
(36, 'toma@gmail.com', 36),
(37, 'rakib@gmail.com', 37),
(38, 'tanvir@gmail.com', 38),
(39, 'anwar@gmail.com', 39),
(40, 'saiful@gmail.com', 40),
(41, 'nasrin@gmail.com', 41),
(42, 'nasrin@gmail.com', 42),
(43, 'kamal@gmail.com', 43),
(44, 'masudrana@gmail.com', 44),
(49, 'ayesha.siddiqua@gmail.com', 49),
(50, 'rahat@gmail.com', 50),
(51, 'laboni@gmail.com', 51),
(52, 'rashi@gmail.com', 52);

-- --------------------------------------------------------

--
-- Table structure for table `user_phone`
--

DROP TABLE IF EXISTS `user_phone`;
CREATE TABLE IF NOT EXISTS `user_phone` (
  `phone_ID` int NOT NULL AUTO_INCREMENT,
  `phone` varchar(11) DEFAULT NULL,
  `user_ID` int DEFAULT NULL,
  PRIMARY KEY (`phone_ID`),
  KEY `fk_phone` (`user_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=59 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `user_phone`
--

INSERT INTO `user_phone` (`phone_ID`, `phone`, `user_ID`) VALUES
(30, '01677889900', 26),
(31, '01566778844', 27),
(32, '01982356123', 28),
(33, '01323232323', 29),
(34, '01884523091', 30),
(35, '01987234561', 31),
(36, '01811111111', 32),
(37, '01987452631', 33),
(38, '01930555998', 34),
(39, '01925935512', 35),
(40, '01722003142', 36),
(41, '01677889900', 37),
(42, '01811111111', 38),
(43, '01566778841', 39),
(44, '01566778111', 40),
(45, '01723423456', 41),
(46, '01566123432', 42),
(47, '01720141468', 30),
(48, '01571505512', 30),
(49, '01677889900', 43),
(50, '01566778844', 44),
(51, '01566778847', 45),
(52, '01566774114', 46),
(53, '01566778841', 47),
(54, '01787654321', 48),
(55, '01711122233', 49),
(56, '01912123112', 50),
(57, '01934567893', 51),
(58, '01811111111', 52);

-- --------------------------------------------------------

--
-- Stand-in structure for view `user_with_age`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `user_with_age`;
CREATE TABLE IF NOT EXISTS `user_with_age` (
`user_ID` int
,`full_name` varchar(250)
,`birth_day` date
,`age` bigint
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_billing_summary`
-- (See below for the actual view)
--
DROP VIEW IF EXISTS `view_billing_summary`;
CREATE TABLE IF NOT EXISTS `view_billing_summary` (
`appointment_ID` int
,`appointment_date` date
,`serial_no` int
,`additional_fees` decimal(10,2)
,`remark` text
,`discount` decimal(10,2)
,`payment_status` enum('Paid','Free','Pending')
,`consultation_fee` decimal(10,2)
,`final_amount` decimal(21,2)
,`patient_name` varchar(250)
,`patient_photo` text
,`patient_phones` text
,`compounder_name` varchar(250)
,`compounder_photo` text
,`compounder_phones` text
,`chamber_name` varchar(250)
,`full_address` text
,`chamber_phones` text
);

-- --------------------------------------------------------

--
-- Table structure for table `works_for`
--

DROP TABLE IF EXISTS `works_for`;
CREATE TABLE IF NOT EXISTS `works_for` (
  `compounder_ID` int NOT NULL,
  `doctor_ID` int NOT NULL,
  PRIMARY KEY (`compounder_ID`,`doctor_ID`),
  KEY `fk_compounder_doctor_doctor` (`doctor_ID`),
  KEY `fk_compounder_doctor` (`compounder_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `works_for`
--

INSERT INTO `works_for` (`compounder_ID`, `doctor_ID`) VALUES
(43, 26),
(44, 26),
(43, 30),
(48, 30),
(46, 38),
(46, 41),
(43, 42),
(46, 42);

-- --------------------------------------------------------

--
-- Table structure for table `works_in`
--

DROP TABLE IF EXISTS `works_in`;
CREATE TABLE IF NOT EXISTS `works_in` (
  `doctor_ID` int NOT NULL,
  `chamber_ID` int NOT NULL,
  PRIMARY KEY (`doctor_ID`,`chamber_ID`),
  KEY `chamber_ID` (`chamber_ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `works_in`
--

INSERT INTO `works_in` (`doctor_ID`, `chamber_ID`) VALUES
(26, 11),
(30, 11),
(42, 11),
(26, 12),
(42, 12),
(38, 13),
(41, 13),
(42, 13),
(49, 13),
(37, 14),
(30, 16),
(50, 17);

-- --------------------------------------------------------

--
-- Structure for view `doctor_with_rating`
--
DROP TABLE IF EXISTS `doctor_with_rating`;

DROP VIEW IF EXISTS `doctor_with_rating`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `doctor_with_rating`  AS SELECT `d`.`user_ID` AS `user_ID`, `d`.`total_rating` AS `total_rating`, `d`.`rating_count` AS `rating_count`, (case when (`d`.`rating_count` = 0) then 0 else (`d`.`total_rating` / `d`.`rating_count`) end) AS `average_rating` FROM `doctor` AS `d` ;

-- --------------------------------------------------------

--
-- Structure for view `user_with_age`
--
DROP TABLE IF EXISTS `user_with_age`;

DROP VIEW IF EXISTS `user_with_age`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `user_with_age`  AS SELECT `systemuser`.`user_ID` AS `user_ID`, `systemuser`.`full_name` AS `full_name`, `systemuser`.`birth_day` AS `birth_day`, timestampdiff(YEAR,`systemuser`.`birth_day`,curdate()) AS `age` FROM `systemuser` ;

-- --------------------------------------------------------

--
-- Structure for view `view_billing_summary`
--
DROP TABLE IF EXISTS `view_billing_summary`;

DROP VIEW IF EXISTS `view_billing_summary`;
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`localhost` SQL SECURITY DEFINER VIEW `view_billing_summary`  AS SELECT `a`.`appointment_ID` AS `appointment_ID`, `a`.`appointment_date` AS `appointment_date`, `a`.`serial_no` AS `serial_no`, `b`.`additional_fees` AS `additional_fees`, `b`.`remark` AS `remark`, `b`.`discount` AS `discount`, `b`.`payment_status` AS `payment_status`, `sd`.`consultation_fee` AS `consultation_fee`, round(((`sd`.`consultation_fee` + `b`.`additional_fees`) * (1 - (`b`.`discount` / 100))),2) AS `final_amount`, `su_p`.`full_name` AS `patient_name`, `su_p`.`photo` AS `patient_photo`, group_concat(distinct `pp`.`phone` separator ', ') AS `patient_phones`, `su_c`.`full_name` AS `compounder_name`, `su_c`.`photo` AS `compounder_photo`, group_concat(distinct `cp`.`phone` separator ', ') AS `compounder_phones`, `ch`.`chamber_name` AS `chamber_name`, concat_ws(', ',`ca`.`house_no`,`ca`.`road`,`ca`.`area`,`ca`.`thana`,`ca`.`district`,`ca`.`division`,`ca`.`postal_code`) AS `full_address`, group_concat(distinct `chp`.`phone` separator ', ') AS `chamber_phones` FROM ((((((((((`appointment` `a` join `billing` `b` on((`a`.`appointment_ID` = `b`.`appointment_ID`))) join `works_in` `w` on(((`a`.`doctor_ID` = `w`.`doctor_ID`) and (`a`.`chamber_ID` = `w`.`chamber_ID`)))) join `schedule_doctor` `sd` on((`a`.`sch_id` = `sd`.`schedule_id`))) join `systemuser` `su_p` on((`a`.`user_ID` = `su_p`.`user_ID`))) left join `user_phone` `pp` on((`su_p`.`user_ID` = `pp`.`user_ID`))) left join `systemuser` `su_c` on((`a`.`compounder_ID` = `su_c`.`user_ID`))) left join `user_phone` `cp` on((`su_c`.`user_ID` = `cp`.`user_ID`))) join `chamber` `ch` on((`a`.`chamber_ID` = `ch`.`chamber_ID`))) left join `chamber_phone` `chp` on((`ch`.`chamber_ID` = `chp`.`chamber_ID`))) left join `chamber_address` `ca` on((`ch`.`chamber_ID` = `ca`.`chamber_ID`))) WHERE (`a`.`status` = 'Completed') GROUP BY `a`.`appointment_ID` ORDER BY (case `b`.`payment_status` when 'Pending' then 0 else 1 end) ASC, `a`.`appointment_date` DESC, `a`.`serial_no` ASC ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `appointment`
--
ALTER TABLE `appointment`
  ADD CONSTRAINT `appointment_ibfk_1` FOREIGN KEY (`user_ID`) REFERENCES `systemuser` (`user_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointment_ibfk_2` FOREIGN KEY (`doctor_ID`) REFERENCES `doctor` (`user_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointment_ibfk_3` FOREIGN KEY (`compounder_ID`) REFERENCES `compounder` (`user_ID`),
  ADD CONSTRAINT `appointment_ibfk_4` FOREIGN KEY (`chamber_ID`) REFERENCES `chamber` (`chamber_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_schid` FOREIGN KEY (`sch_id`) REFERENCES `schedule_doctor` (`schedule_id`) ON DELETE CASCADE;

--
-- Constraints for table `billing`
--
ALTER TABLE `billing`
  ADD CONSTRAINT `billing_ibfk_1` FOREIGN KEY (`appointment_ID`) REFERENCES `appointment` (`appointment_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `billing_ibfk_2` FOREIGN KEY (`compounder_ID`) REFERENCES `compounder` (`user_ID`);

--
-- Constraints for table `chamber_address`
--
ALTER TABLE `chamber_address`
  ADD CONSTRAINT `fk_chamber_address_chamber` FOREIGN KEY (`chamber_ID`) REFERENCES `chamber` (`chamber_ID`) ON DELETE CASCADE;

--
-- Constraints for table `chamber_email`
--
ALTER TABLE `chamber_email`
  ADD CONSTRAINT `fk_email_ch` FOREIGN KEY (`chamber_ID`) REFERENCES `chamber` (`chamber_ID`) ON DELETE CASCADE;

--
-- Constraints for table `chamber_phone`
--
ALTER TABLE `chamber_phone`
  ADD CONSTRAINT `fk_phone_ch` FOREIGN KEY (`chamber_ID`) REFERENCES `chamber` (`chamber_ID`) ON DELETE CASCADE;

--
-- Constraints for table `compounder`
--
ALTER TABLE `compounder`
  ADD CONSTRAINT `fk_compounder_chamber` FOREIGN KEY (`chamber_ID`) REFERENCES `chamber` (`chamber_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_compounder_user` FOREIGN KEY (`user_ID`) REFERENCES `systemuser` (`user_ID`) ON DELETE CASCADE;

--
-- Constraints for table `doctor`
--
ALTER TABLE `doctor`
  ADD CONSTRAINT `fk_doctor_user` FOREIGN KEY (`user_ID`) REFERENCES `systemuser` (`user_ID`) ON DELETE CASCADE;

--
-- Constraints for table `doctor_bmdc_verification`
--
ALTER TABLE `doctor_bmdc_verification`
  ADD CONSTRAINT `fk_bmdc_user` FOREIGN KEY (`user_ID`) REFERENCES `systemuser` (`user_ID`) ON DELETE CASCADE;

--
-- Constraints for table `doctor_schedule_compounder`
--
ALTER TABLE `doctor_schedule_compounder`
  ADD CONSTRAINT `fk_doctor_schedule_compounder` FOREIGN KEY (`compounder_id`) REFERENCES `compounder` (`user_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_doctor_schedule_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctor` (`user_ID`) ON DELETE CASCADE;

--
-- Constraints for table `password_reset`
--
ALTER TABLE `password_reset`
  ADD CONSTRAINT `fk_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `systemuser` (`user_ID`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_matched_user` FOREIGN KEY (`matched_user_ID`) REFERENCES `systemuser` (`user_ID`) ON DELETE CASCADE;

--
-- Constraints for table `schedule_doctor`
--
ALTER TABLE `schedule_doctor`
  ADD CONSTRAINT `fk_schedule_doctor_chamber` FOREIGN KEY (`doctor_id`,`chamber_id`) REFERENCES `works_in` (`doctor_ID`, `chamber_ID`) ON DELETE CASCADE;

--
-- Constraints for table `unavailability`
--
ALTER TABLE `unavailability`
  ADD CONSTRAINT `fk_unavailability_doctor` FOREIGN KEY (`doctor_id`) REFERENCES `doctor` (`user_ID`) ON DELETE CASCADE;

--
-- Constraints for table `user_address`
--
ALTER TABLE `user_address`
  ADD CONSTRAINT `fk_user_address_user` FOREIGN KEY (`user_ID`) REFERENCES `systemuser` (`user_ID`) ON DELETE CASCADE;

--
-- Constraints for table `user_email`
--
ALTER TABLE `user_email`
  ADD CONSTRAINT `fk_email` FOREIGN KEY (`user_ID`) REFERENCES `systemuser` (`user_ID`) ON DELETE CASCADE;

--
-- Constraints for table `user_phone`
--
ALTER TABLE `user_phone`
  ADD CONSTRAINT `fk_phone` FOREIGN KEY (`user_ID`) REFERENCES `systemuser` (`user_ID`) ON DELETE CASCADE;

--
-- Constraints for table `works_for`
--
ALTER TABLE `works_for`
  ADD CONSTRAINT `fk_compounder_doctor` FOREIGN KEY (`compounder_ID`) REFERENCES `compounder` (`user_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_compounder_doctor_doctor` FOREIGN KEY (`doctor_ID`) REFERENCES `doctor` (`user_ID`) ON DELETE CASCADE;

--
-- Constraints for table `works_in`
--
ALTER TABLE `works_in`
  ADD CONSTRAINT `fk_works_in_chamber` FOREIGN KEY (`chamber_ID`) REFERENCES `chamber` (`chamber_ID`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_works_in_doctor` FOREIGN KEY (`doctor_ID`) REFERENCES `doctor` (`user_ID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
