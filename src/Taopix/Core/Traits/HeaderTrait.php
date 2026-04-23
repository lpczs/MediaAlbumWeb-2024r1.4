<?php

namespace Taopix\Core\Traits;

trait HeaderTrait
{
    /**
     * This function will send headers based of the header values set in the $pHeaders param.
     * $pHeaders parram can contain the key 'status'. 'status' is not a valid header so we must send the correct status header
     *
     * @param array $pHeaders an array of http headers ($key) and the corresponding values ($value) to send.
     * @return void
     */
    public function displayHeader(array $pHeaders): void
    {
        foreach ($pHeaders as $key => $value)
        {
            if ($key == 'status')
            {
                switch ($value)
                {
                    case '200':
                    {
                        header("HTTP/1.1 200 OK");
                        break;
                    }
                    case '201':
                    {
                        header("HTTP/1.1 201 Created");
                        break;
                    }
                    case '400':
                    {
                        header("HTTP/1.1 400");
                        break;
                    }
                }
            }
            else
            {
                $headerValue = '';

                if (is_array($value))
                {
                    $headerValue = implode(', ', $value);
                }
                else
                {
                    $headerValue = $value;
                }

                if (is_bool($headerValue))
                {
                    $headerValue = ($headerValue ? 'true' : 'false');
                }
                else
                {
                    $headerValue = strval($headerValue);
                }

                if ($headerValue != '')
                {
                    header($key . ': ' . $headerValue);
                }
            }
        }
    }

    /**
     * Send a json encoded response with the correct headers.
     *
     * @param integer $pHttpStatus http status so the correct header can be sent
     * @param array $pDataArray the data that needs to be json encoded for the request response.
     * @return void
     */
    public function outputJSONData(int $pHttpStatus, array $pDataArray): void
    {
        $headers = ['Content-Type' => 'application/json', 'status' => $pHttpStatus];
        $this->displayHeader($headers);
        
        ob_start();
        echo json_encode($pDataArray);
        ob_end_flush();

    }
}
