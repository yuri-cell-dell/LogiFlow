CREATE DATABASE IF NOT EXISTS logiflow CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE logiflow;

CREATE TABLE IF NOT EXISTS products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  code VARCHAR(40) NOT NULL UNIQUE,
  description TEXT NULL,
  unit VARCHAR(20) NOT NULL DEFAULT 'Un.',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS addresses (
  id INT AUTO_INCREMENT PRIMARY KEY,
  label VARCHAR(120) NOT NULL,
  zip_code VARCHAR(9) NULL,
  street VARCHAR(150) NOT NULL,
  number VARCHAR(20) NULL,
  complement VARCHAR(100) NULL,
  city VARCHAR(100) NOT NULL,
  state CHAR(2) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS carriers (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(150) NOT NULL,
  document VARCHAR(20) NULL,
  phone VARCHAR(30) NULL,
  email VARCHAR(120) NULL,
  vehicle_type VARCHAR(80) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS freights (
  id INT AUTO_INCREMENT PRIMARY KEY,
  freight_date DATE NOT NULL,
  carrier_id INT NOT NULL,
  origin_id INT NOT NULL,
  destination_id INT NOT NULL,
  status VARCHAR(30) NOT NULL DEFAULT 'Pendente',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_freight_carrier FOREIGN KEY (carrier_id) REFERENCES carriers(id),
  CONSTRAINT fk_freight_origin FOREIGN KEY (origin_id) REFERENCES addresses(id),
  CONSTRAINT fk_freight_destination FOREIGN KEY (destination_id) REFERENCES addresses(id)
);

CREATE TABLE IF NOT EXISTS freight_products (
  id INT AUTO_INCREMENT PRIMARY KEY,
  freight_id INT NOT NULL,
  product_id INT NOT NULL,
  quantity DECIMAL(10,2) NOT NULL,
  CONSTRAINT fk_fp_freight FOREIGN KEY (freight_id) REFERENCES freights(id) ON DELETE CASCADE,
  CONSTRAINT fk_fp_product FOREIGN KEY (product_id) REFERENCES products(id)
);

-- Registros iniciais para facilitar o primeiro teste
INSERT IGNORE INTO products (name, code, description, unit) VALUES
('Peças agrícolas', 'PROD-001', 'Peças para máquinas agrícolas', 'Un.'),
('Milho ensacado', 'PROD-002', 'Produto para transporte', 'Sacas');

INSERT INTO addresses (label, zip_code, street, number, complement, city, state)
SELECT 'Depósito Central', '87200-000', 'Rua das Indústrias', '100', '', 'Cianorte', 'PR'
WHERE NOT EXISTS (SELECT 1 FROM addresses WHERE label='Depósito Central');

INSERT INTO addresses (label, zip_code, street, number, complement, city, state)
SELECT 'Cliente Exemplo', '87200-100', 'Avenida Brasil', '250', '', 'Cianorte', 'PR'
WHERE NOT EXISTS (SELECT 1 FROM addresses WHERE label='Cliente Exemplo');

INSERT INTO carriers (name, document, phone, email, vehicle_type)
SELECT 'Transportadora Exemplo', '', '(44) 99999-0000', 'contato@exemplo.com', 'Caminhão'
WHERE NOT EXISTS (SELECT 1 FROM carriers WHERE name='Transportadora Exemplo');
