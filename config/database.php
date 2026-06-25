<?php
// Konfigurasi database
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'dieng_booking');

// Membuat koneksi
function getConnection() {
    try {
        $conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $conn->exec("SET NAMES utf8");
        return $conn;
    } catch(PDOException $e) {
        die("Koneksi gagal: " . $e->getMessage());
    }
}

// Fungsi untuk mendapatkan data properti
function getProperties($type = null, $limit = null) {
    $conn = getConnection();
    $sql = "SELECT * FROM properties WHERE status = 'active'";
    
    if($type) {
        $sql .= " AND type = :type";
    }
    
    $sql .= " ORDER BY created_at DESC";
    
    if($limit) {
        $sql .= " LIMIT :limit";
    }
    
    $stmt = $conn->prepare($sql);
    
    if($type) {
        $stmt->bindParam(':type', $type);
    }
    
    if($limit) {
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    }
    
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Fungsi untuk mendapatkan detail properti
function getPropertyDetail($id) {
    $conn = getConnection();
    $sql = "SELECT * FROM properties WHERE id = :id AND status = 'active'";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':id', $id);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Fungsi untuk membuat booking
function createBooking($data) {
    $conn = getConnection();
    $sql = "INSERT INTO bookings (property_id, customer_name, customer_email, customer_phone, check_in, check_out, guests, total_price, status, booking_code) 
            VALUES (:property_id, :customer_name, :customer_email, :customer_phone, :check_in, :check_out, :guests, :total_price, 'pending', :booking_code)";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute($data);
    return $conn->lastInsertId();
}
?>