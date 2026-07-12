-- database/wpms.sql

CREATE DATABASE IF NOT EXISTS wpms;
USE wpms;

CREATE TABLE IF NOT EXISTS role (
    id_role INT AUTO_INCREMENT PRIMARY KEY,
    nama_role VARCHAR(50) NOT NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS pengguna (
    id_pengguna INT AUTO_INCREMENT PRIMARY KEY,
    nama_lengkap VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    id_role INT NOT NULL,
    status ENUM('aktif', 'nonaktif') DEFAULT 'aktif',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_role) REFERENCES role(id_role) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS hak_akses (
    id_hak_akses INT AUTO_INCREMENT PRIMARY KEY,
    id_role INT NOT NULL,
    nama_modul VARCHAR(50) NOT NULL,
    akses_lihat TINYINT(1) DEFAULT 0,
    akses_tambah TINYINT(1) DEFAULT 0,
    akses_ubah TINYINT(1) DEFAULT 0,
    akses_hapus TINYINT(1) DEFAULT 0,
    FOREIGN KEY (id_role) REFERENCES role(id_role) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS audit_log (
    id_log INT AUTO_INCREMENT PRIMARY KEY,
    id_pengguna INT,
    aksi VARCHAR(255) NOT NULL,
    tabel_terkait VARCHAR(50) NOT NULL,
    waktu TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    ip_address VARCHAR(45),
    FOREIGN KEY (id_pengguna) REFERENCES pengguna(id_pengguna) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB;

-- Insert default roles
INSERT IGNORE INTO role (id_role, nama_role) VALUES 
(1, 'Super Admin'), 
(2, 'Admin Gudang'), 
(3, 'Kasir');

-- Insert default User (password: password123 | Brypt Hash)
INSERT IGNORE INTO pengguna (id_pengguna, nama_lengkap, email, password, id_role, status) 
VALUES (1, 'Super Administrator', 'admin@wpms.local', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 1, 'aktif');
