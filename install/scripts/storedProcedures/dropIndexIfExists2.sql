CREATE PROCEDURE `dropIndexIfExists2`(IN pTableName VARCHAR(50), IN pIndexName VARCHAR(50))

BEGIN

SET @Index_cnt = (
SELECT count(1) cnt
FROM INFORMATION_SCHEMA.STATISTICS
WHERE table_name = pTableName
AND index_name = pIndexName AND TABLE_SCHEMA = DATABASE()
);

IF ifnull(@Index_cnt,0) > 0 THEN
	
	SET @query = CONCAT('DROP INDEX ', pIndexName , ' ON ', pTableName, ';'); 
	PREPARE stmt FROM @query; 
	EXECUTE stmt;
	DEALLOCATE PREPARE stmt;
	
END IF;

END