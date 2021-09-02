<?php
/**
 * @copyright 2021 Nicholas K. Dionysopoulos
 * @license   https://www.gnu.org/licenses/gpl-3.0.en.html GPLv3 or later
 * @author    Nicholas K. Dionysopoulos
 */

defined('_JEXEC') || die;

use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;

/**
 * Helper for collecting user statistics for display
 *
 * @since        1.0.0
 *
 * @noinspection PhpUnused
 */
final class ModUserStatsHelper
{
	public static function getDates(): array
	{
		$app     = Factory::getApplication();
		$session = $app->getSession();

		$nominalMonth  = $session->get('mod_userstats.month', date('M'));
		$nominalYear   = $session->get('mod_userstats.year', date('Y'));
		$nominalDate   = new Date(sprintf('%s-%s-1', $nominalYear, $nominalMonth));
		$nextMonth     = (clone $nominalDate)->add(new DateInterval('P1M'));
		$previousMonth = (clone $nominalDate)->sub(new DateInterval('P1M'));

		return [
			'nominalDate'   => $nominalDate,
			'nextMonth'     => $nextMonth,
			'previousMonth' => $previousMonth,
		];
	}

	/**
	 * Get the statistics for display
	 *
	 * @return array[]
	 *
	 * @throws Exception
	 * @since  1.0.0
	 * @internal
	 */
	public final static function getStats(): array
	{
		/**
		 * @var Date $nominalDate
		 * @var Date $nextMonth
		 * @var Date $previousMonth
		 */
		$dates = self::getDates();
		extract($dates);

		return [
			'current'  => [
				'new'    => self::getUserStats('registerDate', $nominalDate, $nextMonth),
				'active' => self::getUserStats('lastvisitDate', $nominalDate, $nextMonth),
			],
			'previous' => [
				'new'    => self::getUserStats('registerDate', $previousMonth, $nominalDate),
				'active' => self::getUserStats('lastvisitDate', $previousMonth, $nominalDate),
			],
		];
	}

	/**
	 * Set the month and year to serve as the baseline for the statistics.
	 *
	 * @param   int  $year   The year to set. 2001 to the current year.
	 * @param   int  $month  The month to set. 1—12, inclusive.
	 *
	 * @throws Exception Shouldn't happen (the Joomla application would need to be broken).
	 * @since  1.0.0
	 * @internal
	 */
	public final static function setNominalDate(int $year, int $month): void
	{
		// Make sure the year is between 2001 and the current year.
		if (($year > date('Y')) || ($year < 2001))
		{
			$year  = date('Y');
			$month = date('M');
		}

		// Make sure the month is in the range 1–12, inclusive.
		if (($month < 1) || ($month > 12))
		{
			$year  = date('Y');
			$month = date('M');
		}

		// Make sure the month and year are not in the future.

		try
		{
			$nominalDate = new Date(sprintf('%s-%s-1', $year, $month));

			if ($nominalDate->getTimestamp() > time())
			{
				$year  = date('Y');
				$month = date('M');
			}
		}
		catch (Exception $e)
		{
			$year  = date('Y');
			$month = date('M');
		}

		// Store the month and year in the session.
		$app     = Factory::getApplication();
		$session = $app->getSession();

		$session->set('mod_userstats.month', $month);
		$session->set('mod_userstats.year', $year);
	}

	/**
	 * @param   string  $column     The column in the #__users table to sort by date
	 * @param   Date    $startDate  Start date for the query
	 * @param   Date    $endDate    End date for the query
	 *
	 * @return  array  Number of users per usergroup in the form [GroupTitle => Count, …]
	 *
	 * @since  1.0.0
	 * @internal
	 */
	private final static function getUserStats(string $column, Date $startDate, Date $endDate): array
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select([
				'COUNT(*) AS ' . $db->quoteName('count'),
				$db->quoteName('g.id', 'gid'),
				$db->quoteName('g.title', 'title'),
			])
			->from($db->quoteName('#__users', 'u'))
			->leftJoin($db->quoteName('#__user_usergroup_map', 'ug') . ' ON (' .
				$db->quoteName('ug.user_id') . ' = ' . $db->quoteName('u.id')
				. ')')
			->leftJoin($db->quoteName('#__usergroups', 'g') . ' ON (' .
				$db->quoteName('g.id') . ' = ' . $db->quoteName('ug.group_id')
				. ')')
			->where(
				$db->quoteName($column) . ' BETWEEN ' . $db->quote($startDate->toSql()) .
				' AND ' . $db->quote($endDate->toSql())
			)
			->group($db->quoteName('g.id'))
			->order($db->quoteName('g.title'));

		try
		{
			return $db->setQuery($query)->loadAssocList('title', 'count') ?: [];
		}
		catch (Exception $e)
		{
			return [];
		}
	}
}