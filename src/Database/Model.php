<?php

namespace Lenny\Database;

use Nette\Database\Connection,
    Nette\Database\Table\Selection;

class Model extends Selection
{
    public function __construct($table, Connection $connection) {
        parent::__construct($table, $connection);
    }
}