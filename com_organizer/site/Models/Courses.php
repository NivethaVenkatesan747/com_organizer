<?php
/**
 * @package     Organizer
 * @extension   com_organizer
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2020 TH Mittelhessen
 * @license     GNU GPL v.3
 * @link        www.thm.de
 */

namespace Organizer\Models;

use JDatabaseQuery;
use Joomla\CMS\Form\Form;
use Organizer\Helpers;

/**
 * Class retrieves the data regarding a filtered set of courses.
 */
class Courses extends ListModel
{
	use Filtered;

	protected $defaultOrdering = 'name';

	protected $filter_fields = ['campusID', 'categoryID', 'organizationID', 'groupID', 'personID', 'termID'];

	/**
	 * Filters out form inputs which should not be displayed due to menu settings.
	 *
	 * @param   Form  $form  the form to be filtered
	 *
	 * @return void modifies $form
	 */
	protected function filterFilterForm(&$form)
	{
		parent::filterFilterForm($form);
		if ($this->clientContext === self::BACKEND)
		{
			return;
		}

		$params = Helpers\Input::getParams();
		if ($params->get('campusID'))
		{
			$form->removeField('campusID', 'filter');
		}

		if ($params->get('onlyPrepCourses'))
		{
			$form->removeField('categoryID', 'filter');
			$form->removeField('organizationID', 'filter');
			$form->removeField('groupID', 'filter');
			$form->removeField('personID', 'filter');
			$form->removeField('search', 'filter');
		}
		elseif (empty($this->state->get('filter.organizationID')))
		{
			$form->removeField('categoryID', 'filter');
			$form->removeField('personID', 'filter');
			$form->removeField('groupID', 'filter');
		}
		elseif (empty($this->state->get('filter.categoryID')))
		{
			$form->removeField('groupID', 'filter');
		}
	}

	/**
	 * Method to get a list of resources from the database.
	 *
	 * @return JDatabaseQuery
	 */
	protected function getListQuery()
	{
		$query = $this->_db->getQuery(true);
		$query->select('c.*')
			->from('#__organizer_courses AS c')
			->innerJoin('#__organizer_units AS u ON u.courseID = c.id')
			->innerJoin('#__organizer_instances AS i ON i.unitID = u.id')
			->innerJoin('#__organizer_events AS e ON e.id = i.eventID')
			->innerJoin('#__organizer_instance_persons AS ip ON ip.instanceID = i.id')
			->innerJoin('#__organizer_instance_groups AS ig ON ig.assocID = ip.id')
			->innerJoin('#__organizer_groups AS g ON g.id = ig.groupID')
			->innerJoin('#__organizer_associations AS a ON a.categoryID = g.categoryID')
			->where("u.delta != 'removed'")
			->where("i.delta != 'removed'")
			->group('c.id');

		if ($search = $this->state->get('filter.search', '') and preg_match('/^[\d]+$/', $search))
		{
			$query->where("c.id = $search");
		}
		else
		{
			$this->setSearchFilter($query, ['c.name_de', 'c.name_en', 'e.name_de', 'e.name_en']);
		}

		if ($this->clientContext === self::FRONTEND and Helpers\Input::getParams()->get('onlyPrepCourses'))
		{
			$termID = Helpers\Terms::getPreviousID($this->state->get('filter.termID'));
			$query->where("c.termID = $termID")->where('e.preparatory = 1');
		}
		else
		{
			$this->setValueFilters($query, ['c.termID']);
		}

		if ($this->state->get('filter.organizationID'))
		{
			$this->setValueFilters($query, ['g.categoryID', 'a.organizationID', 'ig.groupID', 'ip.personID']);
		}

		$this->addCampusFilter($query, 'c');

		return $query;
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * @param   string  $ordering   An optional ordering field.
	 * @param   string  $direction  An optional direction (asc|desc).
	 *
	 * @return void populates state properties
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		parent::populateState($ordering, $direction);
		$app = Helpers\OrganizerHelper::getApplication();

		if ($this->clientContext === self::FRONTEND)
		{
			$params = Helpers\Input::getParams();

			$campusID = $params->get('campusID', 0);
			if (!$campusID)
			{
				$campusID = $app->getUserStateFromRequest($this->context . '.filter.campusID', 'filter.campusID');
			}
			$this->state->set('filter.campusID', $campusID);

			$rTermID = $app->getUserStateFromRequest($this->context . '.filter.termID', 'filter.termID');
			if (!$rTermID and $params->get('onlyPrepCourses'))
			{
				$this->state->set('filter.termID', Helpers\Terms::getNextID());
			}
		}

		$organizationID = $app->getUserStateFromRequest($this->context . '.filter.organizationID',
			'filter.organizationID');
		$categoryID     = $app->getUserStateFromRequest($this->context . '.filter.categoryID', 'filter.categoryID');
		if (empty($organizationID))
		{
			$this->state->set('filter.categoryID', '');
			$this->state->set('filter.groupID', '');
			$this->state->set('filter.personID', '');
		}
		elseif (empty($categoryID))
		{
			$this->state->set('filter.groupID', '');
		}
	}
}
