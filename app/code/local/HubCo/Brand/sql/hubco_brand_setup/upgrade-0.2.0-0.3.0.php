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
?>