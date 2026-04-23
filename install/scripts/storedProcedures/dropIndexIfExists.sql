CREATE PROCEDURE `dropIndexIfExists`()

BEGIN

SET @Index_cnt = (
SELECT count(1) cnt
FROM INFORMATION_SCHEMA.STATISTICS
WHERE table_name = 'orderitems'
AND index_name = 'orderid' AND TABLE_SCHEMA = DATABASE()
);

IF ifnull(@Index_cnt,0) > 0 THEN

	DROP INDEX `orderid` ON `ORDERITEMS`;

END IF;

END