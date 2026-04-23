<?php
/**
 * This task is used to notify the Klarna to release the charge for the order
 * Order must meet the following criteria to be charged
 * A Status of 60 (Shipped)
 * have a PENDING payment status in the CCILOG
 * Be placed by the Klarna payment gateway
 * NOTE this task does not run through taskManager and must be setup as a manual task or cron
 */


ini_set('display_errors', 1);
error_reporting(E_ALL);

$filePath = dirname($argv[0]);
chdir($filePath);

require_once(__DIR__ . '/../libs/external/vendor/autoload.php');
require_once('../Utils/UtilsCoreIncludes.php');
require_once('../Order/PaymentIntegration/Klarna.php');

set_time_limit(60);

$ac_config = UtilsObj::readConfigFile('../config/mediaalbumweb.conf');
$gatewayConfig = UtilsObj::readConfigFile('../config/Klarna.conf');

class KlarnaCaptureNotification 
{
	private $klarnaInstance;

	public function __construct($pConfig, &$pGetVars, &$pPostVars)
	{
		// Need an empty session to be able to pass by reference.
		$session = [];
		$this->klarnaInstance = new Klarna($pConfig, $session, $pGetVars, $pPostVars);
	}

	public function run()
	{
		$dbObj = DatabaseObj::getGlobalDBConnection();

		if ($dbObj)
		{
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
				$notificationResultArray = $this->createKlarnaCapture($ordersToNotify['orders']);
				$this->updateStatus($dbObj, $notificationResultArray);
			}
		}
		else
		{
			UtilsObj::writeLogEntry('Unable to connect to database ' . $dbObj->error);
		}
	}

	/**
	 * Get a list of all the orders that are to be charged in Klarna
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

		//get all shipped orders for Klarna with the PENDING status

		$sql = 'SELECT oi.status, oi.orderid, oi.productcollectionname, cci.transactionid, oh.totaltopay, oh.languagecode
				 FROM ORDERITEMS as oi
				 LEFT JOIN ccilog as cci
					 ON oi.orderid = cci.orderid
				 LEFT JOIN orderheader as oh
					 ON oi.orderid = oh.id
				 WHERE oi.status IN (' . TPX_ITEM_STATUS_SHIPPED_TO_CUSTOMER . ', ' . TPX_ITEM_STATUS_SHIPPED_COLLECTED_BY_CUSTOMER . ')
					 AND cci.type = "KLARNA"
					 AND cci.pendingreason = "PENDING"
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
	 * Notifies the Klarna backend to charge for the orders.
	 * @param array $pOrders An array of eligible orders.
	 */
	private function createKlarnaCapture($pOrders)
	{
		$processed = [];
		$resultArray = ['CHARGED' => [], 'ERROR' => []];

		foreach($pOrders as $orders)
		{
			if (!in_array($orders['orderid'], $processed))
			{
				$data = ["captured_amount" =>  (int) bcmul($orders['totaltopay'], 100)];
				$captured = false;

				try 
				{
					$this->klarnaInstance->createCapture($orders['transactionid'], $data);
					$captured = true;
				} 
				catch (\Exception $e) 
				{
					UtilsObj::writeLogEntry($e->getMessage() . "\n" . 'For transaction: ' . $orders['transactionid']);
				}

				$keyName = $captured ? 'CHARGED' : 'ERROR';

				$resultArray[$keyName][] = $orders['orderid'];
				$processed[] = $orders['orderid'];
			}
		}

		return $resultArray;
	}

	/** 
	* We need to update the status of the order so it is not returned again in the query 
	* @param obj Database object
	* @param array an array of orders to be updated
	* 				'CHARGED' => Array of orderids that have been successfully charged.
	* 				'ERROR' => Array of orderids which returned an error.
	*/
	private function updateStatus($dbConnection, $pOrderIDs)
	{
		// Loop over the orderids passed.
		foreach ($pOrderIDs as $status => $orderIDs)
		{
			// Make sure there are orders to update for this status.
			if (count($orderIDs) > 0)
			{
				// Prepare the statement for updating the order, this is done in the loop so we do not need to loop over each item individually.
				if ($stmt = $dbConnection->prepare('UPDATE `CCILOG` SET `pendingreason` = ? WHERE `orderid` IN (' . implode(',', $orderIDs) . ')'))
				{
					if( $stmt->bind_param('s', $status))
					{
						if (! $stmt->execute())
						{
							UtilsObj::writeLogEntry('Error Executing ' . $dbConnection->error);
						}
					}
					else
					{
						UtilsObj::writeLogEntry('Error in bind params ' . $dbConnection->error);
					}
				}
				else
				{
					UtilsObj::writeLogEntry('Error in prepared statement ' . $dbConnection->error);
				}
			}
		}
	}
}

$notify = new KlarnaCaptureNotification($gatewayConfig, $_GET, $_POST);
$notify->run();

?>
