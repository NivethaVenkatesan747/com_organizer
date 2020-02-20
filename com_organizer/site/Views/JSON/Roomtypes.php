<?php
/**
 * @package     Organizer
 * @extension   com_organizer
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2020 TH Mittelhessen
 * @license     GNU GPL v.3
 * @link        www.thm.de
 */

namespace Organizer\Views\JSON;

use Organizer\Helpers;

/**
 * Class answers dynamic (degree) program related queries
 */
class Roomtypes extends BaseView
{
	/**
	 * loads model data into view context
	 *
	 * @return void
	 */
	public function display()
	{
		$function = Helpers\Input::getTask();
		if (method_exists('Organizer\\Helpers\\Roomtypes', $function))
		{
			echo json_encode(Helpers\Roomtypes::$function(), JSON_UNESCAPED_UNICODE);
		}
		else
		{
			echo false;
		}
	}
}
