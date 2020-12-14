<?php
/*----------------------------------------------------------------------------------|  www.vdm.io  |----/
				Vast Development Method 
/-------------------------------------------------------------------------------------------------------/

	@version		1.0.2
	@build			14th December, 2020
	@created		5th July, 2020
	@package		Recipe Manager
	@subpackage		recipes.php
	@author			Oh Martin <https://www.vdm.io>	
	@copyright		Copyright (C) 2020. All Rights Reserved
	@license		GNU General Public License version 2 or later; see LICENSE.txt
  ____  _____  _____  __  __  __      __       ___  _____  __  __  ____  _____  _  _  ____  _  _  ____ 
 (_  _)(  _  )(  _  )(  \/  )(  )    /__\     / __)(  _  )(  \/  )(  _ \(  _  )( \( )( ___)( \( )(_  _)
.-_)(   )(_)(  )(_)(  )    (  )(__  /(__)\   ( (__  )(_)(  )    (  )___/ )(_)(  )  (  )__)  )  (   )(  
\____) (_____)(_____)(_/\/\_)(____)(__)(__)   \___)(_____)(_/\/\_)(__)  (_____)(_)\_)(____)(_)\_) (__) 

/------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\Utilities\ArrayHelper;

/**
 * Recipe_manager Model for Recipes
 */
class Recipe_managerModelRecipes extends JModelList
{
	/**
	 * Model user data.
	 *
	 * @var        strings
	 */
	protected $user;
	protected $userId;
	protected $guest;
	protected $groups;
	protected $levels;
	protected $app;
	protected $input;
	protected $uikitComp;

	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return      string  An SQL query
	 */
	protected function getListQuery()
	{
		// Get the current user for authorisation checks
		$this->user = JFactory::getUser();
		$this->userId = $this->user->get('id');
		$this->guest = $this->user->get('guest');
		$this->groups = $this->user->get('groups');
		$this->authorisedGroups = $this->user->getAuthorisedGroups();
		$this->levels = $this->user->getAuthorisedViewLevels();
		$this->app = JFactory::getApplication();
		$this->input = $this->app->input;
		$this->initSet = true; 
		// [Interpretation 4787] Get a db connection.
		$db = JFactory::getDbo();

		// [Interpretation 4804] Create a new query object.
		$query = $db->getQuery(true);

		// [Interpretation 2446] Get from #__recipe_manager_recipe as a
		$query->select($db->quoteName(
			array('a.id','a.asset_id','a.description','a.image','a.preparing_time','a.name','a.alias','a.catid','a.ingredients','a.published','a.created_by','a.modified_by','a.created','a.modified','a.version','a.hits','a.ordering'),
			array('id','asset_id','description','image','preparing_time','name','alias','catid','ingredients','published','created_by','modified_by','created','modified','version','hits','ordering')));
		$query->from($db->quoteName('#__recipe_manager_recipe', 'a'));

		// [Interpretation 2446] Get from #__categories as b
		$query->select($db->quoteName(
			array('b.id','b.asset_id','b.parent_id','b.lft','b.rgt','b.level','b.path','b.extension','b.title','b.alias','b.note','b.description','b.published','b.checked_out','b.checked_out_time','b.access','b.params','b.metadesc','b.metakey','b.metadata','b.created_user_id','b.created_time','b.modified_user_id','b.modified_time','b.hits','b.language','b.version'),
			array('categories_id','categories_asset_id','categories_parent_id','categories_lft','categories_rgt','categories_level','categories_path','categories_extension','categories_title','categories_alias','categories_note','categories_description','categories_published','categories_checked_out','categories_checked_out_time','categories_access','categories_params','categories_metadesc','categories_metakey','categories_metadata','categories_created_user_id','categories_created_time','categories_modified_user_id','categories_modified_time','categories_hits','categories_language','categories_version')));
		$query->join('LEFT', ($db->quoteName('#__categories', 'b')) . ' ON (' . $db->quoteName('a.catid') . ' = ' . $db->quoteName('b.id') . ')');
		// [Interpretation 3456] Get where a.published is 1
		$query->where('a.published = 1');
		$query->order('a.name ASC');

		// [Interpretation 4840] return the query object
		return $query;
	}

	/**
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 */
	public function getItems()
	{
		$user = JFactory::getUser();
		// load parent items
		$items = parent::getItems();

		// Get the global params
		$globalParams = JComponentHelper::getParams('com_recipe_manager', true);

		// [Interpretation 4872] Insure all item fields are adapted where needed.
		if (Recipe_managerHelper::checkArray($items))
		{
			// [Interpretation 2924] Load the JEvent Dispatcher
			JPluginHelper::importPlugin('content');
			$this->_dispatcher = JEventDispatcher::getInstance();
			foreach ($items as $nr => &$item)
			{
				// [Interpretation 4881] Always create a slug for sef URL's
				$item->slug = (isset($item->alias) && isset($item->id)) ? $item->id.':'.$item->alias : $item->id;
				// [Interpretation 2874] Check if we can decode ingredients
				if (Recipe_managerHelper::checkJson($item->ingredients))
				{
					// [Interpretation 2877] Decode ingredients
					$item->ingredients = json_decode($item->ingredients, true);
				}
				// [Interpretation 2935] Check if item has params, or pass whole item.
				$params = (isset($item->params) && Recipe_managerHelper::checkJson($item->params)) ? json_decode($item->params) : $item;
				// [Interpretation 2946] Make sure the content prepare plugins fire on description
				$_description = new stdClass();
				$_description->text =& $item->description; // [Interpretation 2953] value must be in text
				// [Interpretation 2956] Since all values are now in text (Joomla Limitation), we also add the field name (description) to context
				$this->_dispatcher->trigger("onContentPrepare", array('com_recipe_manager.recipes.description', &$_description, &$params, 0));
			}
		}

		// return items
		return $items;
	}
}
