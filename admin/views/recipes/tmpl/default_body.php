<?php
/*----------------------------------------------------------------------------------|  www.vdm.io  |----/
				Vast Development Method 
/-------------------------------------------------------------------------------------------------------/

	@version		1.0.2
	@build			14th December, 2020
	@created		5th July, 2020
	@package		Recipe Manager
	@subpackage		default_body.php
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

$edit = "index.php?option=com_recipe_manager&view=recipes&task=recipe.edit";

?>
<?php foreach ($this->items as $i => $item): ?>
	<?php
		$canCheckin = $this->user->authorise('core.manage', 'com_checkin') || $item->checked_out == $this->user->id || $item->checked_out == 0;
		$userChkOut = JFactory::getUser($item->checked_out);
		$canDo = Recipe_managerHelper::getActions('recipe',$item,'recipes');
	?>
	<tr class="row<?php echo $i % 2; ?>">
		<td class="order nowrap center hidden-phone">
		<?php if ($canDo->get('core.edit.state')): ?>
			<?php
				$iconClass = '';
				if (!$this->saveOrder)
				{
					$iconClass = ' inactive tip-top" hasTooltip" title="' . JHtml::tooltipText('JORDERINGDISABLED');
				}
			?>
			<span class="sortable-handler<?php echo $iconClass; ?>">
				<i class="icon-menu"></i>
			</span>
			<?php if ($this->saveOrder) : ?>
				<input type="text" style="display:none" name="order[]" size="5"
				value="<?php echo $item->ordering; ?>" class="width-20 text-area-order " />
			<?php endif; ?>
		<?php else: ?>
			&#8942;
		<?php endif; ?>
		</td>
		<td class="nowrap center">
		<?php if ($canDo->get('core.edit')): ?>
				<?php if ($item->checked_out) : ?>
					<?php if ($canCheckin) : ?>
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
					<?php else: ?>
						&#9633;
					<?php endif; ?>
				<?php else: ?>
					<?php echo JHtml::_('grid.id', $i, $item->id); ?>
				<?php endif; ?>
		<?php else: ?>
			&#9633;
		<?php endif; ?>
		</td>
		<td class="nowrap">
			<div class="name">
				<?php if ($canDo->get('core.edit')): ?>
					<a href="<?php echo $edit; ?>&id=<?php echo $item->id; ?>"><?php echo $this->escape($item->name); ?></a>
					<?php if ($item->checked_out): ?>
						<?php echo JHtml::_('jgrid.checkedout', $i, $userChkOut->name, $item->checked_out_time, 'recipes.', $canCheckin); ?>
					<?php endif; ?>
				<?php else: ?>
					<?php echo $this->escape($item->name); ?>
				<?php endif; ?>
			</div>
		</td>
		<td class="hidden-phone">
			<?php echo $this->escape($item->category_title); ?>
		</td>
		<td class="center">
		<?php if ($canDo->get('core.edit.state')) : ?>
				<?php if ($item->checked_out) : ?>
					<?php if ($canCheckin) : ?>
						<?php echo JHtml::_('jgrid.published', $item->published, $i, 'recipes.', true, 'cb'); ?>
					<?php else: ?>
						<?php echo JHtml::_('jgrid.published', $item->published, $i, 'recipes.', false, 'cb'); ?>
					<?php endif; ?>
				<?php else: ?>
					<?php echo JHtml::_('jgrid.published', $item->published, $i, 'recipes.', true, 'cb'); ?>
				<?php endif; ?>
		<?php else: ?>
			<?php echo JHtml::_('jgrid.published', $item->published, $i, 'recipes.', false, 'cb'); ?>
		<?php endif; ?>
		</td>
		<td class="nowrap center hidden-phone">
			<?php echo $item->id; ?>
		</td>
	</tr>
<?php endforeach; ?>