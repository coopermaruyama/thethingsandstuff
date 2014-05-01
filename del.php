<?php
error_reporting(E_ALL | E_STRICT);
define('MAGENTO_ROOT', getcwd());
$mageFilename = MAGENTO_ROOT . '/app/Mage.php';
require_once $mageFilename;
Mage::setIsDeveloperMode(true);
ini_set('display_errors', 1);
umask(0);
$setup = new Mage_Eav_Model_Entity_Setup('core_setup');
// below code will add text attribute
$setup->addAttribute('catalog_category','custom_design_apply',
	 array(
            'type'              => 'int',
            'label'             => 'Apply To',
            'frontend'          => '',
            'table'             => '',
            'input'             => 'select',
            'class'             => '',
            'source'            => 'core/design_source_apply',
            'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
            'visible'           => true,
            'required'          => false,
            'user_defined'      => false,
            'default'           => '',
            'searchable'        => false,
            'filterable'        => false,
            'comparable'        => false,
            'visible_on_front'  => false,
            'unique'            => false,
            'group' => 'design',
	    'sort'  => 20
        )
);