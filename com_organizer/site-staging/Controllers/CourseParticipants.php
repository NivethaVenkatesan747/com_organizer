<?php
/**
 * @package     Organizer
 * @extension   com_organizer
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2020 TH Mittelhessen
 * @license     GNU GPL v.3
 * @link        www.thm.de
 */

namespace Organizer\Controllers;


use Exception;
use Joomla\CMS\Router\Route;
use Organizer\Helpers;
use Organizer\Helpers\Input; // Exception for frequency of use
use Organizer\Helpers\OrganizerHelper; // Exception for frequency of use
use Organizer\Models;

trait CourseParticipants
{
	/**
	 * Accepts the selected participants into the course.
	 *
	 * @return void
	 * @throws Exception
	 */
	public function accept()
	{
		$model = new Models\CourseParticipant;

		if ($model->accept())
		{
			OrganizerHelper::message('ORGANIZER_STATUS_CHANGE_SUCCESS');
		}
		else
		{
			OrganizerHelper::message('ORGANIZER_STATUS_CHANGE_FAIL', 'error');
		}

		$this->setRedirect(Input::getInput()->server->getString('HTTP_REFERER'));
	}

	/**
	 * Sends an circular email to all course participants
	 *
	 * @return void
	 * @throws Exception
	 */
	public function circular()
	{
		if (empty($this->getModel('course')->circular()))
		{
			OrganizerHelper::message('ORGANIZER_SEND_FAIL', 'error');
		}
		else
		{
			OrganizerHelper::message('ORGANIZER_SEND_SUCCESS', 'error');
		}

		$lessonID = $this->input->get('lessonID');
		$redirect = Helpers\Routing::getRedirectBase() . "view=courses&lessonID=$lessonID";
		$this->setRedirect(Route::_($redirect, false));
	}

	/**
	 * Changes the participant's course state.
	 *
	 * @return void
	 */
	public function changeState()
	{
		$model = new Models\CourseParticipant;

		if ($model->changeState())
		{
			OrganizerHelper::message('ORGANIZER_STATUS_CHANGE_SUCCESS');
		}
		else
		{
			OrganizerHelper::message('ORGANIZER_STATUS_CHANGE_FAIL', 'error');
		}

		$this->setRedirect(Input::getInput()->server->getString('HTTP_REFERER'));
	}

	/**
	 * Accepts the selected participants into the course.
	 *
	 * @return void
	 * @throws Exception
	 */
	public function confirmAttendance()
	{
		$model = new Models\CourseParticipant;

		if ($model->confirmAttendance())
		{
			OrganizerHelper::message('ORGANIZER_STATUS_CHANGE_SUCCESS');
		}
		else
		{
			OrganizerHelper::message('ORGANIZER_STATUS_CHANGE_FAIL', 'error');
		}

		$this->setRedirect(Input::getInput()->server->getString('HTTP_REFERER'));
	}

	/**
	 * Accepts the selected participants into the course.
	 *
	 * @return void
	 * @throws Exception
	 */
	public function confirmPayment()
	{
		$model = new Models\CourseParticipant;

		if ($model->confirmPayment())
		{
			OrganizerHelper::message('ORGANIZER_STATUS_CHANGE_SUCCESS');
		}
		else
		{
			OrganizerHelper::message('ORGANIZER_STATUS_CHANGE_FAIL', 'error');
		}

		$this->setRedirect(Input::getInput()->server->getString('HTTP_REFERER'));
	}

	/**
	 * Prints badges for the selected participants.
	 *
	 * @return void
	 * @throws Exception
	 */
	public function printBadges()
	{
		// Reliance on POST requires a different method of redirection
		$this->input->set('format', 'pdf');
		$this->input->set('view', 'badges');
		parent::display();
	}

	/**
	 * De-/registers a participant from/to a course.
	 *
	 * @return void
	 */
	public function register()
	{
		$participantID = Input::getInt('participantID');

		if (!Helpers\Participants::canRegister($participantID))
		{
			OrganizerHelper::message('ORGANIZER_PARTICIPANT_REGISTRATION_INCOMPLETE', 'error');
			$this->setRedirect(Input::getInput()->server->getString('HTTP_REFERER'));
		}

		$courseID      = Input::getInt('participantID');
		$eventID       = Input::getInt('eventID');
		$model         = new Models\CourseParticipant;
		$previousState = Helpers\CourseParticipants::getState($courseID, $participantID, $eventID);

		if ($model->register())
		{
			if ($previousState !== self::UNREGISTERED)
			{
				OrganizerHelper::message('ORGANIZER_DEREGISTER_SUCCESS');
			}
			else
			{
				$currentState = Helpers\CourseParticipants::getState($courseID, $participantID, $eventID);

				$msg = $currentState ? 'ORGANIZER_REGISTRATION_ACCEPTED' : 'ORGANIZER_REGISTRATION_WAIT';
				OrganizerHelper::message($msg);
			}
		}
		else
		{
			OrganizerHelper::message('ORGANIZER_STATUS_CHANGE_FAIL', 'error');
		}

		$this->setRedirect(Input::getInput()->server->getString('HTTP_REFERER'));
	}

	/**
	 * Accepts the selected participants into the course.
	 *
	 * @return void
	 * @throws Exception
	 */
	public function remove()
	{
		$model = new Models\CourseParticipant;

		if ($model->remove())
		{
			OrganizerHelper::message('ORGANIZER_REMOVE_SUCCESS');
		}
		else
		{
			OrganizerHelper::message('ORGANIZER_REMOVE_FAIL', 'error');
		}

		$this->setRedirect(Input::getInput()->server->getString('HTTP_REFERER'));
	}

	/**
	 * Toggles binary resource properties from a list view.
	 *
	 * @return void
	 * @throws Exception
	 */
	public function toggle()
	{
		$model = new Models\CourseParticipant;

		if ($model->toggle())
		{
			OrganizerHelper::message('ORGANIZER_TOGGLE_SUCCESS');
		}
		else
		{
			OrganizerHelper::message('ORGANIZER_TOGGLE_FAIL', 'error');
		}

		$this->setRedirect(Input::getInput()->server->getString('HTTP_REFERER'));
	}
}