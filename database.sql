-- ============================================
-- Photographer Booking System - Database Setup
-- ============================================

CREATE DATABASE IF NOT EXISTS customer_booking
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE customer_booking;

CREATE TABLE IF NOT EXISTS bookings (
  couple_id     INT          NOT NULL AUTO_INCREMENT PRIMARY KEY,
  couple_name   VARCHAR(150) NOT NULL,
  contact       VARCHAR(20)  NOT NULL,
  email         VARCHAR(150) NOT NULL,
  location      VARCHAR(255) NOT NULL,
  package       ENUM('Pilot','Mainstream','Blockbuster','Travel') NOT NULL,
  story_notes   TEXT,
  date          DATE         NOT NULL,
  created_at    TIMESTAMP    DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB AUTO_INCREMENT=1;
