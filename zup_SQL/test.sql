use programm_zup;

SELECT  m.mbr_id, DATEDIFF(NOW(), m.signup_dt) AS 'days', NOW(), m.signup_dt AS 'signDt'
  FROM Members AS m;     
