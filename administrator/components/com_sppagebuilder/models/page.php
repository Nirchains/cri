<?php
/**
 * @package SP Page Builder
 * @author JoomShaper http://www.joomshaper.com
 * @copyright Copyright (c) 2010 - 2020 JoomShaper
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or later
*/
//no direct accees
defined ('_JEXEC') or die ('Restricted access');

jimport('joomla.application.component.modeladmin');

JLoader::register('SppagebuilderHelperRoute', JPATH_ROOT . '/components/com_sppagebuilder/helpers/route.php');

class SppagebuilderModelPage extends JModelAdmin {

    public function getTable($type = 'Page', $prefix = 'SppagebuilderTable', $config = array()) {
        return JTable::getInstance($type, $prefix, $config);
    }

    public function getForm($data = array(), $loadData = true) {
        $form = $this->loadForm('com_sppagebuilder.page', 'page',array('control' => 'jform', 'load_data' => $loadData));

        if (empty($form)) {
            return false;
        }

        $jinput = JFactory::getApplication()->input;

        $id = $jinput->get('id', 0);

        // Determine correct permissions to check.
        if ($this->getState('page.id'))
        {
            $id = $this->getState('page.id');

            // Existing record. Can only edit in selected categories.
            $form->setFieldAttribute('catid', 'action', 'core.edit');

            // Existing record. Can only edit own pages in selected categories.
            $form->setFieldAttribute('catid', 'action', 'core.edit.own');
        }
        else
        {
            // New record. Can only create in selected categories.
            $form->setFieldAttribute('catid', 'action', 'core.create');
        }

        $user = JFactory::getUser();

        // Modify the form based on Edit State access controls.
        if ($id != 0 && (!$user->authorise('core.edit.state', 'com_sppagebuilder.page.' . (int) $id))
            || ($id == 0 && !$user->authorise('core.edit.state', 'com_sppagebuilder')))
        {
            // Disable fields for display.
            $form->setFieldAttribute('published', 'disabled', 'true');

            // Disable fields while saving.
            // The controller has already verified this is an page you can edit.
            $form->setFieldAttribute('published', 'filter', 'unset');
        }

        return $form;
    }

    public function getItem($pk = NULL) {
        if ($item = parent::getItem($pk))
		{
            $item = parent::getItem($pk);

            // Get item language code
            $lang_code = (isset($item->language) && $item->language && explode('-',$item->language)[0])? explode('-',$item->language)[0] : '';
            
            // Preview URL
            $item->link = 'index.php?option=com_sppagebuilder&task=page.edit&id=' . $item->id;
            
            $item->preview = SppagebuilderHelperRoute::getPageRoute($item->id, $lang_code);
			$item->frontend_edit = SppagebuilderHelperRoute::getFormRoute($item->id, $lang_code);
        }

        return $item;
    }

    protected function loadFormData() {
        $data = JFactory::getApplication()->getUserState('com_sppagebuilder.edit.page.data', array());

        if (empty($data)) {
            $data = $this->getItem();
        }

        $this->preprocessData('com_sppagebuilder.page', $data);

        return $data;
    }

    protected function canEditState($item) {
        return \JFactory::getUser()->authorise('core.edit.state', 'com_sppagebuilder.page.' . $item->id);
	}

    public function save($data) {
        $app = JFactory::getApplication();

        if ($app->input->get('task') == 'save2copy') {
            $data['title'] = $this->pageGenerateNewTitle( $data['title'] );
        }

        $data['created_by'] = $this->checkExistingUser($data['created_by']);

        parent::save($data);
        return true;
    }

    protected function checkExistingUser($id) {
        $currentUser = JFactory::getUser();
        $user_id = $currentUser->id;

        if($id) {
            $user = JFactory::getUser($id);
            if($user->id) {
                $user_id = $id;
            }
        }

        return $user_id;
    }

    public static function pageGenerateNewTitle($title ) {
        $pageTable = JTable::getInstance('Page', 'SppagebuilderTable');

        while( $pageTable->load(array('title'=>$title)) ) {
            $m = null;
            if (preg_match('#\((\d+)\)$#', $title, $m)) {
                $title = preg_replace('#\(\d+\)$#', '('.($m[1] + 1).')', $title);
            } else {
                $title .= ' (2)';
            }
        }

        return $title;
    }

    public static function getPageInfoById($pageId){
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select( array('a.*') );
		$query->from($db->quoteName('#__sppagebuilder', 'a'));
		$query->where($db->quoteName('a.id')." = ".$db->quote($pageId));
		$db->setQuery($query);
		$result = $db->loadObject();
		
		return $result;
	}

    public function getMySections() {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select($db->quoteName(array('id', 'title', 'section')));
        $query->from($db->quoteName('#__sppagebuilder_sections'));
        //$query->where($db->quoteName('profile_key') . ' LIKE '. $db->quote('\'custom.%\''));
        $query->order('id ASC');
        $db->setQuery($query);
        $results = $db->loadObjectList();
        return json_encode($results);
    }

    public function deleteSection($id){
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        // delete all custom keys for user 1001.
        $conditions = array(
            $db->quoteName('id') . ' = '.$id
        );

        $query->delete($db->quoteName('#__sppagebuilder_sections'));
        $query->where($conditions);

        $db->setQuery($query);

        return $db->execute();
    }

    public function saveSection($title, $section){
        $db = JFactory::getDbo();
        $user = JFactory::getUser();
        $obj = new stdClass();
        $obj->title = $title;
        $obj->section = $section;
        $obj->created = JFactory::getDate()->toSql();
        $obj->created_by = $user->get('id');

        $db->insertObject('#__sppagebuilder_sections', $obj);

        return $db->insertid();
    }

    public function getMyAddons() {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('id', 'title', 'code')));
		$query->from($db->quoteName('#__sppagebuilder_addons'));
		
		$query->order('id ASC');
		$db->setQuery($query);
		$results = $db->loadObjectList();
		return json_encode($results);
	}

    public function saveAddon($title, $addon){
        $db = JFactory::getDbo();
        $user = JFactory::getUser();
        $obj = new stdClass();
        $obj->title = $title;
        $obj->code = $addon;
        $obj->created = JFactory::getDate()->toSql();
        $obj->created_by = $user->get('id');

        $db->insertObject('#__sppagebuilder_addons', $obj);

        return $db->insertid();
    }

    public function deleteAddon($id){
        $db = JFactory::getDbo();

        $query = $db->getQuery(true);

        // delete all custom keys for user 1001.
        $conditions = array(
            $db->quoteName('id') . ' = '.$id
        );

        $query->delete($db->quoteName('#__sppagebuilder_addons'));
        $query->where($conditions);

        $db->setQuery($query);

        return $db->execute();
    }

    public function createBrandNewPage($title = '', $extension = '', $extension_view = '', $view_id = 0) {
		$user = JFactory::getUser();
		$date = JFactory::getDate();
        $db = $this->getDbo();
        $page = new stdClass();
        $page->title = $title;
        $page->text = '[]';
        $page->extension = $extension;
        $page->extension_view = $extension_view;
        $page->view_id = $view_id;
		$page->published = 1;
		$page->created_by = (int) $user->id;
		$page->created_on = $date->toSql();
		$page->language = '*';
		$page->access = 1;
		$page->active = 1;
		$db->insertObject('#__sppagebuilder', $page);
		return $db->insertid();
    }

    public function get_module_page_data($id) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('id')));
		$query->from($db->quoteName('#__sppagebuilder'));
		$query->where($db->quoteName('extension') . ' = '. $db->quote('mod_sppagebuilder'));
		$query->where($db->quoteName('extension_view') . ' = '. $db->quote('module'));
		$query->where($db->quoteName('view_id') . ' = '. $db->quote($id));
		$query->order('ordering ASC');
		$db->setQuery($query);
		$result = $db->loadResult();

		return $result;
	}

    private function save_module_data($id, $title, $content) {
		$user = JFactory::getUser();
		$date = JFactory::getDate();
        $db = JFactory::getDbo();
		$module = new stdClass();
        $module->title = $title;
        $module->text = $content;
        $module->extension = 'mod_spmodulebuilder';
        $module->extension_view = 'module';
        $module->view_id = $id;
		$module->published = 1;
		$module->created_by = (int) $user->id;
		$module->created_on = $date->toSql();
		$module->language = '*';
		$module->access = 1;
		$module->active = 1;

		$db->insertObject('#__sppagebuilder', $module);
		return $db->insertid();
	}
    
    public function update_module_data($view_id, $id, $title, $content) {
		$user = JFactory::getUser();
		$date = JFactory::getDate();
        
        $db = JFactory::getDbo();
        $module = new stdClass();
        $module->id = $view_id;
        $module->title = $title;
        $module->text = $content;
        $module->modified_by = (int) $user->id;
        $module->modified = $date->toSql();
        
		$db->updateObject('#__sppagebuilder', $module, 'id');
		return $db->insertid();
	}
    
}