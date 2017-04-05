<?php
$this->startSetup();

$this->getConnection()
    ->addColumn($this->getTable('hubco_brand/brand'),
    'google',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_SMALLINT,
        'length' => 1,
        'nullable' => true,
        'default' => null,
        'comment' => 'Google Active Product'
    )
);
$this->getConnection()
->addColumn($this->getTable('hubco_brand/brand'),
    'add_handling',
    array(
            'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
            'length' => 4,
            'nullable'  => false,
            'unsigned'  => true,
        'comment' => 'Additional handling'
    )

);
$this->getConnection()
->addColumn($this->getTable('hubco_brand/brand'),
    'surchargeS',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'length' => 4,
        'nullable'  => false,
        'unsigned'  => true,
        'comment' => '$ surcharge'
    )

);
$this->getConnection()
->addColumn($this->getTable('hubco_brand/brand'),
    'surchargeP',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_DECIMAL,
        'length' => 4,
        'nullable'  => false,
        'unsigned'  => true,
        'comment' => '% surcharge'
    )

);
?>