<?php

/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * This file is part of Onlogistics, a web based ERP and supply chain 
 * management application. 
 *
 * Copyright (C) 2003-2008 ATEOR
 *
 * This program is free software: you can redistribute it and/or modify it 
 * under the terms of the GNU Affero General Public License as published by 
 * the Free Software Foundation, either version 3 of the License, or (at your 
 * option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT 
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or 
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Affero General Public 
 * License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * PHP version 5.1.0+
 *
 * @package   Onlogistics
 * @author    ATEOR dev team <dev@ateor.com>
 * @copyright 2003-2008 ATEOR <contact@ateor.com> 
 * @license   http://www.fsf.org/licensing/licenses/agpl-3.0.html GNU AGPL
 * @version   SVN: $Id$
 * @link      http://www.onlogistics.org
 * @link      http://onlogistics.googlecode.com
 * @since     File available since release 0.1.0
 * @filesource
 */

class CustomerPotentialAddEdit extends GenericAddEdit {
    private $_notDeletedEntity = array();

    // CustomerPotentialAddEdit::__construct() {{{

    /**
     * __construct
     *
     * @param array $params
     * @access public
     * @return void
     */
    public function __construct($params=array()) {
        $params['profiles'] = array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW);
        parent::__construct($params);
    }

    // }}}
    // CustomerPotentialAddEdit::getAddEditMapping() {{{

    /**
     * getAddEditMapping
     *
     * @access public
     * @return void
     */
    public function getAddEditMapping() {
        $mapping = parent::getAddEditMapping();
        $mapping['']['MinValue']['validationrules'] = array('numeric',
            _('Minimum value must be an integer.'));
        $mapping['']['MaxValue']['validationrules'] = array('numeric',
            _('Maximum value must be an integer.'));
        return $mapping;
    }

    // }}}
    // CustomerPotentialAddEdit::onBeforeHandlePostData() {{{

    /**
     * onBeforeHandlePostData
     *
     * @access public
     * @return void
     */
    public function onBeforeHandlePostData() {
        // vérifie que la valeur min du potentiel est < à la valeur max
        if(I18N::extractNumber($_POST['CustomerPotential_MinValue']) >
            I18N::extractNumber($_POST['CustomerPotential_MaxValue'])) {
            Template::errorDialog(
                _('Minimum potential value must be lower than maximum potential value.'),
                $this->url);
            exit();
        }
        $objectMapper = Mapper::singleton('CustomerPotential');
        // Unicite du nom, *pour une même unité de mesure*
        $filterCompArray = array();
        $filterCompArray[] = SearchTools::newFilterComponent(
                'Name', '', 'Equals', $_POST['CustomerPotential_Name'], 1);
        $filterCompArray[] = SearchTools::newFilterComponent(
                'UnitType', '', 'Equals', $_POST['CustomerPotential_UnitType'], 1);
        $filterCompArray[] = SearchTools::newFilterComponent(
                'Id', '', 'NotEquals', $this->objID, 1);
        $filter = SearchTools::filterAssembler($filterCompArray);
        $objectCol = $objectMapper->loadCollection($filter, array(), array('Id'));
        if ($objectCol->getCount() > 0) {
            Template::errorDialog(
                _('A potential with this name and measure unit already exists, please correct.'),
                $this->url);
            exit();
        }
        // Controle de la plage valeur mini/maxi: pour une unitType donnee, pas
        // de chevauchement des plages
        $filterCompArray = array();
        $filterCompArray[] = SearchTools::newFilterComponent(
                'MinValue', '', 'LowerThanOrEquals',
                I18N::extractNumber($_POST['CustomerPotential_MaxValue']), 1);
        $filterCompArray[] = SearchTools::newFilterComponent(
                'MaxValue', '', 'GreaterThanOrEquals',
                I18N::extractNumber($_POST['CustomerPotential_MinValue']), 1);
        $filterCompArray[] = SearchTools::newFilterComponent(
                'UnitType', '', 'Equals', $_POST['CustomerPotential_UnitType'], 1);
        $filterCompArray[] = SearchTools::newFilterComponent(
                'Id', '', 'NotEquals', $this->objID, 1);
        $filter = SearchTools::filterAssembler($filterCompArray);
        $objectCol = $objectMapper->loadCollection($filter, array(), array('Id'));
        if ($objectCol->getCount() > 0) {
            Template::errorDialog(
                _('A potential already exists with this measure unit and common values. Please correct minimum/maximum value.'),
                $this->url);
            exit();
        }
    }

    // }}}
    // CustomerPotentialAddEdit::onBeforeDelete() {{{

    /**
     * onBeforeDelete
     *
     * @access protected
     * @return void
     */
    protected function onBeforeDelete() {
        $objectMapper = Mapper::singleton('CustomerPotential');
        $objectCol = $objectMapper->loadCollection(
			array('Id' => $this->objID));

        $okForDelete = array();
        $count = $objectCol->getCount();
        for($i=0 ; $i<$count ; $i++){
            $object = $objectCol->getItem($i);
            // la suppression n'est possible que si le potentiel n'est liée à
            // aucun acteur et à aucune fréquence.
            if ($object instanceof CustomerPotential) {
                if (count($object->getCustomerFrequencyCollectionIds())  == 0
                && count($object->getCustomerPropertiesCollectionIds()) == 0) {
                    //on peut supprimer
                    $okForDelete[] = $object->getId();
	            } else {
	                //ajout dans le tableau des non suprimées
                    $this->_notDeletedEntity[] = $object->getName();
                }
            }
        }
        $this->objID = $okForDelete;
    }

    // }}}
    // CustomerPotentialAddEdit::onAfterDelete() {{{

    /**
     * onAfterDelete
     *
     * @access protected
     * @return void
     */
    protected function onAfterDelete() {
        // redirige vers un message d'info
        $msg = false;
        if (count($this->_notDeletedEntity) > 0) {
            $msg = _('The following potentials could not be deleted because they are associated to one or more frequencies and/or customers')
                . sprintf(':<ul><li>%s</li></ul>', implode('</li><li>', $this->_notDeletedEntity));
        }

        if($msg) {
            Template::infoDialog($msg, $this->guessReturnURL());
            exit();
        }
    }

    // }}}
}

?>