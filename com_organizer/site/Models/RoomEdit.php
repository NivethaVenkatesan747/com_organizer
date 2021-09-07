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

use Organizer\Helpers;
use Organizer\Tables;
use Joomla\CMS\Factory;

/**
 * Class loads a form for editing room data.
 */
class RoomEdit extends EditModel
{
	/**
	 * Checks access to edit the resource.
	 *
	 * @return void
	 */
	protected function authorize()
	{
		if (!Helpers\Can::manage('facilities'))
		{
			Helpers\OrganizerHelper::error(403);
		}
	}

	/**
	 * Method to get a table object, load it if necessary.
	 *
	 * @param   string  $name     The table name. Optional.
	 * @param   string  $prefix   The class prefix. Optional.
	 * @param   array   $options  Configuration array for model. Optional.
	 *
	 * @return Tables\Rooms A Table object
	 *
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function getTable($name = '', $prefix = '', $options = [])
	{
		return new Tables\Rooms;
	}

    public function getItem($pk = 0)
    {
        $pk = empty($pk) ? Helpers\Input::getSelectedID() : $pk;

        // Prevents duplicate execution from getForm and getItem
        if (isset($this->item->id) and ($this->item->id === $pk or $pk === null))
        {
            return $this->item;
        }

        $this->item = parent::getItem($pk);

        $this->authorize();
        $this->item->equipment_list = array();
        if($this->item->id > 0){
            $db = Factory::getDbo();
            $query = $db->getQuery(true);
            $query->select('equipmentId,quantity')->from('#__organizer_room_equipment')->where('roomId ='.$db->q($this->item->id));
            $db->setQuery($query);
            $list = $db->loadObjectList();
            foreach ($list as $key => $single){
                $this->item->equipment_list['periods'][$key] = array(
                    'equipment' => $single->equipmentId,
                    'qty' => $single->quantity
                );
            }
            $this->item->equipment_list = json_encode($this->item->equipment_list);
        }

        return $this->item;
    }
}
