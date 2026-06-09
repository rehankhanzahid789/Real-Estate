-- Billah Dee King schema + seed data
SET FOREIGN_KEY_CHECKS=0;
DROP TABLE IF EXISTS contact_messages;
DROP TABLE IF EXISTS inquiries;
DROP TABLE IF EXISTS favorites;
DROP TABLE IF EXISTS property_images;
DROP TABLE IF EXISTS properties;
DROP TABLE IF EXISTS agents;
DROP TABLE IF EXISTS password_resets;
DROP TABLE IF EXISTS email_verifications;
DROP TABLE IF EXISTS users;
SET FOREIGN_KEY_CHECKS=1;

CREATE TABLE users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('user','admin') NOT NULL DEFAULT 'user',
  is_verified TINYINT(1) NOT NULL DEFAULT 0,
  phone VARCHAR(40) NULL,
  avatar VARCHAR(255) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX(email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE email_verifications (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  code VARCHAR(10) NOT NULL,
  expires_at DATETIME NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX(user_id), INDEX(code),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE password_resets (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  code VARCHAR(10) NOT NULL,
  expires_at DATETIME NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX(user_id), INDEX(code),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE agents (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  role VARCHAR(120) NOT NULL DEFAULT 'Senior Advisor',
  email VARCHAR(190),
  phone VARCHAR(40),
  bio TEXT,
  photo VARCHAR(255),
  facebook VARCHAR(255),
  instagram VARCHAR(255),
  linkedin VARCHAR(255),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE properties (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(200) NOT NULL,
  slug VARCHAR(220) NOT NULL UNIQUE,
  description TEXT,
  price DECIMAL(14,2) NOT NULL,
  city VARCHAR(100) NOT NULL,
  address VARCHAR(255),
  property_type ENUM('villa','penthouse','apartment','house','estate','townhouse') NOT NULL,
  status ENUM('for-sale','for-rent','sold') NOT NULL DEFAULT 'for-sale',
  bedrooms INT NOT NULL DEFAULT 0,
  bathrooms INT NOT NULL DEFAULT 0,
  size_sqft INT NOT NULL DEFAULT 0,
  amenities TEXT,
  agent_id INT,
  is_featured TINYINT(1) NOT NULL DEFAULT 0,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX(city), INDEX(property_type), INDEX(price), INDEX(is_featured),
  FOREIGN KEY (agent_id) REFERENCES agents(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE property_images (
  id INT AUTO_INCREMENT PRIMARY KEY,
  property_id INT NOT NULL,
  image_path VARCHAR(500) NOT NULL,
  is_primary TINYINT(1) NOT NULL DEFAULT 0,
  FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE favorites (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  property_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  UNIQUE KEY (user_id, property_id),
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
  FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE inquiries (
  id INT AUTO_INCREMENT PRIMARY KEY,
  property_id INT NOT NULL,
  user_id INT NULL,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL,
  phone VARCHAR(40),
  message TEXT NOT NULL,
  status ENUM('new','contacted','closed') DEFAULT 'new',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (property_id) REFERENCES properties(id) ON DELETE CASCADE,
  FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE contact_messages (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(120) NOT NULL,
  email VARCHAR(190) NOT NULL,
  subject VARCHAR(200),
  message TEXT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============ SEED ============
-- Admin password: Admin@123  |  User password: User@123
-- bcrypt hashes generated for these passwords.
INSERT INTO users (name, email, password, role, is_verified, phone) VALUES
('Site Admin', 'admin@billahdeeking.com', '$2b$10$a8HLAAuCInvrT.LQXeLs/O29pmJrZbxRPkjQP37kjItu.z59QSGBS', 'admin', 1, '+1-212-555-0100'),
('John Carter',  'john@example.com',    '$2b$10$JsAq0eVcjbl8QLozBugLW.F59wf0sV3DjGQt7KTpG6AHsDQ6nUj4S', 'user', 1, '+1-415-555-0177'),
('Emma Stone',   'emma@example.com',    '$2b$10$JsAq0eVcjbl8QLozBugLW.F59wf0sV3DjGQt7KTpG6AHsDQ6nUj4S', 'user', 1, '+1-310-555-0188');

INSERT INTO agents (name, role, email, phone, bio, photo, facebook, instagram, linkedin) VALUES
('Isabella Romano','Director of Luxury Sales','isabella@billahdeeking.com','+1-212-555-0110','Twelve years curating record-setting penthouse and townhouse sales across Manhattan and the Hamptons.','https://images.unsplash.com/photo-1573497019940-1c28c88b4f3e?w=600&q=80','#','#','#'),
('Marcus Whitfield','Principal Broker','marcus@billahdeeking.com','+1-310-555-0120','Specialist in waterfront estates and architecturally significant West Coast residences.','https://images.unsplash.com/photo-1560250097-0b93528c311a?w=600&q=80','#','#','#'),
('Sophie Laurent','Senior Advisor','sophie@billahdeeking.com','+33-1-55-55-0130','From Parisian pieds-à-terre to Côte d''Azur villas — Sophie represents Europe''s most refined properties.','https://images.unsplash.com/photo-1438761681033-6461ffad8d80?w=600&q=80','#','#','#'),
('David Chen','Investment Specialist','david@billahdeeking.com','+1-415-555-0140','Advising family offices on income-producing luxury portfolios across North America and Asia.','https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=600&q=80','#','#','#'),
('Olivia Hart','Hamptons & Coastal','olivia@billahdeeking.com','+1-631-555-0150','Two decades pairing discerning buyers with the East End''s most coveted beachfront homes.','https://images.unsplash.com/photo-1580489944761-15a19d654956?w=600&q=80','#','#','#'),
('Rafael Costa','International Portfolio','rafael@billahdeeking.com','+351-21-555-0160','Lisbon-based, representing premium estates throughout Portugal, Spain and South America.','https://images.unsplash.com/photo-1492562080023-ab3db95bfbce?w=600&q=80','#','#','#');

-- 12 luxury properties
INSERT INTO properties (title, slug, description, price, city, address, property_type, status, bedrooms, bathrooms, size_sqft, amenities, agent_id, is_featured) VALUES
('Park Avenue Penthouse','park-avenue-penthouse','A trophy duplex penthouse with four exposures, wrapping terraces and unobstructed Central Park views. Designed in collaboration with a renowned interior atelier, every surface speaks of restrained luxury.', 18750000, 'New York', '740 Park Avenue, Manhattan', 'penthouse', 'for-sale', 5, 6, 7400, 'Private elevator,Wraparound terrace,Wine cellar,Smart home,Gym,Concierge,Parking', 1, 1),
('Malibu Cliff Estate','malibu-cliff-estate','A glass-walled architectural masterpiece perched above the Pacific. Infinity pool, private beach access and a guest pavilion set within terraced gardens.', 24500000, 'Malibu', '27000 Pacific Coast Hwy', 'estate', 'for-sale', 6, 7, 9800, 'Ocean view,Infinity pool,Private beach,Home theater,Guest house,4-car garage,Outdoor kitchen', 2, 1),
('Hamptons Beachfront Villa','hamptons-beachfront-villa','Shingle-style oceanfront retreat on two acres of dunes. Heated saltwater pool, tennis court and a chef''s kitchen opening onto the lawn.', 14900000, 'Southampton', 'Meadow Lane, Southampton', 'villa', 'for-sale', 7, 8, 11200, 'Beachfront,Pool,Tennis court,Wine room,Boat dock,Staff quarters', 5, 1),
('SoHo Loft Residence','soho-loft-residence','A full-floor loft in a landmarked cast-iron building. 14ft ceilings, original columns and a private rooftop cabana.', 6450000, 'New York', '88 Wooster Street, SoHo', 'apartment', 'for-sale', 3, 3, 3800, 'Roof terrace,Original details,Chef kitchen,Smart home,Doorman', 1, 0),
('Saint-Tropez Bay Villa','saint-tropez-bay-villa','Mediterranean villa with private mooring, infinity pool overlooking the bay and seven en-suite bedrooms set across mature gardens.', 32000000, 'Saint-Tropez', 'Route des Salins', 'villa', 'for-sale', 7, 9, 12500, 'Sea view,Private dock,Infinity pool,Helipad,Wine cellar,Staff quarters', 3, 1),
('Aspen Mountain Lodge','aspen-mountain-lodge','Ski-in/ski-out chalet hand-built from reclaimed timber and stone. Spa, screening room and a private gondola.', 21750000, 'Aspen', 'Red Mountain Road', 'estate', 'for-sale', 6, 7, 9400, 'Ski-in/ski-out,Spa,Wine cellar,Screening room,Heated driveway,Boot warmer', 2, 1),
('Tribeca Skyline Penthouse','tribeca-skyline-penthouse','A serene penthouse with 360° views, a glass-bottom pool and a 2,000 sqft private terrace.', 13900000, 'New York', '443 Greenwich Street, Tribeca', 'penthouse', 'for-sale', 4, 5, 5200, 'Pool,Private terrace,Concierge,Smart home,Wine room,Parking', 1, 0),
('Bel Air Modern Estate','bel-air-modern-estate','Architecturally significant glass residence with motor court, vanishing-edge pool and a private vineyard.', 39500000, 'Los Angeles', 'Bel Air Road', 'estate', 'for-sale', 8, 11, 16800, 'Vineyard,Pool,Spa,Theater,Gym,Wine cellar,7-car garage', 2, 1),
('Lisbon Riverside Apartment','lisbon-riverside-apartment','Restored 19th-century apartment with river views, original tilework and a curated contemporary fit-out.', 2950000, 'Lisbon', 'Avenida 24 de Julho', 'apartment', 'for-sale', 3, 2, 2200, 'River view,Balcony,Restored details,Doorman', 6, 0),
('Beverly Hills Mediterranean','beverly-hills-mediterranean','Gated estate behind 80ft cypresses. Loggia, pool pavilion and a separate two-bedroom guest house.', 17400000, 'Beverly Hills', 'North Roxbury Drive', 'house', 'for-sale', 6, 8, 10500, 'Pool,Guest house,Tennis court,Wine cellar,Smart home', 2, 0),
('Greenwich Village Townhouse','greenwich-village-townhouse','A rare 25ft-wide townhouse on a tree-lined block, with a private garden and roof terrace.', 11900000, 'New York', 'West 11th Street', 'townhouse', 'for-sale', 5, 5, 6400, 'Private garden,Roof terrace,Elevator,Wine cellar,Smart home', 1, 0),
('Cap Ferrat Seafront Villa','cap-ferrat-seafront-villa','Belle Époque villa with direct sea access, mature gardens and uninterrupted views toward Villefranche bay.', 47500000, 'Cap Ferrat', 'Boulevard du Général de Gaulle', 'villa', 'for-sale', 8, 10, 14200, 'Seafront,Private dock,Pool,Tennis,Wine cellar,Staff quarters,Helipad', 3, 1);

-- Images (using high-quality Unsplash URLs as the image_path)
INSERT INTO property_images (property_id, image_path, is_primary) VALUES
(1,'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?w=1400&q=80',1),
(1,'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?w=1400&q=80',0),
(1,'https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?w=1400&q=80',0),
(1,'https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?w=1400&q=80',0),
(2,'https://images.unsplash.com/photo-1613490493576-7fde63acd811?w=1400&q=80',1),
(2,'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=1400&q=80',0),
(2,'https://images.unsplash.com/photo-1600607687644-c7171b42498f?w=1400&q=80',0),
(3,'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=1400&q=80',1),
(3,'https://images.unsplash.com/photo-1600585154526-990dced4db0d?w=1400&q=80',0),
(3,'https://images.unsplash.com/photo-1600573472550-8090b5e0745e?w=1400&q=80',0),
(4,'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=1400&q=80',1),
(4,'https://images.unsplash.com/photo-1560448204-e02f11c3d0e2?w=1400&q=80',0),
(5,'https://images.unsplash.com/photo-1582268611958-ebfd161ef9cf?w=1400&q=80',1),
(5,'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?w=1400&q=80',0),
(5,'https://images.unsplash.com/photo-1600585154363-67eb9e2e2099?w=1400&q=80',0),
(6,'https://images.unsplash.com/photo-1499793983690-e29da59ef1c2?w=1400&q=80',1),
(6,'https://images.unsplash.com/photo-1505691938895-1758d7feb511?w=1400&q=80',0),
(7,'https://images.unsplash.com/photo-1545324418-cc1a3fa10c00?w=1400&q=80',1),
(7,'https://images.unsplash.com/photo-1600566753086-00f18fe6ba76?w=1400&q=80',0),
(8,'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?w=1400&q=80',1),
(8,'https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?w=1400&q=80',0),
(9,'https://images.unsplash.com/photo-1493809842364-78817add7ffb?w=1400&q=80',1),
(9,'https://images.unsplash.com/photo-1560448204-603b3fc33ddc?w=1400&q=80',0),
(10,'https://images.unsplash.com/photo-1600210491892-03d54c0aaf87?w=1400&q=80',1),
(10,'https://images.unsplash.com/photo-1600585154084-4e5fe7c39198?w=1400&q=80',0),
(11,'https://images.unsplash.com/photo-1568605114967-8130f3a36994?w=1400&q=80',1),
(11,'https://images.unsplash.com/photo-1600566753376-12c8ab7fb75b?w=1400&q=80',0),
(12,'https://images.unsplash.com/photo-1613977257363-707ba9348227?w=1400&q=80',1),
(12,'https://images.unsplash.com/photo-1600566753051-5e2a9e2c6f3f?w=1400&q=80',0);

INSERT INTO favorites (user_id, property_id) VALUES (2,1),(2,3),(2,5),(3,2),(3,8);

INSERT INTO inquiries (property_id, user_id, name, email, phone, message, status) VALUES
(1,2,'John Carter','john@example.com','+1-415-555-0177','Interested in scheduling a private tour next week.','new'),
(5,NULL,'Alex Morgan','alex.morgan@example.com','+1-202-555-0199','Could you share floor plans and HOA details?','contacted'),
(8,3,'Emma Stone','emma@example.com','+1-310-555-0188','Is the seller open to negotiation?','new');

INSERT INTO contact_messages (name, email, subject, message) VALUES
('Hannah Brooks','hannah@example.com','Off-market inquiry','Looking for a discreet introduction to off-market townhouses in the West Village.'),
('Daniel Kim','daniel@example.com','Buyer representation','We are relocating from Singapore and would like exclusive buyer representation.');
