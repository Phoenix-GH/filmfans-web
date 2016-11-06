<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_JEXEC') or die;

require(JPATH_SITE . '/components/com_finder/models/search.php');

class FFFinderModelSearch extends FinderModelSearch {

    protected $context = 'plg_k2_filmfans.search';

    public static function _getTaxonomy($type)
   	{

        $db = JFactory::getDBO();

   		$query = $db->getQuery(true)
   			->select($db->quoteName('id'))
   			->from($db->quoteName('#__finder_taxonomy'))
   			->where($db->quoteName('title') . ' = ' . $db->quote($type));
   		$db->setQuery($query);
   		$result = (int) $db->loadResult();

   		return $result;
   	}

    function getTerms() {
       return array('required' => array_keys($this->requiredTerms), 'included' => array_keys($this->includedTerms), 'excluded' => array_keys($this->excludedTerms));
    }

}