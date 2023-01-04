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

	<table class="table">
		<tbody>
		<?php foreach (['new', 'active'] as $type): ?>
			<tr>
				<th scope="rowgroup" rowspan="<?= max(count($stats[$rangeTitle][$type]), 1) + 1 ?>" width="25%">
					<h5>
						<?= Text::_('MOD_USERSTATS_HEAD_' . $type) ?>
					</h5>
				</th>
			</tr>
			<?php if (empty($stats[$rangeTitle][$type])): ?>
				<tr class="alert alert-warning">
					<td colspan="2">
						<?= Text::_('MOD_USERSTATS_LBL_NO_DATA') ?>
					</td>
				</tr>
			<?php endif; ?>

			<?php foreach ($stats[$rangeTitle][$type] as $groupTitle => $count): ?>
				<tr>
					<th scope="row">
						<?= $groupTitle ?>
					</th>
					<td>
						<?= $count ?>
					</td>
				</tr>
			<?php endforeach ?>
		<?php endforeach; ?>
		</tbody>
	</table>
<?php endforeach; ?>
