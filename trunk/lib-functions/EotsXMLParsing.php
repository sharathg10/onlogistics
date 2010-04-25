<?php
require_once('Objects/Command.php');
require_once('Objects/EotsReport.php');

function parseEotsReport($file, $rang)
{
    $xml = simplexml_load_string($file);
    if (!$xml) {
        return "Erreur de parsing pour le rapport EOTS de rang $rang. <br />";
    }

    $auth = Auth::singleton();

    $cmdMapper = Mapper::singleton('Command');
    $Command = $cmdMapper->load(array('CommandNo' => (string)$xml->commandNo));

    // save the eots report
    $EotsReport = Object::load('EotsReport');
    $EotsReport->setCommand($Command);
    $EotsReport->setShipperId((string)$xml->shipperid);
    $EotsReport->save();

    // save the eots report items
    foreach ($xml->eotsitem as $item) {
        $EotsItem = Object::load('EotsReportItem');
        $EotsItem->setEotsReport($EotsReport);
        $EotsItem->setSN((string)$item->sn);
        $EotsItem->setState((int)$item->state);
        $EotsItem->setDetail((int)$item->detail);
        $EotsItem->save();
    }

    // create the executed movement
    $command_item_col = $Command->getCommandItemCollection();
    foreach ($command_item_col as $cmd_item) {
        $acm = $cmd_item->getActivatedMovement();
        $acm->setState(_ActivatedMovement::ACM_EXECUTE_TOTALEMENT);
        $acm->save();
        $exm = $acm->CreateExecutedMovement($cmd_item->getQuantity(), $cmd_item->getProduct()->getId());
        $exm->save();
    }

    // update the command state
    $Command->setState(_Command::LIV_COMPLETE);
    $Command->save();
}
