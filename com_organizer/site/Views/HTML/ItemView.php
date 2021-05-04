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

use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Organizer\Adapters;
use Organizer\Helpers;

/**
 * Class loads the resource form into display context. Specific resource determined by extending class.
 */
abstract class ItemView extends BaseView
{
    protected $_layout = 'item';

    public $form = null;

    public $item = null;

    /**
     * Method to generate buttons for user interaction
     *
     * @return void
     */
    protected function addToolBar()
    {
        // On demand abstract function.
    }

    /**
     * Method to get display
     *
     * @param   Object  $tpl  template  (default: null)
     *
     * @return void
     */
    public function display($tpl = null)
    {
        $this->item = $this->get('Item');

        $this->addDisclaimer();
        $this->addToolBar();
        $this->setSubtitle();
        $this->addSupplement();
        $this->modifyDocument();

        $defaultConstant = 'ORGANIZER_' . strtoupper(str_replace('Item', '', $this->getName()));
        $itemName        = is_array($this->item['name']) ? $this->item['name']['value'] : $this->item['name'];
        Helpers\HTML::setMenuTitle($defaultConstant, $itemName);
        unset($this->item['name']);

        // This has to be after the title has been set so that it isn't prematurely removed.
        $this->filterAttributes();
        parent::display($tpl);
    }

    /**
     * Filters out invalid and true empty values. (0 is allowed.)
     *
     * @return void modifies the item
     */
    protected function filterAttributes()
    {
        foreach ($this->item as $key => $attribute) {
            // Invalid for HTML Output
            if (!is_array($attribute)
                or !array_key_exists('value', $attribute)
                or !array_key_exists('label', $attribute)
                or $attribute['value'] === null
                or $attribute['value'] === ''
            ) {
                unset($this->item[$key]);
            }
        }
    }

    /**
     * Modifies document variables and adds links to external files
     *
     * @return void
     */
    protected function modifyDocument()
    {
        parent::modifyDocument();

        Adapters\Document::addStyleSheet(Uri::root() . 'components/com_organizer/css/item.css');
    }

    /**
     * Recursively outputs an array of items as a list.
     *
     * @param   array  $items  the items to be displayed.
     *
     * @return void outputs the items as an html list
     */
    public function renderListValue($items, $url, $urlAttribs)
    {
        echo '<ul>';
        foreach ($items as $index => $item) {
            echo '<li>';
            if (is_array($item)) {
                echo $index;
                $this->renderListValue($item, $url, $urlAttribs);
            } else {
                echo empty($url) ? $item : Helpers\HTML::link(Route::_($url . $index), $item, $urlAttribs);
            }
            echo '</li>';
        }
        echo '</ul>';
    }

    /**
     * Creates a subtitle element .
     *
     * @return void modifies the course
     */
    protected function setSubtitle()
    {
        // On demand abstract function.
    }

    /**
     * Adds supplemental information to the display output.
     *
     * @return void modifies the object property supplement
     */
    protected function addSupplement()
    {
        // On demand abstract function.
    }
}
