<?php

namespace Taopix\CLI;

class CLITimer
{
    // Start time of the action (seconds).
    protected $begin;

    // End time of the action (seconds).
    protected $end;

    // Total time the action has taken (seconds).
    protected $duration;

    // Record the time an event occured without stopping the timer (seconds).
    protected $intervalData;

    // Record the number of actions carried out.
    protected $actionCount;

    // Time taken as human readable (Days, hours, minutes, seconds)
    protected $summaryData;


    /**
     * Constructor
     */
    public function __construct()
    {
        $this->init();
    }


    /**
     * Reset the timers.
     */
    protected function init(): void
    {
        $this->begin = 0;
        $this->end = 0;
        $this->duration = 0;

        $this->intervalData = [];

        $this->actionCount = 0;

        $this->summaryData = [
            'days' => 0,
            'hours' => 0,
            'minutes' => 0,
            'seconds' => 0,
            'average' => 0,
            'rate' => 1,
            'long' => ''
        ];
    }


    /**
     * Start the timer.
     */
    public function start(): void
    {
        // Make sure the timer has been reset.
        $this->init();

        // Store the time the action was started.
        $this->begin = \microtime(true);
    }


    /**
     * Set the time an event occurred.
     */
    public function markInterval(): void
    {
        // Mark that an action has occured, even if no ref has been passed.
        $this->actionCount++;
    }


    /**
     * Stop the timer.
     */
    public function stop(bool $pDisplaySummary): void
    {
        // Store the time the action was started.
        $this->end = \microtime(true);

        // Calculate the time taken for the action
        $this->duration = $this->end - $this->begin;

        // Display the final summary if specified.
        if ($pDisplaySummary)
        {
            echo $this;
        }
    }


    /**
     * Convert into days, hours, minutes, seconds.
     * Generate the avereage seconds per action and the rate of the process.
     * Generate a long format string, displaying the duration in days, hours, minutes and seconds.
     */
    protected function formatTime(): void
    {
        // Store the duration to carry out calculations.
        $tempDuration = $this->duration;

        // Seconds.
        $this->summaryData['seconds'] = \round($tempDuration % 60);
        $tempDuration /= 60;

        // Minutes.
        $this->summaryData['minutes'] = \round($tempDuration % 60);
        $tempDuration /= 60;

        // Hours.
        $this->summaryData['hours'] = \round($tempDuration % 24);
        $tempDuration /= 24;

        // Days.
        $this->summaryData['days'] = \round($tempDuration);

        // Average & rate.
        if (0 != $this->actionCount)
        {
            // Actions per second.
            $this->summaryData['rate'] = $this->actionCount / $this->duration;

            // seconds per action.
            $this->summaryData['average'] = $this->duration / $this->actionCount;
        }

        // Long format.
        $durationStr = $this->summaryData['seconds'] . " seconds";

        if (0 != $this->summaryData['minutes'])
        {
            $durationStr = $this->summaryData['minutes'] . " minutes " . $durationStr;

            if (0 != $this->summaryData['hours'])
            {
                $durationStr = $this->summaryData['hours'] . " hours " . $durationStr;

                if (0 != $this->summaryData['days'])
                {
                    $durationStr = $this->summaryData['days'] . " days " . $durationStr;
                }
            }
        }

        $this->summaryData['long'] = $durationStr;
    }


    public function __toString(): string
    {
        $this->formatTime();

        $outPut = "===========================================" . \PHP_EOL;
        $outPut .= " Start: " . \date("Y-m-d H:i:s", $this->begin) . \PHP_EOL;
        $outPut .= " End: " . \date("Y-m-d H:i:s", $this->end) . \PHP_EOL;
        $outPut .= " Duration: " . $this->summaryData['long'] . \PHP_EOL;

        if (0 != $this->actionCount)
        {
            $outPut .= " Rate: " . \floor($this->summaryData['rate']) . " per second." . \PHP_EOL;
        }

        $outPut .= "===========================================" . \PHP_EOL;

        return $outPut;
    }
}