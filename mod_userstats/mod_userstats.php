<?php
/**
 * @copyright 2021 Nicholas K. Dionysopoulos
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3 or later
 * @author    Nicholas K. Dionysopoulos
 */

defined('_JEXEC') || die;

use Joomla\CMS\Date\Date;
use Joomla\CMS\Helper\ModuleHelper;
use Joomla\Registry\Registry;

/**
 * @var Registry $params Module parameters
 * @var stdClass $module Module instance information
 */

// Include the helper functions only once
JLoader::register('ModUserStatsHelper', __DIR__ . '/helper.php');

/**
 * @var Date $nominalDate
 * @var Date $nextMonth
 * @var Date $previousMonth
 */
$moduleclass_sfx = htmlspecialchars($params->get('moduleclass_sfx'), ENT_COMPAT, 'UTF-8');
$dates           = ModUserStatsHelper::getDates();
$stats           = ModUserStatsHelper::getStats();
extract($dates);
unset($dates);

require ModuleHelper::getLayoutPath('mod_userstats', $params->get('layout', 'table'));