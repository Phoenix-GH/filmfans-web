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

class JFormFieldK2FFAffiliateLink extends JFormField
{
    public $type = 'K2FFAffiliateLink';
    public static $classes = array('amazon', 'fandango', 'itunes', 'moviephone', 'netflix', '_custom_');
    public static $sections = array('tickets', 'watch-home', 'watch-online', 'watch-button');

    private static function _fill($opts, $val = null) {
        $res = array('<option></option>');
        foreach ($opts as $v)
            $res[] = '<option value="'.$v.'"'.( $v == $val ? ' selected="selected"' : '').'>'.$v.'</option>';

        return implode(PHP_EOL, $res);
    }

    function getInput() {

        JHtml::_('script', 'media/filmfans/js/afflink.js');
        JHtml::_('script', 'system/html5fallback.js', false, true);

        $value = $this->value;
        if (!is_array($value))
            $value = json_decode($value);

        if (!is_array($value))
            $value = array();

        foreach ($value as $k=>$v)
            if (!count(array_filter($v)))
                unset($value[$k]);

        ob_start();

        ?>

        <div class="ff-afflinks-container">

            <input class="btn ff-afflinks-add" type="button" value="<?php echo JText::_('PLG_K2_FILMFANS_MOVIE_AFFILIATE_LINKS_ADDROW'); ?>" />

            <div class="aff-table ff-afflinks">

                <div class="aff-head">
                    <label class="aff-link"><?php echo JText::_('PLG_K2_FILMFANS_MOVIE_AFFILIATE_LINKS_LINK'); ?>*</label>
                    <label class="aff-class"><?php echo JText::_('PLG_K2_FILMFANS_MOVIE_AFFILIATE_LINKS_CLASS'); ?></label>
                    <label class="aff-class"><?php echo JText::_('PLG_K2_FILMFANS_MOVIE_AFFILIATE_LINKS_SECTION'); ?></label>
                    <label class="aff-title"><?php echo JText::_('PLG_K2_FILMFANS_MOVIE_AFFILIATE_LINKS_TITLE'); ?></label>
                </div>

                <?php $cc = 0; foreach ($value as $k=>$v) { $cc++; ?>
                <div class="ff-afflink">
                    <label class="aff-link"><input name="<?php echo $this->name; ?>[<?php echo $k; ?>][l]" type="url" value="<?php echo isset($v['l']) ? $v['l'] : ''; ?>" /></label>
                    <label class="aff-class"><select name="<?php echo $this->name; ?>[<?php echo $k; ?>][c]" class="no-chosen"><?php echo self::_fill(self::$classes, isset($v['c']) ? $v['c'] : ''); ?>></select></label>
                    <label class="aff-section"><select name="<?php echo $this->name; ?>[<?php echo $k; ?>][s]" class="no-chosen"><?php echo self::_fill(self::$sections, isset($v['s']) ? $v['s'] : ''); ?>></select></label>
                    <label class="aff-title"><input name="<?php echo $this->name; ?>[<?php echo $k; ?>][t]" type="text" value="<?php echo isset($v['t']) ? $v['t'] : ''; ?>" /></label>
                    <label class="aff-title"><input class="btn btn-small ff-afflinks-remove" type="button" value="x" /></label>
                </div>
                <?php } ?>
            </div>

            <div class="ff-afflinks-tmpl" style="display: none;" data-next="<?php echo $cc; ?>">
                <div class="ff-afflink">
                    <label class="aff-link"><input data-name="<?php echo $this->name; ?>[%%%][l]" type="url" value="" /></label>
                    <label class="aff-class"><select data-name="<?php echo $this->name; ?>[%%%][c]" class="no-chosen"><?php echo self::_fill(self::$classes); ?>></select></label>
                    <label class="aff-section"><select data-name="<?php echo $this->name; ?>[%%%][s]" class="no-chosen"><?php echo self::_fill(self::$sections); ?>></select></label>
                    <label class="aff-title"><input data-name="<?php echo $this->name; ?>[%%%][t]" type="text" value="" /></label>
                    <label class="aff-title"><input class="btn btn-small ff-afflinks-remove" type="button" value="x" /></label>
                </div>
            </div>
        </div>

        <?php

        return ob_get_clean();
    }

}