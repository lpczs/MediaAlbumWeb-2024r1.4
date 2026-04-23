DROP TRIGGER IF EXISTS `APPLICATIONFILES_AUCACHE_INSERT`$$
CREATE TRIGGER `APPLICATIONFILES_AUCACHE_INSERT` AFTER INSERT ON `APPLICATIONFILES`
FOR EACH ROW 
BEGIN
    # update the cache version for the type and brand belonging in the new record
    CALL autoUpdateCacheUpdateBrandingVersion(`NEW`.`type`, `NEW`.`webbrandcode`);
END$$


DROP TRIGGER IF EXISTS `APPLICATIONFILES_AUCACHE_UPDATE`$$
CREATE TRIGGER `APPLICATIONFILES_AUCACHE_UPDATE` AFTER UPDATE ON `APPLICATIONFILES`
FOR EACH ROW 
BEGIN
    # if the brand or type has changed in the updated record we need to update the cache version for the original type and brand
    IF (`NEW`.`type` <> `OLD`.`type`) OR (`NEW`.`webbrandcode` <> `OLD`.`webbrandcode`) THEN
        CALL autoUpdateCacheUpdateBrandingVersion(`OLD`.`type`, `OLD`.`webbrandcode`);
    END IF;

    # update the cache version for the type and brand in the updated record
    CALL autoUpdateCacheUpdateBrandingVersion(`NEW`.`type`, `NEW`.`webbrandcode`);
END$$


DROP TRIGGER IF EXISTS `APPLICATIONFILES_AUCACHE_DELETE`$$
CREATE TRIGGER `APPLICATIONFILES_AUCACHE_DELETE` AFTER DELETE ON `APPLICATIONFILES`
FOR EACH ROW 
BEGIN
    # update the cache version for the type and brand in the deleted record
    CALL autoUpdateCacheUpdateBrandingVersion(`OLD`.`type`, `OLD`.`webbrandcode`);
END$$

