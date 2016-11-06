<?php
/**
 * @version		1.0
 * @package		FilmFans K2 Plugin
 * @author		jThemes
 * @copyright	Copyright (c) 2010 - 2014 jThemes. All rights reserved.
 * @license		Private
 */

// no direct access
defined('_JEXEC') or die ;

jimport('joomla.form.formfield');

class JFormFieldK2FFItem extends JFormField
{
    public $type = 'K2FFItem';

    function getInput() {

        $attrs = $this->element->attributes();
        $init = isset($attrs['init']) ? (string) $attrs['init'] : '';
        $placeholder = isset($attrs['placeholder']) ? JText::_((string) $attrs['placeholder']) : JText::_('PLG_K2_FILMFANS_LINK_HINT');
        $childof = isset($attrs['childof']) ? JText::_((string) $attrs['childof']) : 'movie';

        $ajax = JRoute::_('index.php?ffaction=item&filter='.$childof);

        $value = (int) $this->value;
        $item = new JObject();

        if ($value) {
            $db = JFactory::getDBO();
            $db->setQuery("SELECT * FROM `#__k2_items` WHERE `id` = $value");
            $item = $db->loadObject('JObject');
        }

        ob_start();

        ?>
        <div class="k2ffitem" data-init='<?php echo $init; ?>' data-ajax="<?php echo $ajax; ?>">
            <div class="k2ffitem-item"<?php if (!$item->get('id')) echo ' style="display: none;"'; ?>>
                <img src="<?php if ($item->get('id')) echo JURI::root(true).'/media/k2/items/cache/'.md5("Image".$item->get('id')).'_XS.jpg'; ?>" alt="" />
                <h4><?php echo htmlspecialchars($item->get('title')); ?></h4>
                <a class="btn btn-small" href="javascript:void(0)" onclick="with(jQuery(this).closest('.k2ffitem')) { find('.k2ffitem-id').val(''); find('.k2ffitem-item').hide(); }">
                	<span class="icon-cancel"></span><?php echo JText::_('PLG_K2_FILMFANS_LINK_CANCEL'); ?>
                </a>
            </div>
            <label>
                <span><?php echo JText::_('PLG_K2_FILMFANS_LINK_FILTER'); ?></span>
                <input type="text" class="text_area k2ffitem-search"  placeholder="<?php echo $placeholder; ?>" value="<?php echo $item->get('title'); ?>" />
            </label>
            <input type="hidden" class="k2ffitem-id" name="<?php echo $this->name; ?>" value="<?php echo $item->get('id'); ?>" />
            <div style="clear: both;"></div>
        </div>
        <?php

        return ob_get_clean();
    }

}