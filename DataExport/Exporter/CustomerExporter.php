<?php

namespace DataExport\Exporter;

use Exception;
use mysqli;
use mysqli_stmt;
use Smarty;
use UtilsObj;

class CustomerExporter implements DataSetExporterInterface
{
	/**
	 * @var mysqli
	 */
	private $connection;

	/**
	 * @var mixed[]
	 */
	private $filters;

	/**
	 * @var int
	 */
	private $batchCount;

	/**
	 * @var mixed
	 */
	private $lastId;

	/**
	 * @var mysqli_stmt
	 */
	private $stmt;

	/**
	 * @var string[]
	 */
	private $normalizedHeader;

	/**
	 * @var string[]
	 */
	private $header;

	/**
	 * @var int
	 */
	private $counter = 0;

	/**
	 * @var mixed[]
	 */
	private $record;

	/**
	 * @var int
	 */
	private $fieldId;

	/**
	 * @var string
	 */
	private $fieldDateCreated;

	/**
	 * @var string
	 */
	private $fieldCompanyCode;

	/**
	 * @var string
	 */
	private $fieldWebBrandCode;

	/**
	 * @var string
	 */
	private $fieldLicenseKeyCode;

	/**
	 * @var string
	 */
	private $fieldAccountCode;

	/**
	 * @var string
	 */
	private $fieldCompanyName;

	/**
	 * @var string
	 */
	private $fieldAddress1;

	/**
	 * @var string
	 */
	private $fieldAddress2;

	/**
	 * @var string
	 */
	private $fieldAddress3;

	/**
	 * @var string
	 */
	private $fieldAddress4;

	/**
	 * @var string
	 */
	private $fieldCity;

	/**
	 * @var string
	 */
	private $fieldCounty;

	/**
	 * @var string
	 */
	private $fieldState;

	/**
	 * @var string
	 */
	private $fieldRegion;

	/**
	 * @var string
	 */
	private $fieldCountry;

	/**
	 * @var string
	 */
	private $fieldTelephoneNumber;

	/**
	 * @var string
	 */
	private $fieldEmailAddress;

	/**
	 * @var string
	 */
	private $fieldContactFirstName;

	/**
	 * @var string
	 */
	private $fieldContactLastName;

	/**
	 * @var string
	 */
	private $fieldTaxCode;

	/**
	 * @var string
	 */
	private $fieldActive;

	/**
	 * @var string
	 */
	private $fieldLastLoginDate;

	/**
	 * @var string
	 */
	private $fieldOptInStatus;

	/**
	 * @var string
	 */
	private $fieldOptInDate;

	/**
	 * Constructor
	 *
	 * @param mysqli $connection
	 * @param Smarty $smarty
	 * @param mixed[] $filters
	 * @param int $batchCount
	 * @param mixed $lastId
	 */
	public function __construct(mysqli $connection, Smarty $smarty, $filters, $batchCount, $lastId)
	{
		$this->connection = $connection;
		$this->filters = $filters;
		$this->batchCount = $batchCount;
		$this->lastId = $lastId;

		$this->normalizedHeader = [
			'UserID',
			'DateCreated',
			'CompanyCode',
			'WebBrandCode',
			'License Key Code',
			'AccountCode',
			'CompanyName',
			'Address1',
			'Address2',
			'Address3',
			'Address4',
			'TownCity',
			'County',
			'State',
			'Region',
			'Postcode',
			'Country',
			'TelephoneNumber',
			'EmailAddress',
			'FirstName',
			'LastName',
			'Tax Code',
			'Active',
			'LastLoginDate',
			'OptInStatus',
			'OptInDate',
		];

		// Translations are not applied to headers as certain
		// characters break certain output formats upon export.
		$this->header = [
			'User ID',
			'Date Created',
			'Company Code',
			'Web Brand Code',
			'License Key Code',
			'Account Code',
			'Company Name',
			'Address Line 1',
			'Address Line 2',
			'Address Line 3',
			'Address Line 4',
			'Town/City',
			'County',
			'State',
			'Region',
			'Postcode',
			'Country',
			'Telephone Number',
			'Email Address',
			'First Name',
			'Last Name',
			'Tax Code',
			'Active',
			'Last Login Date',
			'Opt in Status',
			'Opt in Date'
		];
	}

	/**
	 * Get normalised header list
	 *
	 * @return string[]
	 */
	public function getNormalizedHeader()
	{
		return $this->normalizedHeader;
	}

	/**
	 * Get header list
	 *
	 * @return string[]
	 */
	public function getHeaders()
	{
		return $this->header;
	}

	/**
	 * Get root element name
	 *
	 * @return string
	 */
	public function getRootElementName()
	{
		return "customers";
	}

	/**
	 * Get data element name
	 *
	 * @return string
	 */
	public function getDataElementName()
	{
		return "customer";
	}

	/**
	 * Rewind the Iterator to the first element
	 * @link http://php.net/manual/en/iterator.rewind.php
	 * @return void Any returned value is ignored.
	 * @since 5.0.0
	 * @throws Exception
	 */
	public function rewind()
	{
		$this->counter = 0;

		if ($this->stmt) {
			$this->stmt->close();
		}

		$sql = "
			SELECT
				u.`id`,
				DATE_FORMAT(u.`datecreated`, '%Y-%m-%dT%T') as datecreated,
				u.`companycode`,
				u.`webbrandcode`,
				u.`groupcode`,
				u.`accountcode`,
				u.`companyname`,
				u.`address1`,
				u.`address2`,
				u.`address3`,
				u.`address4`,
				u.`city`,
				u.`county`,
				u.`state`,
				u.`regioncode`,
				u.`postcode`,
				u.`countryname`,
				u.`telephonenumber`,
				u.`emailaddress`,
				u.`contactfirstname`,
				u.`contactlastname`,
				u.`taxcode`,
				u.`active`,
				DATE_FORMAT(u.`lastlogindate`, '%Y-%m-%dT%T') as lastlogindate,
				u.`sendmarketinginfo`,
				DATE_FORMAT(u.`sendmarketinginfooptindate`, '%Y-%m-%dT%T') as sendmarketinginfooptindate
			from USERS u
			WHERE u.`customer` = 1";

		$queryParameters = [];

		// Add id of last record in last batch
		if (!empty($this->lastId)) {
			$sql .= ' AND u.`id` > ?';
			$queryParameters[] = $this->lastId;
		}

		// Add required filters
		if (!empty($this->filters['companyCode'])) {
			$sql .= ' AND u.`companycode` = ?';
			$queryParameters[] = $this->filters['companyCode'];
		}

		if (!empty($this->filters['groupCode'])) {
			$sql .= ' AND u.`groupcode` = ?';
			$queryParameters[] = $this->filters['groupCode'];
		}

		if (!empty($this->filters['brandCode'])) {
			$sql .= ' AND u.`webbrandcode` = ?';
			$queryParameters[] = $this->filters['brandCode'];
		}

		if (!empty($this->filters['countryCode'])) {
			$sql .= ' AND u.`countrycode` = ?';
			$queryParameters[] = $this->filters['countryCode'];
		}

		if (!empty($this->filters['contactEmail'])) {
			$sql .= ' AND u.`emailaddress` = ?';
			$queryParameters[] = $this->filters['contactEmail'];
		}

		if (!empty($this->filters['contactLastName'])) {
			$sql .= ' AND u.`contactlastname` = ?';
			$queryParameters[] = $this->filters['contactLastName'];
		}

		# Add limit
		$sql .= ' LIMIT ' . (int) $this->batchCount;

		// Prepare and execute statement
		$this->stmt = $this->connection->prepare($sql);
		if (!$this->stmt) {
			throw new Exception(mysqli_error($this->connection));
		}

		if (!empty($queryParameters)) {
			array_unshift($queryParameters, str_repeat('s', count($queryParameters)));
			call_user_func_array([$this->stmt, 'bind_param'], UtilsObj::makeValuesReferenced($queryParameters));
		}

	 	if (!$this->stmt->bind_result(
			$this->fieldId,
			$this->fieldDateCreated,
			$this->fieldCompanyCode,
			$this->fieldWebBrandCode,
			$this->fieldLicenseKeyCode,
			$this->fieldAccountCode,
			$this->fieldCompanyName,
			$this->fieldAddress1,
			$this->fieldAddress2,
			$this->fieldAddress3,
			$this->fieldAddress4,
			$this->fieldCity,
			$this->fieldCounty,
			$this->fieldState,
			$this->fieldRegion,
			$this->fieldPostcode,
			$this->fieldCountry,
			$this->fieldTelephoneNumber,
			$this->fieldEmailAddress,
			$this->fieldContactFirstName,
			$this->fieldContactLastName,
			$this->fieldTaxCode,
			$this->fieldActive,
			$this->fieldLastLoginDate,
			$this->fieldOptInStatus,
			$this->fieldOptInDate
		)) {
	 		throw new Exception(mysqli_error($this->connection));
		};

		if (!$this->stmt->execute()) {
			throw new Exception(mysqli_error($this->connection));
		}

		$this->next();
	}

	/**
	 * Return the current element
	 * @link http://php.net/manual/en/iterator.current.php
	 * @return mixed Can return any type.
	 * @since 5.0.0
	 */
	public function current()
	{
		return $this->record;
	}

	/**
	 * Move forward to next element
	 * @link http://php.net/manual/en/iterator.next.php
	 * @return void Any returned value is ignored.
	 * @since 5.0.0
	 * @throws Exception
	 */
	public function next()
	{
		$result = $this->stmt->fetch();
		if (true === $result)
		{
			++$this->counter;
			$this->record = [
				$this->fieldId,
				$this->fieldDateCreated,
				$this->fieldCompanyCode,
				$this->fieldWebBrandCode,
				$this->fieldLicenseKeyCode,
				$this->fieldAccountCode,
				$this->fieldCompanyName,
				$this->fieldAddress1,
				$this->fieldAddress2,
				$this->fieldAddress3,
				$this->fieldAddress4,
				$this->fieldCity,
				$this->fieldCounty,
				$this->fieldState,
				$this->fieldRegion,
				$this->fieldPostcode,
				$this->fieldCountry,
				$this->fieldTelephoneNumber,
				$this->fieldEmailAddress,
				$this->fieldContactFirstName,
				$this->fieldContactLastName,
				$this->fieldTaxCode,
				$this->fieldActive,
				$this->fieldLastLoginDate,
				$this->fieldOptInStatus,
				$this->fieldOptInDate
			];
		}
		elseif (null === $result)
		{
			$this->record = null;
		}
		else
		{
			throw new Exception(mysqli_error($this->connection));
		}
	}

	/**
	 * Get the number of records processed (so far)
	 *
	 * @return int
	 */
	public function getRecordCount()
	{
		return $this->counter;
	}

	/**
	 * Get the last record id processed
	 *
	 * @return int
	 */
	public function getLastId()
	{
		return $this->fieldId;
	}

	/**
	 * Return the key of the current element
	 * @link http://php.net/manual/en/iterator.key.php
	 * @return mixed scalar on success, or null on failure.
	 * @since 5.0.0
	 */
	public function key()
	{
		return $this->counter;
	}

	/**
	 * Checks if current position is valid
	 * @link http://php.net/manual/en/iterator.valid.php
	 * @return boolean The return value will be casted to boolean and then evaluated.
	 * Returns true on success or false on failure.
	 * @since 5.0.0
	 */
	public function valid()
	{
		return $this->record !== null;
	}
}
