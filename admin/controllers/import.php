<?php
/*----------------------------------------------------------------------------------|  www.vdm.io  |----/
				Vast Development Method 
/-------------------------------------------------------------------------------------------------------/

	@version		1.0.0
	@build			11th December, 2020
	@created		5th July, 2020
	@package		Recipe Manager
	@subpackage		import.php
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
 * Recipemanager Import Controller
 */
class RecipemanagerControllerImport extends JControllerLegacy
{
	/**
	 * Import an spreadsheet.
	 *
	 * @return  void
	 */
	public function import()
	{
		// Check for request forgeries
		JSession::checkToken() or jexit(JText::_('JINVALID_TOKEN'));

		$model = $this->getModel('import');
		if ($model->import())
		{
			$cache = JFactory::getCache('mod_menu');
			$cache->clean();
			// TODO: Reset the users acl here as well to kill off any missing bits
		}

		$app = JFactory::getApplication();
		$redirect_url = $app->getUserState('com_recipemanager.redirect_url');
		if (empty($redirect_url))
		{
			$redirect_url = JRoute::_('index.php?option=com_recipemanager&view=import', false);
		}
		else
		{
			// wipe out the user state when we're going to redirect
			$app->setUserState('com_recipemanager.redirect_url', '');
			$app->setUserState('com_recipemanager.message', '');
			$app->setUserState('com_recipemanager.extension_message', '');
		}
		$this->setRedirect($redirect_url);
	}
}
