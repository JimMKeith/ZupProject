-- *** Load code tables 
-- ***

use programm_zup;

-- *** Make sure all tables are empty before attempting to load them

DELETE FROM Msts;
DELETE FROM Mtype;
DELETE FROM Otype;
DELETE FROM Csts;
DELETE FROM Scope;


-- ***Member Statuses
INSERT INTO Msts VALUES
('a','Active'),
('h','Hold'),
('u','Unverified new member');  


-- *** Member Types
INSERT INTO Mtype
VALUES
  ('m', 'Regular Member'),
  ('c', 'Contributing Member'),
  ('t', 'Trusted Contributor'),
  ('a', 'Administrator');

-- *** Object Types
INSERT INTO Otype VALUES
(1,'Image Gallary'),
(2,'Audio presentation'),
(3,'Slide Show'),
(4,'Video or Movie Presentation');

-- *** Comment Statuses
INSERT INTO Csts VALUES
('a','Active'),
('i','Inactive');

-- *** Scope 
INSERT INTO Scope VALUES
(0,'None, No viewing allowed'),
(1,'Private viewing only'),
(2,'Membership viewing only'),
(3,'Public Viewing');

-- ^^^ 'Guest' user_id is reserved for system use 
INSERT INTO Members
    (user_id, password, name, email)
    VALUES('Guest', 'N/A', ' ', ' '); 