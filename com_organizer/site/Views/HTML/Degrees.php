<?php
/**
 * @package     Organizer
 * @extension   com_organizer
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2020 TH Mittelhessen
 * @license     GNU GPL v.3
 * @link        www.thm.de
 */

namespace Organizer\Views\HTML;

use Joomla\CMS\Toolbar\Toolbar;
use Organizer\Helpers;

/**
 * Class loads persistent information a filtered set of degrees into the display context.
 */
class Degrees extends ListView
{
	protected $rowStructure = ['checkbox' => '', 'name' => 'link', 'abbreviation' => 'link', 'code' => 'link'];

	/**
	 * Method to generate buttons for user interaction
	 *
	 * @return void
	 */
	protected function addToolBar()
	{
		Helpers\HTML::setTitle(Helpers\Languages::_('ORGANIZER_DEGREES'), 'info-cap');
		$toolbar = Toolbar::getInstance();
		$toolbar->appendButton('Standard', 'new', Helpers\Languages::_('ORGANIZER_ADD'), 'degrees.add', false);
		$toolbar->appendButton('Standard', 'edit', Helpers\Languages::_('ORGANIZER_EDIT'), 'degrees.edit', true);
		$toolbar->appendButton(
			'Confirm',
			Helpers\Languages::_('ORGANIZER_DELETE_CONFIRM'),
			'delete',
			Helpers\Languages::_('ORGANIZER_DELETE'),
			'degrees.delete',
			true
		);
	}

	/**
	 * Function determines whether the user may access the view.
	 *
	 * @return bool true if the use may access the view, otherwise false
	 */
	protected function allowAccess()
	{
		return Helpers\Can::administrate();
	}

	/**
	 * Function to set the object's headers property
	 *
	 * @return void sets the object headers property
	 */
	public function setHeaders()
	{
		$ordering  = $this->state->get('list.ordering');
		$direction = $this->state->get('list.direction');
		$headers   = [
			'checkbox'     => '',
			'name'         => Helpers\HTML::sort('NAME', 'name', $direction, $ordering),
			'abbreviation' => Helpers\HTML::sort('ABBREVIATION', 'abbreviation', $direction, $ordering),
			'code'         => Helpers\HTML::sort('DEGREE_CODE', 'code', $direction, $ordering)
		];

		$this->headers = $headers;
	}
}
