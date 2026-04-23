DROP TRIGGER IF EXISTS `APPLICATIONFILES_AUCACHE_INSERT`$$
CREATE TRIGGER `APPLICATIONFILES_AUCACHE_INSERT` AFTER INSERT ON `APPLICATIONFILES`
FOR EACH ROW 
BEGIN
	# update the cache version for the type and brand belonging in the new record
	CALL AUCACHE_UPDATEBRANDINGVERSION(`NEW`.`type`, `NEW`.`webbrandcode`);


	# if this is a product collection we need to update the cache version
	IF `NEW`.`type` = 0 THEN
		CALL AUCACHE_UPDATEVERSION("", "");
	END IF;
END$$


DROP TRIGGER IF EXISTS `APPLICATIONFILES_AUCACHE_UPDATE`$$
CREATE TRIGGER `APPLICATIONFILES_AUCACHE_UPDATE` AFTER UPDATE ON `APPLICATIONFILES`
FOR EACH ROW 
BEGIN
	# if the brand or type has changed in the updated record we need to update the cache version for the original type and brand
	IF (`NEW`.`type` <> `OLD`.`type`) OR (`NEW`.`webbrandcode` <> `OLD`.`webbrandcode`) THEN
		CALL AUCACHE_UPDATEBRANDINGVERSION(`OLD`.`type`, `OLD`.`webbrandcode`);
	END IF;

	# update the cache version for the type and brand in the updated record
	CALL AUCACHE_UPDATEBRANDINGVERSION(`NEW`.`type`, `NEW`.`webbrandcode`);
	
	# if this is a product collection we need to update the cache version
	IF (`NEW`.`type` = 0) OR (`OLD`.`type` = 0) THEN
	CALL AUCACHE_UPDATEVERSION("", "");
	END IF;
END$$


DROP TRIGGER IF EXISTS `APPLICATIONFILES_AUCACHE_DELETE`$$
CREATE TRIGGER `APPLICATIONFILES_AUCACHE_DELETE` AFTER DELETE ON `APPLICATIONFILES`
FOR EACH ROW 
BEGIN
	# update the cache version for the type and brand in the deleted record
	CALL AUCACHE_UPDATEBRANDINGVERSION(`OLD`.`type`, `OLD`.`webbrandcode`);
	
	# if this is a product collection we need to update the cache version
	IF `OLD`.`type` = 0 THEN
	CALL AUCACHE_UPDATEVERSION("", "");
	END IF;
END$$

