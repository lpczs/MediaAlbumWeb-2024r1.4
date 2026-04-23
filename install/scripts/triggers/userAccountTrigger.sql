DROP TRIGGER IF EXISTS `USER_BRANDCHECK_INSERT`$$
CREATE TRIGGER `USER_BRANDCHECK_INSERT` BEFORE INSERT ON `USERS`
FOR EACH ROW 
BEGIN
    DECLARE existingID INTEGER DEFAULT 0;

    # if we are inserting a customer check for an existing login that is not a customer
    # if we are not inserting a customer check for any matching login
    IF `NEW`.`customer` = 1 THEN
        SET existingID = (SELECT `id` FROM `USERS` WHERE (`login` = `NEW`.`login`) 
            AND (`customer` = 0) LIMIT 1);
    ELSE
        SET existingID = (SELECT `id` FROM `USERS` WHERE `login` = `NEW`.`login` LIMIT 1);
    END IF;


    # if we have found an existing record we need to abort the insert with a duplicate key error
    # the easiest way to do this is to set the record id to the same as the one we found
    IF existingID > 0 THEN
        SET `NEW`.`id` = existingID;
    END IF;
END$$


DROP TRIGGER IF EXISTS `USER_BRANDCHECK_UPDATE`$$
CREATE TRIGGER `USER_BRANDCHECK_UPDATE` BEFORE UPDATE ON `USERS`
FOR EACH ROW 
BEGIN
    DECLARE existingID INTEGER DEFAULT 0;

    If (`NEW`.`login` <> `OLD`.`login`) OR (`NEW`.`customer` <> `OLD`.`customer`) THEN
        # if we are updating a customer check for an existing login that is not a customer
        # if we are not updating a customer check for any matching login
        IF `NEW`.`customer` = 1 THEN
            SET existingID = (SELECT `id` FROM `USERS` WHERE (`login` = `NEW`.`login`) 
                AND (`customer` = 0) AND (`id` <> `OLD`.`id`) LIMIT 1);
        ELSE
            SET existingID = (SELECT `id` FROM `USERS` WHERE (`login` = `NEW`.`login`) 
                AND (`id` <> `OLD`.`id`) LIMIT 1);
        END IF;


        # if we have found an existing record we need to abort the update with a duplicate key error
        # the easiest way to do this is to set the webbrandcode to one from the matching login we found
        # (we have to get the column data separately as retrieving multiple columns can send warnings to the host) 
        IF existingID > 0 THEN
            SET `NEW`.`webbrandcode` = (SELECT `webbrandcode` FROM `USERS` WHERE `id` = existingID);
        END IF;
    END IF;
END$$