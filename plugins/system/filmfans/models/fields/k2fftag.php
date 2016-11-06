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

class JFormFieldK2FFTag extends JFormField
{
    public $type = 'K2FFTag';

    function getInput() {

        JHtml::_('script', 'media/filmfans/js/tagit.js');
        JHtml::_('stylesheet', 'media/filmfans/css/tagit.css');

        $attrs = $this->element->attributes();

        $_a = array();

        $_a['init'] = isset($attrs['init']) ? (string) $attrs['init'] : '';
        $_a['placeholder'] = isset($attrs['placeholder']) ? JText::_((string) $attrs['placeholder']) : '';
        $_a['ajax'] = JRoute::_('index.php?ffaction=tag&filter='.$this->fieldname);

        $_a['values'] = array();
        foreach ($this->element->children() as $option) {
            $_a['values'][] = array('label' => (string) $option['value'], 'value' => (string) $option);
        }

        if (!empty($_a['values'])) {
            $_a['values'] = json_encode($_a['values']);
            unset($_a['ajax']);
        } else
            unset($_a['values']);

        $_a = array_filter($_a);
        foreach ($_a as $k=>$v)
            $_a[$k] = "data-{$k}='{$v}'";



        $value = $this->value;
        if (!is_array($value))
            $value = array_map('trim', array_filter(explode(';', $value)));

        ob_start();

        ?>
        <ul class="tagit"data-name="<?php echo $this->name; ?>[]" <?php echo implode(' ', $_a); ?>>
            <?php foreach ($value as $k=>$v) { ?>
            <li><?php echo htmlspecialchars($v); ?></li>
            <?php } ?>
        </ul>
        <span class="tagit-desc"><?php echo JText::_('PLG_K2_FILMFANS_TAG_DESC'); ?></span>
        <?php

        return ob_get_clean();
    }

}