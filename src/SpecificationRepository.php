<?php declare(strict_types=1);

namespace Mediagone\Doctrine\Specifications;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use LogicException;
use function array_shift;


final class SpecificationRepository
{
    //========================================================================================================
    // Properties
    //========================================================================================================
    
    private ManagerRegistry $registry;
    
    
    
    //========================================================================================================
    // Constructor
    //========================================================================================================
    
    public function __construct(ManagerRegistry $registry)
    {
        $this->registry = $registry;
    }
    
    
    
    //========================================================================================================
    // Methods
    //========================================================================================================
    
    public function find(SpecificationCollection $collection)
    {
        $builder = $this->getEntityManagerFor($collection)->createQueryBuilder();
        
        foreach ($collection->getSpecifications() as $spec) {
            $spec->modifyBuilder($builder);
        }
        
        $q = $builder->getQuery();
        
        foreach ($collection->getSpecifications() as $spec) {
            $spec->modifyQuery($q);
        }
        
        if ($collection->getRepositoryResult() === SpecificationRepositoryResult::MANY_OBJECTS) {
            return $q->getResult();
        }
        if ($collection->getRepositoryResult() === SpecificationRepositoryResult::SINGLE_OBJECT) {
            return $q->getOneOrNullResult();
        }
        if ($collection->getRepositoryResult() === SpecificationRepositoryResult::SINGLE_SCALAR) {
            // TODO: patch until https://github.com/doctrine/orm/pull/8340 is fixed
            return (int)array_shift($q->getScalarResult()[0]);
        }
        
        throw new LogicException('Unsupported SpecificationRepositoryResult ('.$collection->getRepositoryResult().')');
    }
    
    
    
    //========================================================================================================
    // Helpers
    //========================================================================================================
    
    public function getEntityManagerFor(SpecificationCollection $collection) : EntityManagerInterface
    {
        return $this->registry->getManagerForClass($collection::entityFqcn());
    }
    
    
    
}
