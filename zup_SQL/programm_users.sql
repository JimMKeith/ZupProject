use programm_zup;

-- *** Create Users and grant access to the Zup database 

--  *** Define users of the Zup Web Site 
CREATE USER 'programm_zup_u'@'localhost'      IDENTIFIED BY 'pNpzzor!lc!n#S7';
CREATE USER 'programm_zup_admin'@'localhost'  IDENTIFIED BY 'ZR.7!cv.bXxaPJ5';
CREATE USER 'programm_contri'@'localhost'     IDENTIFIED BY 'g!Flu#.lIQhbkt7';
CREATE USER 'programm_prgmr'@'localhost'      IDENTIFIED BY 'K#39mUjFYO!!Cg5';

 
--  *** Grant access for Zup users  
GRANT ALL PRIVILEGES ON programm_zup.* TO 'programm_prgmr'@'localhost';

GRANT SELECT, UPDATE, INSERT, DELETE, EXECUTE
    ON programm_zup.*
    TO 'programm_zup_u'@'localhost';

GRANT SELECT, UPDATE, INSERT, DELETE, EXECUTE
    ON programm_zup.*
    TO 'programm_zup_admin'@'localhost';

GRANT SELECT, UPDATE, INSERT, DELETE, EXECUTE
    ON programm_zup.*
    TO 'programm_contri'@'localhost';0