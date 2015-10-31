<?php
class EM_Galaelectronuessettings_Helper_Sample extends Mage_Core_Helper_Abstract
{
	protected $_themeSlug  = 'default/galaelectronues';
	protected $_blockIds = array();
	protected $_pageIds = array();
	protected $_widgetInstanceIds = array();
	protected $_menuIds = array();
	protected $_slideshowIds = array();
	protected $_megaMenuModel = 'galaelectronuessettings/megamenupro';
	
	public function importSampleData(){
		$path = Mage::getBaseDir('design').DS.'frontend'.DS.str_replace('/',DS,$this->_themeSlug).DS.'etc'.DS.'sampledata.xml';
		if (file_exists($path)) {
			$xmlDoc = new DOMDocument();
			$xmlDoc->load($path);
			$this->importStaticContent($xmlDoc);
			$this->importMegaMenu($xmlDoc);
			$this->importSlideshow($xmlDoc);
			$this->importWidgetInstance($xmlDoc);
			//$this->importStaticContent($xmlDoc);
		}
		return $this;
	}
	
	/**
		Import static block and static page from sampledata
	*/
	public function importStaticContent($xmlDoc){
		/* Import static block */
		$blockNodes = $xmlDoc->getElementsByTagname('block');
		//echo '<pre>';print_r($blockNodes);exit;
		foreach($blockNodes	as $v){	
			$data = array();
			$data['title']		=	$v->getElementsByTagName("title")->item(0)->nodeValue;
			$data['identifier']	=	$v->getElementsByTagName("identifier")->item(0)->nodeValue;
			$data['stores']		=	unserialize($v->getElementsByTagName("store_id")->item(0)->nodeValue);
			$data['is_active']	=	$v->getElementsByTagName("is_active")->item(0)->nodeValue;
			$data['content']	=	$v->getElementsByTagName("content")->item(0)->nodeValue;
			$data['id'] 		= 	$v->getAttribute('id');
			$this->insertStaticBlock($data);
		}
		
		/* Import cms page */
		$pageNodes = $xmlDoc->getElementsByTagname('page'); 
		foreach($pageNodes	as $v){	
			$data = array();
			$data['title']		=	$v->getElementsByTagName("title")->item(0)->nodeValue;
			$data['identifier']	=	$v->getElementsByTagName("identifier")->item(0)->nodeValue;
			$data['stores']		=	unserialize($v->getElementsByTagName("store_id")->item(0)->nodeValue);
			$data['is_active']	=	$v->getElementsByTagName("is_active")->item(0)->nodeValue;
			$data['content']	=	$v->getElementsByTagName("content")->item(0)->nodeValue;
			$data['root_template']	=	$v->getElementsByTagName("root_template")->item(0)->nodeValue;
			$data['meta_keywords']	=	$v->getElementsByTagName("meta_keywords")->item(0)->nodeValue;
			$data['meta_description']	=	$v->getElementsByTagName("meta_description")->item(0)->nodeValue;
			$data['content_heading']	=	$v->getElementsByTagName("content_heading")->item(0)->nodeValue;
			$data['sort_order']	=	$v->getElementsByTagName("sort_order")->item(0)->nodeValue;
			$data['layout_update_xml']	=	$v->getElementsByTagName("layout_update_xml")->item(0)->nodeValue;
			$data['custom_theme']	=	$v->getElementsByTagName("custom_theme")->item(0)->nodeValue;
			$data['custom_root_template']	=	$v->getElementsByTagName("custom_root_template")->item(0)->nodeValue;
			$data['custom_layout_update_xml']	=	$v->getElementsByTagName("custom_layout_update_xml")->item(0)->nodeValue;
			$data['custom_theme_from']	=	$v->getElementsByTagName("custom_theme_from")->item(0)->nodeValue;
			$data['custom_theme_to']	=	$v->getElementsByTagName("custom_theme_to")->item(0)->nodeValue;
			$this->insertPage($data);	
		}
		return $this;
	}
	
	public function insertStaticBlock($dataBlock) {
		// insert a block to db if not exists
		$block = Mage::getModel('cms/block')->getCollection()->addFieldToFilter('identifier', $dataBlock['identifier'])->getFirstItem();
		$oldId = $dataBlock['id'];
		unset($dataBlock['id']);
		if (!$block->getId())
			$block->setData($dataBlock)->save();
		$this->_blockIds[$oldId] = $block->getId();
		return $block;
	}
	
	public function insertPage($dataPage) {
		$page = Mage::getModel('cms/page')->getCollection()->addFieldToFilter('identifier', $dataPage['identifier'])->getFirstItem();
		if (!$page->getId())
			$page->setData($dataPage)->save();
		return $page;
	}
	
	public function importMegaMenu($xmlDoc){
		$menus = $xmlDoc->getElementsByTagname('menu');
		foreach($menus as $menu){
			$data = array(
				'name' 		=> $menu->getElementsByTagName("name")->item(0)->nodeValue,
				'type' 		=> $menu->getElementsByTagName("type")->item(0)->nodeValue,
				'content' 	=> $menu->getElementsByTagName("content")->item(0)->nodeValue,
				'status' 	=> $menu->getElementsByTagName("status")->item(0)->nodeValue
			);
			$model = Mage::getModel($this->_megaMenuModel)->setData($data)->save();
			$this->_menuIds[$menu->getAttribute('id')] = $model->getId();
		}
		return $this;
	}
	
	public function importSlideshow($xmlDoc){
		$slideshows = $xmlDoc->getElementsByTagname('slideshow');
		if(count($slideshows)){
			foreach($slideshows as $slideshow){
				$data = array(
					'name' 				=> $slideshow->getElementsByTagName("name")->item(0)->nodeValue,
					'images' 			=> $slideshow->getElementsByTagName("images")->item(0)->nodeValue,
					'slider_type' 		=> $slideshow->getElementsByTagName("slider_type")->item(0)->nodeValue,
					'slider_params' 	=> $slideshow->getElementsByTagName("slider_params")->item(0)->nodeValue,
					'delay' 			=> $slideshow->getElementsByTagName("delay")->item(0)->nodeValue,
					'touch' 			=> $slideshow->getElementsByTagName("touch")->item(0)->nodeValue,
					'stop_hover' 		=> $slideshow->getElementsByTagName("stop_hover")->item(0)->nodeValue,
					'shuffle_mode' 		=> $slideshow->getElementsByTagName("shuffle_mode")->item(0)->nodeValue,
					'stop_slider' 		=> $slideshow->getElementsByTagName("stop_slider")->item(0)->nodeValue,
					'stop_after_loop' 	=> $slideshow->getElementsByTagName("stop_after_loop")->item(0)->nodeValue,
					'stop_at_slide' 	=> $slideshow->getElementsByTagName("stop_at_slide")->item(0)->nodeValue,
					'position' 			=> $slideshow->getElementsByTagName("position")->item(0)->nodeValue,
					'appearance' 		=> $slideshow->getElementsByTagName("appearance")->item(0)->nodeValue,
					'navigation' 		=> $slideshow->getElementsByTagName("navigation")->item(0)->nodeValue,
					'thumbnail' 		=> $slideshow->getElementsByTagName("thumbnail")->item(0)->nodeValue,
					'visibility' 		=> $slideshow->getElementsByTagName("visibility")->item(0)->nodeValue,
					'trouble' 			=> $slideshow->getElementsByTagName("trouble")->item(0)->nodeValue,
					'status' 			=> $slideshow->getElementsByTagName("status")->item(0)->nodeValue
				);
				$this->_slideshowIds[$slideshow->getAttribute('id')] = $model->getId();
			}
		}
		return $this;
	}
	
	public function importWidgetInstance($xmlDoc){
		$widgets = $xmlDoc->getElementsByTagname('widget');
		$test = array();
		foreach($widgets	as $widget){	
			$data = array(
				'title'				=>	$widget->getElementsByTagName("title")->item(0)->nodeValue,
				'store_ids'			=>	unserialize($widget->getElementsByTagName("store_ids")->item(0)->nodeValue),
				'widget_parameters'	=>	$widget->getElementsByTagName("widget_parameters")->item(0)->nodeValue,
				'sort_order'		=>	$widget->getElementsByTagName("sort_order")->item(0)->nodeValue,
				'page_groups'		=>	unserialize($widget->getElementsByTagName("page_groups")->item(0)->nodeValue)
			);
			
			$instanceType = $widget->getElementsByTagName("instance_type")->item(0)->nodeValue;
			$packageTheme = $widget->getElementsByTagName("package_theme")->item(0)->nodeValue;
			
			if($instanceType == 'cmswidget/widget_block'){
				$params = unserialize($data['widget_parameters']);
				$params['block_id'] = $this->_blockIds[$params['block_id']];
				$data['widget_parameters'] = serialize($params);
			} else if($instanceType == 'sliderwidget/slide'){
				$params = unserialize($data['widget_parameters']);
				$params['instance'] = $this->_widgetInstanceIds[$params['instance']];
				$data['widget_parameters'] = serialize($params);
			} else if($instanceType == 'tabs/group'){
				$params = unserialize($data['widget_parameters']);
				for($i = 1;$i <= 10;$i++){
					if(!$params['instance_'.$i])
						break;
					$params['instance_'.$i] = $this->_widgetInstanceIds[$params['instance_'.$i]];	
				}
				$data['widget_parameters'] = serialize($params);
			} else if($instanceType == 'megamenupro/megamenupro'){
				$params = unserialize($data['widget_parameters']);
				$params['menu'] = $this->_menuIds[$params['menu']];
				$data['widget_parameters'] = serialize($params);
			}

			$model = Mage::getModel('widget/widget_instance');
			$model->setData($data)->setType($instanceType)->setPackageTheme($packageTheme)->save();
			if($instanceType == 'tabs/group'){
				$newId = $model->getId();
				$params = unserialize($data['widget_parameters']);
				$params['instance'] = $newId;
				$data['widget_parameters'] = serialize($params);
				$model->setData($data)->setType($instanceType)->setPackageTheme($packageTheme)->setId($newId)->save();
			}
			$this->_widgetInstanceIds[$widget->getAttribute('id')] = $model->getId();
			$data['id'] = $model->getId();
			$test[$model->getId()] = unserialize($data['widget_parameters']);
		}
		return $this;
	}
}
