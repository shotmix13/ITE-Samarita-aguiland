-- SQL Script to create the database and users table for Aguiland
-- Run this in phpMyAdmin to set up your database

-- Create database if not exists
CREATE DATABASE IF NOT EXISTS aguiland_db;
USE aguiland_db;

-- Create users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(100) NOT NULL);

-- Create bookings table
CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(20) NOT NULL,
    tour_date DATE NOT NULL,
    guests INT NOT NULL,
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Optional: Insert a test user
-- INSERT INTO users (username, password) VALUES ('testuser', '$2y$10$abcdefghijklmnopqrstuvwxyz');