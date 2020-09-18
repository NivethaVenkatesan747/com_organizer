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
 * Class loads persistent information a filtered set of event categories into the display context.
 */
class Categories extends ListView
{
	protected $rowStructure = [
		'checkbox' => '',
		'name'     => 'link',
		'active'   => 'value',
		'program'  => 'link',
		'code'     => 'link'
	];

	/**
	 * Method to generate buttons for user interaction
	 *
	 * @return void
	 */
	protected function addToolBar()
	{
		Helpers\HTML::setTitle(Helpers\Languages::_('ORGANIZER_CATEGORIES'), 'list-2');
		$toolbar = Toolbar::getInstance();
		$toolbar->appendButton('Standard', 'edit', Helpers\Languages::_('ORGANIZER_EDIT'), 'categories.edit', true);
		$toolbar->appendButton(
			'Standard',
			'eye-open',
			Helpers\Languages::_('ORGANIZER_ACTIVATE'),
			'categories.activate',
			false
		);
		$toolbar->appendButton(
			'Standard',
			'eye-close',
			Helpers\Languages::_('ORGANIZER_DEACTIVATE'),
			'categories.deactivate',
			false
		);

		if (Helpers\Can::administrate())
		{
			/*$toolbar->appendButton(
				'Standard',
				'attachment',
				Helpers\Languages::_('ORGANIZER_MERGE'),
				'categories.mergeView',
				true
			);*/
		}
	}

	/**
	 * Function determines whether the user may access the view.
	 *
	 * @return void
	 */
	protected function authorize()
	{
		if (!Helpers\Users::getUser())
		{
			Helpers\OrganizerHelper::error(401);
		}
		if (!Helpers\Can::scheduleTheseOrganizations())
		{
			Helpers\OrganizerHelper::error(403);
		}
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
			'checkbox' => '',
			'name'     => Helpers\HTML::sort('DISPLAY_NAME', 'name', $direction, $ordering),
			'active'   => Helpers\Languages::_('ORGANIZER_ACTIVE'),
			'program'  => Helpers\Languages::_('ORGANIZER_PROGRAM'),
			'code'     => Helpers\HTML::sort('UNTIS_ID', 'code', $direction, $ordering)
		];

		$this->headers = $headers;
	}

	/**
	 * Processes the items in a manner specific to the view, so that a generalized  output in the layout can occur.
	 *
	 * @return void processes the class items property
	 */
	protected function structureItems()
	{
		$index           = 0;
		$link            = 'index.php?option=com_organizer&view=category_edit&id=';
		$structuredItems = [];

		foreach ($this->items as $item)
		{
			$tip          = $item->active ? 'ORGANIZER_CLICK_TO_DEACTIVATE' : 'ORGANIZER_CLICK_TO_ACTIVATE';
			$item->active = $this->getToggle('categories', $item->id, $item->active, $tip, 'active');

			$item->program           = Helpers\Categories::getName($item->id);
			$structuredItems[$index] = $this->structureItem($index, $item, $link . $item->id);
			$index++;
		}

		$this->items = $structuredItems;
	}
}
