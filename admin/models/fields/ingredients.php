<?php
/*----------------------------------------------------------------------------------|  www.vdm.io  |----/
				Vast Development Method 
/-------------------------------------------------------------------------------------------------------/

	@version		1.0.2
	@build			14th December, 2020
	@created		5th July, 2020
	@package		Recipe Manager
	@subpackage		ingredients.php
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

// import the list field type
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

/**
 * Ingredients Form Field class for the Recipe_manager component
 */
class JFormFieldIngredients extends JFormFieldList
{
	/**
	 * The ingredients field type.
	 *
	 * @var		string
	 */
	public $type = 'ingredients';

	/**
	 * Method to get a list of options for a list input.
	 *
	 * @return	array    An array of JHtml options.
	 */
	protected function getOptions()
	{
		// Get the user object.
		$user = JFactory::getUser();
		// Get the databse object.
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		$query->select($db->quoteName(array('a.id','a.name','a.unit'),array('id','ingredients_name', 'unit')));
		$query->from($db->quoteName('#__recipe_manager_ingredient', 'a'));
		$query->where($db->quoteName('a.published') . ' = 1');
		$query->order('a.name ASC');
		// Implement View Level Access (if set in table)
		if (!$user->authorise('core.options', 'com_recipe_manager'))
		{
			$columns = $db->getTableColumns('#__recipe_manager_ingredient');
			if(isset($columns['access']))
			{
				$groups = implode(',', $user->getAuthorisedViewLevels());
				$query->where('a.access IN (' . $groups . ')');
			}
		}
		$db->setQuery((string)$query);
		$items = $db->loadObjectList();
		$options = array();
		if ($items)
		{
                         $unitArray = array(
				0 => 'COM_RECIPEMANAGER_INGREDIENT_TEASPOON',
				' 1' => 'COM_RECIPEMANAGER_INGREDIENT_DESSERTSPOON',
				' 2' => 'COM_RECIPEMANAGER_INGREDIENT_TABLESPOON',
				' 3' => 'COM_RECIPEMANAGER_INGREDIENT_FLUIDOUNCE',
				' 4' => 'COM_RECIPEMANAGER_INGREDIENT_CUP',
				' 5' => 'COM_RECIPEMANAGER_INGREDIENT_PINT',
				' 6' => 'COM_RECIPEMANAGER_INGREDIENT_QUART',
				' 7' => 'COM_RECIPEMANAGER_INGREDIENT_GALLON'
			);            
                         
			$options[] = JHtml::_('select.option', '', 'Select an option');
			foreach($items as $item)
			{
                                 $unit = $unitArray[$item->unit];
  				$options[] = JHtml::_('select.option', $item->id, $item->ingredients_name . "(". JText::_($unit) .")");
			}
		}
		return $options;
	}
}
