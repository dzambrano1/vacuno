-- Create users table if it doesn't exist
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `fullname` varchar(100) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `role` enum('admin','buyer','customer') NOT NULL DEFAULT 'customer',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `last_login` datetime DEFAULT NULL,
  `last_logout` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Insert sample users (password is the same as username for demo purposes)
-- Note: In a production environment, you would use hashed passwords with password_hash()

-- Admin user
INSERT INTO `users` (`username`, `password`, `fullname`, `email`, `role`, `status`)
VALUES ('admin', 'admin', 'Administrador Sistema', 'admin@sistema.com', 'admin', 'active');

-- Buyer users
INSERT INTO `users` (`username`, `password`, `fullname`, `email`, `role`, `status`)
VALUES 
('comprador1', 'comprador1', 'Juan Pérez', 'juan@compradores.com', 'buyer', 'active'),
('comprador2', 'comprador2', 'María Gómez', 'maria@compradores.com', 'buyer', 'active');

-- Customer users
INSERT INTO `users` (`username`, `password`, `fullname`, `email`, `role`, `status`)
VALUES 
('cliente1', 'cliente1', 'Roberto Sánchez', 'roberto@clientes.com', 'customer', 'active'),
('cliente2', 'cliente2', 'Ana López', 'ana@clientes.com', 'customer', 'active'),
('cliente3', 'cliente3', 'Carlos Martínez', 'carlos@clientes.com', 'customer', 'active');

-- Example with hashed password (commented out, use as template for production)
/*
INSERT INTO `users` (`username`, `password`, `fullname`, `email`, `role`, `status`)
VALUES ('secure_admin', '$2y$10$xMmX5WK7CuW1yy.iBVJM7OWvK1RXwPQCVPZV/iAiVPhFZnOmJ3W5q', 'Secure Admin', 'secure@admin.com', 'admin', 'active');
*/ 