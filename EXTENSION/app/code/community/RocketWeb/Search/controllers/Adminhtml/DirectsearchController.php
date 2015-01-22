<?php

class RocketWeb_Search_Adminhtml_DirectsearchController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('catalog/direct_search')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Direct Search Results Manager'), Mage::helper('adminhtml')->__('Direct Search Results Manager'));
		
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->renderLayout();
	}

	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('rocketweb_search/query')->load($id);
        $model->queryStatus = $model->status;
		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('query_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('catalog/direct_search');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Direct Search Results Manager'), Mage::helper('adminhtml')->__('Direct Search Results Manager'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('rocketweb_search/adminhtml_directsearch_edit'))
				->_addLeft($this->getLayout()->createBlock('rocketweb_search/adminhtml_directsearch_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('rocketweb_search')->__('Item does not exist'));
			$this->_redirect('*/*/');
		}
	}
 
	public function newAction() {
		$this->_forward('edit');
	}
 
	public function saveAction() {
		if ($data = $this->getRequest()->getPost()) {
            $productIds = array();
            $cmsIds = array();
            $blogIds = array();
            $categoryIds = array();
            if(isset($data['links'])){
                $products = isset($data['links']['products'])?Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['links']['products']):null;
                if($products && is_array($products) && !empty($products)) {
                    $i = 0;
                    foreach($products as $id => $product) {
                        $position = 0;
                        if(is_array($product) && array_key_exists('position', $product) && !empty($product['position']))
                        {
                            $position = $product['position'];
                        }
                        $p = Mage::getModel('catalog/product')->load($id);
                        $productIds[$i] = array(
                        	'entity_id' => $p->getId(),
                            'order'  => $position,
                            'delete' => ''
                        );
                        $i++;
                    }
                }
                $cms = isset($data['links']['cms'])?Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['links']['cms']):null;
                if($cms && is_array($cms) && !empty($cms)) {
                	$i = 0;
                	foreach($cms as $id => $item) {
                		$position = 0;
                		if(is_array($item) && array_key_exists('position', $item) && !empty($item['position']))
                		{
                			$position = $item['position'];
                		}
                		$p = Mage::getModel('cms/page')->load($id);
                		$cmsIds[$i] = array(
                				'entity_id' => $p->getPageId(),
                				'order'  => $position,
                				'delete' => ''
                		);
                		$i++;
                	}
                }
                $blogs = isset($data['links']['blogs'])?Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['links']['blogs']):null;
                if($blogs && is_array($blogs) && !empty($blogs)) {
                	$i = 0;
                	foreach($blogs as $id => $item) {
                		$position = 0;
                		if(is_array($item) && array_key_exists('position', $item) && !empty($item['position']))
                		{
                			$position = $item['position'];
                		}
                		$p = Mage::getModel('blog/post')->load($id,'post_id');
                		$blogIds[$i] = array(
                				'entity_id' => $p->getPostId(),
                				'order'  => $position,
                				'delete' => ''
                		);
                		$i++;
                	}
                }
                $categories = isset($data['links']['categories'])?Mage::helper('adminhtml/js')->decodeGridSerializedInput($data['links']['categories']):null;
                
                if($categories && is_array($categories) && !empty($categories)) {
                	$i = 0;
                	foreach($categories as $id => $item) {
                		$position = 0;
                		if(is_array($item) && array_key_exists('position', $item) && !empty($item['position']))
                		{
                			$position = $item['position'];
                		}
                		$p = Mage::getModel('catalog/category')->load($id);
                		$categoryIds[$i] = array(
                				'entity_id' => $p->getId(),
                				'order'  => $position,
                				'delete' => ''
                		);
                		$i++;
                	}
                }
            }

            $queryId = $this->getRequest()->getParam('id', null);
            if($queryId) {
                $queryModel  = Mage::getModel('rocketweb_search/query')->load($queryId);
                if ($queryModel->getId()) {
                    $queryData = $queryModel->getData();
                    if(!empty($queryData) && is_array($queryData)) {
                        if(array_key_exists('products',$queryData) && is_array($queryData['products']) && !empty($queryData['products']) && !isset($data['links']['products']))
                        {
                            $j = 0;
                            foreach($queryData['products'] as $productId) {
                                $productIds[$j] = array(
                                	'entity_id' => $productId['entity_id'],
                                	'entity_type' => 'product',
                                    'order'  => $productId['order'],
                                    'delete' => ''
                                );
                                $j++;
                            }
                        }
                        if(array_key_exists('cms',$queryData) && is_array($queryData['cms']) && !empty($queryData['cms']) && !isset($data['links']['cms']))
                        {
                        	$j = 0;
                        	foreach($queryData['cms'] as $cmsId) {
                        		$cmsIds[$j] = array(
                        				'entity_id' => $cmsId['entity_id'],
                        				'entity_type' => 'cms',
                        				'order'  => $cmsId['order'],
                        				'delete' => ''
                        		);
                        		$j++;
                        	}
                        }
                        if(array_key_exists('blogs',$queryData) && is_array($queryData['blogs']) && !empty($queryData['blogs']) && !isset($data['links']['blogs']))
                        {
                        	$j = 0;
                        	foreach($queryData['blogs'] as $blogId) {
                        		$blogIds[$j] = array(
                        				'entity_id' => $blogId['entity_id'],
                        				'entity_type' => 'blog',
                        				'order'  => $blogId['order'],
                        				'delete' => ''
                        		);
                        		$j++;
                        	}
                        }
                        if(array_key_exists('categories',$queryData) && is_array($queryData['categories']) && !empty($queryData['categories']) && !isset($data['links']['categories']))
                        {
                        	$j = 0;
                        	foreach($queryData['categories'] as $categoryId) {
                        		$categoryIds[$j] = array(
                        				'entity_id' => $categoryId['entity_id'],
                        				'entity_type' => 'category',
                        				'order'  => $categoryId['order'],
                        				'delete' => ''
                        		);
                        		$j++;
                        	}
                        }
                    }
                }
            }
            $data['status'] = $data['query_status'];
            $data['products'] = $productIds;
            $data['cms'] = $cmsIds;
            $data['categories'] = $categoryIds;
            $data['blogs'] = $blogIds;
			$model = Mage::getModel('rocketweb_search/query');		
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));
			
			try {
					
				//make sure the phrase is unique		
				$queryCollection = Mage::getModel('rocketweb_search/query')->getCollection();
				$queryCollection->addFieldToFilter('search_phrase',$model->getSearchPhrase());
				if($model->getId()) {
					$queryCollection->addFieldToFilter('query_id',array('neq'=>$model->getId()));
				}
				if(count($queryCollection)) {
					Mage::getSingleton('adminhtml/session')->addError('Search Phrase already used');
	                Mage::getSingleton('adminhtml/session')->setFormData($data);
	                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
	                return;
				}
				
				if(count($model->getProducts()) == 0 && count($model->getCms())==0 && count($model->getCategories())==0 && count($model->getBlogs())==0 ) {
					Mage::getSingleton('adminhtml/session')->addError('Please add at least one product, category, cms page or blog post');
					Mage::getSingleton('adminhtml/session')->setFormData($data);
					$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
					return;
				}
					
				
				if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
					$model->setCreatedTime(now())
						->setUpdateTime(now());
				} else {
					$model->setUpdateTime(now());
				}	
				
				$model->save();
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('rocketweb_search')->__('Item was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('rocketweb_search')->__('Unable to find banner to save'));
        $this->_redirect('*/*/');
	}
 
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				$model = Mage::getModel('rocketweb_search/query');
				 
				$model->setId($this->getRequest()->getParam('id'))
					->delete();
					 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Item was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}

    public function massDeleteAction() {
        $queryIds = $this->getRequest()->getParam('query_ids');
        if(!is_array($queryIds)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select direct search results'));
        } else {
            try {
                foreach ($queryIds as $queryId) {
                    $query = Mage::getModel('rocketweb_search/query')->load($queryId);
                    $query->delete();
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($queryIds)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	
    public function massStatusAction()
    {
        $queryIds = $this->getRequest()->getParam('query_ids');
        if(!is_array($queryIds)) {
            Mage::getSingleton('adminhtml/session')->addError($this->__('Please select direct search results'));
        } else {
            try {
                foreach ($queryIds as $queryId) {
                    $query = Mage::getSingleton('rocketweb_search/query')
                        ->load($queryId)
                        ->setStatus($this->getRequest()->getParam('status'))
                        ->setIsMassupdate(true)
                        ->save();
                }
                $this->_getSession()->addSuccess(
                    $this->__('Total of %d record(s) were successfully updated', count($queryIds))
                );
            } catch (Exception $e) {
                $this->_getSession()->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }

    public function productsAction(){
        $this->loadLayout();
        $this->getLayout()->getBlock('products.grid')->setProducts($this->getRequest()->getPost('products', null));
        $this->renderLayout();
    }

    public function productsgridAction(){
        $this->loadLayout();
        $this->getLayout()->getBlock('products.grid')
            ->setProducts($this->getRequest()->getPost('products', null));
        $this->renderLayout();
    }
    
    public function cmsAction(){
    	$this->loadLayout();
    	$this->getLayout()->getBlock('cms.grid')->setCms($this->getRequest()->getPost('cms', null));
    	$this->renderLayout();
    }
    
    public function cmsgridAction(){
    	$this->loadLayout();
    	$this->getLayout()->getBlock('cms.grid')->setCms($this->getRequest()->getPost('cms', null));
    	$this->renderLayout();
    }
    
    public function blogsAction(){
    	$this->loadLayout();
    	$this->getLayout()->getBlock('blogs.grid')->setBlogs($this->getRequest()->getPost('blogs', null));
    	$this->renderLayout();
    }
    
    public function blogsgridAction(){
    	$this->loadLayout();
    	$this->getLayout()->getBlock('blogs.grid')->setBlogs($this->getRequest()->getPost('blogs', null));
    	$this->renderLayout();
    }
    
    public function categoriesgridAction(){
    	$this->loadLayout();
    	$this->getLayout()->getBlock('categories.grid')->setCategories($this->getRequest()->getPost('categories', null));
    	$this->renderLayout();
    }
    
    public function categoriesAction(){
    	$this->loadLayout();
    	$this->getLayout()->getBlock('categories.grid')->setCategories($this->getRequest()->getPost('categories', null));
    	$this->renderLayout();
    }
    
    
}