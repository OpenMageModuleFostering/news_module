<?php

$installer = $this;

$installer->startSetup();

$installer->run("

    ALTER TABLE  `{$this->getTable('news_data')}` ADD  `related_news` VARCHAR(255) NOT NULL default '';
    
    ALTER TABLE  `{$this->getTable('news_data')}` ADD  `category_id` VARCHAR(255) NOT NULL default '';
        

 -- DROP TABLE IF EXISTS {$this->getTable('news_category')};
        CREATE TABLE {$this->getTable('news_category')} (
        `category_id` int(11) unsigned NOT NULL auto_increment,
        PRIMARY KEY (`category_id`)
        ) ENGINE=InnoDB;   
       

-- DROP TABLE IF EXISTS {$this->getTable('news_category_data')};
        CREATE TABLE {$this->getTable('news_category_data')} (
        `data_id` int(11) unsigned NOT NULL auto_increment,
        `category_id` int(11) unsigned  NOT NULL ,
        `store_id` int(11) NOT NULL default '0',
        `name` varchar(255) NOT NULL default '',
        `enable_ticker` smallint(6) NOT NULL default '0',
        `add_to_pages` varchar(255) NOT NULL default '',
        `status` smallint(6) NOT NULL default '0',
        PRIMARY KEY (`data_id`)
        ) ENGINE=InnoDB;  

    
    
    ALTER TABLE `{$this->getTable('news_category_data')}` ADD INDEX ( `category_id` ); 
        
    ALTER TABLE `{$this->getTable('news_category_data')}` ADD FOREIGN KEY ( `category_id` ) REFERENCES `{$this->getTable('news_category')}` (`category_id`) ON DELETE CASCADE ON UPDATE CASCADE ;

");

$installer->endSetup();
