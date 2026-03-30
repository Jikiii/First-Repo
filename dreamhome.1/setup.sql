-- Create database
CREATE DATABASE IF NOT EXISTS dreamhome_db;
USE dreamhome_db;

-- Branch table
CREATE TABLE Branch (
    branch_no VARCHAR(5) PRIMARY KEY,
    street VARCHAR(50),
    area VARCHAR(50),
    city VARCHAR(50),
    postcode VARCHAR(10),
    telephone VARCHAR(15),
    fax VARCHAR(15)
);

-- Staff table
CREATE TABLE Staff (
    staff_no VARCHAR(5) PRIMARY KEY,
    first_name VARCHAR(30),
    last_name VARCHAR(30),
    address VARCHAR(100),
    telephone VARCHAR(15),
    sex CHAR(1),
    date_of_birth DATE,
    national_insurance_no VARCHAR(15) UNIQUE,
    job_title VARCHAR(30),
    salary DECIMAL(10,2),
    date_joined DATE,
    branch_no VARCHAR(5),
    supervisor_no VARCHAR(5),
    typing_speed INT NULL,
    car_allowance DECIMAL(10,2) NULL,
    monthly_bonus DECIMAL(10,2) NULL,
    manager_start_date DATE NULL,
    FOREIGN KEY (branch_no) REFERENCES Branch(branch_no),
    FOREIGN KEY (supervisor_no) REFERENCES Staff(staff_no)
);

-- Next of Kin table
CREATE TABLE NextOfKin (
    staff_no VARCHAR(5) PRIMARY KEY,
    full_name VARCHAR(60),
    relationship VARCHAR(30),
    address VARCHAR(100),
    telephone VARCHAR(15),
    FOREIGN KEY (staff_no) REFERENCES Staff(staff_no) ON DELETE CASCADE
);

-- Property table
CREATE TABLE Property (
    property_no VARCHAR(5) PRIMARY KEY,
    street VARCHAR(50),
    area VARCHAR(50),
    city VARCHAR(50),
    postcode VARCHAR(10),
    type VARCHAR(20),
    rooms INT,
    monthly_rent DECIMAL(10,2),
    staff_no VARCHAR(5),
    branch_no VARCHAR(5),
    date_withdrawn DATE NULL,
    status VARCHAR(20) DEFAULT 'available',
    FOREIGN KEY (staff_no) REFERENCES Staff(staff_no),
    FOREIGN KEY (branch_no) REFERENCES Branch(branch_no)
);

-- Renter table
CREATE TABLE Renter (
    renter_no VARCHAR(5) PRIMARY KEY,
    first_name VARCHAR(30),
    last_name VARCHAR(30),
    address VARCHAR(100),
    telephone VARCHAR(15),
    preferred_property_type VARCHAR(20),
    max_monthly_rent DECIMAL(10,2),
    comments TEXT,
    date_registered DATE,
    seen_by_staff_no VARCHAR(5),
    branch_no VARCHAR(5),
    FOREIGN KEY (seen_by_staff_no) REFERENCES Staff(staff_no),
    FOREIGN KEY (branch_no) REFERENCES Branch(branch_no)
);

-- Lease Agreement table
CREATE TABLE LeaseAgreement (
    lease_no VARCHAR(5) PRIMARY KEY,
    property_no VARCHAR(5),
    renter_no VARCHAR(5),
    arranged_by_staff_no VARCHAR(5),
    monthly_rent DECIMAL(10,2),
    payment_method VARCHAR(20),
    deposit_amount DECIMAL(10,2),
    deposit_paid BOOLEAN,
    start_date DATE,
    end_date DATE,
    duration INT,
    FOREIGN KEY (property_no) REFERENCES Property(property_no),
    FOREIGN KEY (renter_no) REFERENCES Renter(renter_no),
    FOREIGN KEY (arranged_by_staff_no) REFERENCES Staff(staff_no)
);

-- Property Inspection table
CREATE TABLE PropertyInspection (
    inspection_id INT AUTO_INCREMENT PRIMARY KEY,
    property_no VARCHAR(5),
    staff_no VARCHAR(5),
    inspection_date DATE,
    comments TEXT,
    FOREIGN KEY (property_no) REFERENCES Property(property_no),
    FOREIGN KEY (staff_no) REFERENCES Staff(staff_no)
);

-- Advertisement table
CREATE TABLE Advertisement (
    advert_id INT AUTO_INCREMENT PRIMARY KEY,
    property_no VARCHAR(5),
    newspaper_name VARCHAR(50),
    date_placed DATE,
    cost DECIMAL(10,2),
    FOREIGN KEY (property_no) REFERENCES Property(property_no)
);

-- Insert sample data
INSERT INTO Branch VALUES 
('B3', '163 Main Street', 'Patrick', 'Glasgow', 'G11 9QX', '0141-123-4567', '0141-123-4568'),
('B85', '19 Taylor Street', 'Cranford', 'London', 'SW1A 1AA', '0171-884-5112', '0171-884-5113');

INSERT INTO Staff VALUES 
('SL21', 'John', 'White', '19 Taylor Street, Cranford, London', '0171-884-5112', 'M', '1965-03-15', 'AB123456C', 'Manager', 75000.00, '1988-10-24', 'B85', NULL, NULL, 5000.00, 1000.00, '1988-10-24'),
('SG14', 'Susan', 'Brand', '45 Queen Street, Glasgow', '0141-234-5678', 'F', '1975-07-20', 'CD789012E', 'Supervisor', 45000.00, '2010-03-01', 'B3', NULL, NULL, NULL, NULL, NULL),
('SA12', 'Ann', 'Beech', '12 Park Avenue, Glasgow', '0141-345-6789', 'F', '1980-11-30', 'EF345678F', 'Supervisor', 48000.00, '2012-06-15', 'B3', NULL, NULL, NULL, NULL, NULL),
('ST456', 'Mike', 'Johnson', '8 Station Road, Glasgow', '0141-456-7890', 'M', '1988-02-25', 'GH456789G', 'Staff', 32000.00, '2015-01-10', 'B3', 'SG14', NULL, NULL, NULL, NULL);

INSERT INTO NextOfKin VALUES 
('SL21', 'Mary White', 'Wife', '19 Taylor Street, Cranford, London', '0171-884-5112'),
('SG14', 'Robert Brand', 'Husband', '45 Queen Street, Glasgow', '0141-234-5679');

INSERT INTO Property VALUES 
('PG46', 'Lawrence St.', 'Patrick', 'Glasgow', 'G11 9QX', 'Flat', 3, 350.00, 'ST456', 'B3', NULL, 'available'),
('PG36', 'Manor Road', NULL, 'Glasgow', 'G32 4QX', 'Flat', 3, 375.00, 'ST456', 'B3', NULL, 'available'),
('PG21', 'Dale Road', 'Hyndland', 'Glasgow', 'G12', 'House', 5, 600.00, 'ST456', 'B3', NULL, 'available'),
('PG16', 'Novar', 'Hyndland', 'Glasgow', 'G12 9AX', 'Flat', 4, 450.00, 'ST456', 'B3', NULL, 'available');

INSERT INTO Renter VALUES 
('CR74', 'Mike', 'Ritchie', '18 Tain Street, Gourock', 'PAIG IYQ', 'House', 750.00, 'Currently living at home with parents. Getting married in August.', '1995-03-24', 'SA12', 'B3');

INSERT INTO LeaseAgreement VALUES 
('L001', 'PG21', 'CR74', 'SA12', 600.00, 'Bank Transfer', 1200.00, TRUE, '2024-01-01', '2024-12-31', 12);

INSERT INTO PropertyInspection (property_no, staff_no, inspection_date, comments) VALUES 
('PG21', 'SG14', '2024-03-15', 'No problems found'),
('PG21', 'SG14', '2024-09-30', 'Cracked ceiling in living room. Requires urgent repair.'),
('PG21', 'SA12', '2024-07-01', 'Crockery needs to be replaced.');

INSERT INTO Advertisement VALUES 
(1, 'PG21', 'Glasgow Times', '2024-01-15', 150.00),
(2, 'PG21', 'Daily Record', '2024-02-01', 200.00);