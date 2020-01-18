<?php
class ControllerModuleBestSeller extends Controller {
	protected function index($setting) {
		$this->language->load('module/bestseller');

		$this->data['heading_title'] = $this->language->get('heading_title');
				
		$this->data['button_cart'] = $this->language->get('button_cart');
		$this->data['text_tax'] = $this->language->get('text_tax');
        $this->data['position'] = $setting['position'];
        $this->data['button_compare'] = $this->language->get('button_compare');
        $this->data['button_wishlist'] = $this->language->get('button_wishlist');
		
		$change = $this->config->get('change_module_css');

	if($change && $this->config->get('change_module_css_status')){
        $this->data['change'] = $change['bestseller'];
        $this->data['description_status'] = $change['bestseller']['desc_status'];
        $this->data['max'] = $change['bestseller']['css_strlen'];
        $this->data['title_length'] = $change['bestseller']['css_productname'];
        $this->data['tax_status'] = $change['bestseller']['tax_status'];
        $this->data['status'] = true;
                   
        $this->data['sidebar_info'] =  $this->model_module_module->getPositions($setting);
                 
    } else {
                           $this->data['change'] = array();
                           $this->data['status'] = false;
                           $this->data['description_status'] = '';
                           $this->data['max'] = '';
                           $this->data['title_length'] = '';
                           $this->data['tax_status'] = '';
    }
		$this->load->model('catalog/product');
		
		$this->load->model('tool/image');

		$this->data['products'] = array();

		$results = $this->model_catalog_product->getBestSellerProducts($setting['limit']);
		
		foreach ($results as $result) {
			if ($result['image']) {
				$image = $this->model_tool_image->resize($result['image'], $setting['image_width'], $setting['image_height']);
			} else {
				$image = $this->model_tool_image->resize('placeholder.png', $setting['image_width'], $setting['image_height']);
			}
			
			if (($this->config->get('config_customer_price') && $this->customer->isLogged()) || !$this->config->get('config_customer_price')) {
				$price = $this->currency->format($this->tax->calculate($result['price'], $result['tax_class_id'], $this->config->get('config_tax')));
			} else {
				$price = false;
			}
					
			if ((float)$result['special']) {
				$special = $this->currency->format($this->tax->calculate($result['special'], $result['tax_class_id'], $this->config->get('config_tax')));
			} else {
				$special = false;
			}	
			
					if ($this->config->get('config_tax')) {
						$tax = $this->currency->format((float)$result['special'] ? $result['special'] : $result['price']);
					} else {
						$tax = false;
					}
					
			if ($this->config->get('config_review_status')) {
				$rating = $result['rating'];
			} else {
				$rating = false;
			}
							
			$this->data['products'][] = array(
				'product_id' => $result['product_id'],
				'thumb'   	 => $image,
				'name'    	 => $result['name'],
				'description' => strip_tags(html_entity_decode($result['description'], ENT_QUOTES, 'UTF-8')),
				'tax'         => $tax,				
				'price'   	 => $price,
				'special' 	 => $special,
				'rating'     => $rating,
				'reviews'    => sprintf($this->language->get('text_reviews'), (int)$result['reviews']),
				'href'    	 => $this->url->link('product/product', 'product_id=' . $result['product_id']),
			);
		}

		if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/module/bestseller.tpl')) {
			$this->template = $this->config->get('config_template') . '/template/module/bestseller.tpl';
		} else {
			$this->template = 'default/template/module/bestseller.tpl';
		}

		$this->render();
	}
}
?>