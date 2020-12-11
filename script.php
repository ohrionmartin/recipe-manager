<?php
/*----------------------------------------------------------------------------------|  www.vdm.io  |----/
				Vast Development Method 
/-------------------------------------------------------------------------------------------------------/

	@version		1.0.0
	@build			11th December, 2020
	@created		5th July, 2020
	@package		Recipe Manager
	@subpackage		script.php
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

JHTML::_('behavior.modal');

/**
 * Script File of Recipemanager Component
 */
class com_recipemanagerInstallerScript
{
	/**
	 * Constructor
	 *
	 * @param   JAdapterInstance  $parent  The object responsible for running this script
	 */
	public function __construct(JAdapterInstance $parent) {}

	/**
	 * Called on installation
	 *
	 * @param   JAdapterInstance  $parent  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function install(JAdapterInstance $parent) {}

	/**
	 * Called on uninstallation
	 *
	 * @param   JAdapterInstance  $parent  The object responsible for running this script
	 */
	public function uninstall(JAdapterInstance $parent)
	{
		// [Interpretation 8043] Get Application object
		$app = JFactory::getApplication();

		// [Interpretation 8048] Get The Database object
		$db = JFactory::getDbo();

		// [Interpretation 8255] Create a new query object.
		$query = $db->getQuery(true);
		// [Interpretation 8259] Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// [Interpretation 8266] Where Ingredient alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_recipemanager.ingredient') );
		$db->setQuery($query);
		// [Interpretation 8273] Execute query to see if alias is found
		$db->execute();
		$ingredient_found = $db->getNumRows();
		// [Interpretation 8279] Now check if there were any rows
		if ($ingredient_found)
		{
			// [Interpretation 8285] Since there are load the needed  ingredient type ids
			$ingredient_ids = $db->loadColumn();
			// [Interpretation 8293] Remove Ingredient from the content type table
			$ingredient_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_recipemanager.ingredient') );
			// [Interpretation 8300] Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($ingredient_condition);
			$db->setQuery($query);
			// [Interpretation 8310] Execute the query to remove Ingredient items
			$ingredient_done = $db->execute();
			if ($ingredient_done)
			{
				// [Interpretation 8318] If successfully remove Ingredient add queued success message.
				$app->enqueueMessage(JText::_('The (com_recipemanager.ingredient) type alias was removed from the <b>#__content_type</b> table'));
			}

			// [Interpretation 8329] Remove Ingredient items from the contentitem tag map table
			$ingredient_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_recipemanager.ingredient') );
			// [Interpretation 8335] Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($ingredient_condition);
			$db->setQuery($query);
			// [Interpretation 8345] Execute the query to remove Ingredient items
			$ingredient_done = $db->execute();
			if ($ingredient_done)
			{
				// [Interpretation 8353] If successfully remove Ingredient add queued success message.
				$app->enqueueMessage(JText::_('The (com_recipemanager.ingredient) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// [Interpretation 8364] Remove Ingredient items from the ucm content table
			$ingredient_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_recipemanager.ingredient') );
			// [Interpretation 8370] Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($ingredient_condition);
			$db->setQuery($query);
			// [Interpretation 8380] Execute the query to remove Ingredient items
			$ingredient_done = $db->execute();
			if ($ingredient_done)
			{
				// [Interpretation 8388] If successfully removed Ingredient add queued success message.
				$app->enqueueMessage(JText::_('The (com_recipemanager.ingredient) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// [Interpretation 8399] Make sure that all the Ingredient items are cleared from DB
			foreach ($ingredient_ids as $ingredient_id)
			{
				// [Interpretation 8407] Remove Ingredient items from the ucm base table
				$ingredient_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $ingredient_id);
				// [Interpretation 8414] Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($ingredient_condition);
				$db->setQuery($query);
				// [Interpretation 8424] Execute the query to remove Ingredient items
				$db->execute();

				// [Interpretation 8431] Remove Ingredient items from the ucm history table
				$ingredient_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $ingredient_id);
				// [Interpretation 8437] Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($ingredient_condition);
				$db->setQuery($query);
				// [Interpretation 8447] Execute the query to remove Ingredient items
				$db->execute();
			}
		}

		// [Interpretation 8255] Create a new query object.
		$query = $db->getQuery(true);
		// [Interpretation 8259] Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// [Interpretation 8266] Where Recipe alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_recipemanager.recipe') );
		$db->setQuery($query);
		// [Interpretation 8273] Execute query to see if alias is found
		$db->execute();
		$recipe_found = $db->getNumRows();
		// [Interpretation 8279] Now check if there were any rows
		if ($recipe_found)
		{
			// [Interpretation 8285] Since there are load the needed  recipe type ids
			$recipe_ids = $db->loadColumn();
			// [Interpretation 8293] Remove Recipe from the content type table
			$recipe_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_recipemanager.recipe') );
			// [Interpretation 8300] Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($recipe_condition);
			$db->setQuery($query);
			// [Interpretation 8310] Execute the query to remove Recipe items
			$recipe_done = $db->execute();
			if ($recipe_done)
			{
				// [Interpretation 8318] If successfully remove Recipe add queued success message.
				$app->enqueueMessage(JText::_('The (com_recipemanager.recipe) type alias was removed from the <b>#__content_type</b> table'));
			}

			// [Interpretation 8329] Remove Recipe items from the contentitem tag map table
			$recipe_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_recipemanager.recipe') );
			// [Interpretation 8335] Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($recipe_condition);
			$db->setQuery($query);
			// [Interpretation 8345] Execute the query to remove Recipe items
			$recipe_done = $db->execute();
			if ($recipe_done)
			{
				// [Interpretation 8353] If successfully remove Recipe add queued success message.
				$app->enqueueMessage(JText::_('The (com_recipemanager.recipe) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// [Interpretation 8364] Remove Recipe items from the ucm content table
			$recipe_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_recipemanager.recipe') );
			// [Interpretation 8370] Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($recipe_condition);
			$db->setQuery($query);
			// [Interpretation 8380] Execute the query to remove Recipe items
			$recipe_done = $db->execute();
			if ($recipe_done)
			{
				// [Interpretation 8388] If successfully removed Recipe add queued success message.
				$app->enqueueMessage(JText::_('The (com_recipemanager.recipe) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// [Interpretation 8399] Make sure that all the Recipe items are cleared from DB
			foreach ($recipe_ids as $recipe_id)
			{
				// [Interpretation 8407] Remove Recipe items from the ucm base table
				$recipe_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $recipe_id);
				// [Interpretation 8414] Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($recipe_condition);
				$db->setQuery($query);
				// [Interpretation 8424] Execute the query to remove Recipe items
				$db->execute();

				// [Interpretation 8431] Remove Recipe items from the ucm history table
				$recipe_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $recipe_id);
				// [Interpretation 8437] Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($recipe_condition);
				$db->setQuery($query);
				// [Interpretation 8447] Execute the query to remove Recipe items
				$db->execute();
			}
		}

		// [Interpretation 8255] Create a new query object.
		$query = $db->getQuery(true);
		// [Interpretation 8259] Select id from content type table
		$query->select($db->quoteName('type_id'));
		$query->from($db->quoteName('#__content_types'));
		// [Interpretation 8266] Where Recipe catid alias is found
		$query->where( $db->quoteName('type_alias') . ' = '. $db->quote('com_recipemanager.recipe.category') );
		$db->setQuery($query);
		// [Interpretation 8273] Execute query to see if alias is found
		$db->execute();
		$recipe_catid_found = $db->getNumRows();
		// [Interpretation 8279] Now check if there were any rows
		if ($recipe_catid_found)
		{
			// [Interpretation 8285] Since there are load the needed  recipe_catid type ids
			$recipe_catid_ids = $db->loadColumn();
			// [Interpretation 8293] Remove Recipe catid from the content type table
			$recipe_catid_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_recipemanager.recipe.category') );
			// [Interpretation 8300] Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__content_types'));
			$query->where($recipe_catid_condition);
			$db->setQuery($query);
			// [Interpretation 8310] Execute the query to remove Recipe catid items
			$recipe_catid_done = $db->execute();
			if ($recipe_catid_done)
			{
				// [Interpretation 8318] If successfully remove Recipe catid add queued success message.
				$app->enqueueMessage(JText::_('The (com_recipemanager.recipe.category) type alias was removed from the <b>#__content_type</b> table'));
			}

			// [Interpretation 8329] Remove Recipe catid items from the contentitem tag map table
			$recipe_catid_condition = array( $db->quoteName('type_alias') . ' = '. $db->quote('com_recipemanager.recipe.category') );
			// [Interpretation 8335] Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__contentitem_tag_map'));
			$query->where($recipe_catid_condition);
			$db->setQuery($query);
			// [Interpretation 8345] Execute the query to remove Recipe catid items
			$recipe_catid_done = $db->execute();
			if ($recipe_catid_done)
			{
				// [Interpretation 8353] If successfully remove Recipe catid add queued success message.
				$app->enqueueMessage(JText::_('The (com_recipemanager.recipe.category) type alias was removed from the <b>#__contentitem_tag_map</b> table'));
			}

			// [Interpretation 8364] Remove Recipe catid items from the ucm content table
			$recipe_catid_condition = array( $db->quoteName('core_type_alias') . ' = ' . $db->quote('com_recipemanager.recipe.category') );
			// [Interpretation 8370] Create a new query object.
			$query = $db->getQuery(true);
			$query->delete($db->quoteName('#__ucm_content'));
			$query->where($recipe_catid_condition);
			$db->setQuery($query);
			// [Interpretation 8380] Execute the query to remove Recipe catid items
			$recipe_catid_done = $db->execute();
			if ($recipe_catid_done)
			{
				// [Interpretation 8388] If successfully removed Recipe catid add queued success message.
				$app->enqueueMessage(JText::_('The (com_recipemanager.recipe.category) type alias was removed from the <b>#__ucm_content</b> table'));
			}

			// [Interpretation 8399] Make sure that all the Recipe catid items are cleared from DB
			foreach ($recipe_catid_ids as $recipe_catid_id)
			{
				// [Interpretation 8407] Remove Recipe catid items from the ucm base table
				$recipe_catid_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $recipe_catid_id);
				// [Interpretation 8414] Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_base'));
				$query->where($recipe_catid_condition);
				$db->setQuery($query);
				// [Interpretation 8424] Execute the query to remove Recipe catid items
				$db->execute();

				// [Interpretation 8431] Remove Recipe catid items from the ucm history table
				$recipe_catid_condition = array( $db->quoteName('ucm_type_id') . ' = ' . $recipe_catid_id);
				// [Interpretation 8437] Create a new query object.
				$query = $db->getQuery(true);
				$query->delete($db->quoteName('#__ucm_history'));
				$query->where($recipe_catid_condition);
				$db->setQuery($query);
				// [Interpretation 8447] Execute the query to remove Recipe catid items
				$db->execute();
			}
		}

		// [Interpretation 8458] If All related items was removed queued success message.
		$app->enqueueMessage(JText::_('All related items was removed from the <b>#__ucm_base</b> table'));
		$app->enqueueMessage(JText::_('All related items was removed from the <b>#__ucm_history</b> table'));

		// [Interpretation 8467] Remove recipemanager assets from the assets table
		$recipemanager_condition = array( $db->quoteName('name') . ' LIKE ' . $db->quote('com_recipemanager%') );

		// [Interpretation 8473] Create a new query object.
		$query = $db->getQuery(true);
		$query->delete($db->quoteName('#__assets'));
		$query->where($recipemanager_condition);
		$db->setQuery($query);
		$recipe_catid_done = $db->execute();
		if ($recipe_catid_done)
		{
			// [Interpretation 8486] If successfully removed recipemanager add queued success message.
			$app->enqueueMessage(JText::_('All related items was removed from the <b>#__assets</b> table'));
		}

		// little notice as after service, in case of bad experience with component.
		echo '<h2>Did something go wrong? Are you disappointed?</h2>
		<p>Please let me know at <a href="mailto:oh.martin@vdm.io">oh.martin@vdm.io</a>.
		<br />We at Vast Development Method are committed to building extensions that performs proficiently! You can help us, really!
		<br />Send me your thoughts on improvements that is needed, trust me, I will be very grateful!
		<br />Visit us at <a href="https://www.vdm.io" target="_blank">https://www.vdm.io</a> today!</p>';
	}

	/**
	 * Called on update
	 *
	 * @param   JAdapterInstance  $parent  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function update(JAdapterInstance $parent){}

	/**
	 * Called before any type of action
	 *
	 * @param   string  $type  Which action is happening (install|uninstall|discover_install|update)
	 * @param   JAdapterInstance  $parent  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function preflight($type, JAdapterInstance $parent)
	{
		// get application
		$app = JFactory::getApplication();
		// is redundant or so it seems ...hmmm let me know if it works again
		if ($type === 'uninstall')
		{
			return true;
		}
		// the default for both install and update
		$jversion = new JVersion();
		if (!$jversion->isCompatible('3.8.0'))
		{
			$app->enqueueMessage('Please upgrade to at least Joomla! 3.8.0 before continuing!', 'error');
			return false;
		}
		// do any updates needed
		if ($type === 'update')
		{
		}
		// do any install needed
		if ($type === 'install')
		{
		}
		// check if the PHPExcel stuff is still around
		if (JFile::exists(JPATH_ADMINISTRATOR . '/components/com_recipemanager/helpers/PHPExcel.php'))
		{
			// We need to remove this old PHPExcel folder
			$this->removeFolder(JPATH_ADMINISTRATOR . '/components/com_recipemanager/helpers/PHPExcel');
			// We need to remove this old PHPExcel file
			JFile::delete(JPATH_ADMINISTRATOR . '/components/com_recipemanager/helpers/PHPExcel.php');
		}
		return true;
	}

	/**
	 * Called after any type of action
	 *
	 * @param   string  $type  Which action is happening (install|uninstall|discover_install|update)
	 * @param   JAdapterInstance  $parent  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 */
	public function postflight($type, JAdapterInstance $parent)
	{
		// get application
		$app = JFactory::getApplication();
		// [Interpretation 8670] We check if we have dynamic folders to copy
		$this->setDynamicF0ld3rs($app, $parent);
		// set the default component settings
		if ($type === 'install')
		{

			// [Interpretation 7801] Get The Database object
			$db = JFactory::getDbo();

			// [Interpretation 7810] Create the ingredient content type object.
			$ingredient = new stdClass();
			$ingredient->type_title = 'Recipemanager Ingredient';
			$ingredient->type_alias = 'com_recipemanager.ingredient';
			$ingredient->table = '{"special": {"dbtable": "#__recipemanager_ingredient","key": "id","type": "Ingredient","prefix": "recipemanagerTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$ingredient->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","unit":"unit","image":"image"}}';
			$ingredient->router = 'RecipemanagerHelperRoute::getIngredientRoute';
			$ingredient->content_history_options = '{"formFile": "administrator/components/com_recipemanager/models/forms/ingredient.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// [Interpretation 7843] Set the object into the content types table.
			$ingredient_Inserted = $db->insertObject('#__content_types', $ingredient);

			// [Interpretation 7810] Create the recipe content type object.
			$recipe = new stdClass();
			$recipe->type_title = 'Recipemanager Recipe';
			$recipe->type_alias = 'com_recipemanager.recipe';
			$recipe->table = '{"special": {"dbtable": "#__recipemanager_recipe","key": "id","type": "Recipe","prefix": "recipemanagerTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$recipe->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "catid","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","alias":"alias","preparing_time":"preparing_time","image":"image","description":"description"}}';
			$recipe->router = 'RecipemanagerHelperRoute::getRecipeRoute';
			$recipe->content_history_options = '{"formFile": "administrator/components/com_recipemanager/models/forms/recipe.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","catid","preparing_time"],"displayLookup": [{"sourceColumn": "catid","targetTable": "#__categories","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// [Interpretation 7843] Set the object into the content types table.
			$recipe_Inserted = $db->insertObject('#__content_types', $recipe);

			// [Interpretation 7810] Create the recipe category content type object.
			$recipe_category = new stdClass();
			$recipe_category->type_title = 'Recipemanager Recipe Catid';
			$recipe_category->type_alias = 'com_recipemanager.recipe.category';
			$recipe_category->table = '{"special":{"dbtable":"#__categories","key":"id","type":"Category","prefix":"JTable","config":"array()"},"common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}';
			$recipe_category->field_mappings = '{"common":{"core_content_item_id":"id","core_title":"title","core_state":"published","core_alias":"alias","core_created_time":"created_time","core_modified_time":"modified_time","core_body":"description", "core_hits":"hits","core_publish_up":"null","core_publish_down":"null","core_access":"access", "core_params":"params", "core_featured":"null", "core_metadata":"metadata", "core_language":"language", "core_images":"null", "core_urls":"null", "core_version":"version", "core_ordering":"null", "core_metakey":"metakey", "core_metadesc":"metadesc", "core_catid":"parent_id", "core_xreference":"null", "asset_id":"asset_id"}, "special":{"parent_id":"parent_id","lft":"lft","rgt":"rgt","level":"level","path":"path","extension":"extension","note":"note"}}';
			$recipe_category->router = 'RecipemanagerHelperRoute::getCategoryRoute';
			$recipe_category->content_history_options = '{"formFile":"administrator\/components\/com_categories\/models\/forms\/category.xml", "hideFields":["asset_id","checked_out","checked_out_time","version","lft","rgt","level","path","extension"], "ignoreChanges":["modified_user_id", "modified_time", "checked_out", "checked_out_time", "version", "hits", "path"],"convertToInt":["publish_up", "publish_down"], "displayLookup":[{"sourceColumn":"created_user_id","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"modified_user_id","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"parent_id","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"}]}';

			// [Interpretation 7843] Set the object into the content types table.
			$recipe_category_Inserted = $db->insertObject('#__content_types', $recipe_category);


			// [Interpretation 7934] Install the global extenstion params.
			$query = $db->getQuery(true);
			// [Interpretation 7947] Field to update.
			$fields = array(
				$db->quoteName('params') . ' = ' . $db->quote('{"autorName":"Oh Martin","autorEmail":"oh.martin@vdm.io","check_in":"-1 day","save_history":"1","history_limit":"10"}'),
			);
			// [Interpretation 7954] Condition.
			$conditions = array(
				$db->quoteName('element') . ' = ' . $db->quote('com_recipemanager')
			);
			$query->update($db->quoteName('#__extensions'))->set($fields)->where($conditions);
			$db->setQuery($query);
			$allDone = $db->execute();

			echo '<a target="_blank" href="https://www.vdm.io" title="Recipe Manager">
				<img src="components/com_recipemanager/assets/images/vdm-component.png"/>
				</a>';
		}
		// do any updates needed
		if ($type === 'update')
		{

			// [Interpretation 7801] Get The Database object
			$db = JFactory::getDbo();

			// [Interpretation 7810] Create the ingredient content type object.
			$ingredient = new stdClass();
			$ingredient->type_title = 'Recipemanager Ingredient';
			$ingredient->type_alias = 'com_recipemanager.ingredient';
			$ingredient->table = '{"special": {"dbtable": "#__recipemanager_ingredient","key": "id","type": "Ingredient","prefix": "recipemanagerTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$ingredient->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "null","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "null","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","unit":"unit","image":"image"}}';
			$ingredient->router = 'RecipemanagerHelperRoute::getIngredientRoute';
			$ingredient->content_history_options = '{"formFile": "administrator/components/com_recipemanager/models/forms/ingredient.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering"],"displayLookup": [{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// [Interpretation 7823] Check if ingredient type is already in content_type DB.
			$ingredient_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($ingredient->type_alias));
			$db->setQuery($query);
			$db->execute();

			// [Interpretation 7843] Set the object into the content types table.
			if ($db->getNumRows())
			{
				$ingredient->type_id = $db->loadResult();
				$ingredient_Updated = $db->updateObject('#__content_types', $ingredient, 'type_id');
			}
			else
			{
				$ingredient_Inserted = $db->insertObject('#__content_types', $ingredient);
			}

			// [Interpretation 7810] Create the recipe content type object.
			$recipe = new stdClass();
			$recipe->type_title = 'Recipemanager Recipe';
			$recipe->type_alias = 'com_recipemanager.recipe';
			$recipe->table = '{"special": {"dbtable": "#__recipemanager_recipe","key": "id","type": "Recipe","prefix": "recipemanagerTable","config": "array()"},"common": {"dbtable": "#__ucm_content","key": "ucm_id","type": "Corecontent","prefix": "JTable","config": "array()"}}';
			$recipe->field_mappings = '{"common": {"core_content_item_id": "id","core_title": "name","core_state": "published","core_alias": "alias","core_created_time": "created","core_modified_time": "modified","core_body": "null","core_hits": "hits","core_publish_up": "null","core_publish_down": "null","core_access": "access","core_params": "params","core_featured": "null","core_metadata": "null","core_language": "null","core_images": "null","core_urls": "null","core_version": "version","core_ordering": "ordering","core_metakey": "null","core_metadesc": "null","core_catid": "catid","core_xreference": "null","asset_id": "asset_id"},"special": {"name":"name","alias":"alias","preparing_time":"preparing_time","image":"image","description":"description"}}';
			$recipe->router = 'RecipemanagerHelperRoute::getRecipeRoute';
			$recipe->content_history_options = '{"formFile": "administrator/components/com_recipemanager/models/forms/recipe.xml","hideFields": ["asset_id","checked_out","checked_out_time","version"],"ignoreChanges": ["modified_by","modified","checked_out","checked_out_time","version","hits"],"convertToInt": ["published","ordering","catid","preparing_time"],"displayLookup": [{"sourceColumn": "catid","targetTable": "#__categories","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "created_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"},{"sourceColumn": "access","targetTable": "#__viewlevels","targetColumn": "id","displayColumn": "title"},{"sourceColumn": "modified_by","targetTable": "#__users","targetColumn": "id","displayColumn": "name"}]}';

			// [Interpretation 7823] Check if recipe type is already in content_type DB.
			$recipe_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($recipe->type_alias));
			$db->setQuery($query);
			$db->execute();

			// [Interpretation 7843] Set the object into the content types table.
			if ($db->getNumRows())
			{
				$recipe->type_id = $db->loadResult();
				$recipe_Updated = $db->updateObject('#__content_types', $recipe, 'type_id');
			}
			else
			{
				$recipe_Inserted = $db->insertObject('#__content_types', $recipe);
			}

			// [Interpretation 7810] Create the recipe category content type object.
			$recipe_category = new stdClass();
			$recipe_category->type_title = 'Recipemanager Recipe Catid';
			$recipe_category->type_alias = 'com_recipemanager.recipe.category';
			$recipe_category->table = '{"special":{"dbtable":"#__categories","key":"id","type":"Category","prefix":"JTable","config":"array()"},"common":{"dbtable":"#__ucm_content","key":"ucm_id","type":"Corecontent","prefix":"JTable","config":"array()"}}';
			$recipe_category->field_mappings = '{"common":{"core_content_item_id":"id","core_title":"title","core_state":"published","core_alias":"alias","core_created_time":"created_time","core_modified_time":"modified_time","core_body":"description", "core_hits":"hits","core_publish_up":"null","core_publish_down":"null","core_access":"access", "core_params":"params", "core_featured":"null", "core_metadata":"metadata", "core_language":"language", "core_images":"null", "core_urls":"null", "core_version":"version", "core_ordering":"null", "core_metakey":"metakey", "core_metadesc":"metadesc", "core_catid":"parent_id", "core_xreference":"null", "asset_id":"asset_id"}, "special":{"parent_id":"parent_id","lft":"lft","rgt":"rgt","level":"level","path":"path","extension":"extension","note":"note"}}';
			$recipe_category->router = 'RecipemanagerHelperRoute::getCategoryRoute';
			$recipe_category->content_history_options = '{"formFile":"administrator\/components\/com_categories\/models\/forms\/category.xml", "hideFields":["asset_id","checked_out","checked_out_time","version","lft","rgt","level","path","extension"], "ignoreChanges":["modified_user_id", "modified_time", "checked_out", "checked_out_time", "version", "hits", "path"],"convertToInt":["publish_up", "publish_down"], "displayLookup":[{"sourceColumn":"created_user_id","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"modified_user_id","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"parent_id","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"}]}';

			// [Interpretation 7823] Check if recipe category type is already in content_type DB.
			$recipe_category_id = null;
			$query = $db->getQuery(true);
			$query->select($db->quoteName(array('type_id')));
			$query->from($db->quoteName('#__content_types'));
			$query->where($db->quoteName('type_alias') . ' LIKE '. $db->quote($recipe_category->type_alias));
			$db->setQuery($query);
			$db->execute();

			// [Interpretation 7843] Set the object into the content types table.
			if ($db->getNumRows())
			{
				$recipe_category->type_id = $db->loadResult();
				$recipe_category_Updated = $db->updateObject('#__content_types', $recipe_category, 'type_id');
			}
			else
			{
				$recipe_category_Inserted = $db->insertObject('#__content_types', $recipe_category);
			}


			echo '<a target="_blank" href="https://www.vdm.io" title="Recipe Manager">
				<img src="components/com_recipemanager/assets/images/vdm-component.png"/>
				</a>
				<h3>Upgrade to Version 1.0.0 Was Successful! Let us know if anything is not working as expected.</h3>';
		}
		return true;
	}

	/**
	 * Remove folders with files
	 * 
	 * @param   string   $dir     The path to folder to remove
	 * @param   boolean  $ignore  The folders and files to ignore and not remove
	 *
	 * @return  boolean   True in all is removed
	 * 
	 */
	protected function removeFolder($dir, $ignore = false)
	{
		if (JFolder::exists($dir))
		{
			$it = new RecursiveDirectoryIterator($dir);
			$it = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
			// remove ending /
			$dir = rtrim($dir, '/');
			// now loop the files & folders
			foreach ($it as $file)
			{
				if ('.' === $file->getBasename() || '..' ===  $file->getBasename()) continue;
				// set file dir
				$file_dir = $file->getPathname();
				// check if this is a dir or a file
				if ($file->isDir())
				{
					$keeper = false;
					if ($this->checkArray($ignore))
					{
						foreach ($ignore as $keep)
						{
							if (strpos($file_dir, $dir.'/'.$keep) !== false)
							{
								$keeper = true;
							}
						}
					}
					if ($keeper)
					{
						continue;
					}
					JFolder::delete($file_dir);
				}
				else
				{
					$keeper = false;
					if ($this->checkArray($ignore))
					{
						foreach ($ignore as $keep)
						{
							if (strpos($file_dir, $dir.'/'.$keep) !== false)
							{
								$keeper = true;
							}
						}
					}
					if ($keeper)
					{
						continue;
					}
					JFile::delete($file_dir);
				}
			}
			// delete the root folder if not ignore found
			if (!$this->checkArray($ignore))
			{
				return JFolder::delete($dir);
			}
			return true;
		}
		return false;
	}

	/**
	 * Check if have an array with a length
	 *
	 * @input	array   The array to check
	 *
	 * @returns bool/int  number of items in array on success
	 */
	protected function checkArray($array, $removeEmptyString = false)
	{
		if (isset($array) && is_array($array) && ($nr = count((array)$array)) > 0)
		{
			// also make sure the empty strings are removed
			if ($removeEmptyString)
			{
				foreach ($array as $key => $string)
				{
					if (empty($string))
					{
						unset($array[$key]);
					}
				}
				return $this->checkArray($array, false);
			}
			return $nr;
		}
		return false;
	}

	/**
	 * Method to set/copy dynamic folders into place (use with caution)
	 *
	 * @return void
	 */
	protected function setDynamicF0ld3rs($app, $parent)
	{
		// [Interpretation 8697] get the instalation path
		$installer = $parent->getParent();
		$installPath = $installer->getPath('source');
		// [Interpretation 8702] get all the folders
		$folders = JFolder::folders($installPath);
		// [Interpretation 8706] check if we have folders we may want to copy
		$doNotCopy = array('media','admin','site'); // Joomla already deals with these
		if (count((array) $folders) > 1)
		{
			foreach ($folders as $folder)
			{
				// [Interpretation 8714] Only copy if not a standard folders
				if (!in_array($folder, $doNotCopy))
				{
					// [Interpretation 8718] set the source path
					$src = $installPath.'/'.$folder;
					// [Interpretation 8721] set the destination path
					$dest = JPATH_ROOT.'/'.$folder;
					// [Interpretation 8724] now try to copy the folder
					if (!JFolder::copy($src, $dest, '', true))
					{
						$app->enqueueMessage('Could not copy '.$folder.' folder into place, please make sure destination is writable!', 'error');
					}
				}
			}
		}
	}
}
