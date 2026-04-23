CREATE PROCEDURE `AddColumn`(
	IN tableName TINYTEXT,
	IN fieldName TINYTEXT,
	IN fieldDef TEXT
)
BEGIN

	IF NOT EXISTS (
		SELECT * FROM `INFORMATION_SCHEMA`.`COLUMNS`
		WHERE `COLUMN_NAME`=fieldName
		and `TABLE_NAME`=tableName
		and `TABLE_SCHEMA`=DATABASE()
		)
	THEN
		SET @ddl=CONCAT('ALTER TABLE `',DATABASE(),'`.`',tableName,
			'` ADD COLUMN `',fieldName,'` ',fieldDef);
		PREPARE stmt FROM @ddl;
		EXECUTE stmt;
	END IF;

END