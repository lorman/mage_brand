<?php
$this->startSetup();

$this->getConnection()
    ->addColumn($this->getTable('hubco_brand/brand'),
    'channels',
    array(
        'type' => Varien_Db_Ddl_Table::TYPE_TEXT,
        'length' => 128,
        'nullable' => true,
        'comment' => 'Dis-Allowed Channels'
    )
);


$this->endSetup();
?>