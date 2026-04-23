#
SET FOREIGN_KEY_CHECKS = 0;

#
# DDL START
#


ALTER TABLE `CONSTANTS`
    MODIFY `defaultlanguagecode` varchar(10) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci;
#
#  Fieldformat of
#    CONSTANTS.defaultlanguagecode changed from varchar(2) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci to varchar(10) NOT NULL DEFAULT '' COMMENT '' COLLATE utf8_general_ci.
#  Possibly data modifications needed!
#

#
# DDL END
#

SET FOREIGN_KEY_CHECKS = 1;