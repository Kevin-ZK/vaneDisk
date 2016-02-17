<?php


/** @var $l OC_L10N */
/** @var $theme OC_Defaults */
/** @var $_ array */
/** @var $displayHelper \OCA\Activity\Display */
$displayHelper = $_['displayHelper'];

$lastDate = null;
foreach ($_['activity'] as $event) {
	// group by date
	// TODO: use more efficient way to group by date (don't group by localized string...)
	$currentDate = (string)(\OCP\relative_modified_date($event['timestamp'], true));

	// new date group
	if ($currentDate !== $lastDate) {
		// not first date group ?
		if ($lastDate !== null) {
?>
	</div>
</div>

<?php
		}
		$lastDate = $currentDate;
?>
<div class="section activity-section group" data-date="<?php p($currentDate) ?>">
	<h2>
		<span class="tooltip" title="<?php p(\OCP\Util::formatDate(strip_time($event['timestamp']), true)) ?>">
			<?php p(ucfirst($currentDate)) ?>
		</span>
	</h2>
	<div class="boxcontainer">
<?php
	}
	print_unescaped($displayHelper->show($event));
}
if (!empty($_['activity'])): ?>
	</div>
</div>
<?php endif;
