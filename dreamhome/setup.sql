-- ============================================
-- DREAMHOME DATABASE - COMPLETE SCHEMA
-- ============================================

SET FOREIGN_KEY_CHECKS = 0;

-- Drop existing tables (in correct order)
DROP TABLE IF EXISTS Advertisement;
DROP TABLE IF EXISTS Inspection;
DROP TABLE IF EXISTS Viewing;
DROP TABLE IF EXISTS Lease;
DROP TABLE IF EXISTS Renter;
DROP TABLE IF EXISTS Property;
DROP TABLE IF EXISTS NextOfKin;
DROP TABLE IF EXISTS Staff;
DROP TABLE IF EXISTS Branch;

SET FOREIGN_KEY_CHECKS = 1;

-- ============================================
-- CREATE TABLES
-- ============================================

-- Branch Table
CREATE TABLE Branch (
    BranchNo VARCHAR(10) PRIMARY KEY,
    BranchName VARCHAR(100) NOT NULL,
    Street VARCHAR(100),
    Area VARCHAR(100),
    City VARCHAR(100) NOT NULL,
    PostCode VARCHAR(20),
    ContactNo VARCHAR(20),
    Email VARCHAR(100)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Staff Table
CREATE TABLE Staff (
    StaffNo VARCHAR(10) PRIMARY KEY,
    FName VARCHAR(50) NOT NULL,
    LName VARCHAR(50) NOT NULL,
    Address VARCHAR(200),
    Phone VARCHAR(20),
    Email VARCHAR(100),
    Gender CHAR(1),
    BirthDate DATE,
    NationalID VARCHAR(20),
    JobTitle VARCHAR(50) NOT NULL,
    Salary DECIMAL(10,2) NOT NULL,
    HireDate DATE NOT NULL,
    BranchNo VARCHAR(10) NOT NULL,
    CONSTRAINT chk_salary CHECK (Salary > 0),
    FOREIGN KEY (BranchNo) REFERENCES Branch(BranchNo)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Next Of Kin Table
CREATE TABLE NextOfKin (
    KinID INT AUTO_INCREMENT PRIMARY KEY,
    StaffNo VARCHAR(10) NOT NULL,
    KinName VARCHAR(100) NOT NULL,
    Relation VARCHAR(50) NOT NULL,
    Address VARCHAR(200),
    Phone VARCHAR(20),
    FOREIGN KEY (StaffNo) REFERENCES Staff(StaffNo)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Property Table
CREATE TABLE Property (
    PropertyNo VARCHAR(10) PRIMARY KEY,
    StreetName VARCHAR(100) NOT NULL,
    District VARCHAR(50),
    City VARCHAR(50) NOT NULL,
    PostCode VARCHAR(20),
    PropertyType VARCHAR(30),
    Rooms INT,
    RentAmount DECIMAL(10,2),
    StaffNo VARCHAR(10),
    BranchNo VARCHAR(10) NOT NULL,
    Status ENUM('Available','Rented','Withdrawn') DEFAULT 'Available',
    DateAvailable DATE,
    CONSTRAINT chk_rent CHECK (RentAmount > 0),
    CONSTRAINT chk_rooms CHECK (Rooms > 0),
    FOREIGN KEY (StaffNo) REFERENCES Staff(StaffNo)
        ON UPDATE CASCADE ON DELETE SET NULL,
    FOREIGN KEY (BranchNo) REFERENCES Branch(BranchNo)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Renter Table
CREATE TABLE Renter (
    RenterNo VARCHAR(10) PRIMARY KEY,
    FName VARCHAR(50) NOT NULL,
    LName VARCHAR(50) NOT NULL,
    Address VARCHAR(200),
    Phone VARCHAR(20),
    PreferredType VARCHAR(30),
    MaxBudget DECIMAL(10,2),
    Notes TEXT,
    BranchNo VARCHAR(10),
    FOREIGN KEY (BranchNo) REFERENCES Branch(BranchNo)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Lease Table
CREATE TABLE Lease (
    LeaseNo VARCHAR(10) PRIMARY KEY,
    PropertyNo VARCHAR(10) NOT NULL,
    RenterNo VARCHAR(10) NOT NULL,
    StaffNo VARCHAR(10),
    Rent DECIMAL(10,2) NOT NULL,
    DepositAmount DECIMAL(10,2),
    IsDepositPaid BOOLEAN DEFAULT FALSE,
    StartDate DATE NOT NULL,
    EndDate DATE NOT NULL,
    LeaseDuration INT,
    PaymentMethod VARCHAR(50),
    Status ENUM('Active','Expired','Terminated') DEFAULT 'Active',
    CONSTRAINT chk_lease_dates CHECK (EndDate > StartDate),
    FOREIGN KEY (PropertyNo) REFERENCES Property(PropertyNo)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (RenterNo) REFERENCES Renter(RenterNo)
        ON UPDATE CASCADE ON DELETE RESTRICT,
    FOREIGN KEY (StaffNo) REFERENCES Staff(StaffNo)
        ON UPDATE CASCADE ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Viewing Table
CREATE TABLE Viewing (
    ViewingID INT AUTO_INCREMENT PRIMARY KEY,
    PropertyNo VARCHAR(10) NOT NULL,
    RenterNo VARCHAR(10) NOT NULL,
    ViewDate DATE NOT NULL,
    Remarks TEXT,
    FOREIGN KEY (PropertyNo) REFERENCES Property(PropertyNo)
        ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (RenterNo) REFERENCES Renter(RenterNo)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Inspection Table
CREATE TABLE Inspection (
    InspectionID INT AUTO_INCREMENT PRIMARY KEY,
    PropertyNo VARCHAR(10) NOT NULL,
    StaffNo VARCHAR(10) NOT NULL,
    InspectDate DATE NOT NULL,
    Notes TEXT,
    FOREIGN KEY (PropertyNo) REFERENCES Property(PropertyNo)
        ON UPDATE CASCADE ON DELETE CASCADE,
    FOREIGN KEY (StaffNo) REFERENCES Staff(StaffNo)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Advertisement Table
CREATE TABLE Advertisement (
    AdID INT AUTO_INCREMENT PRIMARY KEY,
    PropertyNo VARCHAR(10) NOT NULL,
    MediaSource VARCHAR(100),
    PublishDate DATE,
    FOREIGN KEY (PropertyNo) REFERENCES Property(PropertyNo)
        ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ============================================
-- CREATE INDEXES FOR PERFORMANCE
-- ============================================

CREATE INDEX idx_staff_branch ON Staff(BranchNo);
CREATE INDEX idx_property_branch ON Property(BranchNo);
CREATE INDEX idx_property_staff ON Property(StaffNo);
CREATE INDEX idx_property_status ON Property(Status);
CREATE INDEX idx_lease_property ON Lease(PropertyNo);
CREATE INDEX idx_lease_renter ON Lease(RenterNo);
CREATE INDEX idx_lease_status ON Lease(Status);
CREATE INDEX idx_renter_branch ON Renter(BranchNo);
CREATE INDEX idx_viewing_property ON Viewing(PropertyNo);
CREATE INDEX idx_inspection_property ON Inspection(PropertyNo);
CREATE INDEX idx_nextofkin_staff ON NextOfKin(StaffNo);

-- ============================================
-- INSERT SAMPLE DATA
-- ============================================

-- Branch Data
INSERT INTO Branch (BranchNo, BranchName, Street, Area, City, PostCode, ContactNo, Email) VALUES 
('B001', 'London Main', '19 Taylor Street', 'Cranford', 'London', 'W12 8RT', '0171-884-5112', 'london@dreamhome.com'),
('B002', 'Glasgow Central', '163 Main Street', 'Patrick', 'Glasgow', 'G11 9QX', '0141-339-2178', 'glasgow@dreamhome.com'),
('B003', 'Manchester', '45 Deansgate', 'City Centre', 'Manchester', 'M3 2AY', '0161-234-5678', 'manchester@dreamhome.com');

-- Staff Data
INSERT INTO Staff (StaffNo, FName, LName, Address, Phone, Email, Gender, BirthDate, NationalID, JobTitle, Salary, HireDate, BranchNo) VALUES 
('SL21', 'John', 'White', '19 Taylor Street, Cranford, London', '0171-884-5112', 'john.white@dreamhome.com', 'M', '1965-03-15', 'WK442011B', 'Manager', 75000.00, '1988-10-24', 'B001'),
('SL42', 'Sarah', 'Brand', '45 Park Lane, London', '0171-884-5114', 'sarah.brand@dreamhome.com', 'F', '1970-05-15', 'WK442012C', 'Supervisor', 45000.00, '1990-03-15', 'B001'),
('SL55', 'Ann', 'Beech', '78 Queen Street, Glasgow', '0141-339-2180', 'ann.beech@dreamhome.com', 'F', '1975-08-20', 'WK442013D', 'Supervisor', 48000.00, '1991-06-20', 'B002'),
('SL60', 'Mike', 'Ritchie', '12 King Road, Glasgow', '0141-339-2181', 'mike.ritchie@dreamhome.com', 'M', '1980-02-10', 'WK442014E', 'Administrator', 32000.00, '1995-01-10', 'B002'),
('SL70', 'Emma', 'Wilson', '33 Victoria Street, London', '0171-884-5115', 'emma.wilson@dreamhome.com', 'F', '1982-11-05', 'WK442015F', 'Secretary', 28000.00, '1996-08-05', 'B001'),
('SL80', 'David', 'Brown', '56 High Street, Manchester', '0161-234-5680', 'david.brown@dreamhome.com', 'M', '1978-03-22', 'WK442016G', 'Administrator', 35000.00, '1994-05-15', 'B003');

-- Next Of Kin Data
INSERT INTO NextOfKin (StaffNo, KinName, Relation, Address, Phone) VALUES 
('SL21', 'Mary White', 'Wife', '19 Taylor Street, Cranford, London', '0171-884-5112'),
('SL42', 'David Brand', 'Husband', '45 Park Lane, London', '0171-555-1234'),
('SL55', 'Robert Beech', 'Husband', '78 Queen Street, Glasgow', '0141-555-5678'),
('SL60', 'Jennifer Ritchie', 'Mother', '12 King Road, Glasgow', '0141-555-9012'),
('SL70', 'James Wilson', 'Father', '33 Victoria Street, London', '0171-555-3456'),
('SL80', 'Susan Brown', 'Wife', '56 High Street, Manchester', '0161-555-7890');

-- Property Data
INSERT INTO Property (PropertyNo, StreetName, District, City, PostCode, PropertyType, Rooms, RentAmount, StaffNo, BranchNo, Status, DateAvailable) VALUES 
('PG4', '6 Lawrence St.', 'Patrick', 'Glasgow', 'G11 9QX', 'Flat', 3, 350.00, 'SL55', 'B002', 'Available', '2024-01-01'),
('PG36', '2 Manor Road', '', 'Glasgow', 'G32 4QX', 'Flat', 3, 375.00, 'SL55', 'B002', 'Available', '2024-02-01'),
('PG21', '18 Dale Road', 'Hyndland', 'Glasgow', 'G12', 'House', 5, 600.00, 'SL55', 'B002', 'Available', '2024-03-01'),
('PG16', '5 Novar', 'Hyndland', 'Glasgow', 'G12 9AX', 'Flat', 4, 450.00, 'SL55', 'B002', 'Available', '2024-04-01'),
('PG8', '12 Park Avenue', 'Cranford', 'London', 'W12 7RT', 'Flat', 2, 500.00, 'SL42', 'B001', 'Available', '2024-05-01'),
('PG10', '88 Main Road', 'City Centre', 'Manchester', 'M3 2AB', 'House', 4, 550.00, 'SL80', 'B003', 'Available', '2024-06-01');

-- Renter Data
INSERT INTO Renter (RenterNo, FName, LName, Address, Phone, PreferredType, MaxBudget, Notes, BranchNo) VALUES 
('CR74', 'Mike', 'Ritchie', '18 Tain Street, Gourock, PAIG 1YQ', '01475 392178', 'House', 750.00, 'Currently living at home with parents. Getting married in August', 'B002'),
('CR75', 'Jane', 'Smith', '25 Oak Lane, London', '0171-555-1234', 'Flat', 500.00, 'First time renter, needs reference', 'B001'),
('CR76', 'Robert', 'Johnson', '100 High Street, Manchester', '0161-555-5678', 'House', 600.00, 'Relocating for work', 'B003'),
('CR77', 'Lisa', 'Thompson', '45 Queen Road, Glasgow', '0141-555-9012', 'Flat', 400.00, 'Student, quiet tenant', 'B002');

-- Lease Data
INSERT INTO Lease (LeaseNo, PropertyNo, RenterNo, StaffNo, Rent, DepositAmount, IsDepositPaid, StartDate, EndDate, LeaseDuration, PaymentMethod, Status) VALUES 
('L001', 'PG21', 'CR74', 'SL55', 600.00, 1200.00, TRUE, '2024-06-01', '2025-06-01', 12, 'Bank Transfer', 'Active'),
('L002', 'PG8', 'CR75', 'SL42', 500.00, 1000.00, TRUE, '2024-07-01', '2025-07-01', 12, 'Direct Debit', 'Active'),
('L003', 'PG4', 'CR77', 'SL55', 350.00, 700.00, FALSE, '2024-08-01', '2025-02-01', 6, 'Cash', 'Active');

-- Viewing Data
INSERT INTO Viewing (PropertyNo, RenterNo, ViewDate, Remarks) VALUES 
('PG4', 'CR74', '2024-05-01', 'Interested in property, liked the location'),
('PG21', 'CR74', '2024-05-10', 'Liked the property, proceeding with lease'),
('PG8', 'CR75', '2024-06-15', 'Very interested, perfect size'),
('PG16', 'CR76', '2024-06-20', 'May consider, needs to check commute'),
('PG36', 'CR77', '2024-07-01', 'Good for student, budget friendly');

-- Inspection Data
INSERT INTO Inspection (PropertyNo, StaffNo, InspectDate, Notes) VALUES 
('PG21', 'SL21', '2024-04-12', 'No problems'),
('PG21', 'SL21', '2024-09-30', 'Cracked ceiling in living room. Requires urgent repair.'),
('PG21', 'SL21', '2024-07-01', 'Crockery needs to be replaced.'),
('PG8', 'SL42', '2024-06-01', 'Property in good condition'),
('PG4', 'SL55', '2024-05-15', 'Minor cleaning needed'),
('PG16', 'SL55', '2024-06-10', 'All appliances working correctly');

-- Advertisement Data
INSERT INTO Advertisement (PropertyNo, MediaSource, PublishDate) VALUES 
('PG4', 'Local Newspaper', '2024-01-15'),
('PG36', 'Online Portal', '2024-02-15'),
('PG21', 'Local Newspaper', '2024-03-15'),
('PG16', 'Estate Agent Window', '2024-04-15'),
('PG8', 'Local Newspaper', '2024-05-15'),
('PG10', 'Online Portal', '2024-06-15');

COMMIT;