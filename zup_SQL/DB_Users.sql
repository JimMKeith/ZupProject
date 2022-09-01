use Zup;

-- *** Create Users and grant access to the Zup database 

--  *** Define users of the Zup Web Site 
CREATE USER 'ZupUser'@'localhost' IDENTIFIED BY 'Zuppy_U1';
CREATE USER 'ZupAdmin'@'localhost' IDENTIFIED BY 'Zuppy_A1';
CREATE USER 'ZupContributor'@'localhost' IDENTIFIED BY 'Zuppy_C1';
CREATE USER 'ZupPrgmr'@'localhost' IDENTIFIED BY 'Zuppy_P1';

 
--  *** Grant access for Zup users  
GRANT ALL PRIVILEGES ON Zup.* TO 'ZupPrgmr'@'localhost';

GRANT SELECT, UPDATE, INSERT, DELETE, EXECUTE
    ON Zup.*
    TO 'ZupUser'@'localhost';

GRANT SELECT, UPDATE, INSERT, DELETE, EXECUTE
    ON Zup.*
    TO 'ZupAdmin'@'localhost';

GRANT SELECT, UPDATE, INSERT, DELETE, EXECUTE
    ON Zup.*
    TO 'ZupContributor'@'localhost';