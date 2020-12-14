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

use Joomla\Utilities\ArrayHelper;

/**
 * Ingredients Controller
 */
class Recipe_managerControllerIngredients extends JControllerAdmin
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_RECIPE_MANAGER_INGREDIENTS';

	/**
	 * Method to get a model object, loading it if required.
	 *
	 * @param   string  $name    The model name. Optional.
	 * @param   string  $prefix  The class prefix. Optional.
	 * @param   array   $config  Configuration array for model. Optional.
	 *
	 * @return  JModelLegacy  The model.
	 *
	 * @since   1.6
	 */
	public function getModel($name = 'Ingredient', $prefix = 'Recipe_managerModel', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}

	public function exportData()
	{
		// [Interpretation 15227] Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
		// [Interpretation 15231] check if export is allowed for this user.
		$user = JFactory::getUser();
		if ($user->authorise('ingredient.export', 'com_recipe_manager') && $user->authorise('core.export', 'com_recipe_manager'))
		{
			// [Interpretation 15240] Get the input
			$input = JFactory::getApplication()->input;
			$pks = $input->post->get('cid', array(), 'array');
			// [Interpretation 15246] Sanitize the input
			$pks = ArrayHelper::toInteger($pks);
			// [Interpretation 15249] Get the model
			$model = $this->getModel('Ingredients');
			// [Interpretation 15254] get the data to export
			$data = $model->getExportData($pks);
			if (Recipe_managerHelper::checkArray($data))
			{
				// [Interpretation 15262] now set the data to the spreadsheet
				$date = JFactory::getDate();
				Recipe_managerHelper::xls($data,'Ingredients_'.$date->format('jS_F_Y'),'Ingredients exported ('.$date->format('jS F, Y').')','ingredients');
			}
		}
		// [Interpretation 15275] Redirect to the list screen with error.
		$message = JText::_('COM_RECIPE_MANAGER_EXPORT_FAILED');
		$this->setRedirect(JRoute::_('index.php?option=com_recipe_manager&view=ingredients', false), $message, 'error');
		return;
	}


	public function importData()
	{
		// [Interpretation 15290] Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
		// [Interpretation 15294] check if import is allowed for this user.
		$user = JFactory::getUser();
		if ($user->authorise('ingredient.import', 'com_recipe_manager') && $user->authorise('core.import', 'com_recipe_manager'))
		{
			// [Interpretation 15303] Get the import model
			$model = $this->getModel('Ingredients');
			// [Interpretation 15308] get the headers to import
			$headers = $model->getExImPortHeaders();
			if (Recipe_managerHelper::checkObject($headers))
			{
				// [Interpretation 15316] Load headers to session.
				$session = JFactory::getSession();
				$headers = json_encode($headers);
				$session->set('ingredient_VDM_IMPORTHEADERS', $headers);
				$session->set('backto_VDM_IMPORT', 'ingredients');
				$session->set('dataType_VDM_IMPORTINTO', 'ingredient');
				// [Interpretation 15327] Redirect to import view.
				$message = JText::_('COM_RECIPE_MANAGER_IMPORT_SELECT_FILE_FOR_INGREDIENTS');
				$this->setRedirect(JRoute::_('index.php?option=com_recipe_manager&view=import', false), $message);
				return;
			}
		}
		// [Interpretation 15358] Redirect to the list screen with error.
		$message = JText::_('COM_RECIPE_MANAGER_IMPORT_FAILED');
		$this->setRedirect(JRoute::_('index.php?option=com_recipe_manager&view=ingredients', false), $message, 'error');
		return;
	}
}
