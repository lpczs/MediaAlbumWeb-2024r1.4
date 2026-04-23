CREATE PROCEDURE `AUCACHE_UPDATEBRANDINGVERSION`(IN pFileType INTEGER, IN pBrandCode VARCHAR(50))
BEGIN
    # update the cache version for the specified type and brand

    DECLARE newCacheVersion VARCHAR(50) DEFAULT '';

    SET newCacheVersion = CONCAT(NOW(), "_", FLOOR(RAND() * 100000));

    CASE pFileType
        WHEN 1 THEN UPDATE `BRANDING` SET `BRANDING`.`aucacheversionmasks` = newCacheVersion WHERE `BRANDING`.`code` = pBrandCode;
        WHEN 2 THEN UPDATE `BRANDING` SET `BRANDING`.`aucacheversionbackgrounds` = newCacheVersion WHERE `BRANDING`.`code` = pBrandCode;
        WHEN 3 THEN UPDATE `BRANDING` SET `BRANDING`.`aucacheversionscrapbook` = newCacheVersion WHERE `BRANDING`.`code` = pBrandCode;
        WHEN 4 THEN UPDATE `BRANDING` SET `BRANDING`.`aucacheversionframes` = newCacheVersion WHERE `BRANDING`.`code` = pBrandCode;
        ELSE
            BEGIN
            END;
    END CASE;
END