<?php
/*----------------------------------------------------------------------------------|  www.vdm.io  |----/
				Vast Development Method 
/-------------------------------------------------------------------------------------------------------/

	@version		1.0.0
	@build			11th December, 2020
	@created		5th July, 2020
	@package		Recipe Manager
	@subpackage		view.html.php
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

/**
 * Recipemanager View class for the Recipes
 */
class RecipemanagerViewRecipes extends JViewLegacy
{
	// Overwriting JView display method
	function display($tpl = null)
	{		
		// get combined params of both component and menu
		$this->app = JFactory::getApplication();
		$this->params = $this->app->getParams();
		$this->menu = $this->app->getMenu()->getActive();
		// get the user object
		$this->user = JFactory::getUser();
		// [Interpretation 5123] Initialise variables.
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');

		// [Interpretation 5172] Set the toolbar
		$this->addToolBar();

		// [Interpretation 5175] set the document
		$this->_prepareDocument();

		// [Interpretation 5198] Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode(PHP_EOL, $errors), 500);
		}

		parent::display($tpl);
	}

	/**
	 * Prepares the document
	 */
	protected function _prepareDocument()
	{

		// [Interpretation 6203] always make sure jquery is loaded.
		JHtml::_('jquery.framework');
		// [Interpretation 6206] Load the header checker class.
		require_once( JPATH_COMPONENT_SITE.'/helpers/headercheck.php' );
		// [Interpretation 6218] Initialize the header checker.
		$HeaderCheck = new recipemanagerHeaderCheck;
		// add the document default css file
		$this->document->addStyleSheet(JURI::root(true) .'/components/com_recipemanager/assets/css/recipes.css', (RecipemanagerHelper::jVersion()->isCompatible('3.8.0')) ? array('version' => 'auto') : 'text/css');
	}

	/**
	 * Setting the toolbar
	 */
	protected function addToolBar()
	{
		
		// set help url for this view if found
		$help_url = RecipemanagerHelper::getHelpUrl('recipes');
		if (RecipemanagerHelper::checkString($help_url))
		{
			JToolbarHelper::help('COM_RECIPEMANAGER_HELP_MANAGER', false, $help_url);
		}
		// now initiate the toolbar
		$this->toolbar = JToolbar::getInstance();
	}

	/**
	 * Escapes a value for output in a view script.
	 *
	 * @param   mixed  $var  The output to escape.
	 *
	 * @return  mixed  The escaped value.
	 */
	public function escape($var, $sorten = false, $length = 40)
	{
		// use the helper htmlEscape method instead.
		return RecipemanagerHelper::htmlEscape($var, $this->_charset, $sorten, $length);
	}
}
