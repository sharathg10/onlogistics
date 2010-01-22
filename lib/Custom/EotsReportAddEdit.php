<?php

class EotsReportAddEdit extends GenericAddEdit {

    public function additionalFormContent() {
        if($this->object->getId() > 0) {
            $grid = new Grid();
            $grid->withNoCheckBox = true;
            $grid->withNoSortableColumn = true;
            $grid->newColumn('FieldMapper', _('SN'), array('Macro'=>'%SN%'));
            $grid->newColumn('FieldMapperWithTranslation', _('State'), array(
                'Macro'=>'%State%',
                'TranslationMap' => EotsReportItem::getStateConstArray()));
            $grid->newColumn('FieldMapperWithTranslation', _('Detail'), array(
                'Macro'=>'%Detail%',
                'TranslationMap' => EotsReportItem::getDetailConstArray()));
            return "<tr><th colspan=\"4\">" .
                _('List of pallet') .
                "</th><tr>\n" .
                "<tr><td colspan=\"4\">\n" .
                $grid->render('EotsReportItem', false,
                    array('EotsReport' => $this->object->getId()),
                    array('SN' => SORT_ASC),
                    'GridLite.html') .
                "</td></tr>";
        }
    }

}
