<?php 
    class Trego_Trego_Adminhtml_ImportController extends Mage_Adminhtml_Controller_Action{ 
        public function indexAction() {
            $this->getResponse()->setRedirect($this->getUrl("adminhtml/system_config/edit/section/trego_settings/"));
        }
        public function blocksAction() {
            $isoverwrite = Mage::helper('trego')->getCfg('install/overwrite_blocks');
            Mage::getSingleton('trego/import_cms')->importCms('cms/block', 'blocks', $isoverwrite);
            $this->getResponse()->setRedirect($this->getUrl("adminhtml/system_config/edit/section/trego_settings/"));
        }
        public function pagesAction() {
            $isoverwrite = Mage::helper('trego')->getCfg('install/overwrite_pages');
            Mage::getSingleton('trego/import_cms')->importCms('cms/page', 'pages', $isoverwrite);
            $this->getResponse()->setRedirect($this->getUrl("adminhtml/system_config/edit/section/trego_settings/")); 
        }
    }
?>