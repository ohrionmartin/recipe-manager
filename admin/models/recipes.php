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
 * Recipes Model
 */
class Recipe_managerModelRecipes extends JModelList
{
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
        {
			$config['filter_fields'] = array(
				'a.id','id',
				'a.published','published',
				'a.access','access',
				'a.ordering','ordering',
				'a.created_by','created_by',
				'a.modified_by','modified_by',
				'c.title','category_title',
				'c.id', 'category_id',
				'a.catid','catid',
				'a.name','name'
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return  void
	 *
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$app = JFactory::getApplication();

		// Adjust the context to support modal layouts.
		if ($layout = $app->input->get('layout'))
		{
			$this->context .= '.' . $layout;
		}

		// [Interpretation 21133] Check if the form was submitted
		$formSubmited = $app->input->post->get('form_submited');

		$access = $this->getUserStateFromRequest($this->context . '.filter.access', 'filter_access', 0, 'int');
		if ($formSubmited)
		{
			$access = $app->input->post->get('access');
			$this->setState('filter.access', $access);
		}

		$published = $this->getUserStateFromRequest($this->context . '.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$created_by = $this->getUserStateFromRequest($this->context . '.filter.created_by', 'filter_created_by', '');
		$this->setState('filter.created_by', $created_by);

		$created = $this->getUserStateFromRequest($this->context . '.filter.created', 'filter_created');
		$this->setState('filter.created', $created);

		$sorting = $this->getUserStateFromRequest($this->context . '.filter.sorting', 'filter_sorting', 0, 'int');
		$this->setState('filter.sorting', $sorting);

		$search = $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$category = $app->getUserStateFromRequest($this->context . '.filter.category', 'filter_category');
		$this->setState('filter.category', $category);

		$categoryId = $this->getUserStateFromRequest($this->context . '.filter.category_id', 'filter_category_id');
		$this->setState('filter.category_id', $categoryId);

		$catid = $this->getUserStateFromRequest($this->context . '.filter.catid', 'filter_catid');
		if ($formSubmited)
		{
			$catid = $app->input->post->get('catid');
			$this->setState('filter.catid', $catid);
		}

		$name = $this->getUserStateFromRequest($this->context . '.filter.name', 'filter_name');
		if ($formSubmited)
		{
			$name = $app->input->post->get('name');
			$this->setState('filter.name', $name);
		}

		// List state information.
		parent::populateState($ordering, $direction);
	}
	
	/**
	 * Method to get an array of data items.
	 *
	 * @return  mixed  An array of data items on success, false on failure.
	 */
	public function getItems()
	{
		// [Interpretation 21355] check in items
		$this->checkInNow();

		// load parent items
		$items = parent::getItems();
        
		// return items
		return $items;
	}
	
	/**
	 * Method to build an SQL query to load the list data.
	 *
	 * @return	string	An SQL query
	 */
	protected function getListQuery()
	{
		// [Interpretation 15526] Get the user object.
		$user = JFactory::getUser();
		// [Interpretation 15528] Create a new query object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		// [Interpretation 15533] Select some fields
		$query->select('a.*');
		$query->select($db->quoteName('c.title','category_title'));

		// [Interpretation 15543] From the recipe_manager_item table
		$query->from($db->quoteName('#__recipe_manager_recipe', 'a'));
		$query->join('LEFT', $db->quoteName('#__categories', 'c') . ' ON (' . $db->quoteName('a.catid') . ' = ' . $db->quoteName('c.id') . ')');

		// [Interpretation 15562] Filter by published state
		$published = $this->getState('filter.published');
		if (is_numeric($published))
		{
			$query->where('a.published = ' . (int) $published);
		}
		elseif ($published === '')
		{
			$query->where('(a.published = 0 OR a.published = 1)');
		}

		// [Interpretation 15582] Join over the asset groups.
		$query->select('ag.title AS access_level');
		$query->join('LEFT', '#__viewlevels AS ag ON ag.id = a.access');
		// [Interpretation 15596] Filter by access level.
		$_access = $this->getState('filter.access');
		if ($_access && is_numeric($_access))
		{
			$query->where('a.access = ' . (int) $_access);
		}
		elseif (Recipe_managerHelper::checkArray($_access))
		{
			// [Interpretation 15611] Secure the array for the query
			$_access = ArrayHelper::toInteger($_access);
			// [Interpretation 15616] Filter by the Access Array.
			$query->where('a.access IN (' . implode(',', $_access) . ')');
		}
		// [Interpretation 15622] Implement View Level Access
		if (!$user->authorise('core.options', 'com_recipe_manager'))
		{
			$groups = implode(',', $user->getAuthorisedViewLevels());
			$query->where('a.access IN (' . $groups . ')');
		}
		// [Interpretation 15783] Filter by search.
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			if (stripos($search, 'id:') === 0)
			{
				$query->where('a.id = ' . (int) substr($search, 3));
			}
			else
			{
				$search = $db->quote('%' . $db->escape($search) . '%');
				$query->where('(a.name LIKE '.$search.' OR a.catid LIKE '.$search.')');
			}
		}


		// [Interpretation 15642] Filter by a single or group of categories.
		$baselevel = 1;
		$categoryId = $this->getState('filter.category_id');

		if (is_numeric($categoryId))
		{
			$cat_tbl = JTable::getInstance('Category', 'JTable');
			$cat_tbl->load($categoryId);
			$rgt = $cat_tbl->rgt;
			$lft = $cat_tbl->lft;
			$baselevel = (int) $cat_tbl->level;
			$query->where('c.lft >= ' . (int) $lft)
				->where('c.rgt <= ' . (int) $rgt);
		}
		elseif (is_array($categoryId))
		{
			$categoryId = ArrayHelper::toInteger($categoryId);
			$categoryId = implode(',', $categoryId);
			$query->where('a.catid IN (' . $categoryId . ')');
		}


		// [Interpretation 15730] Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering', 'a.id');
		$orderDirn = $this->state->get('list.direction', 'desc');
		if ($orderCol != '')
		{
			$query->order($db->escape($orderCol . ' ' . $orderDirn));
		}

		return $query;
	}

	/**
	 * Method to get list export data.
	 *
	 * @param   array  $pks  The ids of the items to get
	 * @param   JUser  $user  The user making the request
	 *
	 * @return mixed  An array of data items on success, false on failure.
	 */
	public function getExportData($pks, $user = null)
	{
		// [Interpretation 14980] setup the query
		if (($pks_size = Recipe_managerHelper::checkArray($pks)) !== false || 'bulk' === $pks)
		{
			// [Interpretation 14987] Set a value to know this is export method. (USE IN CUSTOM CODE TO ALTER OUTCOME)
			$_export = true;
			// [Interpretation 14992] Get the user object if not set.
			if (!isset($user) || !Recipe_managerHelper::checkObject($user))
			{
				$user = JFactory::getUser();
			}
			// [Interpretation 15000] Create a new query object.
			$db = JFactory::getDBO();
			$query = $db->getQuery(true);

			// [Interpretation 15006] Select some fields
			$query->select('a.*');

			// [Interpretation 15010] From the recipe_manager_recipe table
			$query->from($db->quoteName('#__recipe_manager_recipe', 'a'));
			// [Interpretation 15017] The bulk export path
			if ('bulk' === $pks)
			{
				$query->where('a.id > 0');
			}
			// [Interpretation 15026] A large array of ID's will not work out well
			elseif ($pks_size > 500)
			{
				// [Interpretation 15031] Use lowest ID
				$query->where('a.id >= ' . (int) min($pks));
				// [Interpretation 15035] Use highest ID
				$query->where('a.id <= ' . (int) max($pks));
			}
			// [Interpretation 15041] The normal default path
			else
			{
				$query->where('a.id IN (' . implode(',',$pks) . ')');
			}
			// [Interpretation 15093] Implement View Level Access
			if (!$user->authorise('core.options', 'com_recipe_manager'))
			{
				$groups = implode(',', $user->getAuthorisedViewLevels());
				$query->where('a.access IN (' . $groups . ')');
			}

			// [Interpretation 15134] Order the results by ordering
			$query->order('a.ordering  ASC');

			// [Interpretation 15140] Load the items
			$db->setQuery($query);
			$db->execute();
			if ($db->getNumRows())
			{
				$items = $db->loadObjectList();

				// [Interpretation 21893] Set values to display correctly.
				if (Recipe_managerHelper::checkArray($items))
				{
					foreach ($items as $nr => &$item)
					{
						// [Interpretation 22035] unset the values we don't want exported.
						unset($item->asset_id);
						unset($item->checked_out);
						unset($item->checked_out_time);
					}
				}
				// [Interpretation 22050] Add headers to items array.
				$headers = $this->getExImPortHeaders();
				if (Recipe_managerHelper::checkObject($headers))
				{
					array_unshift($items,$headers);
				}
				return $items;
			}
		}
		return false;
	}

	/**
	* Method to get header.
	*
	* @return mixed  An array of data items on success, false on failure.
	*/
	public function getExImPortHeaders()
	{
		// Get a db connection.
		$db = JFactory::getDbo();
		// get the columns
		$columns = $db->getTableColumns("#__recipe_manager_recipe");
		if (Recipe_managerHelper::checkArray($columns))
		{
			// remove the headers you don't import/export.
			unset($columns['asset_id']);
			unset($columns['checked_out']);
			unset($columns['checked_out_time']);
			$headers = new stdClass();
			foreach ($columns as $column => $type)
			{
				$headers->{$column} = $column;
			}
			return $headers;
		}
		return false;
	}
	
	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * @return  string  A store id.
	 *
	 */
	protected function getStoreId($id = '')
	{
		// [Interpretation 20592] Compile the store id.
		$id .= ':' . $this->getState('filter.id');
		$id .= ':' . $this->getState('filter.search');
		$id .= ':' . $this->getState('filter.published');
		// [Interpretation 20757] Check if the value is an array
		$_access = $this->getState('filter.access');
		if (Recipe_managerHelper::checkArray($_access))
		{
			$id .= ':' . implode(':', $_access);
		}
		// [Interpretation 20772] Check if this is only an number or string
		elseif (is_numeric($_access)
		 || Recipe_managerHelper::checkString($_access))
		{
			$id .= ':' . $_access;
		}
		$id .= ':' . $this->getState('filter.ordering');
		$id .= ':' . $this->getState('filter.created_by');
		$id .= ':' . $this->getState('filter.modified_by');
		// [Interpretation 20757] Check if the value is an array
		$_category = $this->getState('filter.category');
		if (Recipe_managerHelper::checkArray($_category))
		{
			$id .= ':' . implode(':', $_category);
		}
		// [Interpretation 20772] Check if this is only an number or string
		elseif (is_numeric($_category)
		 || Recipe_managerHelper::checkString($_category))
		{
			$id .= ':' . $_category;
		}
		// [Interpretation 20757] Check if the value is an array
		$_category_id = $this->getState('filter.category_id');
		if (Recipe_managerHelper::checkArray($_category_id))
		{
			$id .= ':' . implode(':', $_category_id);
		}
		// [Interpretation 20772] Check if this is only an number or string
		elseif (is_numeric($_category_id)
		 || Recipe_managerHelper::checkString($_category_id))
		{
			$id .= ':' . $_category_id;
		}
		// [Interpretation 20757] Check if the value is an array
		$_catid = $this->getState('filter.catid');
		if (Recipe_managerHelper::checkArray($_catid))
		{
			$id .= ':' . implode(':', $_catid);
		}
		// [Interpretation 20772] Check if this is only an number or string
		elseif (is_numeric($_catid)
		 || Recipe_managerHelper::checkString($_catid))
		{
			$id .= ':' . $_catid;
		}
		$id .= ':' . $this->getState('filter.name');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to checkin all items left checked out longer then a set time.
	 *
	 * @return  a bool
	 *
	 */
	protected function checkInNow()
	{
		// [Interpretation 21373] Get set check in time
		$time = JComponentHelper::getParams('com_recipe_manager')->get('check_in');

		if ($time)
		{

			// [Interpretation 21381] Get a db connection.
			$db = JFactory::getDbo();
			// [Interpretation 21384] reset query
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from($db->quoteName('#__recipe_manager_recipe'));
			$db->setQuery($query);
			$db->execute();
			if ($db->getNumRows())
			{
				// [Interpretation 21395] Get Yesterdays date
				$date = JFactory::getDate()->modify($time)->toSql();
				// [Interpretation 21399] reset query
				$query = $db->getQuery(true);

				// [Interpretation 21403] Fields to update.
				$fields = array(
					$db->quoteName('checked_out_time') . '=\'0000-00-00 00:00:00\'',
					$db->quoteName('checked_out') . '=0'
				);

				// [Interpretation 21412] Conditions for which records should be updated.
				$conditions = array(
					$db->quoteName('checked_out') . '!=0', 
					$db->quoteName('checked_out_time') . '<\''.$date.'\''
				);

				// [Interpretation 21421] Check table
				$query->update($db->quoteName('#__recipe_manager_recipe'))->set($fields)->where($conditions); 

				$db->setQuery($query);

				$db->execute();
			}
		}

		return false;
	}
}
