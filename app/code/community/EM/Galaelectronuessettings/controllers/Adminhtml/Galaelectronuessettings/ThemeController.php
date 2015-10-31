<?php
class EM_Galaelectronuessettings_Adminhtml_Galaelectronuessettings_ThemeController extends Mage_Adminhtml_Controller_Action
{
	protected $_prefixBlock = 'galaelectronues_';
	protected $_prefixPage = 'galaelectronues_';
	protected $_packageTheme = 'default/galaelectronues';
	protected $_megaMenuModel = 'megamenupro/megamenupro';
	
	public function exportAction(){
		$doc = new DOMDocument('1.0','utf-8');
		$doc->formatOutput = true;
		$root = $doc->createElement( "sample_data" );
		$doc->appendChild( $root );
		
		$this->exportStaticContent($root,$doc);
		$this->exportMegaMenu($root,$doc);
		//$this->exportSlideshow($root,$doc);
		$this->backupWidgetInstanceInTheme($root,$doc);
		
		header("Content-Type: application/octet-stream");
		header('Content-Disposition: attachment; filename="sampledata.xml"');
		//echo $docWidgetInstance->saveXml();
		echo $doc->saveXML();
	}
	
	/*
		Create a widget_<package>_<theme>.xml to backup widget instance of a theme
		@param : string $prefix (EX: em0040/default)
		$return : EM_Designer_Helper_Data
	*/
	public function backupWidgetInstanceInTheme($root,$doc){
		$collection = Mage::getModel('widget/widget_instance')->getCollection()
			->addFieldToFilter('package_theme',$this->_packageTheme)
			->addStoreFilter(0);

		$excluded = array();
		$r = $doc->createElement( 'widget_instance' );
		$root->appendChild($r);
		
		foreach( $collection as $data)
		{	
			$value	=	$data->getData();
			$store	=	array(0);
			if($value['store_ids'] != '0')
					continue;
			$pageGroups	=	Mage::getModel('widget/widget_instance')->load($data->getId())->getPageGroups();
			
			$page_group_array = array();
			foreach ($pageGroups as $pageGroup) {
			
				$page_group_array[] = array(
					'page_group' => $pageGroup['page_group'],
					$pageGroup['page_group'] => array(
						'page_id' => $pageGroup['page_id'],
						'group' => $pageGroup['page_group'],
						'block' => $pageGroup['block_reference'],
						'for_value'   => $pageGroup['page_for'],
						'layout_handle' => $pageGroup['layout_handle'],
						'template' => $pageGroup['page_template']
					)
				);
			}
			
			$b = $doc->createElement("widget");
			
			$tmp = $doc->createElement('instance_id');
			$cdata = $doc->createCDATASection($value['instance_id']);
			$tmp->appendChild($cdata);
			$b->appendChild($tmp);
			
			$tmp = $doc->createElement('title');
			$cdata = $doc->createCDATASection($value['title']);
			$tmp->appendChild($cdata);
			$b->appendChild($tmp);
			
			$tmp = $doc->createElement('store_ids');
			$cdata = $doc->createCDATASection(serialize($store));
			$tmp->appendChild($cdata);
			$b->appendChild($tmp);
			
			$tmp = $doc->createElement('widget_parameters');
			$cdata = $doc->createCDATASection($value['widget_parameters']);
			$tmp->appendChild($cdata);
			$b->appendChild($tmp);
			
			$tmp = $doc->createElement('sort_order');
			$cdata = $doc->createCDATASection($value['sort_order']);
			$tmp->appendChild($cdata);
			$b->appendChild($tmp);
			
			$tmp = $doc->createElement('page_groups');
			$cdata = $doc->createCDATASection(serialize($page_group_array));
			$tmp->appendChild($cdata);
			$b->appendChild($tmp);
			
			$tmp = $doc->createElement('instance_type');
			$cdata = $doc->createCDATASection($value['instance_type']);
			$tmp->appendChild($cdata);
			$b->appendChild($tmp);
			
			$tmp = $doc->createElement('package_theme');
			$cdata = $doc->createCDATASection($value['package_theme']);
			$tmp->appendChild($cdata);
			$b->appendChild($tmp);
			
			$att = $doc->createAttribute('id');
			$att->value = $data->getId();
			$b->appendChild($att);
			
			$r->appendChild($b);
		}
		//$doc->save($backupPath);
		
		return $excluded;
	}
	
	/**
     * Export Static block and cms page of theme       
     */
	public function exportStaticContent($root,$doc)
	{	
		$collectionBlock = Mage::getModel('cms/block')->getCollection()->distinct(true)
			->addStoreFilter(0)
			->addFieldToFilter('identifier',array('like'=>$this->_prefixBlock.'%'));
		
		$collectionPage = Mage::getModel('cms/page')->getCollection()
			->addFieldToFilter('identifier',array('like'=>$this->_prefixPage.'%'));	
			
		$this->exportType($collectionBlock,$root,'staticblock',$doc);
		$this->exportType($collectionPage,$root,'cmspage',$doc);
		return $doc;
	}
	
	public function exportType($collection,$root,$type,$doc){
		if($collection->count()){
			$r = $doc->createElement( $type );
			$root->appendChild($r);
			if($type == 'staticblock')
				$blockType = 'block';
			else
				$blockType = 'page';
			foreach( $collection as $data)
			{	
				$value	=	$data->getData();
				$store= $data->getResource()->lookupStoreIds($data->getId());
				if(count(array_diff($store,array(0))) > 0)
					continue;
					
				$b = $doc->createElement($blockType);
				$att = $doc->createAttribute('id');
				$att->value = $data->getId();
				$b->appendChild($att);

				$this->appendElementXml('title',$value['title'],$doc,$b);
				$this->appendElementXml('identifier',$value['identifier'],$doc,$b);
				$this->appendElementXml('store_id',$store,$doc,$b,true);
				$this->appendElementXml('content',$value['content'],$doc,$b);
				$this->appendElementXml('is_active',$value['is_active'],$doc,$b);
				
				if($type == 'cmspage'){
					$this->appendElementXml('root_template',$value['root_template'],$doc,$b);
					$this->appendElementXml('meta_keywords',$value['meta_keywords'],$doc,$b);
					$this->appendElementXml('meta_description',$value['meta_description'],$doc,$b);
					$this->appendElementXml('content_heading',$value['content_heading'],$doc,$b);
					$this->appendElementXml('sort_order',$value['sort_order'],$doc,$b);
					$this->appendElementXml('layout_update_xml',$value['layout_update_xml'],$doc,$b);
					$this->appendElementXml('custom_theme',$value['custom_theme'],$doc,$b);
					$this->appendElementXml('custom_root_template',$value['custom_root_template'],$doc,$b);
					$this->appendElementXml('custom_layout_update_xml',$value['custom_layout_update_xml'],$doc,$b);
					$this->appendElementXml('custom_theme_from',$value['custom_theme_from'],$doc,$b);
					$this->appendElementXml('custom_theme_to',$value['custom_theme_to'],$doc,$b);
				}

				$r->appendChild($b);
			}
		}
		return $this;	
	}
	
	public function appendElementXml($name,$value,$doc,$parent,$isSerialize = false){
		$content = $doc->createElement($name);
		if($isSerialize)
			$cdata = $doc->createCDATASection(serialize($value));
		else	
			$cdata = $doc->createCDATASection($value);
		$content->appendChild($cdata);	
		$parent->appendChild($content);	
		return $this;
	}
	
	public function exportMegaMenu($root,$doc){
		$r = $doc->createElement( 'megamenu' );
		$root->appendChild($r);
		$collection = Mage::getModel($this->_megaMenuModel)->getCollection()->addFieldToFilter('status',1);
		foreach($collection as $menu){
			$b = $doc->createElement('menu');
			$att = $doc->createAttribute('id');
			$att->value = $menu->getId();
			$b->appendChild($att);
			$this->appendElementXml('name',$menu->getName(),$doc,$b);
			$this->appendElementXml('type',$menu->getType(),$doc,$b);
			$this->appendElementXml('status',$menu->getStatus(),$doc,$b);
			$this->appendElementXml('content',$menu->getContent(),$doc,$b);
			$r->appendChild($b);
		}
		return $this;
	}
	
	public function exportSlideshow($root,$doc){
		$r = $doc->createElement( 'slideshows' );
		$root->appendChild($r);
		$collection = Mage::getModel('slideshow2/slider')->getCollection()->addFieldToFilter('status',1);
		foreach($collection as $slideshow){
			$b = $doc->createElement('slideshow');
			$att = $doc->createAttribute('id');
			$att->value = $slideshow->getId();
			$b->appendChild($att);
			$this->appendElementXml('name',$slideshow->getName(),$doc,$b);
			$this->appendElementXml('images',$slideshow->getImages(),$doc,$b);
			$this->appendElementXml('slider_type',$slideshow->getSliderType(),$doc,$b);
			$this->appendElementXml('slider_params',$slideshow->getSliderParams(),$doc,$b);
			$this->appendElementXml('delay',$slideshow->getDelay(),$doc,$b);
			$this->appendElementXml('touch',$slideshow->getTouch(),$doc,$b);
			$this->appendElementXml('stop_hover',$slideshow->getStopHover(),$doc,$b);
			$this->appendElementXml('shuffle_mode',$slideshow->getShuffleMode(),$doc,$b);
			$this->appendElementXml('stop_slider',$slideshow->getStopSlider(),$doc,$b);
			$this->appendElementXml('stop_after_loop',$slideshow->getStopAfterLoop(),$doc,$b);
			$this->appendElementXml('stop_at_slide',$slideshow->getStopAtSlide(),$doc,$b);
			$this->appendElementXml('position',$slideshow->getPosition(),$doc,$b);
			$this->appendElementXml('appearance',$slideshow->getAppearance(),$doc,$b);
			$this->appendElementXml('navigation',$slideshow->getNavigation(),$doc,$b);
			$this->appendElementXml('thumbnail',$slideshow->getThumbnail(),$doc,$b);
			$this->appendElementXml('visibility',$slideshow->getVisibility(),$doc,$b);
			$this->appendElementXml('trouble',$slideshow->getTrouble(),$doc,$b);
			$this->appendElementXml('status',$slideshow->getStatus(),$doc,$b);
			$r->appendChild($b);
		}
		return $this;
	}
}
?>