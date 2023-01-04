<?php
/**
 * @copyright 2021-2023 Nicholas K. Dionysopoulos
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3 or later
 * @author    Nicholas K. Dionysopoulos
 */

defined('_JEXEC') || die;

use Joomla\CMS\Date\Date;
use Joomla\CMS\Language\Text;

/**
 * Variables already set in the current scope by mod_userstats.php which included this file
 *
 * @var Date  $nominalDate
 * @var Date  $nextMonth
 * @var Date  $previousMonth
 * @var array $stats
 */

?>
<?php foreach (['current', 'previous'] as $rangeTitle): ?>
	<h3>
		<?= (($rangeTitle == 'current') ? $nominalDate : $previousMonth)->format('F Y') ?>
	</h3>

	<?php foreach (['new', 'active'] as $type): ?>
		<h5>
			<?= Text::_('MOD_USERSTATS_HEAD_' . $type) ?>
		</h5>
		<div class="row-striped">
			<?php if (empty($stats[$rangeTitle][$type])): ?>
			<div class="alert alert-warning">
				<?= Text::_('MOD_USERSTATS_LBL_NO_DATA') ?>
			</div>
			<?php endif; ?>
			<?php foreach ($stats[$rangeTitle][$type] as $groupTitle => $count): ?>
				<div class="row-fluid">
					<div class="span4">
						<?= $groupTitle ?>
					</div>
					<div class="span8">
						<?= $count ?>
					</div>
				</div>
			<?php endforeach ?>
		</div>
	<?php endforeach; ?>
<?php endforeach; ?>