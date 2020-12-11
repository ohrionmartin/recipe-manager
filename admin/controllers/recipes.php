<?php
/*----------------------------------------------------------------------------------|  www.vdm.io  |----/
				Vast Development Method 
/-------------------------------------------------------------------------------------------------------/

	@version		1.0.0
	@build			11th December, 2020
	@created		5th July, 2020
	@package		Recipe Manager
	@subpackage		recipes.php
	@author			Oh Martin <https://www.vdm.io>	
	@copyright		Copyright (C) 2020. All Rights Reserved
	@license		GNU/GPL Version 2 or later - http://www.gnu.org/licenses/gpl-2.0.html
  ____  _____  _____  __  __  __      __       ___  _____  __  __  ____  _____  _  _  ____  _  _  ____ 
 (_  _)(  _  )(  _  )(  \/  )(  )    /__\     / __)(  _  )(  \/  )(  _ \(  _  )( \( )( ___)( \( )(_  _)
.-_)(   )(_)(  )(_)(  )    (  )(__  /(__)\   ( (__  )(_)(  )    (  )___/ )(_)(  )  (  )__)  )  (   )(  
\____) (_____)(_____)(_/\/\_)(____)(__)(__)   \___)(_____)(_/\/\_)(__)  (_____)(_)\_)(____)(_)\_) (__) 

/------------------------------------------------------------------------------------------------------*/

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

use Joomla\Utilities\ArrayHelper;

/**
 * Recipes Controller
 */
class RecipemanagerControllerRecipes extends JControllerAdmin
{
	/**
	 * The prefix to use with controller messages.
	 *
	 * @var    string
	 * @since  1.6
	 */
	protected $text_prefix = 'COM_RECIPEMANAGER_RECIPES';

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
	public function getModel($name = 'Recipe', $prefix = 'RecipemanagerModel', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, $config);
	}

	public function exportData()
	{
		// [Interpretation 15235] Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
		// [Interpretation 15239] check if export is allowed for this user.
		$user = JFactory::getUser();
		if ($user->authorise('recipe.export', 'com_recipemanager') && $user->authorise('core.export', 'com_recipemanager'))
		{
			// [Interpretation 15248] Get the input
			$input = JFactory::getApplication()->input;
			$pks = $input->post->get('cid', array(), 'array');
			// [Interpretation 15254] Sanitize the input
			$pks = ArrayHelper::toInteger($pks);
			// [Interpretation 15257] Get the model
			$model = $this->getModel('Recipes');
			// [Interpretation 15262] get the data to export
			$data = $model->getExportData($pks);
			if (RecipemanagerHelper::checkArray($data))
			{
				// [Interpretation 15270] now set the data to the spreadsheet
				$date = JFactory::getDate();
				RecipemanagerHelper::xls($data,'Recipes_'.$date->format('jS_F_Y'),'Recipes exported ('.$date->format('jS F, Y').')','recipes');
			}
		}
		// [Interpretation 15283] Redirect to the list screen with error.
		$message = JText::_('COM_RECIPEMANAGER_EXPORT_FAILED');
		$this->setRedirect(JRoute::_('index.php?option=com_recipemanager&view=recipes', false), $message, 'error');
		return;
	}


	public function importData()
	{
		// [Interpretation 15298] Check for request forgeries
		JSession::checkToken() or die(JText::_('JINVALID_TOKEN'));
		// [Interpretation 15302] check if import is allowed for this user.
		$user = JFactory::getUser();
		if ($user->authorise('recipe.import', 'com_recipemanager') && $user->authorise('core.import', 'com_recipemanager'))
		{
			// [Interpretation 15311] Get the import model
			$model = $this->getModel('Recipes');
			// [Interpretation 15316] get the headers to import
			$headers = $model->getExImPortHeaders();
			if (RecipemanagerHelper::checkObject($headers))
			{
				// [Interpretation 15324] Load headers to session.
				$session = JFactory::getSession();
				$headers = json_encode($headers);
				$session->set('recipe_VDM_IMPORTHEADERS', $headers);
				$session->set('backto_VDM_IMPORT', 'recipes');
				$session->set('dataType_VDM_IMPORTINTO', 'recipe');
				// [Interpretation 15335] Redirect to import view.
				$message = JText::_('COM_RECIPEMANAGER_IMPORT_SELECT_FILE_FOR_RECIPES');
				$this->setRedirect(JRoute::_('index.php?option=com_recipemanager&view=import', false), $message);
				return;
			}
		}
		// [Interpretation 15366] Redirect to the list screen with error.
		$message = JText::_('COM_RECIPEMANAGER_IMPORT_FAILED');
		$this->setRedirect(JRoute::_('index.php?option=com_recipemanager&view=recipes', false), $message, 'error');
		return;
	}
}
