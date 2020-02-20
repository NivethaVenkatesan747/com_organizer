<?php
/**
 * @package     Organizer
 * @extension   com_organizer
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2020 TH Mittelhessen
 * @license     GNU GPL v.3
 * @link        www.thm.de
 */

namespace Organizer\Tables;

use JDatabaseDriver;
use Organizer\Helpers;

/**
 * Models the organizer_holidays table.
 */
class Holidays extends BaseTable
{
	/**
	 * The end date of the resource.
	 * DATE DEFAULT NULL
	 *
	 * @var string
	 */
	public $endDate;

	/**
	 * The resource's German name.
	 * VARCHAR(150) NOT NULL
	 *
	 * @var string
	 */
	public $name_de;

	/**
	 * The resource's English name.
	 * VARCHAR(150) NOT NULL
	 *
	 * @var string
	 */
	public $name_en;

	/**
	 * The start date of the resource.
	 * DATE DEFAULT NULL
	 *
	 * @var string
	 */
	public $startDate;

	/**
	 * The impact of the holiday on the planning process. Values: 1 - Automatic, 2 - Manual, 3 - Unplannable
	 * TINYINT(1) UNSIGNED NOT NULL DEFAULT 3
	 *
	 * @var int
	 */
	public $type;

	/**
	 * Declares the associated table
	 *
	 * @param   JDatabaseDriver &$dbo  A database connector object
	 */
	public function __construct(&$dbo = null)
	{
		parent::__construct('#__organizer_holidays', 'id', $dbo);
	}

	/**
	 * Checks the start date and end date
	 *
	 * @return boolean true on success, otherwise false
	 */
	public function check()
	{
		if ($this->endDate < $this->startDate)
		{
			Helpers\OrganizerHelper::message('ORGANIZER_DATE_CHECK', 'error');

			return false;
		}

		return true;
	}
}