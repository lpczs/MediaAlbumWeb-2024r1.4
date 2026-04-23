<?php

namespace Taopix\ControlCentre\Listener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class SchemaUpdate
{
    private array $tableMappings = [];

    public function __construct(ParameterBagInterface $config)
    {
        $this->tableMappings = $config->get('maw.dbmap');
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs)
    {
        $classMetadata = $eventArgs->getClassMetadata();

        // We don't need to update any details for mapped super classes.
        if ($classMetadata->isMappedSuperclass) {
            return;
        }

        $table = $classMetadata->table;

        $table['schema'] = $this->tableMappings[$table['schema']];

        $classMetadata->setPrimaryTable($table);
    }
}