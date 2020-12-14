<?php
/*----------------------------------------------------------------------------------|  www.vdm.io  |----/
				Vast Development Method 
/-------------------------------------------------------------------------------------------------------/

	@version		1.0.2
	@build			14th December, 2020
	@created		5th July, 2020
	@package		Recipe Manager
	@subpackage		recipe_manager.php
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
JHtml::_('behavior.tabstate');

// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_recipe_manager'))
{
	throw new JAccessExceptionNotallowed(JText::_('JERROR_ALERTNOAUTHOR'), 403);
};

// Add CSS file for all pages
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_recipe_manager/assets/css/admin.css');
$document->addScript('components/com_recipe_manager/assets/js/admin.js');

// require helper files
JLoader::register('Recipe_managerHelper', __DIR__ . '/helpers/recipe_manager.php'); 
JLoader::register('JHtmlBatch_', __DIR__ . '/helpers/html/batch_.php'); 

// Get an instance of the controller prefixed by Recipe_manager
$controller = JControllerLegacy::getInstance('Recipe_manager');

// Perform the Request task
$controller->execute(JFactory::getApplication()->input->get('task'));

// Redirect if set by the controller
$controller->redirect();
