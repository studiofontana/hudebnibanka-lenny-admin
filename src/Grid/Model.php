<?php

namespace Lenny\Grid;

use Lenny,
    Nette;

class Model extends Nette\Object
{

	public $database;
	public $query;

	public function __construct(Nette\Database\Connection $database)
    {
        $this->database = $database;
		$this->database->setDatabaseReflection(new Nette\Database\Reflection\DiscoveredReflection);
    }
	
	public function table($table)
	{
		$this->query = $this->database->table($table);
		return $this->query;
		//return $this->database->table($table);
	}
	
	/**
	 *
	 * @return \Doctrine\ORM\QueryBuilder
	 */
	public function getQb()
	{
		return $this->qb;
	}

	public function getItems()
	{
		return $this->qb->getQuery()->getResult();
	}

	public function setPaginator(Nette\Utils\Paginator $paginator)
	{		
		$paginator->itemCount = $this->query->count();
		
		// nastaveni qb podle paginatoru
		$this->query->limit($paginator->length, $paginator->offset);
	}

}
