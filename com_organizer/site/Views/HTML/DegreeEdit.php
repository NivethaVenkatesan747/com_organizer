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
 * Class loads the degree form into display context.
 */
class DegreeEdit extends EditView
{
	/**
	 * Method to generate buttons for user interaction
	 *
	 * @return void
	 */
	protected function addToolBar()
	{
		$new   = empty($this->item->id);
		$title = $new ?
			Helpers\Languages::_('ORGANIZER_DEGREE_NEW') : Helpers\Languages::_('ORGANIZER_DEGREE_EDIT');
		Helpers\HTML::setTitle($title, 'info-cap');
		$toolbar   = Toolbar::getInstance();
		$applyText = $new ? Helpers\Languages::_('ORGANIZER_CREATE') : Helpers\Languages::_('ORGANIZER_APPLY');
		$toolbar->appendButton('Standard', 'apply', $applyText, 'degrees.apply', false);
		$toolbar->appendButton('Standard', 'save', Helpers\Languages::_('ORGANIZER_SAVE'), 'degrees.save', false);
		$cancelText = $new ? Helpers\Languages::_('ORGANIZER_CANCEL') : Helpers\Languages::_('ORGANIZER_CLOSE');
		$toolbar->appendButton('Standard', 'cancel', $cancelText, 'degrees.cancel', false);
	}
}
