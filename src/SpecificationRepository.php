<?php declare(strict_types=1);

namespace Mediagone\Doctrine\Specifications;

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
    
    public function find(SpecificationCompound $compound)
    {
        $entityManager = $compound->getEntityManager($this->registry);
        $queryBuilder = $entityManager->createQueryBuilder();
        
        foreach ($compound->getSpecifications() as $spec) {
            $spec->modifyBuilder($queryBuilder);
        }
        
        $query = $queryBuilder->getQuery();
        
        foreach ($compound->getSpecifications() as $spec) {
            $spec->modifyQuery($query);
        }
        
        if ($compound->getRepositoryResult() === SpecificationRepositoryResult::MANY_OBJECTS) {
            return $query->getResult();
        }
        if ($compound->getRepositoryResult() === SpecificationRepositoryResult::MANY_OBJECTS_AS_ITERABLE) {
            return $query->toIterable();
        }
        if ($compound->getRepositoryResult() === SpecificationRepositoryResult::SINGLE_OBJECT) {
            return $query->getOneOrNullResult();
        }
        if ($compound->getRepositoryResult() === SpecificationRepositoryResult::SINGLE_SCALAR) {
            // TODO: patch until https://github.com/doctrine/orm/pull/8340 is fixed
            return (int)array_shift($query->getScalarResult()[0]);
        }
        
        throw new LogicException('Unsupported SpecificationRepositoryResult ('.$compound->getRepositoryResult().')');
    }
    
    
    
}
