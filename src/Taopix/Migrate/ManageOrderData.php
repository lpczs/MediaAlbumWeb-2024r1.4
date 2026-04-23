<?php

namespace Taopix\Migrate;

use Taopix\CLI\CLITimer;

class ManageOrderData
{
    // Number of directories to read in each block.
    const MIGRATE_BLOCKSIZE = 1000;

    // Format of the destination directory to move the thumbnails into.
    const MIGRATE_PATH_TEMPLATE = 'Y/m/d/H';

    // Migration states.
    const MIGRATE_STATE_OK = 0;
    const MIGRATE_STATE_COMPLETE = 1;
    const MIGRATE_STATE_NOT_REQUIRED = 2;
    const MIGRATE_STATE_NO_SPACE = 8;
    const MIGRATE_STATE_NO_DESTINATION = 9;

    // Track the time the process takes.
    private $migrateTimer;

    // Directory / file paths to use.
    private $paths = [
        'thumbnails' => '',
        'destination' => '',
        'logfile' => ''
    ];

    // Status of the migration.
    private $status = 0;

    // State messages based on the possible states.
    private $states = [
        0 => 'OK',
        1 => 'Complete',
        2 => 'No Source Directory - No mirgration required',
        8 => 'Error - Not enough space on destination volume',
        9 => 'Error - Unable to create destination directory'
    ];

    // Processing results for final summary.
    private $processResult = [
        'total' => 0,
        'success' => 0,
        'failed' => 0,
        'empty' => 0,
        'unknown' => 0
    ];

    // List of annoying, potenially hidden files that may prevent a direcotry from being deleted.
    private $filesToAutoDelete = [
        '.DS_Store', 
        '.localized', 
        'Thumbs.db', 
        'lock'
    ];

    // Track failed directory removals from the source.
    private $failedRefList = [];

    // Track any unexpected content in the thumbnails directory.
    private $unknownFiles = false;

    // Screen reporting level.
    private $reportLevel = 1;

    // Track the free space remaining on the destination volume.
    private $freeSpace = 0;

    /**
     * Constructor
     */
    public function __construct(array $pConfig)
    {
        // Create a new timer.
        $this->migrateTimer = new CLITimer();

        // Set the destination directory.
        $this->paths['destination'] = \UtilsObj::correctPath($pConfig['destination'], \DIRECTORY_SEPARATOR, true);

        // Set the path to the error log.
        $this->paths['logfile'] = \implode(\DIRECTORY_SEPARATOR, [$pConfig['root'], 'logs', 'migrateThumbnails', \date('Ymd') . '.log']);

        // Generate the path to the original order data directory.
        $this->paths['thumbnails'] = \implode(\DIRECTORY_SEPARATOR, [$pConfig['root'], 'webroot', 'OrderData', 'Thumbnails', 'pages']) . \DIRECTORY_SEPARATOR;

        // Make sure the source directory exists.
        // Check source directory exists.
        if (\file_exists($this->paths['thumbnails']))
        {
            // Create the destination root directory.
            if (! \UtilsObj::createAllFolders($this->paths['destination']))
            {
                $this->status = self::MIGRATE_STATE_NO_DESTINATION;
            }
        }
        else
        {
            $this->status = self::MIGRATE_STATE_NOT_REQUIRED;
        }
    }


    /**
     * output message to terminal and/or logfile
     * 
     * @param string $pStrToOutput - Message to output.
     * @param int $pOutputAtLevel - set the level at which output should be sent to screen.
     * 
     */
    private function output(string $pStrToOutput, int $pOutputAtLevel): void
    {
        // Output everything to a log file.
        if ('' != $this->paths['logfile'])
        {
            $this->forceFileContents($this->paths['logfile'], $pStrToOutput . \PHP_EOL, \FILE_APPEND | \LOCK_EX);
        }

        // Selective output to terminal.
        if ($pOutputAtLevel >= $this->reportLevel)
        {
            echo $pStrToOutput . \PHP_EOL;
        }
    }


    /**
     * Force file to be created, including the directory path if it does not exist.
     * 
     * @param string $pFullPath - Full path of the file to update.
     * @param string $pContents - Content to write to the file via file_put_contents function.
     * @param int $pFlags - flags to pass to the file_put_contents function.
     */
    private function forceFileContents(string $pFullPath, string $pContents, int $pFlags): void
    {
        $pSource = \UtilsObj::correctPath($pFullPath, \DIRECTORY_SEPARATOR, false);

        // Remove the file name from the path
        $dir = \dirname($pSource);

        // Check if directory exists.
        if (! \is_dir($dir))
        {
            // Create the directory.
            \mkdir($dir, 0777, true);
        }

        // Write content to the file.
        \file_put_contents($pSource, $pContents, $pFlags);
    }


    /**
     * start the migration process
     */
    public function startMigration(): void
    {
        // Start the migration timer.
        $this->migrateTimer->start();

        $this->output("Starting order thumbnail migration:", 1);
        $this->output("Source: " . $this->paths['thumbnails'], 1);
        $this->output("Destination: " . $this->paths['destination'], 1);
        $this->output("======================", 1);

        do
        {
            // Read the uploaded order directory.
            $uploadRefsArray = $this->readOrderUploads();

            if (self::MIGRATE_STATE_OK === $this->status)
            {
                $this->processBlock($uploadRefsArray);
            }
        }
        while (self::MIGRATE_STATE_OK === $this->status);

        // Attempt to delete the now, unused directories.
        if ((self::MIGRATE_STATE_COMPLETE === $this->status) && (0 === $this->processResult['failed']) && (! $this->unknownFiles))
        {
            // Attempt to remove the OrderData/Thumbnails/pages directory
            if (\file_exists($this->paths['thumbnails']))
            {
                if (! @\rmdir($this->paths['thumbnails']))
                {
                    $this->output("Unable to delete " . $this->paths['thumbnails'], 1);
                    $this->unknownFiles = true;
                }
            }

            // If the OrderData/Thumbnails/pages has been removed, attempt to also remove the OrderData/Thumbnails directory
            $thumbsDirPath = \dirname($this->paths['thumbnails']);
            if ((! $this->unknownFiles) && (\file_exists($thumbsDirPath)))
            {
                // Check the OrderData/Thumbnails for any unexpected files.
                $dir = new \DirectoryIterator($thumbsDirPath);
                foreach ($dir as $fileinfo)
                {
                    if ($fileinfo->isFile())
                    {
                        if (\in_array($fileinfo->getFilename(), $this->filesToAutoDelete))
                        {
                            if (! @\unlink($fileinfo->getPathname()))
                            {
                                // An unexcepted file exists in the thumbnails directory.
                                $this->unknownFiles = true;
                                break;
                            }
                        }
                        else
                        {
                            // An unexcepted file exists in the thumbnails directory.
                            $this->unknownFiles = true;
                            break;
                        }
                    }
                    else if ((! $fileinfo->isDot()) && ($fileinfo->isDir()))
                    {
                        // An unexcepted file exists in the thumbnails directory.
                        $this->unknownFiles = true;
                        break;
                    }
                }

                // Attempt to delete the OrderData/Thumbnails if it is empty.
                if (! $this->unknownFiles)
                {
                    if (! @\rmdir($thumbsDirPath))
                    {
                        $this->output("Unable to delete " . $thumbsDirPath, 1);
                    }
                }
                else
                {
                    $this->output("Unable to delete " . $thumbsDirPath, 1);
                }
            }
        }

        // Stop the timer.
        $this->migrateTimer->stop(true);
    }


    /**
     * Read the content of the orderdata directory.
     */
    private function readOrderUploads(): array
    {
        $uploadRefsArray = [];
        $thumbDirCount = 0;

        if (\file_exists($this->paths['thumbnails']))
        {
            $this->output(\PHP_EOL . "Reading order uploads (" . $this->paths['thumbnails'] . ")", 1);

            $dir = new \DirectoryIterator($this->paths['thumbnails']);
            foreach ($dir as $fileinfo)
            {
                $uploadRefDir = $fileinfo->getFilename();
                if ((! $fileinfo->isDot()) && ($fileinfo->isDir()) && (! \in_array($uploadRefDir, $this->failedRefList)))
                {
                    // Generate a list of directories/uploadrefs.
                    $uploadRefsArray[] = $uploadRefDir;
                    $thumbDirCount++;

                    if (self::MIGRATE_BLOCKSIZE === $thumbDirCount)
                    {
                        break;
                    }
                }
                else if ($fileinfo->isFile())
                {
                    // A file has been found in the pages directory, 
                    // attempt to remove it if it is one of the files to auto delete.
                    if (\in_array($uploadRefDir, $this->filesToAutoDelete))
                    {
                        if (! \unlink($fileinfo->getPathname()))
                        {
                            // An unexcepted file exists in the thumbnails directory.
                            $this->unknownFiles = true;
                        }
                    }
                    else
                    {
                        // An unexcepted file exists in the thumbnails directory.
                        $this->unknownFiles = true;
                    }
                }
            }
        }

        // Check for order thumbnail directories to process.
        if (0 === $thumbDirCount)
        {
            // No thumbnails to process, set exit status.
            $this->status = self::MIGRATE_STATE_COMPLETE;
        }
        else
        {
            // Dispaly a directory count.
            $this->output($thumbDirCount . " directories found.", 1);
        }

        // Return the list of upload refs.
        return $uploadRefsArray;
    }


    /**
     * Read the content of the order upload directory.
     * 
     * @param string $pPath - path to the order data thumbnails directory.
     * @return array containing a list of thumbnail file names, and the total size of the files in bytes.
     */
    private function readOrderThumbnails(string $pPath): array
    {
        $thumbsArray = [
            'filelist' => [],
            'count' => 0,
            'size' => 0
        ];

        // If the file does not exist, return the empty data.
        if (! \file_exists($pPath))
        {
            return $thumbsArray;
        }

        // Read the thumbnails stored within the directory.
        $dir = new \DirectoryIterator($pPath);
        foreach ($dir as $fileinfo)
        {
            // Check each file, recording the size as well as the name.
            if ((! $fileinfo->isDot()) && ($fileinfo->isFile()))
            {
                $thumbsArray['filelist'][] = $fileinfo->getFilename();
                $thumbsArray['size'] += $fileinfo->getSize();
                $thumbsArray['count']++;
            }
        }

        return $thumbsArray;
    }


    /**
     * Copy all of the files in the source directory into the destination directory. 
     * On sucess, remove the source directory.
     * On failure, remove the destination directory.
     * 
     * @param string $pSrc - source directory
     * @param string $pDst - destination directory
     */
    private function moveDirectory(string $pSrc, string $pDst): string
    {
        $result = '';

        // Create the destination directory.
        if (\UtilsObj::createAllFolders($pDst))
        {
            // Set the directory to delete to be the source directory.
            $dirToDelete = $pSrc;

            $dir = new \DirectoryIterator($pSrc);
            foreach ($dir as $fileinfo)
            {
                if (! $fileinfo->isDot())
                {
                    $fileName = $fileinfo->getFilename();
                    $srcFileName = $pSrc . \DIRECTORY_SEPARATOR . $fileName;
                    $dstFileName = $pDst . \DIRECTORY_SEPARATOR . $fileName;

                    if (! \copy($srcFileName, $dstFileName))
                    {
                        // Store the error.
                        $errors = \error_get_last();

                        // Set the result as a failure, using the error message.
                        $result = $errors['message'];

                        // Change the directory to delete to be the destination directory.
                        $dirToDelete = $pDst;

                        // Stop processing other files.
                        break;
                    }
                }
            }

            // Remove the content of the unwanted directory.
            // This will be the source directory on sucessful copy or the destination directory on failure.
            @\UtilsObj::deleteFolder($dirToDelete);

            // Check that the unwanted directory has been removed.
            if (\file_exists($dirToDelete))
            {
                if ($dirToDelete === $pSrc)
                {
                    // If the source was unable to be removed, track it so that it does not cause an infinite loop.
                    $this->failedRefList[] = \basename($dirToDelete);
                }

                // If the directory could not be deleted for some reason, output the message.
                $this->output("Unable to delete directory: " . $dirToDelete, 1);
            }
        }
        else
        {
            $result = 'Unable to create ' . $pDst;
        }

        return $result;
    }


    /**
     * Process a block of thumbnails
     * 
     * @param array $pUploadRefData - An array of upload refs to migrate.
     */
    private function processBlock(array $pUploadRefData): void
    {
        // Get the order creation date for the block of projects.
        $processBatchData = \DatabaseObj::getOrderDateFromUploadRefs($pUploadRefData);
        $processedRefs = [];

        // Update the estimated free space of the destination volume.
        $this->freeSpace = \disk_free_space($this->paths['destination']);

        // Check that at least one of the upload refs have been matched to an order item.
        if ('' === $processBatchData['result'])
        {
            foreach ($processBatchData['data'] as $ref => $date)
            {
                $processedRefs[] = $ref;

                $this->processUploadRef($ref, $date);

                if (self::MIGRATE_STATE_NO_SPACE === $this->status)
                {
                    // With a no space based message stop processing the current block.
                    break;
                }
            }
        }

        // Remove processed refs from the original list.
        $unprocessed = \array_diff($pUploadRefData, $processedRefs);

        // Update the estimated free space of the destination volume.
        $this->freeSpace = \disk_free_space($this->paths['destination']);

        foreach ($unprocessed as $ref)
        {
            $this->processResult['unknown']++;

            $this->processUploadRef($ref, 0);

            if (self::MIGRATE_STATE_NO_SPACE === $this->status)
            {
                break;
            }
        }
    }

    /**
     * Move the thumbnail directory using the date specified.
     *  
     * @param string $pUploadRef - Upload ref to migrate.
     * @param int $pDate - Date the order was created.
     */
    private function processUploadRef(string $pUploadRef, int $pDate): void
    {
        // Create the source path for the upload ref.
        $sourceDir = $this->paths['thumbnails'] . $pUploadRef;

        // Read the source order thumbnails.
        $thumbnailList = $this->readOrderThumbnails($sourceDir);

        // Make sure files exist to be migrated.
        if ((0 < $thumbnailList['size']) && (0 < $thumbnailList['count']))
        {
            $this->output($pUploadRef . " - Processing.", 0);

            // Check if the destination volume has space for the current project thumbnails.
            if ($thumbnailList['size'] > $this->freeSpace)
            {
                // The estimated free space has been reached. 
                // Update the estimated free space of the destination volume.
                // (The source and destination may be the same volume, so the free space used would be released when the files are moved)
                $this->freeSpace = \disk_free_space($this->paths['destination']);

                if ($thumbnailList['size'] > $this->freeSpace)
                {
                    // If there is still not enough free space, stop the migration process.
                    $this->status = self::MIGRATE_STATE_NO_SPACE;
                }
            }

            if (self::MIGRATE_STATE_OK === $this->status)
            {
                $destSubPath = (0 === $pDate) ? 'unknown' : \date(self::MIGRATE_PATH_TEMPLATE, $pDate);
                $destinationDir = $this->paths['destination'] . $destSubPath . \DIRECTORY_SEPARATOR . $pUploadRef;

                $sourceDir = \UtilsObj::correctPath($sourceDir, \DIRECTORY_SEPARATOR, false);
                $destinationDir = \UtilsObj::correctPath($destinationDir, \DIRECTORY_SEPARATOR, false);

                // Perform the copy.
                $copyResult = $this->moveDirectory($sourceDir, $destinationDir);

                if ('' !== $copyResult)
                {
                    $this->processResult['failed']++;
                    $this->output($pUploadRef . " - Failed to copy thumbnail: " . $copyResult, 1);
                }
                else
                {
                    $this->processResult['success']++;

                    $this->freeSpace -= $thumbnailList['size'];
                }

                $this->processResult['total']++;
            }
        }
        else
        {
            // No action can be carried out.
            $this->processResult['total']++;
            $this->processResult['empty']++;
            $this->output($pUploadRef . " - No order data files to migrate.", 1);

            // Delete the source directory.
            \UtilsObj::deleteFolder($sourceDir);

            // Check that the unwanted directory has been removed.
            if (\file_exists($sourceDir))
            {
                // If the directory could not be deleted for some reason, output the message.
                $this->output("Unable to delete directory: " . $sourceDir, 1);
            }
        }

        // Mark the interval.
        $this->migrateTimer->markInterval();
    }

    /**
     * return the status of the migration progress.
     */
    public function getStatus(): string
    {
        return $this->states[$this->status];
    }

    /**
     * display the final result
     */
    public function showResults(): void
    {
        // Display the time calculation summary.
        $this->output("===========================================", 1);
        $this->output(" Success: " . $this->processResult['success'], 1);
        $this->output(" Failed: " . $this->processResult['failed'], 1);
        $this->output(" Empty: " . $this->processResult['empty'], 1);
        $this->output(" No order item record: " . $this->processResult['unknown'], 1);
        $this->output("", 1);
        $this->output(" Total: " . $this->processResult['total'], 1);
        $this->output("===========================================", 1);
    }
}
