<?php
/**
 * @package     Organizer
 * @extension   com_organizer
 * @author      James Antrim, <james.antrim@nm.thm.de>
 * @copyright   2020 TH Mittelhessen
 * @license     GNU GPL v.3
 * @link        www.thm.de
 */

use Organizer\Helpers;

$resourceID = Helpers\Input::getID();
$view       = Helpers\Input::getView();

require_once 'language_selection.php';
echo Helpers\OrganizerHelper::getApplication()->JComponentTitle;
echo $this->subtitle;
echo $this->supplement;
?>
<div id="j-main-container" class="span10">
	<?php
	foreach ($this->item as $key => $attribute)
	{
		if (empty($attribute['value']))
		{
			continue;
		}
		echo '<div class="attribute-item">';
		echo '<div class="attribute-label">' . $attribute['label'] . '</div>';
		echo '<div class="attribute-content">';
		switch ($attribute['type'])
		{
			case 'list':
				$urlAttribs = ['target' => '_blank'];
				$url        = empty($attribute['url']) ? '' : $attribute['url'];
				$this->renderListValue($attribute['value'], $url, $urlAttribs);
				break;
			case 'star':
				$this->renderStarValue($attribute['value']);
				break;
			case 'text':
			default:
				echo $attribute['value'];
				break;
		}
		echo '</div></div>';
	}
	echo $this->disclaimer;
	?>
</div>
