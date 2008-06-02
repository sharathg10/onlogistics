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

require_once('config.inc.php');
// fichiers requis par les sessions
require_once('ActorAddEditTools.php');
includeSessionRequirements();

require_once('Objects/DocumentModel.inc.php');
require_once('Objects/Actor.inc.php');
require_once('Objects/Action.php');
require_once('Objects/FormModel.php');

/**
 * Session et authentification: none shall pass ;)
 **/
$auth = Auth::Singleton();
$auth->checkProfiles(
    array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW, UserAccount::PROFILE_ADMIN_VENTES, 
          UserAccount::PROFILE_COMMERCIAL,UserAccount::PROFILE_AERO_ADMIN_VENTES,
          UserAccount::PROFILE_AERO_INSTRUCTOR,UserAccount::PROFILE_AERO_CUSTOMER,
          UserAccount::PROFILE_DIR_COMMERCIAL, UserAccount::PROFILE_GED_PROJECT_MANAGER));

$actorConnected = $auth->getActor();
$ProfileId = $auth->getProfile();
$withCommRights = in_array($ProfileId, array(UserAccount::PROFILE_COMMERCIAL, UserAccount::PROFILE_DIR_COMMERCIAL));
$tvaSurtax = Preferences::get('TvaSurtax', 0);
$fodecTax = Preferences::get('FodecTax', 0);
$taxStamp = Preferences::get('TaxStamp', 0);

$retURL = isset($_REQUEST['retURL'])?$_REQUEST['retURL']:'ActorList.php';

$session = Session::Singleton();
SearchTools::prolongDataInSession();

/**
 * Messages
 **/
$errorTitle = E_ERROR_TITLE;
$errorBody  = _('Actor "%s" cannot be saved.');
$okTitle    = I_CONFIRM_DO;
$okBody     = _('Actor "%s" successfully saved');
$pageTitle  = _('Add or update actor');
$noSite     = _('Actor must have at least one site with an address, please correct.');
$actorAlreadyExist = _('Actor could not be saved: an actor with the name provided already exists.');
$actorNotDisable = _('Actor cannot be deactivated because he is the owner of a stock in a storage site.');
$severalPdtsWithoutSupplier = _('Several active products have no main supplier.');
$pdtsWithoutSupplier = _('The following active products have no main supplier: <ul><li>%s</li></ul>');


$activePdtWithoutActiveMainSupplier = array();
$severalPdtsWithoutActiveMainSupplier = false;
$actorMapper = Mapper::singleton('Actor');
/**
 * Si l'acteur est passé en paramètre on le charge sinon on en construit un
 * nouveau, le tout via objectLoader. On gère aussi le cas ou l'acteur
 * est en session
 **/
$actorID = isset($_REQUEST['actId'])?intval($_REQUEST['actId']):false;
if (false == $actorID && isset($_SESSION['actor']) &&
    $_SESSION['actor'] instanceof Actor) {
    $actor = $_SESSION['actor'];
}else{
	cleanActorAddEditSessionData();
	$actorTest = Object::load('Actor', $actorID);
	$actor = clone $actorTest;  // il faut une COPIE ici !!!
	if ($actor->getId() <= 0) {
	    $actor->setId($actorMapper->generateId());
    }
}

// on cleane la session d'un éventuel Site, ActorBankDetail
unset($_SESSION['site'], $_SESSION['ActorBankDetail']);

/**
 * Si la classe a changé, il faut lui assigner la bonne classe.
 **/
if (isset($_REQUEST['Actor_ClassName']) &&
    get_class($actor) != $_REQUEST['Actor_ClassName']) {
	$actor = $actor->mutate($_REQUEST['Actor_ClassName']);
}

/**
 * On check si l'acteur est bien chargé, et on renvoie vers un dialogue
 * d'erreur au cas où.
 **/
if (Tools::isException($actor)) {
    Template::errorDialog($actor->getMessage(), $retURL);
	exit;
}

/**
 * On met l'acteur en session
 **/
$session->register('actor', $actor, 4);
/**
 * Gestion du SupplierCustomer
 **/
if (isset($_SESSION['supplierCustomer'])) {
    $supplierCustomer = $_SESSION['supplierCustomer'];
}else {
	$supplierCustomer = $_SESSION['actor']->getSupplierCustomer();
	if (!$_SESSION['actor']->hasBeenInitialized) {
	    $supplierCustomer->setHasTVA(1);
	}
	$session->register('supplierCustomer', $supplierCustomer, 4);
}

// Gestion du CustomerProperties
if ($withCommRights) {
    if (isset($_SESSION['CustomerProperties'])) {
        $CustomerProperties = $_SESSION['CustomerProperties'];
    }else {
    	$CustomerProperties = $_SESSION['actor']->getCustomerProperties();
    	if (Tools::isEmptyObject($CustomerProperties)) {
    	    $CustomerProperties = new CustomerProperties();
    	}
    	$session->register('CustomerProperties', $CustomerProperties, 4);
    }
}

// Gestion du ActorDetail
$tradeContext = Preferences::get('TradeContext');
$consultingContext = ($auth->isRootUserAccount()
        || (!is_null($tradeContext) && in_array('consulting', $tradeContext)));
if ($consultingContext) {
    if (isset($_SESSION['actorDetail'])) {
        $ActorDetail = $_SESSION['actorDetail'];
    }else {
    	$ActorDetail = $_SESSION['actor']->getActorDetail();
    	if (Tools::isEmptyObject($ActorDetail)) {
    	    $ActorDetail = new ActorDetail();
    	}
    	$session->register('actorDetail', $ActorDetail, 4);
    }
}

$DocumentTypeArray = getDocumentTypeArray();

/**
 * Traitement de l'envoi du formulaire
 **/
if (isset($_POST['formSubmitted'])) {
	/**
	 * La transaction est geree dans ActorAddEditTools::saveAll() !!!
	 **/
	/**
	 * On remplit l'objet acteur
	 **/
    // gestion des time/centièmes d'heures: cette fonction est dans le fichier
    // lib-functions/ActorAddEditTools.php
    convertHoursAndMinutesToHundredthOfHours();
    // autoHandlePostData
	FormTools::autoHandlePostDataWithLinks($_POST, $_SESSION['actor'], 'Actor' );
	if (Tools::isException($_SESSION['actor'])) {
		Template::errorDialog($_SESSION['actor']->getMessage(), $retURL);
		exit;
	}

    $res = $_SESSION['actor']->setLogoFromFileInput('Actor_Logo');
    if (Tools::isException($res)) {
         Template::errorDialog($res->getMessage(), $retURL);
         exit;
    }

	/*  Verification qu'un Actor n'est pas deja en base avec le Name saisi  */
    $ActorTest = $actorMapper->load(array('Name' => $_POST['Actor_Name']));
    // si un Actor existe deja avec ce Name
	if ($ActorTest instanceof Actor && $_SESSION['actor']->getId() != $ActorTest->getId()) {
        Template::infoDialog($actorAlreadyExist, $_SERVER['PHP_SELF']);
        exit;
    }

    // l'acteur n'est pas desactivable
    if(isset($_POST['Actor_Active']) && $_POST['Actor_Active']==0) {
        if ((!Tools::isEmptyObject($_SESSION['actor']->getStorageSiteCollection()))
        || !Tools::isEmptyObject($_SESSION['actor']->getStoreCollection())) {
            Template::infoDialog($actorNotDisable, $_SERVER['HTTP_REFERER']);
            exit;
        }
        // Collection des Products pour lesquels il est le mainSupplier
        $pdtColl = $actor->isMainSupplier(true);
        if ($pdtColl instanceof Collection) {
            foreach($pdtColl as $pdt) {
                $activePdtWithoutActiveMainSupplier[] = $pdt->getBaseReference();
                // Si plus de 10 Products dans ce cas, on ne les liste pas
                if (count($activePdtWithoutActiveMainSupplier) > 10) {
                    $severalPdtsWithoutActiveMainSupplier = true;
                    break;
                }
            }
        }

        // Met les ActorProduct.Priority a 0 si besoin
        $_SESSION['actor']->removeMainSupplierLinks();
    }

    // On gère le OnlogisticsAccount
    if (method_exists($_SESSION['actor'], 'getOnlogisticsAccount')
    && $_SESSION['actor']->getOnlogisticsAccount() == '##') {
        $_SESSION['actor']->setOnlogisticsAccount(null);
    };

	/**
	 * On gère le supplier customer
	 **/
	FormTools::autoHandlePostData($_POST, $_SESSION['supplierCustomer']);
	if (Tools::isException($_SESSION['supplierCustomer'])) {
		Template::errorDialog($_SESSION['supplierCustomer']->getMessage(), $retURL);
		exit;
	}
    if (empty($_REQUEST['SupplierCustomer_MaxIncur'])) {
        $_SESSION['supplierCustomer']->setMaxIncur(null);
    }
	// Si 1 des boutons radio selectionne
	if (isset($_REQUEST['SupplierCustomer_Type'])
    && $_REQUEST['SupplierCustomer_Type'] != -1) {
	    $supplier = ($_REQUEST['SupplierCustomer_Type'] == 0)?
                $actorConnected:$_SESSION['actor'];
		$customer = ($_REQUEST['SupplierCustomer_Type'] == 0)?
                $_SESSION['actor']:$actorConnected;
		$_SESSION['supplierCustomer']->setSupplier($supplier);
		$_SESSION['supplierCustomer']->setCustomer($customer);

		/*  Gestion des Modeles de document  */
		$DocumentModelIds = array();
		foreach($DocumentTypeArray as $key => $value) {
			if (!empty($_REQUEST[$key.'Model'])) {
			    $DocumentModelIds[] = $_REQUEST[$key.'Model'];
			}
		}
		$_SESSION['supplierCustomer']->setDocumentModelCollectionIds($DocumentModelIds);
	}

	// CustomerProperties
    if ($withCommRights) {
        $customerSituation = $_SESSION['CustomerProperties']->getSituation();
        if($customerSituation instanceof CustomerSituation) {
            $actualSituation = $customerSituation->getType();
        }
        FormTools::autoHandlePostData($_POST, $_SESSION['CustomerProperties']);
    	if (Tools::isException($_SESSION['CustomerProperties'])) {
    		Template::errorDialog($_SESSION['CustomerProperties']->getMessage(), $retURL);
    		exit;
    	}
    }
    
    // Consulting context: gestion du ActorDetail
    if ($consultingContext) {
        FormTools::autoHandlePostData($_POST, $_SESSION['actorDetail'], 'ActorDetail');
    }

    
	/**
	 * On gère les actions possibles
	 **/
	if (isset($_REQUEST['SiteToAdd']) && $_REQUEST['SiteToAdd'] != -1) {
		// le formulaire a été soumis avec la variable 'SiteToAdd' à true
		// on redirige vers le formulaire d'ajout de site
		Tools::redirectTo(sprintf("SiteAddEdit.php?sitId=%s&retURL=%s",
            $_REQUEST['SiteToAdd'], 'ActorAddEdit.php'));
		exit;
	} else if(isset($_REQUEST['SiteToDelete']) && $_REQUEST['SiteToDelete'] != -1) {
        $siteCollection = &$_SESSION['actor']->getSiteCollection();
        $site = $siteCollection->getItemById($_REQUEST['SiteToDelete']);
        if ($site instanceof Site) {
            try {
                // mode "fake"
                $site->delete(true);
            } catch (Exception $exc) {
                Template::errorDialog($exc->getMessage(), $_SERVER['PHP_SELF']);
                exit(1);
            }
            $siteCollection->removeItemById($_REQUEST['SiteToDelete']);
        }
	} else if(isset($_REQUEST['ActorBankDetailToAdd'])
            && $_REQUEST['ActorBankDetailToAdd'] != -1) {
		Tools::redirectTo(sprintf("ActorBankDetailAddEdit.php?abdId=%s&retURL=%s",
                           $_REQUEST['ActorBankDetailToAdd'], 'ActorAddEdit.php'));
		exit;
	} else if(isset($_REQUEST['ActorBankDetailToDelete'])
            && $_REQUEST['ActorBankDetailToDelete'] != -1) {
        $actorBankDetailCollection = $_SESSION['actor']->getActorBankDetailCollection();

        // Pour l'acteur qui est le DataBaseOwner, un ActorBankDetail
        // n'est supprimable que s'il n'est affecte a aucun Payment.
        if ($_SESSION['actor']->getDatabaseOwner() == 1) {
            $paymentMapper = Mapper::singleton('Payment');
            $paymentCol = $paymentMapper->loadCollection(
                array('ActorBankDetail' => $_REQUEST['ActorBankDetailToDelete'])
            );
            if (count($paymentCol) == 0) {
                $actorBankDetailCollection->removeItemById($_REQUEST['ActorBankDetailToDelete']);
            }
        } else {
            $actorBankDetailCollection->removeItemById($_REQUEST['ActorBankDetailToDelete']);
        }
	} else {
	    // CustomerProperties
        if ($withCommRights) {
            // Si Frequence par defaut choisie, il faut la determiner
            if ($CustomerProperties->getPersonalFrequencyId() == -1) {
                $Category = $actor->getCategory();
                $custFreqMapper = Mapper::singleton('CustomerFrequency');
                $custFreqTest = $custFreqMapper->load(
                    array('Potential' => $CustomerProperties->getPotentialId(),
                          'Attractivity' => $Category->getAttractivityId()));
                if (Tools::isEmptyObject($custFreqTest)) {
                    $custFreq = 0;  // Compat php4
                    $CustomerProperties->setPersonalFrequency($custFreq);
                } else {
                    $CustomerProperties->setPersonalFrequency($custFreqTest);
                }
            }
            // On sauve dans le saveAll() pour beneficier de la transaction
        }
	    $siteCol = $actor->getSiteCollection();
	    if ($siteCol instanceof Collection && $siteCol->getCount() == 0) {
		    Template::errorDialog($noSite, basename($_SERVER['PHP_SELF']));
		    exit;
        }
        
       
	    // on sauve tout
        saveAll($_SESSION['actor']);
        $actorID = $_SESSION['actor']->getId();
        // on nettoie la session
	    unset($_SESSION['actor'], $_SESSION['supplierCustomer'],
            $_SESSION['CustomerProperties']);

        /* check sur le passage en alert,
        dans ce cas il faut ouvrir un formulaire pour
        la saisie du commentaire */
        if ($withCommRights && (isset($actualSituation) && $actualSituation!=CustomerSituation::TYPE_SITUATION_ALERT)) {
    	    $customerSituation = $CustomerProperties->getSituation();
    	    if ($customerSituation->getType() == CustomerSituation::TYPE_SITUATION_ALERT) {
    	        $action = new Action();
    	        $action->setType(FormModel::ACTION_TYPE_CUSTOMER_ALERT);
    	        $action->setActionDate(date('Y-m-d h:i:s'));
    	        $action->setCommercial($actor->getCommercial());
    	        $action->setActor($actor);
    	        $action->setState(Action::ACTION_STATE_ALERT);
                saveInstance($action, basename($_SERVER['PHP_SELF']));
                Tools::redirectTo('ActionAddEdit.php?actorID=' . $actorID .
                    '&aID=' . $action->getId() . '&retURL=ActorList.php&fromActorAddEdit=1');
                exit();
    	    }
        }
        if ($severalPdtsWithoutActiveMainSupplier) {
            $msg = $severalPdtsWithoutSupplier;
        }
        elseif (!empty($activePdtWithoutActiveMainSupplier)) {
            $msg = sprintf($pdtsWithoutSupplier,
                implode('</li><li>', $activePdtWithoutActiveMainSupplier));
        }
        if (isset($msg)) {
            Template::infoDialog($msg, $retURL);
        	exit;
        }
	    Tools::redirectTo($retURL);
	    exit;
    }
}
/**
 * Assignation des variables au formulaire avec smarty
 **/
$smarty = new Template();
$smarty->register_function('hour_minute_widget', 'hour_minute_widget');
$smarty->assign('FormAction', $_SERVER['PHP_SELF']);
$smarty->assign('retURL', $retURL);
$smarty->assign('withCommRights', $withCommRights);
$smarty->assign('actId', $actorID);
// Gestion de la tva surtaxee
$smarty->assign('withTvaSurtax', ((float)$tvaSurtax > 0)?1:0);
// Gestion de la taxe Fodec
$smarty->assign('withFodecTax', ((float)$fodecTax > 0)?1:0);
// Gestion du timbre fiscal
$smarty->assign('withTaxStamp', ((float)$taxStamp > 0)?1:0);



/**
 * On assigne les propriétés de site ainsi que son adresse
 **/
$siteCountry = 0;
$smarty->assign('Actor', $_SESSION['actor']);

$options = SupplierCustomer::getOptionConstArray();
$modalities = SupplierCustomer::getModalityConstArray();
$option = $_SESSION['supplierCustomer']->getOption();
$smarty->assign('OptionList',
    join("\n\t\t", FormTools::writeOptionsFromArray($options, $option)));
$modality = $_SESSION['supplierCustomer']->getModality();
$smarty->assign('ModalityList',
    join("\n\t\t", FormTools::writeOptionsFromArray($modalities, $modality)));
$smarty->assign('SupplierCustomer', $_SESSION['supplierCustomer']);
$supplier = $_SESSION['supplierCustomer']->getSupplier();
// soumis à la TVA ?
$hasTVA = $_SESSION['supplierCustomer']->getHasTVA();
$smarty->assign('SupplierCustomer_HasTVA', $hasTVA);
// soumis a la TVA surtaxee ?
$hasTvaSurtax = $_SESSION['supplierCustomer']->getHasTvaSurtax();
$smarty->assign('SupplierCustomer_HasTvaSurtax', $hasTvaSurtax);
// soumis a la Taxe Fodec?
$hasFodecTax = $_SESSION['supplierCustomer']->getHasFodecTax();
$smarty->assign('SupplierCustomer_HasFodecTax', $hasFodecTax);
// soumis au timbre fiscal?
$hasTaxStamp = $_SESSION['supplierCustomer']->getHasTaxStamp();
$smarty->assign('SupplierCustomer_HasTaxStamp', $hasTaxStamp);
$pdtCustomerCmdBehaviour = Preferences::get('CustomerProductCommandBehaviour');
$smarty->assign('PdtCustomerCmdBehaviour', $pdtCustomerCmdBehaviour);

$logo = $_SESSION['actor']->getLogo();
if (!empty($logo)) {
    $smarty->assign('Logo', 1);
}

if ($supplier instanceof Actor) {
    $smarty->assign('IsSupplier', $supplier->getId() == $_SESSION['actor']->getId()?
            ' checked="checked"':'');
    $smarty->assign('IsCustomer', $supplier->getId() == $actorConnected->getId()?
            ' checked="checked"':'');
}

/*  On affiche ou pas le pave 'Modeles de document'  */
$smarty->assign('IsCustomerOrSupplier',
				($supplier instanceof Actor &&
                ($supplier->getId() == $_SESSION['actor']->getId() ||
                $supplier->getId() == $actorConnected->getId()))?' block':' none');

$DocumentModelCollection = $_SESSION['supplierCustomer']->getDocumentModelCollection();
$DocumentModelArray = array();
if(!Tools::isEmptyObject($DocumentModelCollection)) {
    $count = $DocumentModelCollection->getCount();
	for ($i=0; $i<$count; $i++) {
		$DocumentModel = $DocumentModelCollection->getItem($i);
		$DocumentModelArray[$DocumentModel->getDocType()] = $DocumentModel->getId();
	}
}
$DocTypeArrayForDisplay = array();
$DocModelOptions = array();
foreach($DocumentTypeArray as $key => $value) {
	$model = (isset($DocumentModelArray[$key]))?$DocumentModelArray[$key]:0;
	$DocTypeArrayForDisplay[] = array(
        'DocType' => $key,
        'DocTypeName' => $value,
        'Options' => join("\n\t\t", FormTools::writeOptionsFromObject(
            'DocumentModel',
            $model,
            array('DocType'=>$key),
            array('Name'=>SORT_ASC))
            )
        );
}
$smarty->assign('DocumentTypeArray', $DocTypeArrayForDisplay);


// Gestion des OnlogisticsAccount si Supplier edite, et si root connecte
if ($auth->isRootUserAccount()) {
    // recuperation des DSN
    $accountArray = getOnlogisticsAccountArray(false);
    // Reconnexion a la base 'initiale'
    Database::connection(getDSNForRealm());
    $selAccount = (($_SESSION['actor'] instanceof Supplier
            || $_SESSION['actor'] instanceof AeroSupplier)
            && !is_null($_SESSION['actor']->getOnlogisticsAccount()))?
            $_SESSION['actor']->getOnlogisticsAccount():'##';
    $olAccountList = FormTools::writeOptionsFromArray($accountArray, $selAccount, true);
    $smarty->assign('OnlogisticsAccountList', join("\n\t\t", $olAccountList));
}
$smarty->assign('isRootConnected', $auth->isRootUserAccount());

/**
 * reste à assigner les propriétés multiples,
 * ici les contacts et les plannings et les modes de communication
 **/

/**
 * Et les propriétés qui doivent être affichées sous forme de select
 **/
// Class names
$clsNameArray  = array('Actor' => _('Actor'),
					   'Customer' => _('Customer'),
					   'Supplier' => _('Supplier'),
					   );

$aeroProfiles = array(UserAccount::PROFILE_AERO_CUSTOMER,
				      UserAccount::PROFILE_AERO_SUPPLIER, UserAccount::PROFILE_AERO_INSTRUCTOR,
				      UserAccount::PROFILE_AERO_OPERATOR, UserAccount::PROFILE_AERO_ADMIN_VENTES);

// Contexte metier: necessaire de checker, car ADMIN == AERO_ADMIN
if ($auth->isRootUserAccount() || in_array($ProfileId, $aeroProfiles)
|| (!is_null($tradeContext) && in_array('aero', $tradeContext))) {
    $clsNameArray = array_merge(
            $clsNameArray,
            array('AeroCustomer' => _('Aeronautical customer'),
				  'AeroOperator' => _('Aeronautical operator'),
                  'AeroSupplier' => _('Aeronautical supplier'),
				  'AeroInstructor' => _('Instructor')));
}
if ($auth->isRootUserAccount() || $consultingContext) {
    $clsNameArray = array_merge($clsNameArray,
        array('ProjectManager'=>_('Project manager')));
}

// Consulting context
if ($consultingContext) {
    $smarty->assign('ConsultingContext', 1);
    $actorDetail = $_SESSION['actorDetail'];
    if ($actorDetail instanceof ActorDetail) {
        $intAffectationId = $actorDetail->getInternalAffectationId();
        $signatoryId = $actorDetail->getSignatoryId();
        $bProviderId = $actorDetail->getBusinessProviderId();
        $isInternalAffectation = ($actorDetail->getIsInternalAffectation())?
                'checked="checked"':'';
        $isNotInternalAffectation = (!$actorDetail->getIsInternalAffectation())?
                'checked="checked"':'';
    } else {
        $intAffectationId = 0;
        $signatoryId = 0;
        $bProviderId = 0;
        $isInternalAffectation = '';
        $isNotInternalAffectation = 'checked="checked"';
    }
    
    $intAffectationOptions  = FormTools::writeOptionsFromObject(
        'Actor', $intAffectationId, 
        array('ActorDetail.IsInternalAffectation' => 1, 'Active' => 1), 
        array('Name' => SORT_ASC), 'toString', array('Name'), true
    );
    $smarty->assign('InternalAffectationList', join("\n\t\t", $intAffectationOptions));
    
    $actorOptions  = FormTools::writeOptionsFromObject(
        'Customer', $signatoryId, array('Active' => 1), array('Name' => SORT_ASC), 
        'toString', array('Name'), true
    );
    $smarty->assign('SignatoryList', join("\n\t\t", $actorOptions));
    
    $actorOptions  = FormTools::writeOptionsFromObject(
        'Actor', $bProviderId, array('Active' => 1), array('Name' => SORT_ASC), 
        'toString', array('Name'), true
    );
    $smarty->assign('BusinessProviderList', join("\n\t\t", $actorOptions));
    $smarty->assign('IsInternalAffectation', $isInternalAffectation);
    $smarty->assign('IsNotInternalAffectation', $isNotInternalAffectation);
}else {
    $smarty->assign('ConsultingContext', 0);
}

foreach($clsNameArray as $value=>$label){
	$sel = ($value == get_class($_SESSION['actor']))?' selected="selected"':'';
	$clsNameOptions[] = sprintf('<option value="%s"%s>%s</option>',
        $value, $sel, $label);
}
$smarty->assign('ClassNameList', join("\n\t\t", $clsNameOptions));

// catégories
$catId = $_SESSION['actor']->getCategoryID();
$catOptions  = FormTools::writeOptionsFromObject(
        'Category', $catId, array(), array('Name' => SORT_ASC));
$smarty->assign('CatList', join("\n\t\t", $catOptions));

// acteurs génériques
$gaId = $_SESSION['actor']->getGenericActorID();
$filter = array('Generic'=>1);
$genActOptions  = FormTools::writeOptionsFromObject(
        'Actor', $gaId, $filter, array('Name'=>SORT_ASC));
$smarty->assign('GenericActorList', join("\n\t\t", $genActOptions));

// types de société
$companyTypesOptions = getCompanyTypesAsOptions(
    $_SESSION['actor']->getCompanyType());
$smarty->assign('CompanyTypeList', join("\n\t\t", $companyTypesOptions));

// Qualite
$qualityOptions = Actor::getQualityConstArray();
$quality = $_SESSION['actor']->getQuality();
$smarty->assign('QualityList',
    join("\n\t\t", FormTools::writeOptionsFromArray($qualityOptions, $quality)));

// jobs
$jobIds = $_SESSION['actor']->getJobCollectionIds();  // $jobCol
$jobOptions  = FormTools::writeOptionsFromObject(
        'Job', $jobIds, array(), array('Name' => SORT_ASC));
$smarty->assign('JobList', join("\n\t\t", $jobOptions));

// commerciaux: tous les user accounts dont l'acteur est l'acteur
// connecté et dont le profil est 'commercial'.

$profiles = array(UserAccount::PROFILE_ADMIN_VENTES, UserAccount::PROFILE_COMMERCIAL,
    UserAccount::PROFILE_AERO_ADMIN_VENTES, UserAccount::PROFILE_AERO_INSTRUCTOR,
    UserAccount::PROFILE_AERO_CUSTOMER);
if (in_array($ProfileId, $profiles)) {
    $filter = new FilterComponent(
        new FilterRule(
            'Actor',
            FilterRule::OPERATOR_EQUALS,
            $actorConnected->getId()
        ),
        new FilterRule(
            'Profile',
            FilterRule::OPERATOR_EQUALS,
            UserAccount::PROFILE_COMMERCIAL
        ),
        FilterComponent::OPERATOR_AND
    );
} else {
    $filter = array('Profile'=>UserAccount::PROFILE_COMMERCIAL);
}
$comId = $_SESSION['actor']->getCommercialID();
$comOptions  = FormTools::writeOptionsFromObject(
        'UserAccount', $comId, $filter, array('Identity'=>SORT_ASC));
$smarty->assign('CommercialList', join("\n\t\t", $comOptions));

// incoterms en fait
$incotermId = $_SESSION['actor']->getIncotermID();
$incotermOptions  = FormTools::writeOptionsFromObject(
        'Incoterm', $incotermId, array(), array('Label'=>SORT_ASC));
$smarty->assign('IncotermList', join("\n\t\t", $incotermOptions));

// devises
$cur = $_SESSION['actor']->getCurrency();
$curId = $_SESSION['actor']->getCurrencyID();
$curOptions  = FormTools::writeOptionsFromObject('Currency', $curId);
$smarty->assign('CurrencyList', join("\n\t\t", $curOptions));
$smarty->assign('Currency', $cur instanceof Currency?$cur->getSymbol():'&euro;');

// sites
$siteCollection = $_SESSION['actor']->getSiteCollection();
$siteList = array();
for($i = 0; $i < $siteCollection->getCount(); $i++){
	$aSite = $siteCollection->getItem($i);
	$siteList[] = $aSite;
	unset($aSite);
}
$smarty->assign('SiteList', $siteList);

// ActorBankDetail
$abdCollection = $_SESSION['actor']->getActorBankDetailCollection();
$ActorBankDetailList = array();
for($i = 0; $i < $abdCollection->getCount(); $i++){
	$abd = $abdCollection->getItem($i);
	$ActorBankDetailList[] = $abd;
	unset($abd);
}
$smarty->assign('ActorBankDetailList', $ActorBankDetailList);

// CustomerProperties
if ($withCommRights) {
    $cusProp = $_SESSION['CustomerProperties'];
    $smarty->assign('CustomerProperties', $cusProp);
    //$CustomerProperties = $_SESSION['actor']->getCustomerProperties();
    $customerSitOptions  = FormTools::writeOptionsFromObject(
            'CustomerSituation', $cusProp->getSituationId());
    $smarty->assign('CustomerSituationList', join("\n\t\t", $customerSitOptions));
    $customerPotOptions  = FormTools::writeOptionsFromObject(
            'CustomerPotential', $cusProp->getPotentialId());
    $smarty->assign('CustomerPotentialList', join("\n\t\t", $customerPotOptions));
    $customerFreqOptions  = FormTools::writeOptionsFromObject(
            'CustomerFrequency', $cusProp->getPersonalFrequencyId());
    $smarty->assign('CustomerFrequencyList', join("\n\t\t", $customerFreqOptions));
    // Prochaine visite
    $action = $_SESSION['actor']->getNextMeetingAction();
    if (Tools::isEmptyObject($action)) {
        $nextMeeting = _('N/A');
    }
    else {
        $nextMeeting = _('Week ') . $action->getWishedDate('W')
                . _(': appointment arranged on ') . $action->getWishedDate('localedate');
        $marge = 604800 * 2;  // nb de secondes dans 2 semaines
        $nextMeeting .= "\r" . _('Visit possible as soon as week ')
                . date('W', $action->getWishedDate('timestamp') - $marge)
                . "\r" . _('Deadline for visit on week ')
                . date('W', $action->getWishedDate('timestamp') + $marge);
    }
    $smarty->assign('nextMeeting', $nextMeeting);
}


// Donnees aeronautiques
$aeroAttributes = array_keys(AeroActor::getAeroProperties());
assignObjectAttributes($smarty, $actor, $aeroAttributes);

// ATTENTION: pour Smarty, une string vide donne une var non definie!!
if (method_exists($actor, 'getTrainee')) {
	$smarty->assign('IsTrainee', ($actor->getTrainee() == 1)?'checked="checked"':' ');
	$smarty->assign('IsNotTrainee', ($actor->getTrainee() == 1)?' ':'checked="checked"');
}
if (method_exists($actor, 'getSoloFly')) {
	$smarty->assign('IsSoloFly', ($actor->getSoloFly() == 1)?'checked="checked"':' ');
	$smarty->assign('IsNotSoloFly', ($actor->getSoloFly() == 1)?' ':'checked="checked"');
}
$InstructorId = (method_exists($actor, 'getInstructorId'))?$actor->getInstructorId():0;
$instructorOptions  = FormTools::writeOptionsFromObject('AeroInstructor',
    $InstructorId, array('Generic'=>0), array('Name'=>SORT_ASC), 'toString',
    array('Name'));
$smarty->assign('InstructorList', join("\n\t\t", $instructorOptions));
$AuthorizedFlyTypeIds = (method_exists($actor, 'getAuthorizedFlyTypeCollectionIds'))?
        $actor->getAuthorizedFlyTypeCollectionIds():0;
$FlyTypeOptions  = FormTools::writeOptionsFromObject(
        'FlyType', $AuthorizedFlyTypeIds, array(),
    	array('Name'=>SORT_ASC), 'toString', array('Name'));
$smarty->assign('FlyTypeList', join("\n\t\t", $FlyTypeOptions));

// type comptable
$gaId = $_SESSION['actor']->getGenericActorID();
$accountingTypeOptions  = FormTools::writeOptionsFromObject(
        'AccountingType',
        $_SESSION['actor']->getAccountingTypeID(), array(),
        array('Type'=>SORT_ASC), 'GetType');

$smarty->assign('AccountingTypeList', join("\n\t\t", $accountingTypeOptions));
$smarty->assign('MainSiteId', $_SESSION['actor']->getMainSite() instanceof Site ?
    $_SESSION['actor']->getMainSiteId() : 0);
/**
 * Et on affiche la page
 **/
$template = 'Actor/ActorAddEdit.html';
$pageContent = $smarty->fetch($template);
Template::ajaxPage($pageTitle, $pageContent,
    array('js/lib-functions/checkForm.js', 'js/includes/ActorAddEdit.js'));

?>
