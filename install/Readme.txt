****************************************
******** TAOPIX INSTALL SCRIPT: ********
****************************************

Put the whole Install folder into your MediaAlbumWeb & run install.php by command line 

1. For new installation: Choose option 1 & follow the instruction on screen.
2. For upgrading: Choose option 2 & follow the instruction on screen. 


***** NOTES ******

- You should always back up database before upgrading
- In case of failure, you will need to restate the backup & run the install script again. 
- After installation / upgrade, the script also check for the Admin username & Password of control centre. 
  passwordBlackList.txt includes all md5 encrypted passwords we recommend people not to use. 
  The script will not stop if it detected these password, it will only recommend people not to use it. 

 
***** Stored procedures & Triggers *****

- Put store procedures in "storedProcedures" folder
- Put triggers in "triggers" folder 
- Store procedure name should be the same as the file name, i.e file name: dropIndexIfExists2.sql, store procedure name: dropIndexIfExists2.
- The calls to run the store procedures are in install.php, you only need to pass in the stored procedure

      For example: runStoredProcedure($pConnection,"dropIndexIfExists2");

- The calls to execute triggers are in install.php, you only need to pass in the file name without its extension 

      For example:  runTriggers($pConnection, 'updateLicenseKeyCacheVersion');
