<?php declare(strict_types=1);

namespace Mediagone\Doctrine\Specifications\Universal;

use Doctrine\ORM\Query;
use InvalidArgumentException;
use Mediagone\Doctrine\Specifications\Specification;


final class LimitResultsPaginate extends Specification
{
    //========================================================================================================
    // Properties
    //========================================================================================================
    
    private int $offset;
    
    private int $maxResults;
    
    
    
    //========================================================================================================
    // Constructors
    //========================================================================================================
    
    private function __construct(int $pageNumber, int $itemsPerPage)
    {
        if ($pageNumber < 0) {
            throw new InvalidArgumentException('PageNumber must be zero or positive integer.');
        }
    
        if ($itemsPerPage <= 0) {
            throw new InvalidArgumentException('ItemsPerPage must be a positive integer.');
        }
        
        $this->offset = ($pageNumber - 1) * $itemsPerPage;
        $this->maxResults = $itemsPerPage;
    }
    
    
    public static function specification(int $pageNumber, int $itemsPerPage) : self
    {
        return new self($pageNumber, $itemsPerPage);
    }
    
    
    
    //========================================================================================================
    // Methods
    //========================================================================================================
    
    public function modifyQuery(Query $query) : void
    {
        $query->setMaxResults($this->maxResults);
        
        if ($this->offset > 0) {
            $query->setFirstResult($this->offset);
        }
    }
    
    
    
}
