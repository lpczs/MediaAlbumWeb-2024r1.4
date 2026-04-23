<?php
/**
 * This task is used to notify the Dibs Easy backend to release the charge for the order
 * Order must meet the following criteria to be charged
 * A Status of 60 (Shipped)
 * have an AWAITNG payment status in the CCILOG
 * Be placed by the Dibs easy payment gateway
 * NOTE this task does not run through taskManager and must be setup as a manual task or cron
 */


ini_set('display_errors', 1);
error_reporting(E_ALL);

$file = '/opt/taopix/controlcentre/tasks/output.txt';

$filePath = dirname($argv[0]);
chdir($filePath);

require_once(__DIR__ . '/../libs/external/vendor/autoload.php');
require_once('../Utils/UtilsCoreIncludes.php');
require_once('../Order/PaymentIntegration/Request/CurlHandler.php');

set_time_limit(60);

$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');

$gConstants = DatabaseObj::getConstants();

class DibsEasyNotification 
{
    public function run()
    {
        $dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
			$paymentConfig = UtilsObj::readConfigFile('../config/DibsEasy.conf');

			$ordersToNotify = $this->getValidOrders($dbObj);

			if($ordersToNotify['error'] != '')
			{
				UtilsObj::writeLogEntry($ordersToNotify['error']);
			}
			else if (empty($ordersToNotify['orders']))
			{
				UtilsObj::writeLogEntry('No orders to update');
			}
			else
			{
				$notifications = $this->notifyDibs($ordersToNotify['orders'], $paymentConfig);

				$this->updateStatus($dbObj, $notifications);
			}
		}
		else
		{
            UtilsObj::writeLogEntry('Unable to connect to database ' . $dbObj->error);
        }
    }

    /**
     * Get a list of all the orders that are to be charged in Dibs
     * @param obj Database object
     */
    private function getValidOrders($dbConnection)
    {
        $orderStatus = 0;
        $orderID = 0;
        $collectionName = '';
        $transactionid = '';
        $total = 0;
        $resultArray = array();
        $resultArray['error'] = '';
		$resultArray['orders']= [];
		$languageCode = '';

		// Get all shipped orders for DibsEasy with the AWAITING status.
		$sql = 'SELECT oi.status, oi.orderid, oi.productcollectionname, cci.transactionid, oh.totaltopay, oh.languagecode
				 FROM ORDERITEMS as oi
				 LEFT JOIN ccilog as cci
					 ON oi.orderid = cci.orderid
				 LEFT JOIN orderheader as oh
					 ON oi.orderid = oh.id
				 WHERE oi.status IN (' . TPX_ITEM_STATUS_SHIPPED_TO_CUSTOMER . ', ' . TPX_ITEM_STATUS_SHIPPED_COLLECTED_BY_CUSTOMER . ')
					 AND cci.type = "DibsEasy"
					 AND cci.pendingreason = "AWAITING"
				 ORDER BY oi.id';

		if($stmt = $dbConnection->prepare($sql))
		{
			if($stmt->bind_result($orderStatus, $orderID, $collectionName, $transactionid, $total, $languageCode))
			{
				if($stmt->execute())
				{
					if($result = $stmt->get_result())
					{
						while($data = $result->fetch_assoc())
						{
							$resultArray['orders'][] = $data;
						}
					}
					else
					{
						$resultArray['error'] = 'Error getting results ' . $dbConnection->error;
					}

					$stmt->free_result();
					$stmt->close();
					$stmt = null;
				}
				else
				{
					$resultArray['error'] = 'Error executing query ' . $dbConnection->error;
				}
			}
			else
			{
				$resultArray['error'] = 'Error binding result ' . $dbConnection->error;
			}
		}
		else
		{
			$resultArray['error'] = 'Error in prepared statement ' . $dbConnection->error;
		}

        return $resultArray;
       
    }

    /**
     * notifies the dibs easy backend to charge for the orders
     * @param Array pOrders an array of eligible orders
     * @param Array the dibs easy config file
     */

    private function notifyDibs($pOrders, $config)
    {
		$processed = [];
        $resultArray = [];

        $apiUrl = $config['TRANSACTIONMODE'] ? $config['CHARGEENDPOINT'] : $config['TESTCHARGEENDPOINT'];
        $configToken = $config['TRANSACTIONMODE'] ? $config['LIVESECRETKEY'] : $config['TESTSECRETKEY'];
        
        //the token needs to be stripped down to the token
        $token = substr($configToken, strrpos($configToken, '-') + 1);

        $curlOptions = [
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_HTTPHEADER => array('Content-Type: application/json','Accept: application/json','Authorization:' . $token),
			CURLOPT_ENCODING => '',
			CURLOPT_TIMEOUT => 30,
			CURLOPT_MAXREDIRS => 1,
			CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CAINFO => UtilsObj::getCurlPEMFilePath(),
        ];
        
        $this->connection = new CurlHandler('json', $curlOptions);

		foreach($pOrders as $order)
		{
			// Check we have not processed this order in this request.
			if (! in_array($order['orderid'], $processed))
			{
				// Build up the data to send to Dibs.
				$ref = "";

				if ((UtilsObj::getArrayParam($config, "ORDERREFERENCEMODE", 0) == 1) && (UtilsObj::getArrayParam($config, "ORDERREFERENCE", '') != ''))
				{
					$ref = $config['ORDERREFERENCE'];
				}
				else
				{
					// dibs does not like the following characters, remove them
					$charactersToRemove = array('"', "'", '&', "<", ">");
					// Get the localized product collection name, use the first available if there is no localized name available.
					$translatedOrderName = LocalizationObj::getLocaleString($order['productcollectionname'], $order['languagecode'], true);

					$ref = str_replace($charactersToRemove, "", $translatedOrderName);
				}

				$orderItems = [
					'amount' => (int) bcmul($order['totaltopay'], 100),
					'orderItems' => [],
				];

				$orderDetails = [
					'reference' => $ref,
					'name' => $ref,
					'quantity' => 1,
					'unit' => 'pcs',
					'unitPrice' => (int) bcmul($order['totaltopay'], 100),
					'taxRate' => 0,
					'taxAmount' => 0,
					'grossTotalAmount' => (int) bcmul($order['totaltopay'], 100),
					'netTotalAmount' => (int) bcmul($order['totaltopay'], 100)
				];
				$orderItems['orderItems'][] = $orderDetails;

				$response = $this->connection->connectionSend($apiUrl . $order['transactionid'] . '/charges', '', 'POST', $orderItems, 1);

				$responseArray = json_decode($response, true);

				$orderResponse = [
					'orderid' => $order['orderid'],
					'response' => $responseArray,
					'status' => 'CHARGED'
				];

				// Flag if we have an error from dibs.
				if (array_key_exists('errors', $responseArray))
				{
					$orderResponse['status'] = 'ERROR';
				}
				else if (array_key_exists('code', $responseArray))
				{
					$orderResponse['status'] = 'FAILED';

					/*
					 * This may be an item we have pulled more than once, and are trying to charge for again.
					 * Or this may be an order the captured manually at a reduced price
					 * If the message contains 3 values that are all the same we can safely mark this as done
					 */
					if ("1001" === $responseArray['code'])
					{
						if (! $this->overchargeIsOrderValue($responseArray['message']))
						{
							$orderResponse['status'] = 'ERROR';
						}
					}
				}

				$resultArray[] = $orderResponse;
				$processed[] = $order['orderid'];
			}
		}
        
        return $resultArray;
    }

    /** 
    * We need to update the status of the order so it is not returned again in the query 
    * @param obj Database object
    * @param Array an array of orders to be updated  
    */
    private function updateStatus($dbConnection, $pOrders)
    {
		// Only prepare the sql statement once.
		if ($stmt = $dbConnection->prepare('UPDATE `CCILOG` SET `pendingreason` = ? WHERE `orderid` = ?'))
		{
			// Loop over each order passed and process what we do with it.
			foreach($pOrders as $order)
			{
				$status = $order['status'];
				// Update the cci log entry if the order does not have an error.
				if($stmt->bind_param('si', $status, $order['orderid']))
				{
					if(! $stmt->execute())
					{
						UtilsObj::writeLogEntry('Error Executing ' . $dbConnection->error);
					}
				}
				else
				{
					UtilsObj::writeLogEntry('Error binding params ' . $dbConnection->error);
				}

				if ($order['status'] !== 'CHARGED')
				{
					$logEntry = 'Errors for order ' . $order['orderid'] . ': ' . json_encode($order['response']);
					UtilsObj::writeLogEntry($logEntry);
				}
			}
		}
		else
		{
			UtilsObj::writeLogEntry('Error in prepared statement ' . $dbConnection->error);
		}
    }

	/**
	 * Check if the message we received from DIBS has 3 payment values that match.
	 *
	 * @param string $pMessage Message from DIBS
	 * @return bool True if all values match otherwise false.
	 */
	private function overchargeIsOrderValue($pMessage)
	{
		$parts = explode('.', trim($pMessage, '. '));
		$values = [];

		// Loop over each sentance in the message
		foreach ($parts as $message)
		{
			// If the message contains : this splits the message and amount.
			if (strstr($message, ':'))
			{
				$messageParts = explode(':', $message);
				$value = trim($messageParts[1]);

				// Check if we have this value listed for the message.
				if (! in_array($value, $values))
				{
					$values[] = $value;
				}
			}
		}

		// If we have 1 value in the array all values matched.
		return (count($values) === 1);
	}
}

$notify = new DibsEasyNotification();
$notify->run();

?>