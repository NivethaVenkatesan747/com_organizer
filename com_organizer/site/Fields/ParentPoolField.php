<?php
/**
 * @package     Organizer
 * @extension   com_organizer
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2020 TH Mittelhessen
 * @license     GNU GPL v.3
 * @link        www.thm.de
 */

namespace Organizer\Fields;

use Joomla\CMS\Form\FormField;
use Organizer\Helpers;

/**
 * Class creates a select box for superordinate (subject) pool mappings.
 */
class ParentPoolField extends FormField
{
	use Translated;

	/**
	 * Type
	 *
	 * @var    String
	 */
	protected $type = 'ParentPool';

	/**
	 * Returns a select box in which pools can be chosen as a parent node
	 *
	 * @return string  the HTML for the parent pool select box
	 */
	public function getInput()
	{
		$options = $this->getOptions();
		$select  = '<select id="jformparentID" name="jform[parentID][]" multiple="multiple" size="10">';
		$select  .= implode('', $options) . '</select>';

		return $select;
	}

	/**
	 * Gets pool options for a select list. All parameters come from the
	 *
	 * @return array  the options
	 */
	protected function getOptions()
	{
		// Get basic resource data
		$resourceID   = Helpers\Input::getID();
		$contextParts = explode('.', $this->form->getName());
		$resourceType = str_replace('edit', '', $contextParts[1]);

		$mappings   = [];
		$mappingIDs = [];
		$parentIDs  = [];
		Helpers\Mappings::setMappingData($resourceID, $resourceType, $mappings, $mappingIDs, $parentIDs);

		$options   = [];
		$options[] = '<option value="-1">' . Helpers\Languages::_('JNONE') . '</option>';

		if (!empty($mappings))
		{
			$unwantedMappings = [];
			$programEntries   = Helpers\Programs::getRanges($mappings);
			$programMappings  = Helpers\Mappings::getProgramMappings($programEntries);

			// Pools should not be allowed to be placed anywhere where recursion could occur
			if ($resourceType == 'pool')
			{
				$children         = Helpers\Mappings::getChildMappingIDs($mappings);
				$unwantedMappings = array_merge($unwantedMappings, $mappingIDs, $children);
			}

			foreach ($programMappings as $mapping)
			{
				// Recursive mappings or mappings belonging to subjects should not be offered
				if (in_array($mapping['id'], $unwantedMappings) or !empty($mapping['subjectID']))
				{
					continue;
				}

				if (!empty($mapping['poolID']))
				{
					$options[] = Helpers\Mappings::getPoolOption($mapping, $parentIDs);
				}
				else
				{
					$options[] = Helpers\Mappings::getProgramOption($mapping, $parentIDs, $resourceType);
				}
			}
		}

		return $options;
	}
}
