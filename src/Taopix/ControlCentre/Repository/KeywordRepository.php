<?php

namespace Taopix\ControlCentre\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr;
use Taopix\ControlCentre\Entity\KeywordGroup;

class KeywordRepository extends EntityRepository
{
    public function getKeywordList(int $groupId): array
    {
        return $this->createQueryBuilder('k')
            ->select('k.ref, k.code, k.name, k.description, k.type, k.maxLength as maxlength, k.height, k.width, k.flags, g.sortOrder as sortorder, g.defaultValue as defaultvalue')
            ->leftJoin(KeywordGroup::class, 'g', Expr\Join::WITH, 'g.keywordCode = k.code')
            ->where('g.keywordGroupHeaderId = :groupId')
            ->orderBy('g.sortOrder')
            ->setParameter('groupId', $groupId)
            ->getQuery()
            ->setHint(Query::HINT_FORCE_PARTIAL_LOAD, 1)
            ->getArrayResult();
    }
}
