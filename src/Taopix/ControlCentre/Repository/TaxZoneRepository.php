<?php

namespace Taopix\ControlCentre\Repository;

use Exception;
use Taopix\ControlCentre\Entity\TaxZone;
use Doctrine\ORM\EntityRepository;

class TaxZoneRepository extends EntityRepository
{
	public function getTaxZone(array $companyCodes, string $countryCode, string $region): TaxZone
	{
		/** @var TaxZone[] $taxZones */
		$taxZones = $this->createQueryBuilder('tz')
				->select('tz')
				->where('tz.companyCode IN (:companyCodes)')
				->andWhere('tz.taxLevel1 != :empty')
				->setParameter('companyCodes', $companyCodes)
				->setParameter('empty', '')
				->orderBy('tz.companyCode')
				->getQuery()
				->getResult();

		// Define list of available taxzones we will check later which one we want to return.
		$available = [
			'company' => [
				'region' => null,
				'country' => null,
				'default' => null,
			],
			'global' => [
				'region' => null,
				'country' => null,
				'default' => null,
			]
		];

		$regionCode = implode('_', [$countryCode, $region]);
		foreach ($taxZones as $key => $taxZone) {
			$availableKey = '' === $taxZone->getCompanyCode() ? 'global' : 'company';
			if ('' === $taxZone->getCode()) {
				$available[$availableKey]['default'] = $taxZone;
			} else {
				$applyList = explode(',', $taxZone->getCountryCodes());
				if (in_array($countryCode, $applyList)) {
					$available[$availableKey]['country'] = $taxZone;
				}
				if (in_array($regionCode, $applyList)) {
					$available[$availableKey]['region'] = $taxZone;
				}
			}
		}
		/*
		 * Return the appropriate tax zone, this is in the following order.
		 * Region -> Country -> Default we prefer those related to the company over global values.
		 */
		return match(true) {
			null !== $available['company']['region'] => $available['company']['region'],
			null !== $available['company']['country'] => $available['company']['country'],
			null !== $available['company']['default'] => $available['company']['default'],
			null !== $available['global']['region'] => $available['global']['region'],
			null !== $available['global']['country'] => $available['global']['country'],
			null !== $available['global']['default'] => $available['global']['default'],
			default => throw new Exception('cant find tax zone'),
		};
	}
}