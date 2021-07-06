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

use Joomla\CMS\Uri\Uri;
use Organizer\Adapters;
use Organizer\Helpers;

/**
 * Class loads the schedule export filter form into the display context.
 */
class ScheduleExport extends SelectionView
{
	/**
	 * Modifies document variables and adds links to external files
	 *
	 * @return void
	 */
	protected function modifyDocument()
	{
		parent::modifyDocument();

		$user = Helpers\Users::getUser();

		if ($user->id)
		{
			// Joomla documented the wrong type for registerDate which is a string
			/** @noinspection PhpToStringImplementationInspection */
			$auth = urlencode(password_hash($user->email . $user->registerDate, PASSWORD_BCRYPT));
			Adapters\Document::addScriptDeclaration("const username = '$user->username', auth = '$auth';");
		}

		Adapters\Document::addScript(Uri::root() . 'components/com_organizer/js/schedule_export.js');
	}

	/**
	 * Sets form fields used to define the content of the exported schedule.
	 *
	 * @return void modifies the sets property
	 */
	private function setContentFields()
	{
		$this->sets['content'] = ['label' => 'ORGANIZER_CONTENT_SETTINGS'];

		$attribs = ['multiple' => 'multiple'];

		$this->setResourceField('group', 'content', $attribs);

		$userID      = Helpers\Users::getID();
		$showPersons = ($userID and (Helpers\Can::viewTheseOrganizations() or Helpers\Persons::getIDByUserID()));
		if ($showPersons)
		{
			$this->setResourceField('person', 'content', $attribs);
		}

		$this->setResourceField('room', 'content', $attribs);

	}

	/**
	 * Sets form fields used to define the way in which the schedule is displayed.
	 *
	 * @return void modifies the sets property
	 */
	private function setDisplayFields()
	{
		$this->sets['display'] = ['label' => 'ORGANIZER_DISPLAY_SETTINGS'];

		$formatAttributes = ['onChange' => 'setFormat();'];

		$titlesFormats = [
			'full'        => 'ORGANIZER_FULL_NAMES',
			'short'       => 'ORGANIZER_SHORT_NAMES',
			'abbreviated' => 'ORGANIZER_ABBREVIATIONS'
		];

		$this->setListField('titles', 'display', $titlesFormats, [], 'full');
		$this->setResourceField('grid', 'display', [], true);

		$date = '<input name="date" type="date" value="' . date('Y-m-d') . '">';
		$this->setField('date', 'display', 'ORGANIZER_DATE', $date);

		$intervals = [
			'day'      => 'ORGANIZER_DAY',
			'week'     => 'ORGANIZER_WEEK',
			'month'    => 'ORGANIZER_MONTH',
			'semester' => 'ORGANIZER_SEMESTER'
		];

		$this->setListField('interval', 'display', $intervals, $formatAttributes, 'week');
	}

	/**
	 * Sets form fields used to filter the resources available for selection.
	 *
	 * @return void modifies the sets property
	 */
	private function setFilterFields()
	{
		$this->sets['filters'] = ['label' => 'ORGANIZER_FILTERS'];

		$orgAttribs = ['onChange' => 'repopulateCategories();repopulateResources();'];
		$this->setResourceField('organization', 'filters', $orgAttribs, true);

		$categoryAttribs = ['multiple' => 'multiple', 'onChange' => 'repopulateResources();'];
		$this->setResourceField('category', 'filters', $categoryAttribs);
	}

	/**
	 * Sets form fields used to define the format used for the export.
	 *
	 * @return void modifies the sets property
	 */
	private function setFormatFields()
	{
		$this->sets['format'] = ['label' => 'ORGANIZER_FORMAT_SETTINGS'];
		$formatAttributes     = ['onChange' => 'setFormat();'];

		$fileTypes   = [
			'ics' => 'ORGANIZER_ICS_CALENDAR',
			'pdf' => 'ORGANIZER_PDF_DOCUMENT',
			'xls' => 'ORGANIZER_XLS_WORKBOOK'
		];
		$defaultType = $this->isSeeingImpaired() ? 'xls' : 'pdf';

		$this->setListField('format', 'format', $fileTypes, $formatAttributes, $defaultType);

		$pdfFormats = [
			'a3' => 'ORGANIZER_ICS_CALENDAR',
			'a4' => 'ORGANIZER_PDF_DOCUMENT'
		];

		$this->setListField('pdfFormat', 'format', $pdfFormats, $formatAttributes, 'a4');

		$grouping = [
			'none'       => 'ORGANIZER_NO_GROUPING',
			'byresource' => 'ORGANIZER_GROUPED_BY_RESOURCE'
		];

		$this->setListField('grouping', 'format', $grouping, [], 'none');

		$sheets = [
			'collected'  => 'ORGANIZER_ON_ONE_WORKSHEET',
			'individual' => 'ORGANIZER_ON_INDIVIDUAL_WORKSHEETS'
		];

		$this->setListField('xlsFormat', 'format', $sheets, [], 'collected');
	}

	/**
	 * Sets form fields used to define the content of the exported schedule.
	 *
	 * @return void modifies the sets property
	 */
	private function setPersonalFields()
	{
		$this->sets['personal'] = ['label' => 'ORGANIZER_MY_PLANS'];

		$myScheduleField = '<input type="checkbox" id="myschedule" onclick="toggleMySchedule();">';
		$this->setField('myschedule', 'personal', 'MY_SCHEDULE', $myScheduleField);

		if (Helpers\Persons::getIDByUserID())
		{
			$personField = '<input type="checkbox" id="myschedule" onclick="toggleMySchedule();">';
			$this->setField('myteachingschedule', 'personal', 'MY_TEACHING_SCHEDULE', $personField);
		}
	}

	/**
	 * Function to define field sets and fill sets with fields
	 *
	 * @return void sets the fields property
	 */
	protected function setSets()
	{
		$user = Helpers\Users::getUser();

		if (!empty($user->id))
		{
			$this->setPersonalFields();
		}
		$this->setFilterFields();
		$this->setContentFields();
		$this->setDisplayFields();
		$this->setFormatFields();
	}
}
