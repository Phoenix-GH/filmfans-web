<?php
// no direct access
defined('_JEXEC') or die;

JLoader::register('K2Plugin', JPATH_ADMINISTRATOR . '/components/com_k2/lib/k2plugin.php');

class plgK2FFUser extends K2Plugin {

    var $pluginName = 'ffuser';
    var $pluginNameHumanReadable = 'FilmFans User Extended';

    function plgK2Userextendedfields(&$subject, $params) {
        parent::__construct($subject, $params);
    }

    function onRenderAdminForm(&$item, $type, $tab = '') {

        $manifest = JPATH_SITE . DS . 'plugins' . DS . 'k2' . DS . $this->pluginName . DS . $this->pluginName . '.xml';
        $path = $type . (!empty($tab) ? $tab : '');

        if (!isset($item->plugins)) $item->plugins = null;
        $plugins = (array) json_decode($item->plugins);

        jimport('joomla.form.form');
        $form = JForm::getInstance('plg_k2_' . $this->pluginName . '_' . $path, $manifest, array(), true, 'fields[@group="' . $path . '"]');
        $values = array();

        if (!empty($plugins[$this->pluginName])) {
            foreach ($plugins[$this->pluginName] as $name => $value) {
                $values[$name] = $value;
            }
            if (isset($plugins['ffcover']))
                $values['ffcover'] = $plugins['ffcover'];
            $form->bind($values);
        }

        $fields = '';
        foreach ($form->getFieldset() as $field) {

            if ($field->name == 'ffcover') {
                $search = 'name="' . $field->name . '"';
                $replace = 'name="plugins[' . $field->name . ']"';
            } else if (strpos($field->name, '[]') !== false) {
                $search = 'name="' . $field->name . '"';
                $replace = 'name="plugins[' . $this->pluginName . '][' . str_replace('[]', '', $field->name) . '][]"';
            } else {
                $search = 'name="' . $field->name . '"';
                $replace = 'name="plugins[' . $this->pluginName . '][' . $field->name . ']"';
            }
            $input = JString::str_ireplace($search, $replace, $field->__get('input'));
            $fields .= $field->__get('label') . ' ' . $input;
        }

        if ($fields) {
            $plugin = new stdClass;
            $plugin->id = $this->pluginName;
            $plugin->name = $this->pluginNameHumanReadable;
            $plugin->fields = '<div style="padding: 30px 60px;">'.$fields.'</div>';
            $plugin->form = $form;

            return $plugin;
        }
    }

}
