CREATE PROCEDURE `defaultPriceConvert`(IN pSection VARCHAR(50))
BEGIN
	DECLARE varMasterID INT DEFAULT 0;
	DECLARE varMasterCompanyCode VARCHAR(50) DEFAULT "";
	DECLARE masterComponentCode VARCHAR(50) DEFAULT "";
	DECLARE varMasterProductCode VARCHAR(50) DEFAULT "";
	DECLARE varMasterGroupCode VARCHAR(50) DEFAULT "";
	DECLARE varMasterPriceActive INT DEFAULT 0;
	DECLARE varProductCode VARCHAR(50) DEFAULT "";
	DECLARE varComponentCompanyCode VARCHAR(50) DEFAULT "";
	DECLARE masterComponentExit BOOLEAN DEFAULT FALSE;

	DROP VIEW IF EXISTS COMPONENTPRICESVIEW;
	IF (pSection = 'COVER') THEN
		SET @stmt_ComponentView = "CREATE VIEW COMPONENTPRICESVIEW AS SELECT `price`.`id`, `price`.`companycode`, `price`.`covercode` AS componentcode, `price`.`productcode`, `price`.`groupcode`, `price`.`active`, `component`.`companycode` AS compcompany
					FROM COVERPRICES AS price LEFT JOIN COVERS AS component ON component.code = price.covercode 
					WHERE productcode = '' AND parentid = 0 ORDER BY `id`";
	ELSE
		SET @stmt_ComponentView = "CREATE VIEW COMPONENTPRICESVIEW AS SELECT `price`.`id`, `price`.`companycode`, `price`.`papercode` AS componentcode, `price`.`productcode`, `price`.`groupcode`, `price`.`active`, `component`.`companycode` AS compcompany 
					FROM PAPERPRICES AS price LEFT JOIN PAPER AS component ON component.code = price.papercode 
					WHERE productcode = '' AND parentid = 0 ORDER BY `id`";
	END IF;

	PREPARE componentSelect FROM @stmt_ComponentView;
	EXECUTE componentSelect;

	COMPONENTBLOCK: BEGIN
		# 'Select id of master records which has parentid = 0'
		DECLARE masterComponentCursor CURSOR FOR
			SELECT `id`,`companycode`,`componentcode`,`productcode`,`groupcode`,`active`, `compcompany` FROM COMPONENTPRICESVIEW;

		DECLARE CONTINUE HANDLER FOR NOT FOUND SET masterComponentExit = TRUE;
		OPEN masterComponentCursor;

		# 'Create temporary table, this will be automatically deleted after this script finished'
		CREATE TEMPORARY TABLE IF NOT EXISTS KeyView (
			`companycode` 	  VARCHAR(50)	CHARACTER SET utf8 NOT NULL DEFAULT "",
			`componentcode`   VARCHAR(50)	CHARACTER SET utf8 NOT NULL DEFAULT "",
			`productcode`     VARCHAR(50)	CHARACTER SET utf8 NOT NULL DEFAULT "",
			`groupcode`       VARCHAR(50)	CHARACTER SET utf8 NOT NULL DEFAULT "",
			`active`          TINYINT(1)					   NOT NULL DEFAULT 0
		);


		COMPONENTLOOP: LOOP
			FETCH masterComponentCursor INTO varMasterID, varMasterCompanyCode, masterComponentCode, varMasterProductCode,varMasterGroupCode,varMasterPriceActive, varComponentCompanyCode;

			IF masterComponentExit THEN
				CLOSE masterComponentCursor;
				LEAVE COMPONENTLOOP;
			END IF;


			# 'Empty KeyView for each CoverPrice Record'
			DELETE FROM KeyView;

			# 'Insert master record into KeyView'
			INSERT INTO KeyView (`companycode`,`componentcode`,`productcode`,`groupcode`,`active`) VALUES (varMasterCompanyCode, masterComponentCode, varMasterProductCode,varMasterGroupCode,varMasterPriceActive);

			# 'Insert child records into KeyView'
			IF (pSection = 'COVER') THEN
				INSERT INTO KeyView (`companycode`,`componentcode`,`productcode`,`groupcode`,`active`) SELECT `companycode`, `covercode`, `productcode`,`groupcode`,`active` FROM COVERPRICES WHERE parentid = varMasterID ORDER BY `id`;
			ELSE
				INSERT INTO KeyView (`companycode`,`componentcode`,`productcode`,`groupcode`,`active`) SELECT `companycode`, `papercode`, `productcode`,`groupcode`,`active` FROM PAPERPRICES WHERE parentid = varMasterID ORDER BY `id`;
			END IF;

			PRODUCTBLOCK: BEGIN
				DECLARE productExit BOOLEAN DEFAULT FALSE;

				# 'Select product which belong to a company or global product'
				DECLARE productCursor CURSOR FOR
					SELECT `code` FROM PRODUCTS WHERE companycode = '' OR companycode = varMasterCompanyCode;

				DECLARE CONTINUE HANDLER FOR NOT FOUND SET productExit = TRUE;
				OPEN productCursor;

				# 'Product loop'
				PRODUCTLOOP: LOOP
					FETCH productCursor INTO varProductCode;

					IF productExit THEN
						CLOSE productCursor;
						LEAVE PRODUCTLOOP;
					END IF;

					KEYVIEWBLOCK: BEGIN
						DECLARE keyViewExit BOOLEAN DEFAULT FALSE;
						DECLARE varKeyId INT DEFAULT 0;
						DECLARE varKeyCompanyCode VARCHAR(50) DEFAULT "";
						DECLARE varKeyComponentCode VARCHAR(50) DEFAULT "";
						DECLARE varKeyProductCode VARCHAR(50) DEFAULT "";
						DECLARE varKeyGroupCode VARCHAR(50) DEFAULT "";
						DECLARE varKeyPriceActive INT DEFAULT 0;

						# 'Reset these flags for each product'
						DECLARE varParentID INT DEFAULT 0;
						DECLARE FirstRecord BOOLEAN DEFAULT TRUE;

						# 'Select everything from the keyview'
						DECLARE keyViewCursor CURSOR FOR
							SELECT `companycode`,`componentcode`,`productcode`,`groupcode`,`active` FROM KeyView;

						DECLARE CONTINUE HANDLER FOR NOT FOUND SET keyViewExit = TRUE;
						OPEN keyViewCursor;

						# 'KEYVIEW LOOP'
						KEYVIEWLOOP: LOOP
							FETCH keyViewCursor INTO varKeyCompanyCode,varKeyComponentCode,varKeyProductCode,varKeyGroupCode,varKeyPriceActive;

							IF keyViewExit THEN
								CLOSE keyViewCursor;
								LEAVE KEYVIEWLOOP;
							END IF;

							# 'Insert new records for COVER / PAPER depends the parameter passed in'
							IF (pSection = 'COVER') THEN
								INSERT INTO PRICELINK (`datecreated`, `parentid`, `companycode`, `productcode`, `groupcode`, `componentcode`,`parentpath`,`sectioncode`, `priceid`, `active`) VALUES
								(now(), varParentID, varKeyCompanyCode, varProductCode, varKeyGroupCode, IF (varComponentCompanyCode = "", CONCAT("COVER",".",varKeyComponentCode), CONCAT(varComponentCompanyCode,".","COVER",".",varKeyComponentCode)), '$COVER\\', 'COVER', '-1', varKeyPriceActive);
							ELSE
								INSERT INTO PRICELINK (`datecreated`, `parentid`, `companycode`, `productcode`, `groupcode`, `componentcode`,`parentpath`,`sectioncode`, `priceid`, `active`) VALUES
								(now(), varParentID, varKeyCompanyCode, varProductCode, varKeyGroupCode, IF (varComponentCompanyCode = "", CONCAT("PAPER",".",varKeyComponentCode), CONCAT(varComponentCompanyCode,".","PAPER",".",varKeyComponentCode)), '$PAPER\\', 'PAPER', '-1', varKeyPriceActive);
							END IF;

							IF (FirstRecord = TRUE) THEN
								SET varParentID = (SELECT MAX(`id`) FROM PRICELINK);
							END IF;

							SET FirstRecord = FALSE;

						END LOOP KEYVIEWLOOP;
					END KEYVIEWBLOCK;

				END LOOP PRODUCTLOOP;
			END PRODUCTBLOCK;

		END LOOP COMPONENTLOOP;
	END COMPONENTBLOCK;

	# 'Drop the view after finished'
	DROP VIEW IF EXISTS COMPONENTPRICESVIEW;

END;