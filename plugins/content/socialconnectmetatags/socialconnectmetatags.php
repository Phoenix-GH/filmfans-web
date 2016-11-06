<?php
/**
 * @version		$Id: socialconnectmetatags.php 3401 2013-07-19 12:35:14Z lefteris.kavadas $
 * @package		SocialConnect
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die ;

jimport('joomla.plugin.plugin');

class plgContentSocialConnectMetaTags extends JPlugin
{

	public function plgContentSocialConnectMetaTags(&$subject, $config)
	{
		parent::__construct($subject, $config);
	}

	// Joomla! 2.5/3.x event
	public function onContentPrepare($context, $row, $params, $offset)
	{
		if (!is_null($context))
		{
			$this->addHeadData($context, $row, $params, $offset);
		}
	}

	// Joomla! 1.5 event
	public function onPrepareContent($row, $params, $offset)
	{
		$context = $this->detectContext();
		$this->addHeadData($context, $row, $params, $offset);
	}

	// RedShop custom event
	public function onPrepareProduct($template_desc, $params, $data)
	{
		$context = 'com_redshop.product';
		$row = $data;
		$offset = 0;
		$this->addHeadData($context, $row, $params, $offset);
	}

	// Main function
	private function addHeadData($context, $row, $params, $offset)
	{
		// Format check
		$document = JFactory::getDocument();
		if ($document->getType() != 'html')
		{
			return false;
		}

		// Get default meta description value from configuration
		$config = JFactory::getConfig();
		$metaDesc = version_compare(JVERSION, '3.0', 'ge') ? $config->get('MetaDesc') : $config->getValue('config.MetaDesc');

		// Initialize values
		$title = $document->getTitle();
		$description = $document->getDescription() == $metaDesc ? null : $document->getDescription();
		$url = null;
		$type = 'article';
		$card = 'summary';
		$image = null;
		switch($context)
		{

			default :
				return false;
				break;

			case 'com_content.article' :
				if (empty($title))
				{
					$title = $row->title;
				}
				if (empty($description))
				{
					$description = $row->text;
				}
				$url = JURI::root(false).'index.php?option=com_content&amp;view=article&amp;id='.$row->id;
				$images = json_decode($row->images);
				if (isset($images->image_fulltext) && !empty($images->image_fulltext))
				{
					$image = JURI::root().$images->image_fulltext;
				}
				if (is_null($image))
				{
					$image = $this->getFirstImage($row->text);
				}
				break;

			case 'com_virtuemart.productdetails' :
			case 'com_virtuemart.shop.product_details' :
				if (empty($title))
				{
					$title = $row->product_name;
				}
				if (empty($description))
				{
					$description = $row->product_s_desc.$row->product_desc;
				}
				$url = JURI::root().'/index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$row->virtuemart_product_id;
				$productModel = VmModel::getModel('product');
				$productModel->addImages($row);
				if (!empty($row->images))
				{
					$image = JURI::root().$row->images[0]->file_url;
				}
				break;

			case 'com_redshop.product' :
				if (empty($title))
				{
					$title = $row->product_name;
				}
				if (empty($description))
				{
					$description = $row->product_s_desc.$row->product_desc;
				}
				$url = JURI::root().'/index.php?option=com_redshop&view=products&pid='.$row->product_id;
				// NOTE: Redshop overrides the Open graph image tag after this plugin renders. The bad thing is that the URL it provides is incorrect...
				if ($row->product_full_image)
				{
					$image = REDSHOP_FRONT_IMAGES_ABSPATH.'product/'.$row->product_full_image;
				}
				break;
		}

		// Set the description
		$document->setDescription(JString::trim(strip_tags($description)));

		// Add type meta tags
		$document->setMetaData('og:type', $type);
		$document->setMetaData('twitter:card', $card);

		// Add image
		if ($image)
		{
			$document->setMetaData('og:image', $image);
			$document->setMetaData('twitter:image', $image);
		}

		// Add canonical URL (Facebook only)
		$document->setMetaData('og:url', $url);

		// Set a constant to notify the system plugin to process
		if (!defined('SOCIALCONNECT_HEAD_DATA'))
		{
			define('SOCIALCONNECT_HEAD_DATA', true);
		}

	}

	private function detectContext()
	{
		$option = JRequest::getCmd('option');
		$view = JRequest::getCmd('view');
		if ($option == 'com_virtuemart' && empty($view))
		{
			$view = JRequest::getCmd('page');
		}
		$task = JRequest::getCmd('task');
		$context = $option.'.'.$view;
		if ($task)
		{
			$context .= '.'.$task;
		}
		return $context;
	}

	private function getFirstImage($html)
	{
		$result = null;
		if ($html)
		{
			if (class_exists('DOMDocument'))
			{
				libxml_use_internal_errors(true);
				$dom = new DOMDocument;
				$dom->loadHTML($html);
				$images = $dom->getElementsByTagName('img');
				foreach ($images as $image)
				{
					if ($image->hasAttribute('src'))
					{
						$src = $image->getAttribute('src');
						break;
					}
				}
			}
			else
			{
				$regex = "#<img.+?>#s";
				if (preg_match_all($regex, $html, $matches, PREG_PATTERN_ORDER) > 0)
				{
					$image = array();
					$srcPattern = "#src=\".+?\"#s";
					if (preg_match($srcPattern, $matches[0][0], $match))
					{
						$src = str_replace('src="', '', $match[0]);
						$src = str_replace('"', '', $src);
					}
				}
			}
		}

		if (isset($src) && !empty($src))
		{
			$src = JString::str_ireplace(JURI::root(false), '', $src);
			$src = JString::str_ireplace(JURI::root(true).'/', '', $src);
			$result = JURI::root(false).$src;
		}
		return $result;
	}

}
