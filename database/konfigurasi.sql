USE wpms;
CREATE TABLE IF NOT EXISTS konfigurasi (
    id_konfig INT AUTO_INCREMENT PRIMARY KEY,
    nama_toko VARCHAR(100),
    alamat VARCHAR(255),
    telepon VARCHAR(20),
    logo VARCHAR(255)
) ENGINE=InnoDB;

INSERT IGNORE INTO konfigurasi (id_konfig, nama_toko, alamat, telepon) 
VALUES (1, 'Toko WPMS Pro', 'Jl. Algoritma Merdeka No.99 - Jakarta', '0812-3214-9988');
