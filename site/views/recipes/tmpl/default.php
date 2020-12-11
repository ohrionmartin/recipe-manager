<?php
/*----------------------------------------------------------------------------------|  www.vdm.io  |----/
				Vast Development Method 
/-------------------------------------------------------------------------------------------------------/

	@version		1.0.0
	@build			11th December, 2020
	@created		5th July, 2020
	@package		Recipe Manager
	@subpackage		default.php
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


?>
<form action="<?php echo JRoute::_('index.php?option=com_recipemanager'); ?>" method="post" name="adminForm" id="adminForm">
<?php echo $this->toolbar->render(); ?>
<!--[JCBGUI.site_view.default.26.$$$$]-->
<div id="app">
<p>{{ message }}</p>
<button v-on:click="reverseMessage">Reverse Message</button>
<div class="container">
  <div class="row">
<?php foreach ($this->items as $item): ?>
       <div class="card-container">
  <div class="card u-clearfix">
    <div class="card-body">
      <span class="card-number card-circle subtle"><?php echo $item->preparing_time; ?></span>
      <span class="card-author subtle"><?php echo $item->categories_title; ?></span>
      <h2 class="card-title"><?php echo $item->name; ?></h2>
      <span class="card-description subtle"><?php echo $item->description; ?></span>
      <div class="card-read">Read</div>
      <span class="card-tag card-circle subtle">C</span>
    </div>
    <img src="<?php echo $item->image; ?>" alt="<?php echo $item->image; ?>" class="card-media" />
  </div>
  <div class="card-shadow"></div>
</div>

<?php endforeach; ?>
  </div>
</div>
</div>

<script>
var app = new Vue({
  el: '#app',
  data: {
    message: 'Hello Vue!'
  },
methods: {
reverseMessage: function () {
this.message = this.message.split(' ').reverse().join(' ')
}
}
})
</script>

<!--[/JCBGUI$$$$]-->


<?php if (isset($this->items) && isset($this->pagination) && isset($this->pagination->pagesTotal) && $this->pagination->pagesTotal > 1): ?>
	<div class="pagination">
		<?php if ($this->params->def('show_pagination_results', 1)) : ?>
			<p class="counter pull-right"> <?php echo $this->pagination->getPagesCounter(); ?> <?php echo $this->pagination->getLimitBox(); ?></p>
		<?php endif; ?>
		<?php echo $this->pagination->getPagesLinks(); ?>
	</div>
<?php endif; ?>
<input type="hidden" name="task" value="" />
<?php echo JHtml::_('form.token'); ?>
</form>
