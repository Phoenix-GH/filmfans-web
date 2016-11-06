<?php
/**
 * @version		$Id: socialconnectcomments.php 3397 2013-07-19 11:28:17Z lefteris.kavadas $
 * @package		SocialConnect
 * @author		JoomlaWorks http://www.joomlaworks.net
 * @copyright	Copyright (c) 2006 - 2013 JoomlaWorks Ltd. All rights reserved.
 * @license		http://www.joomlaworks.net/license
 */

// no direct access
defined('_JEXEC') or die ;

jimport('joomla.plugin.plugin');

class plgContentSocialConnectComments extends JPlugin
{

	protected $comments = null;
	protected $counter = null;
	protected $service = null;
	protected $identifierURL = null;
	protected $itemURL = null;

	public function plgContentSocialConnectComments(&$subject, $config)
	{
		parent::__construct($subject, $config);
		$language = JFactory::getLanguage();
		$language->load('plg_content_socialconnectcomments', JPATH_ADMINISTRATOR);
		$languageTag = str_replace('-', '_', $language->getTag());
		$params = JComponentHelper::getParams('com_socialconnect');
		if ($params->get('commentsService') == 'facebook')
		{
			$this->service = 'Facebook';
			$this->comments = '<div class="fb-comments" data-href="[SC_ID_URL]" data-colorscheme="'.$params->get('facebookCommentsColorScheme').'" data-width="'.$params->get('facebookCommentsWidth').'" data-num-posts="'.$params->get('facebookCommentsNumOfPosts').'" data-order-by="'.$params->get('facebookCommentsOrdering').'"></div>';
			$this->counter = '<span class="fb-comments-count" data-href="[SC_ID_URL]">0</span>';
			$document = JFactory::getDocument();
			if ($document->getType() == 'html')
			{
				$document->addScript('//connect.facebook.net/'.$languageTag.'/all.js#xfbml=1&appId='.$params->get('facebookCommentsApplicationId'));
				$document->setMetaData('fb:app_id', $params->get('facebookCommentsApplicationId'));
			}

		}
		else if ($params->get('commentsService') == 'disqus')
		{
			$this->service = 'Disqus';
			$this->comments = "<div id=\"disqus_thread\"></div>
			<script type=\"text/javascript\">
        	/* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
        	var disqus_shortname = '".$params->get('disqusShortName')."'; // required: replace example with your forum shortname
        	var disqus_developer = '".$params->get('disqusDevMode')."'; // Development mode
        	var disqus_identifier = '[SC_ID_URL]'; // Identifier
        	var disqus_config = function () { 
			  this.language = '".$languageTag."';
			};
        	/* * * DON'T EDIT BELOW THIS LINE * * */
	        (function() {
	            var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
	            dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
	            (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
	        })();
	    	</script>";
			$this->counter = '<a href="[SC_ITEM_URL]#disqus_thread" data-disqus-identifier="[SC_ID_URL]">0 '.JText::_('JW_SC_COMMENTS').'</a>';
			if (!defined('SOCIALCONNECT_DISQUS_COUNTERS'))
			{
				define('SOCIALCONNECT_DISQUS_COUNTERS', true);
				define('SOCIALCONNECT_DISQUS_SHORTNAME', $params->get('disqusShortName'));
			}
		}

	}

	// K2 comments block
	public function onK2CommentsBlock($row, $params, $offset)
	{
		$options = JComponentHelper::getParams('com_socialconnect');
		if ($options->get('commentsService'))
		{
			$this->identifierURL = JURI::root(false).'index.php?option=com_k2&view=item&id='.$row->id;
			$this->itemURL = $row->link;
			$comments = str_replace('[SC_ID_URL]', $this->identifierURL, $this->comments);
			$comments = str_replace('[SC_ITEM_URL]', $this->itemURL, $comments);
			return $comments;
		}
	}

	// K2 comments counter
	public function onK2CommentsCounter($row, $params, $offset)
	{
		$options = JComponentHelper::getParams('com_socialconnect');
		if ($options->get('commentsService'))
		{
			$this->identifierURL = JURI::root(false).'index.php?option=com_k2&view=item&id='.$row->id;
			$this->itemURL = $row->link;
			$counter = str_replace('[SC_ID_URL]', $this->identifierURL, $this->counter);
			$counter = str_replace('[SC_ITEM_URL]', $this->itemURL, $counter);
			if ($this->service == 'Facebook')
			{
				return '<span>'.$counter.' '.JText::_('JW_SC_COMMENTS').'</span>';
			}
			else
			{
				return $counter;
			}
		}
	}

	// Joomla! 2.5/3.x events
	public function onContentPrepare($context, $row, $params, $offset)
	{
		if (!is_null($context))
		{
			$this->render($context, $row, $params, $offset);
		}
	}

	// Joomla! 1.5 events
	public function onPrepareContent($row, $params, $offset)
	{
		$context = $this->detectContext();
		$this->render($context, $row, $params, $offset);
	}

	// RedShop custom event
	public function onPrepareProduct($template_desc, $params, $data)
	{
		$context = 'com_redshop.product';
		$row = $data;
		$offset = 0;
		$this->render($context, $row, $params, $offset);
	}

	public function render($context, $row, $params, $offset)
	{
		$options = JComponentHelper::getParams('com_socialconnect');
		if ($options->get('commentsService'))
		{
			switch($context)
			{
				case 'com_content.article' :
				case 'com_content.category' :
				case 'com_content.featured' :
				case 'com_content.frontpage' :
				case 'com_content.section' :
					$this->identifierURL = JURI::root(false).'index.php?option=com_content&view=article&id='.$row->id;
					if (!class_exists('ContentHelperRoute'))
					{
						require_once JPATH_SITE.'/components/com_content/helpers/route.php';
					}
					if (isset($row->catslug))
					{
						$this->itemURL = JRoute::_(ContentHelperRoute::getArticleRoute($row->slug, $row->catslug));
					}
					else if (isset($row->catid))
					{
						$this->itemURL = JRoute::_(ContentHelperRoute::getArticleRoute($row->id, $row->catid));
					}
					$layout = ($context == 'com_content.article') ? 'item' : 'listing';
					break;

				case 'com_virtuemart.shop.product_details' :
					if ($context == 'com_virtuemart.shop.product_details')
					{
						$id = JRequest::getInt('product_id');
					}
					else
					{
						$id = $row->virtuemart_product_id;
					}
					$this->identifierURL = JURI::root(false).'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id='.$id;
					$uri = JURI::getInstance();
					$this->itemURL = $uri->toString();
					$layout = 'item';
					break;
				case 'com_redshop.product' :
					$this->identifierURL = JURI::root(false).'index.php?option=com_redshop&view=products&pid='.$row->product_id;
					$uri = JURI::getInstance();
					$this->itemURL = $uri->toString();
					$layout = 'item';
					break;
			}

			if (isset($layout))
			{
				$this->row = $row;
				$row->text = $this->display($layout);
			}
		}
	}

	private function display($layout)
	{
		jimport('joomla.filesystem.file');
		$application = JFactory::getApplication();
		$template = $application->getTemplate();
		if (JFile::exists(JPATH_SITE.'/templates/'.$template.'/html/socialconnectcomments/'.$layout.'.php'))
		{
			$file = JPATH_SITE.'/templates/'.$template.'/html/socialconnectcomments/'.$layout.'.php';
		}
		else if (JFile::exists(JPATH_SITE.'/plugins/content/socialconnectcomments/includes/tmpl/'.$layout.'.php'))
		{
			$file = JPATH_SITE.'/plugins/content/socialconnectcomments/includes/tmpl/'.$layout.'.php';
		}
		else
		{
			$file = JPATH_SITE.'/plugins/content/socialconnectcomments/socialconnectcomments/includes/tmpl/'.$layout.'.php';
		}
		ob_start();
		include $file;
		$buffer = ob_get_contents();
		ob_end_clean();
		$buffer = str_replace('[SC_ID_URL]', $this->identifierURL, $buffer);
		$buffer = str_replace('[SC_ITEM_URL]', $this->itemURL, $buffer);
		return $buffer;
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

}
