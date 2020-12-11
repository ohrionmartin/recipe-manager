<?php
/*----------------------------------------------------------------------------------|  www.vdm.io  |----/
				Vast Development Method 
/-------------------------------------------------------------------------------------------------------/

	@version		1.0.0
	@build			11th December, 2020
	@created		5th July, 2020
	@package		Recipe Manager
	@subpackage		headercheck.php
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

class recipemanagerHeaderCheck
{
	function js_loaded($script_name)
	{
		// UIkit check point
		if (strpos($script_name,'uikit') !== false)
		{
			$app            	= JFactory::getApplication();
			$getTemplateName  	= $app->getTemplate('template')->template;
			
			if (strpos($getTemplateName,'yoo') !== false)
			{
				return true;
			}
		}
		
		$document 	= JFactory::getDocument();
		$head_data 	= $document->getHeadData();
		foreach (array_keys($head_data['scripts']) as $script)
		{
			if (stristr($script, $script_name))
			{
				return true;
			}
		}

		return false;
	}
	
	function css_loaded($script_name)
	{
		// UIkit check point
		if (strpos($script_name,'uikit') !== false)
		{
			$app            	= JFactory::getApplication();
			$getTemplateName  	= $app->getTemplate('template')->template;
			
			if (strpos($getTemplateName,'yoo') !== false)
			{
				return true;
			}
		}
		
		$document 	= JFactory::getDocument();
		$head_data 	= $document->getHeadData();
		
		foreach (array_keys($head_data['styleSheets']) as $script)
		{
			if (stristr($script, $script_name))
			{
				return true;
			}
		}

		return false;
	}
}