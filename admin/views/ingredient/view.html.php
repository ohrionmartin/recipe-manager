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
 * Ingredient View class
 */
class RecipemanagerViewIngredient extends JViewLegacy
{
	/**
	 * display method of View
	 * @return void
	 */
	public function display($tpl = null)
	{
		// set params
		$this->params = JComponentHelper::getParams('com_recipemanager');
		// Assign the variables
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->script = $this->get('Script');
		$this->state = $this->get('State');
		// get action permissions
		$this->canDo = RecipemanagerHelper::getActions('ingredient', $this->item);
		// get input
		$jinput = JFactory::getApplication()->input;
		$this->ref = $jinput->get('ref', 0, 'word');
		$this->refid = $jinput->get('refid', 0, 'int');
		$return = $jinput->get('return', null, 'base64');
		// set the referral string
		$this->referral = '';
		if ($this->refid && $this->ref)
		{
			// return to the item that referred to this item
			$this->referral = '&ref=' . (string)$this->ref . '&refid=' . (int)$this->refid;
		}
		elseif($this->ref)
		{
			// return to the list view that referred to this item
			$this->referral = '&ref=' . (string)$this->ref;
		}
		// check return value
		if (!is_null($return))
		{
			// add the return value
			$this->referral .= '&return=' . (string)$return;
		}

		// Set the toolbar
		$this->addToolBar();
		
		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			throw new Exception(implode("\n", $errors), 500);
		}

		// Display the template
		parent::display($tpl);

		// Set the document
		$this->setDocument();
	}


	/**
	 * Setting the toolbar
	 */
	protected function addToolBar()
	{
		JFactory::getApplication()->input->set('hidemainmenu', true);
		$user = JFactory::getUser();
		$userId	= $user->id;
		$isNew = $this->item->id == 0;

		JToolbarHelper::title( JText::_($isNew ? 'COM_RECIPEMANAGER_INGREDIENT_NEW' : 'COM_RECIPEMANAGER_INGREDIENT_EDIT'), 'pencil-2 article-add');
		// [Interpretation 20862] Built the actions for new and existing records.
		if (RecipemanagerHelper::checkString($this->referral))
		{
			if ($this->canDo->get('core.create') && $isNew)
			{
				// [Interpretation 20887] We can create the record.
				JToolBarHelper::save('ingredient.save', 'JTOOLBAR_SAVE');
			}
			elseif ($this->canDo->get('core.edit'))
			{
				// [Interpretation 20912] We can save the record.
				JToolBarHelper::save('ingredient.save', 'JTOOLBAR_SAVE');
			}
			if ($isNew)
			{
				// [Interpretation 20919] Do not creat but cancel.
				JToolBarHelper::cancel('ingredient.cancel', 'JTOOLBAR_CANCEL');
			}
			else
			{
				// [Interpretation 20926] We can close it.
				JToolBarHelper::cancel('ingredient.cancel', 'JTOOLBAR_CLOSE');
			}
		}
		else
		{
			if ($isNew)
			{
				// [Interpretation 20936] For new records, check the create permission.
				if ($this->canDo->get('core.create'))
				{
					JToolBarHelper::apply('ingredient.apply', 'JTOOLBAR_APPLY');
					JToolBarHelper::save('ingredient.save', 'JTOOLBAR_SAVE');
					JToolBarHelper::custom('ingredient.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
				};
				JToolBarHelper::cancel('ingredient.cancel', 'JTOOLBAR_CANCEL');
			}
			else
			{
				if ($this->canDo->get('core.edit'))
				{
					// [Interpretation 20989] We can save the new record
					JToolBarHelper::apply('ingredient.apply', 'JTOOLBAR_APPLY');
					JToolBarHelper::save('ingredient.save', 'JTOOLBAR_SAVE');
					// [Interpretation 20995] We can save this record, but check the create permission to see
					// [Interpretation 20997] if we can return to make a new one.
					if ($this->canDo->get('core.create'))
					{
						JToolBarHelper::custom('ingredient.save2new', 'save-new.png', 'save-new_f2.png', 'JTOOLBAR_SAVE_AND_NEW', false);
					}
				}
				$canVersion = ($this->canDo->get('core.version') && $this->canDo->get('ingredient.version'));
				if ($this->state->params->get('save_history', 1) && $this->canDo->get('core.edit') && $canVersion)
				{
					JToolbarHelper::versions('com_recipemanager.ingredient', $this->item->id);
				}
				if ($this->canDo->get('core.create'))
				{
					JToolBarHelper::custom('ingredient.save2copy', 'save-copy.png', 'save-copy_f2.png', 'JTOOLBAR_SAVE_AS_COPY', false);
				}
				JToolBarHelper::cancel('ingredient.cancel', 'JTOOLBAR_CLOSE');
			}
		}
		JToolbarHelper::divider();
		// [Interpretation 21102] set help url for this view if found
		$help_url = RecipemanagerHelper::getHelpUrl('ingredient');
		if (RecipemanagerHelper::checkString($help_url))
		{
			JToolbarHelper::help('COM_RECIPEMANAGER_HELP_MANAGER', false, $help_url);
		}
	}

	/**
	 * Escapes a value for output in a view script.
	 *
	 * @param   mixed  $var  The output to escape.
	 *
	 * @return  mixed  The escaped value.
	 */
	public function escape($var)
	{
		if(strlen($var) > 30)
		{
    		// use the helper htmlEscape method instead and shorten the string
			return RecipemanagerHelper::htmlEscape($var, $this->_charset, true, 30);
		}
		// use the helper htmlEscape method instead.
		return RecipemanagerHelper::htmlEscape($var, $this->_charset);
	}

	/**
	 * Method to set up the document properties
	 *
	 * @return void
	 */
	protected function setDocument()
	{
		$isNew = ($this->item->id < 1);
		if (!isset($this->document))
		{
			$this->document = JFactory::getDocument();
		}
		$this->document->setTitle(JText::_($isNew ? 'COM_RECIPEMANAGER_INGREDIENT_NEW' : 'COM_RECIPEMANAGER_INGREDIENT_EDIT'));
		$this->document->addStyleSheet(JURI::root() . "administrator/components/com_recipemanager/assets/css/ingredient.css", (RecipemanagerHelper::jVersion()->isCompatible('3.8.0')) ? array('version' => 'auto') : 'text/css');
		$this->document->addScript(JURI::root() . $this->script, (RecipemanagerHelper::jVersion()->isCompatible('3.8.0')) ? array('version' => 'auto') : 'text/javascript');
		$this->document->addScript(JURI::root() . "administrator/components/com_recipemanager/views/ingredient/submitbutton.js", (RecipemanagerHelper::jVersion()->isCompatible('3.8.0')) ? array('version' => 'auto') : 'text/javascript'); 
		JText::script('view not acceptable. Error');
	}
}
