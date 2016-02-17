<?php

/** @var $l OC_L10N */
/** @var $theme OC_Defaults */
/** @var $_ array */
script('activity', 'script');
style('activity', 'style');
?>

<?php $_['appNavigation']->printPage(); ?>

<div id="app-content">
	<div id="emptycontent" class="hidden">
		<div class="icon-activity"></div>
		<h2><?php p($l->t('No activity yet')); ?></h2>
		<p><?php p($l->t('This stream will show events like additions, changes & shares')); ?></p>
	</div>

	<div id="container" data-activity-filter="<?php p($_['filter']) ?>">
	</div>

	<div id="loading_activities" class="icon-loading"></div>

	<div id="no_more_activities" class="hidden">
		<?php p($l->t('No more events to load')) ?>
	</div>
</div>
