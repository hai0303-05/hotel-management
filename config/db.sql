  CREATE TABLE users (
      id INT AUTO_INCREMENT PRIMARY KEY,
      username VARCHAR(50) NOT NULL UNIQUE,
      password VARCHAR(255) NOT NULL,
      role ENUM('admin','staff') NOT NULL
);

  CREATE TABLE rooms (
      id INT AUTO_INCREMENT PRIMARY KEY,
      room_number VARCHAR(20) NOT NULL,
      room_type VARCHAR(50) NOT NULL,
      price DECIMAL(10,2) NOT NULL,
      status ENUM('available','booked') DEFAULT 'available'
);

  CREATE TABLE customers (
      id INT AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(100) NOT NULL,
      phone VARCHAR(20),
      email VARCHAR(100),
      id_card VARCHAR(20)
);

  CREATE TABLE bookings (
      id INT AUTO_INCREMENT PRIMARY KEY,
      room_id INT NOT NULL,
      customer_id INT NOT NULL,
      check_in DATE NOT NULL,
      check_out DATE,
      total_price DECIMAL(10,2),
      FOREIGN KEY (room_id) REFERENCES rooms(id),
      FOREIGN KEY (customer_id) REFERENCES customers(id)
);

  INSERT INTO users (username, password, role) VALUES
  ('admin', MD5('123456'), 'admin'),
  ('staff', MD5('123456'), 'staff');
    
  INSERT INTO rooms (room_number, room_type, price, status) VALUES
  ('101', 'Standard', 300000, 'available'),
  ('102', 'Deluxe', 500000, 'available');

  INSERT INTO customers (name, phone, email, id_card) VALUES
  ('Nguyen Van A', '0909123456', 'a@gmail.com', '012345678901'),
  ('Tran Thi B', '0911222333', 'b@gmail.com', NULL);
