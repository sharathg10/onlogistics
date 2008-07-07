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

$menu_metadata = array(
    // Administration {{{
    array(
        'title'       => _('Admin'),
        'link'        => 'home.php?help_page=Administration',
        'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW),
        'children'    => array(
            array(
                'title'       => _('Operations tolerances'),
                'link'        => 'OperationTolerance.php',
                'description' => _('Operation tolerances management'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW)
            ),
            array(
                'title'       => _('Alerts'),
                'link'        => 'dispatcher.php?entity=Alert',
                'description' => _('Alerts management'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW)
            ),
            array(
                'title'       => _('Help'),
                'link'        => 'dispatcher.php?entity=HelpPage',
                'description' => _('Help messages management'),
                'restrict_to' => array(UserAccount::PROFILE_ROOT)
            ),
            array(
                'title'       => _('Product types'),
                'link'        => 'ProductTypeList.php',
                'description' => _('Product types management'),
                'restrict_to' => array(UserAccount::PROFILE_ROOT)
            ),
            array(
                'title'       => _('Catalogues'),
                'link'        => 'CatalogList.php',
                'description' => _('Catalogues management'),
                'restrict_to' => array(UserAccount::PROFILE_ROOT)
            ),
            array(
                'title'       => _('Users'),
                'link'        => 'dispatcher.php?entity=UserAccount',
                'description' => _('List of users'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW)
            ),
            array(
                'title'       => _('Scheduled tasks'),
                'link'        => 'dispatcher.php?entity=CronTask',
                'description' => _('Scheduled tasks management'),
                'restrict_to' => array(UserAccount::PROFILE_ROOT)
            ),
            array(
                'title'       => _('Cities'),
                'link'        => 'CountryCityList.php',
                'description' => _('List of cities'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW)
            ),
            array(
                'title'       => _('Licenses'),
                'link'        => 'dispatcher.php?entity=LicenceType',
                'description' => _('List of license types'),
                'restrict_to' => array(UserAccount::PROFILE_ROOT)
            ),
            array(
                'title'       => _('Qualifications'),
                'link'        => 'dispatcher.php?entity=RatingType',
                'description' => _('List of qualifications'),
                'restrict_to' => array(UserAccount::PROFILE_ROOT)
            ),
            array(
                'title'       => _('Data import'),
                'link'        => 'dispatcher.php?entity=SpreadSheet',
                'description' => _('Spreadsheet models'),
                'restrict_to' => array(UserAccount::PROFILE_ROOT)
            ),
            array(
                'title'       => _('Entities'),
                'link'        => 'dispatcher.php?entity=Entity',
                'description' => _('List of entities'),
                'restrict_to' => array(UserAccount::PROFILE_ROOT)
            ),
            array(
                'title'       => _('Business forms'),
                'link'        => 'home.php',
                'description' => _('Business forms'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW),
                'children'    => array(
                    array(
                        'title'       => _('Form models'),
                        'link'        => 'FormModelList.php',
                        'description' => _('Form models'),
                        'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW),
                    ),
                    array(
                        'title'       => _('Paragraphs models'),
                        'link'        => 'dispatcher.php?entity=ParagraphModel',
                        'description' => _('Paragraphs models'),
                        'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW),
                    ),
                    array(
                        'title'       => _('Themes'),
                        'link'        => 'dispatcher.php?entity=Theme',
                        'description' => _('Themes'),
                        'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW),
                    ),
                    array(
                        'title'       => _('Questions'),
                        'link'        => 'dispatcher.php?entity=Question',
                        'description' => _('Questions'),
                        'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW),
                    ),
                    array(
                        'title'       => _('Answer models'),
                        'link'        => 'dispatcher.php?entity=AnswerModel',
                        'description' => _('Answer models'),
                        'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW),
                    ),
                )
            ),
            array(
                'title'       => _('Customer properties'),
                'link'        => 'home.php',
                'description' => _('Customer properties'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW),
                'children'    => array(
                    array(
                        'title'       => _('Categories'),
                        'link'        => 'dispatcher.php?entity=Category',
                        'description' => _('Categories'),
                        'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW),
                    ),
                    array(
                        'title'       => _('Attractivity level'),
                        'link'        => 'CustomerAttractivityList.php',
                        'description' => _('Attractivity level'),
                        'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW),
                    ),
                    array(
                        'title'       => _('Potentials'),
                        'link'        => 'dispatcher.php?entity=CustomerPotential',
                        'description' => _('Potentials'),
                        'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW),
                    ),
                    array(
                        'title'       => _('Visit frequencies'),
                        'link'        => 'CustomerFrequencyList.php',
                        'description' => _('Visit frequencies'),
                        'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW),
                    ),
                    array(
                        'title'       => _('Situations'),
                        'link'        => 'dispatcher.php?entity=CustomerSituation',
                        'description' => _('Situations'),
                        'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW),
                    )
                )
            ),
            array(
                'title'       => _('Generic actors'),
                'link'        => 'dispatcher.php?entity=Actor&altname=GenericActor',
                'description' => _('Generic actors management'),
                'restrict_to' => array(UserAccount::PROFILE_ROOT)
            ),
            array(
                'title'       => _('Preferences'),
                'link'        => 'home.php?help_page=Préférences',
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW),
                'children'    => array(
                    array(
                        'title'       => _('Movements'),
                        'link'        => 'PreferencesMovement.php',
                        'description' => _('Movement execution preferences'),
                        'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW)
                    ),
                    array(
                        'title'       => _('Supplying optimization'),
                        'link'        => 'PreferencesSupplyingOptimization.php',
                        'description' => _('Supplying optimization preferences'),
                        'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW)
                    ),
                    array(
                        'title'       => _('Website'),
                        'link'        => 'PreferencesWebSite.php',
                        'description' => _('Preferences for actors created from a linked website'),
                        'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW)
                    ),
                    array(
                        'title'       => _('Order'),
                        'link'        => 'PreferencesCommand.php',
                        'description' => _('Preferences for order and chain activation'),
                        'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW)
                    ),
                    array(
                        'title'       => _('Trade context'),
                        'link'        => 'PreferencesContext.php',
                        'description' => _('Preferences for trade context'),
                        'restrict_to' => array(UserAccount::PROFILE_ROOT)
                    ),
                    array(
                        'title'       => _('Upload'),
                        'link'        => 'PreferencesUpload.php',
                        'description' => _('Preferences for file uploads'),
                        'restrict_to' => array(UserAccount::PROFILE_ROOT),
                        'restrict_to_context' => array('consulting')
                    ),
                )
            ),
            array(
                'title'       => _('VAT'),
                'link'        => 'dispatcher.php?entity=TVA',
                'description' => _('VAT management'),
                'restrict_to' => array(UserAccount::PROFILE_ROOT)
            ),
        )
    ),
    // }}}
    // Modélisation {{{
    array(
        'title'       => _('Back office'),
        'link'        => 'home.php?help_page=Modelisation',
        'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_COMMERCIAL,UserAccount::PROFILE_ADMIN_VENTES,UserAccount::PROFILE_AERO_ADMIN_VENTES,UserAccount::PROFILE_AERO_INSTRUCTOR,UserAccount::PROFILE_AERO_OPERATOR,UserAccount::PROFILE_AERO_CUSTOMER,UserAccount::PROFILE_DIR_COMMERCIAL,UserAccount::PROFILE_GED_PROJECT_MANAGER, UserAccount::PROFILE_PRODUCT_MANAGER),
        'children'    => array(
            array(
                'title'       => _('Chains'),
                'link'        => 'dispatcher.php?entity=Chain',
                'description' => _('List of chains'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN, UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW)
            ),
            array(
                'title'       => _('Products'),
                'link'        => 'home.php?help_page=Products',
                'description' => _('List of products'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_ADMIN_VENTES,UserAccount::PROFILE_AERO_ADMIN_VENTES,UserAccount::PROFILE_AERO_INSTRUCTOR,UserAccount::PROFILE_AERO_OPERATOR,UserAccount::PROFILE_AERO_CUSTOMER),
                'children'    => array(
                    array(
                        'title'       => _('List of products'),
                        'link'        => 'ProductList.php',
                        'description' => _('List of products'),
                        'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_AERO_ADMIN_VENTES,UserAccount::PROFILE_AERO_OPERATOR)
                    ),
                    array(
                        'title'       => _('Pricing zones'),
                        'link'        => 'dispatcher.php?entity=PricingZone',
                        'description' => _('Pricing zones management'),
                        'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_AERO_ADMIN_VENTES,UserAccount::PROFILE_AERO_OPERATOR)
                    ),
                    array(
                        'title'       => _('Caracteristics'),
                        'link'        => 'dispatcher.php?entity=ProductKind',
                        'description' => _('List of product caracteristics'),
                        'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW)
                    ),
                    array(
                        'title'       => _('References by customer'),
                        'link'        => 'dispatcher.php?entity=ActorProduct',
                        'description' => _('Product references by customer'),
                        'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_ADMIN_VENTES,UserAccount::PROFILE_AERO_ADMIN_VENTES)
                    ),
                    array(
                        'title'       => _('SN/Lots'),
                        'link'        => 'ConcreteProductList.php',
                        'description' => _('List of SN/Lots'),
                        'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_AERO_OPERATOR,UserAccount::PROFILE_AERO_INSTRUCTOR,UserAccount::PROFILE_AERO_CUSTOMER)
                    ),
                    array(
                        'title'       => _('Assignments'),
                        'link'        => 'AffectationList.php',
                        'description' => _('List of assignations'),
                        'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW)
                    )
                )
            ),
            array(
                'title'       => _('Actors'),
                'link'        => 'ActorList.php',
                'description' => _('List of actors'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_COMMERCIAL,UserAccount::PROFILE_ADMIN_VENTES,UserAccount::PROFILE_AERO_ADMIN_VENTES,UserAccount::PROFILE_AERO_CUSTOMER,UserAccount::PROFILE_AERO_INSTRUCTOR,UserAccount::PROFILE_DIR_COMMERCIAL,UserAccount::PROFILE_GED_PROJECT_MANAGER, UserAccount::PROFILE_PRODUCT_MANAGER)
            ),
            array(
                'title'       => _('Contact roles'),
                'link'        => 'dispatcher.php?entity=ContactRole',
                'description' => _('Contact roles management'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW)
            ),
            array(
                'title'       => _('Salesmen commission settings'),
                'link'        => 'SalesmenCommissionSettings.php',
                'description' =>  _('Salesmen commission settings'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_ADMIN_VENTES,UserAccount::PROFILE_AERO_ADMIN_VENTES,UserAccount::PROFILE_DIR_COMMERCIAL)
            ),
            array(
                'title'       => _('Supplying settings'),
                'link'        => 'SupplierDelayStock.php',
                'description' => _('Supplying settings'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW)
            ),
            array(
                'title'       => _('Zones'),
                'link'        => 'ZoneList.php',
                'description' => _('list of zones'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW)
            ),
            array(
                'title'       => _('Services'),
                'link'        => 'dispatcher.php?entity=Prestation',
                'description' => _('List of services'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_AERO_ADMIN_VENTES)
            ),
            array(
                'title'       => _('Airplane types'),
                'link'        => 'dispatcher.php?entity=FlyType',
                'description' => _('List of airplane types'),
                'restrict_to' => array(UserAccount::PROFILE_ROOT),
                'restrict_to_context' => array('aero')
            ),
            array(
                'title'       => _('Costs'),
                'link'        => 'home.php?help_page=Ressources',
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW),
                'children'    => array(
                    array(
                        'title'       => _('Resources'),
                        'link'        => 'dispatcher.php?entity=Ressource',
                        'description' => _('List of resources'),
                        'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW)
                    ),
                    array(
                        'title'       => _('Product resources'),
                        'link'        => 'dispatcher.php?entity=Ressource&altname=ProductRessource',
                        'description' => _('List of product resources'),
                        'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW)
                    ),
                    array(
                        'title'       => _('Cost models'),
                        'link'        => 'dispatcher.php?entity=RessourceGroup',
                        'description' => _('List of cost models'),
                        'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW)
                    )
                )
            ),
            // pret a porter
            array(
                'title'       => _('Data management'),
                'link'        => 'home.php',
                'description' => _('Data management'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_PRODUCT_MANAGER),
                'restrict_to_context' => array('readytowear'),
                'children'    => array(
                    array(
                        'title'       => _('Colors'),
                        'link'        => 'dispatcher.php?entity=RTWColor',
                        'description' => _('Colors management'),
                        'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_PRODUCT_MANAGER),
                        'restrict_to_context' => array('readytowear'),
                    ),
                    array(
                        'title'       => _('Sizes'),
                        'link'        => 'dispatcher.php?entity=RTWSize',
                        'description' => _('Sizes management'),
                        'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_PRODUCT_MANAGER),
                        'restrict_to_context' => array('readytowear'),
                    ),
                    array(
                        'title'       => _('Seasons'),
                        'link'        => 'dispatcher.php?entity=RTWSeason',
                        'description' => _('Seasons management'),
                        'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_PRODUCT_MANAGER),
                        'restrict_to_context' => array('readytowear'),
                    ),
                    array(
                        'title'       => _('Construction types'),
                        'link'        => 'dispatcher.php?entity=RTWConstructionType',
                        'description' => _('Construction types management'),
                        'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_PRODUCT_MANAGER),
                        'restrict_to_context' => array('readytowear'),
                    ),
                    array(
                        'title'       => _('Construction codes'),
                        'link'        => 'dispatcher.php?entity=RTWConstructionCode',
                        'description' => _('Construction codes management'),
                        'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_PRODUCT_MANAGER),
                        'restrict_to_context' => array('readytowear'),
                    ),
                    array(
                        'title'       => _('Shapes'),
                        'link'        => 'dispatcher.php?entity=RTWShape',
                        'description' => _('Shapes management'),
                        'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_PRODUCT_MANAGER),
                        'restrict_to_context' => array('readytowear'),
                    ),
                    array(
                        'title'       => _('Press names'),
                        'link'        => 'dispatcher.php?entity=RTWPressName',
                        'description' => _('Press names management'),
                        'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_PRODUCT_MANAGER),
                        'restrict_to_context' => array('readytowear'),
                    ),
                    array(
                        'title'       => _('Labels (griffes)'),
                        'link'        => 'dispatcher.php?entity=RTWLabel',
                        'description' => _('Labels (griffes) management'),
                        'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_PRODUCT_MANAGER),
                        'restrict_to_context' => array('readytowear'),
                    ),
                    array(
                        'title'       => _('Heel heights'),
                        'link'        => 'dispatcher.php?entity=RTWHeelHeight',
                        'description' => _('Heel heights management'),
                        'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_PRODUCT_MANAGER),
                        'restrict_to_context' => array('readytowear'),
                    ),
                )
            ),
            array(
                'title'       => _('Materials management'),
                'link'        => 'dispatcher.php?entity=RTWMaterial',
                'description' => _('Materials management'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_PRODUCT_MANAGER),
                'restrict_to_context' => array('readytowear'),
            ),
            array(
                'title'       => _('Worksheets'),
                'link'        => 'dispatcher.php?entity=RTWModel',
                'description' => _('Worksheets management'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_PRODUCT_MANAGER),
                'restrict_to_context' => array('readytowear'),
            ),
        )
    ),
    // }}}
    // Pricing {{{
    array(
        'title'       => _('Pricing'),
        'link'        => 'home.php?help_page=Pricing',
        'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW),
        'children'    => array(
            array(
                'title'       => _('Selling prices'),
                'link'        => 'dispatcher.php?entity=PriceByCurrency&altname=SellingPrices',
                'description' => _('Selling prices'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_ADMIN_VENTES,UserAccount::PROFILE_AERO_ADMIN_VENTES)
            ),
            array(
                'title'       => _('Supplier prices'),
                'link'        => 'dispatcher.php?entity=PriceByCurrency&altname=SupplierPrices',
                'description' => _('Supplier prices'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_ADMIN_VENTES,UserAccount::PROFILE_AERO_ADMIN_VENTES)
            ),
            array(
                'title'       => _('Seasonalities'),
                'link'        => 'SaisonalityList.php',
                'description' => _('List of seasonalities'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW)
            ),
            array(
                'title'       => _('Offers on sale'),
                'link'        => 'PromotionList.php',
                'description' => _('List of offers on sale'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW)
            ),
            array(
                'title'       => _('Discounts by category'),
                'link'        => 'ProductHandingByCategoryList.php',
                'description' => _('List of discounts by customer category'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_ADMIN_VENTES,UserAccount::PROFILE_AERO_ADMIN_VENTES)
            )
        )
    ),
    // }}}
    // Nomenclatures {{{
    array(
        'title'       => _('Nomenclatures'),
        'link'        => 'home.php?help_page=Nomenclatures',
        'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW),
        'children'    => array(
            array(
                'title'       => _('Models'),
                'link'        => 'NomenclatureList.php',
                'description' => _('List of nomenclatures models'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW)
            ),
            array(
                'title'       => _('SN/Lots'),
                'link'        => 'NomenclatureConcreteProductList.php',
                'description' => _('List of SN/Lots'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW)
            )
        )
    ),
    // }}}
    // Commandes {{{
    array(
        'title'       => _('Orders'),
        'link'        => 'home.php?help_page=Commandes',
        'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_ADMIN_VENTES,UserAccount::PROFILE_COMMERCIAL,UserAccount::PROFILE_CUSTOMER,UserAccount::PROFILE_AERO_CUSTOMER,UserAccount::PROFILE_AERO_ADMIN_VENTES,UserAccount::PROFILE_AERO_INSTRUCTOR,UserAccount::PROFILE_CLIENT_TRANSPORT,UserAccount::PROFILE_DIR_COMMERCIAL,UserAccount::PROFILE_OWNER_CUSTOMER, UserAccount::PROFILE_PRODUCT_MANAGER),
        'children'    => array(
            array(
                'title'       => _('Supplier catalogue'),
                'link'        => 'SupplierCatalog.php',
                'description' => _('Supplier catalogue'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_ADMIN_VENTES,UserAccount::PROFILE_AERO_ADMIN_VENTES,UserAccount::PROFILE_DIR_COMMERCIAL, UserAccount::PROFILE_PRODUCT_MANAGER)
            ),
            array(
                'title'       => _('Customer catalogue'),
                'link'        => 'CustomerCatalog.php',
                'description' => _('Customer catalogue'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_CUSTOMER,UserAccount::PROFILE_COMMERCIAL,UserAccount::PROFILE_ADMIN_VENTES,UserAccount::PROFILE_AERO_ADMIN_VENTES,UserAccount::PROFILE_DIR_COMMERCIAL,UserAccount::PROFILE_OWNER_CUSTOMER,UserAccount::PROFILE_PRODUCT_MANAGER)
            ),
            array(
                'title'       => _('Carriage service'),
                'link'        => 'TransportChainList.php',
                'description' => _('List of carriage services'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_CLIENT_TRANSPORT,UserAccount::PROFILE_DIR_COMMERCIAL)
            ),
            array(
                'title'       => _('Class booking'),
                'link'        => 'CourseCommand.php?Cancel=1',
                'description' => _('Class booking'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_AERO_CUSTOMER,UserAccount::PROFILE_AERO_ADMIN_VENTES,UserAccount::PROFILE_AERO_INSTRUCTOR,UserAccount::PROFILE_DIR_COMMERCIAL),
                'restrict_to_context' => array('aero')
            ),
            /*
            array(
                'title'       => _('Catalogue'),
                'link'        => 'RTWCatalog.php',
                'description' => _('Catalogue'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_PRODUCT_MANAGER),
                'restrict_to_context' => array('readytowear'),
            )
            */
        )
    ),
    // }}}
    // Suivi commandes {{{
    array(
        'title'       => _('Orders follow-up'),
        'link'        => 'home.php?help_page=Commandes',
        'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_ACTOR,UserAccount::PROFILE_ADMIN_VENTES,UserAccount::PROFILE_COMMERCIAL,UserAccount::PROFILE_CUSTOMER,UserAccount::PROFILE_AERO_ADMIN_VENTES,UserAccount::PROFILE_AERO_CUSTOMER,UserAccount::PROFILE_CLIENT_TRANSPORT,UserAccount::PROFILE_DIR_COMMERCIAL,UserAccount::PROFILE_GED_PROJECT_MANAGER, UserAccount::PROFILE_PRODUCT_MANAGER),
        'children'    => array(
            array(
                'title'       => _('Product orders'),
                'link'        => 'CommandList.php',
                'description' => _('State of orders'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_ACTOR,UserAccount::PROFILE_ADMIN_VENTES,UserAccount::PROFILE_COMMERCIAL,UserAccount::PROFILE_CUSTOMER,UserAccount::PROFILE_AERO_ADMIN_VENTES,UserAccount::PROFILE_DIR_COMMERCIAL,UserAccount::PROFILE_GED_PROJECT_MANAGER, UserAccount::PROFILE_PRODUCT_MANAGER)
            ),
            array(
                'title'       => _('Carriage orders'),
                'link'        => 'ChainCommandList.php',
                'description' => _('State of orders'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_CLIENT_TRANSPORT,UserAccount::PROFILE_DIR_COMMERCIAL)
            ),
            array(
                'title'       => _('Class bookings'),
                'link'        => 'CourseCommandList.php',
                'description' => _('Class bookings follow-up'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_AERO_ADMIN_VENTES,UserAccount::PROFILE_AERO_CUSTOMER,UserAccount::PROFILE_DIR_COMMERCIAL),
                'restrict_to_context' => array('aero')
            ),
            array(
                'title'       => _('Services'),
                'link'        => 'PrestationCommandList.php',
                'description' => _('Services invoices'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_DIR_COMMERCIAL)
            ),
            array(
                'title'       => _('Estimates'),
                'link'        => 'EstimateList.php',
                'description' => _('List of estimates'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_ADMIN_VENTES,UserAccount::PROFILE_AERO_ADMIN_VENTES, UserAccount::PROFILE_PRODUCT_MANAGER)
            ),
            array(
                'title'       => _('Credit notes'),
                'link'        => 'ToHaveList.php',
                'description' => _('List of credit notes'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_ADMIN_VENTES,UserAccount::PROFILE_AERO_ADMIN_VENTES)
            ),
        )
    ),
    // }}}
    // Trésorerie {{{
    array(
        'title'       => _('Cash flow'),
        'link'        => 'home.php?help_page=Trésorerie',
        'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_VENTES,UserAccount::PROFILE_AERO_ADMIN_VENTES,UserAccount::PROFILE_ACCOUNTANT,UserAccount::PROFILE_SUBSIDIARY_ACCOUNTANT),
        'children'    => array(
            array(
                'title'       => _('Accounts'),
                'link'        => 'dispatcher.php?entity=Account',
                'description' => _('Accounts management'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ACCOUNTANT,UserAccount::PROFILE_ADMIN_VENTES,UserAccount::PROFILE_AERO_ADMIN_VENTES,UserAccount::PROFILE_SUBSIDIARY_ACCOUNTANT)
            ),
            array(
                'title'       => _('Banks'),
                'link'        => 'BankList.php',
                'description' => _('List of banks'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN)
            ),
            array(
                'title' => _('Models'),
                'link' => 'home.php',
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ACCOUNTANT),
                'children' => array(
                    array(
                    'title'       => _('Accounting types'),
                    'link'        => 'dispatcher.php?entity=AccountingType',
                    'description' => _('Accounting types management'),
                    'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ACCOUNTANT,UserAccount::PROFILE_ADMIN_VENTES,UserAccount::PROFILE_AERO_ADMIN_VENTES,UserAccount::PROFILE_SUBSIDIARY_ACCOUNTANT)
                    ),
                    array(
                    'title'       => _('Categories of expenses and receipts'),
                    'link'        => 'dispatcher.php?entity=FlowCategory',
                    'description' => _('Categories of expenses and receipts'),
                    'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ACCOUNTANT,UserAccount::PROFILE_SUBSIDIARY_ACCOUNTANT)
                    ),
                    array(
                    'title'       => _('Expenses and receipts models'),
                    'link'        => 'dispatcher.php?entity=FlowType',
                    'description' => _('Expenses and receipts models'),
                    'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ACCOUNTANT,UserAccount::PROFILE_SUBSIDIARY_ACCOUNTANT)
                    ),
                )
            ),
            array(
                'title'       => _('Forecasts of expenses and receipts'),
                'link'        => 'dispatcher.php?entity=ForecastFlow',
                'description' => _('Forecasts of expenses and receipts'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ACCOUNTANT,UserAccount::PROFILE_SUBSIDIARY_ACCOUNTANT)
            ),
            array(
                'title'       => _('Expenses and receipts'),
                'link'        => 'FlowList.php',
                'description' => _('Expenses and receipts'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ACCOUNTANT,UserAccount::PROFILE_SUBSIDIARY_ACCOUNTANT)
            ),
            array(
                'title'       => _('History'),
                'link'        => 'InvoiceList.php',
                'description' => _('History'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_VENTES,UserAccount::PROFILE_AERO_ADMIN_VENTES,UserAccount::PROFILE_ACCOUNTANT,UserAccount::PROFILE_SUBSIDIARY_ACCOUNTANT)
            ),
            array(
                'title'       => _('Accounts receivable aging'),
                'link'        => 'AgedTrialBalance.php',
                'description' => _('Accounts receivable aging'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ACCOUNTANT,UserAccount::PROFILE_SUBSIDIARY_ACCOUNTANT)
            ),
            array(
                'title'       => _('Cash flow'),
                'link'        => 'CashBalance.php',
                'description' => _('Cash flow'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ACCOUNTANT,UserAccount::PROFILE_SUBSIDIARY_ACCOUNTANT)
            ),
            array(
                'title'       => _('Breaking-down'),
                'link'        => 'ApportionmentExport.php',
                'description' => _('Breaking-down data export'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ACCOUNTANT,UserAccount::PROFILE_SUBSIDIARY_ACCOUNTANT)
            ),
            array(
                'title'       => _('Currency converter'),
                'link'        => 'dispatcher.php?entity=CurrencyConverter',
                'description' => _('Currency converter'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ACCOUNTANT,UserAccount::PROFILE_SUBSIDIARY_ACCOUNTANT)
            )
        )
    ),
    // }}}
    // O.T. {{{
    array(
        'title'       => _('Work order'),
        'link'        => 'home.php?help_page=O.T.',
        'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_ACTOR,UserAccount::PROFILE_SUPERVISOR,UserAccount::PROFILE_ADMIN_VENTES,UserAccount::PROFILE_AERO_ADMIN_VENTES,UserAccount::PROFILE_GESTIONNAIRE_STOCK,UserAccount::PROFILE_TRANSPORTEUR),
        'children'    => array(
            array(
                'title'       => _('Scheduled chains'),
                'link'        => 'ActivatedChainList.php',
                'description' => _('List of activated chains'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_ACTOR,UserAccount::PROFILE_ADMIN_VENTES,UserAccount::PROFILE_AERO_ADMIN_VENTES)
            ),
            array(
                'title'       => _('Work orders'),
                'link'        => 'WorkOrderList.php',
                'description' => _('Work orders management'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_ACTOR,UserAccount::PROFILE_SUPERVISOR,UserAccount::PROFILE_ADMIN_VENTES,UserAccount::PROFILE_GESTIONNAIRE_STOCK,UserAccount::PROFILE_TRANSPORTEUR)
            ),
            array(
                'title'       => _('Regrouping in work order'),
                'link'        => 'ActivatedChainOperationList.php',
                'description' => _('Regrouping in work order'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_ACTOR,UserAccount::PROFILE_ADMIN_VENTES,UserAccount::PROFILE_AERO_ADMIN_VENTES,UserAccount::PROFILE_GESTIONNAIRE_STOCK,UserAccount::PROFILE_TRANSPORTEUR)
            )
        )
    ),
    // }}}
    // Stock {{{
    array(
        'title'       => _('Stock'),
        'link'        => 'home.php?help_page=Stock',
        'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_SUPPLIER,UserAccount::PROFILE_ACTOR,UserAccount::PROFILE_GESTIONNAIRE_STOCK,UserAccount::PROFILE_SUPPLIER_CONSIGNE,UserAccount::PROFILE_OPERATOR,UserAccount::PROFILE_ADMIN_VENTES,UserAccount::PROFILE_AERO_ADMIN_VENTES,UserAccount::PROFILE_OWNER_CUSTOMER),
        'children'    => array(
            array(
                'title'       => _('Amount of stock'),
                'link'        => 'StockProductRealandVirtualList.php',
                'description' => _('Amount of stock'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_SUPPLIER,UserAccount::PROFILE_SUPPLIER_CONSIGNE,UserAccount::PROFILE_GESTIONNAIRE_STOCK,UserAccount::PROFILE_OPERATOR,UserAccount::PROFILE_ADMIN_VENTES,UserAccount::PROFILE_AERO_ADMIN_VENTES,UserAccount::PROFILE_OWNER_CUSTOMER)
            ),
            array(
                'title'       => _('Stock state'),
                'link'        => 'StockStorageSiteList.php',
                'description' => _('List of storage sites'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_ACTOR,UserAccount::PROFILE_SUPPLIER_CONSIGNE,UserAccount::PROFILE_GESTIONNAIRE_STOCK,UserAccount::PROFILE_OPERATOR)
            ),
            array(
                'title'       => _('Expected movements'),
                'link'        => 'ActivatedMovementList.php',
                'description' => _('Expected movements'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_GESTIONNAIRE_STOCK,UserAccount::PROFILE_OPERATOR,UserAccount::PROFILE_ADMIN_VENTES,UserAccount::PROFILE_AERO_ADMIN_VENTES)
            ),
            array(
                'title'       => _('Unexpected movements'),
                'link'        => 'ActivatedMovementAddWithoutPrevision.php',
                'description' => _('Unexpected movement execution'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_GESTIONNAIRE_STOCK,UserAccount::PROFILE_OPERATOR)
            ),
            array(
                'title'       => _('History'),
                'link'        => 'LocationExecutedMovementList.php',
                'description' => _('List of executed movements'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_GESTIONNAIRE_STOCK,UserAccount::PROFILE_SUPPLIER_CONSIGNE,UserAccount::PROFILE_ADMIN_VENTES,UserAccount::PROFILE_AERO_ADMIN_VENTES,UserAccount::PROFILE_OWNER_CUSTOMER)
            ),
            array(
                'title'       => _('Inventory'),
                'link'        => 'InventoryDetailList.php',
                'description' => _('Inventory'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_GESTIONNAIRE_STOCK)
            ),
            array(
                'title'       => _('Material accounting'),
                'link'        => 'StockAccountingExport.php',
                'description' => _('Material accounting'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_TRANSPORTEUR,UserAccount::PROFILE_GESTIONNAIRE_STOCK)
            ),
            array(
                'title'       => _('Supplying optimization'),
                'link'        => 'SupplyingOptimization.php',
                'description' => _('Supplying quantities optimization'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN)
            ),
            array(
                'title'       => _('Regrouping'),
                'link'        => 'GroupableBoxActivatedChainTaskList.php',
                'description' => _('List of regrouping tasks'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_TRANSPORTEUR,UserAccount::PROFILE_GESTIONNAIRE_STOCK)
            ),
            array(
                'title'       => _('History of regroupings'),
                'link'        => 'BoxList.php',
                'description' => _('History of regroupings'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_TRANSPORTEUR,UserAccount::PROFILE_GESTIONNAIRE_STOCK)
            )
        )
    ),
    // }}}
    // Documents {{{
    array(
        'title'       => _('Documents'),
        'link'        => 'home.php?help_page=Documents',
        'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_GESTIONNAIRE_STOCK,UserAccount::PROFILE_OPERATOR,UserAccount::PROFILE_ADMIN_VENTES,UserAccount::PROFILE_AERO_ADMIN_VENTES,UserAccount::PROFILE_TRANSPORTEUR,UserAccount::PROFILE_DIR_COMMERCIAL),
        'children'    => array(
            array(
                'title'       => _('Models'),
                'link'        => 'DocumentModelList.php',
                'description' => _('List of document models'),
                'restrict_to' => array(UserAccount::PROFILE_ROOT)
                //'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_ADMIN_VENTES,UserAccount::PROFILE_AERO_ADMIN_VENTES)
            ),
            array(
                'title'       => _('Reprinting of documents'),
                'link'        => 'DocumentList.php',
                'description' => _('Reprinting of documents'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_GESTIONNAIRE_STOCK,UserAccount::PROFILE_OPERATOR,UserAccount::PROFILE_ADMIN_VENTES,UserAccount::PROFILE_AERO_ADMIN_VENTES,UserAccount::PROFILE_TRANSPORTEUR,UserAccount::PROFILE_DIR_COMMERCIAL)
            ),
            array(
                'title'       => _('Lookbook printing'),
                'link'        => 'dispatcher.php?entity=RTWModel&altname=RTWModelForLookbook',
                'description' => _('Lookbook printing'),
                'restrict_to_context' => array('readytowear'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_ADMIN_VENTES,UserAccount::PROFILE_AERO_ADMIN_VENTES,UserAccount::PROFILE_TRANSPORTEUR,UserAccount::PROFILE_DIR_COMMERCIAL)
            ),
            /*
            array(
                'title'       => _('Document appendices'),
                'link'        => 'dispatcher.php?entity=DocumentAppendix',
                'description' => _('Document appendices'),
                'restrict_to_context' => array('readytowear'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_ADMIN_VENTES,UserAccount::PROFILE_AERO_ADMIN_VENTES,UserAccount::PROFILE_TRANSPORTEUR,UserAccount::PROFILE_DIR_COMMERCIAL)
            ),
            */
        )
    ),
    // }}}
    // Tableau de bord {{{
    array(
        'title'       => _('Scoreboard'),
        'link'        => 'home.php?help_page=Board',
        'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_DIR_COMMERCIAL),
        'children'    => array(
            array(
                'title'       => _('Salesman'),
                'link'        => 'home.php?help_page=Board',
                'description' => _('salesman'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_DIR_COMMERCIAL),
                'children' => array(
                    array(
                        'title'       => _('Turnover by salesman'),
                        'link'        => 'BoardByCommercial.php',
                        'description' => _('Turnover by salesman'),
                        'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_DIR_COMMERCIAL)
                    ),
                    array(
                        'title'       => _('Salesmen commissions'),
                        'link'        => 'SalesmenCommission.php',
                        'description' => _('Salesmen commissions'),
                        'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_DIR_COMMERCIAL)
                    )
                )
            ),
            array(
                'title'       => _('Customer'),
                'link'        => 'BoardByCustomer.php',
                'description' => _('Turnover by customer'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_DIR_COMMERCIAL)
            ),
            array(
                'title'       => _('Category'),
                'link'        => 'BoardByCategory.php',
                'description' => _('Turnover by category'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_DIR_COMMERCIAL)
            ),
            array(
                'title'       => _('Product'),
                'link'        => 'BoardByProduct.php',
                'description' => _('Turnover by product'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_DIR_COMMERCIAL)
            ),
            array(
                'title'       => _('Turnover by supplier'),
                'link'        => 'BoardByCoast.php',
                'description' => _('Turnover by supplier'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_DIR_COMMERCIAL)
            ),
            array(
                'title'       => _('Turnover by customer/supplier'),
                'link'        => 'BoardBySupplier.php',
                'description' => _('Details of customer turnover sorted by supplier'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_DIR_COMMERCIAL)
            ),
            array(
                'title'       => _('Activity based costing'),
                'link'        => 'ActivityBasedCosting.php',
                'description' => _('Activity based costing'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW)
            )
        )
    ),
    // }}}
    // Tâches {{{
    array(
        'title'       => _('Tasks'),
        'link'        => 'home.php?help_page=Tâches',
        'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_AERO_OPERATOR,UserAccount::PROFILE_AERO_CUSTOMER,UserAccount::PROFILE_AERO_INSTRUCTOR,UserAccount::PROFILE_SUPERVISOR,UserAccount::PROFILE_TRANSPORTEUR,UserAccount::PROFILE_GESTIONNAIRE_STOCK,UserAccount::PROFILE_OPERATOR,UserAccount::PROFILE_GED_PROJECT_MANAGER),
        'children'    => array(
            array(
                'title'       => _('Validation'),
                'link'        => 'ActivatedChainTaskList.php',
                'description' => _('List of tasks'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_SUPERVISOR,UserAccount::PROFILE_AERO_OPERATOR,UserAccount::PROFILE_AERO_CUSTOMER,UserAccount::PROFILE_AERO_INSTRUCTOR,UserAccount::PROFILE_OPERATOR,UserAccount::PROFILE_GED_PROJECT_MANAGER)
            ),
            array(
                'title'       => _('History'),
                'link'        => 'ActivatedChainTaskHistory.php',
                'description' => _('List of tasks'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_AERO_OPERATOR,UserAccount::PROFILE_AERO_CUSTOMER,UserAccount::PROFILE_AERO_INSTRUCTOR)
            ),
            array(
                'title'       => _('Time schedule'),
                'link'        => 'UnavailabilityPlanning.php?from=tasks',
                'description' => _('Time schedule'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_OPERATOR,UserAccount::PROFILE_GED_PROJECT_MANAGER)
            ),
            array(
                'title'       => _('Gantt'),
                'link'        => 'ActivatedChainList.php',
                'description' => _('List of activated chains'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_GED_PROJECT_MANAGER),
                'restrict_to_context' => array('consulting'),
            ),
        )
    ),
    // }}}
    // Vol {{{
    array(
        'title'       => _('Flights'),
        'link'        => 'home.php?help_page=Vol',
        'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_AERO_ADMIN_VENTES,UserAccount::PROFILE_AERO_CUSTOMER,UserAccount::PROFILE_AERO_INSTRUCTOR),
        'restrict_to_context' => array('aero'),
        'children'    => array(
            array(
                'title'       => _('Log book'),
                'link'        => 'FlightNotebook.php',
                'description' => _('Log book'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_AERO_ADMIN_VENTES,UserAccount::PROFILE_AERO_CUSTOMER,UserAccount::PROFILE_AERO_INSTRUCTOR),
                'restrict_to_context' => array('aero')
            ),
            array(
                'title'       => _('Board book'),
                'link'        => 'FlyBoardBook.php',
                'description' => _('Board book'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_AERO_ADMIN_VENTES,UserAccount::PROFILE_AERO_CUSTOMER,UserAccount::PROFILE_AERO_OPERATOR),
                'restrict_to_context' => array('aero')
            ),
            array(
                'title'       => _('Expected flights'),
                'link'        => 'ActivatedFlightList.php',
                'description' => _('List of expected flights'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_AERO_ADMIN_VENTES,UserAccount::PROFILE_AERO_OPERATOR,UserAccount::PROFILE_AERO_INSTRUCTOR),
                'restrict_to_context' => array('aero')
            ),
            array(
                'title'       => _('Completed flights'),
                'link'        => 'FlightNotebookAdmin.php',
                'description' => _('List of completed flights'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW),
                'restrict_to_context' => array('aero')
            ),
            array(
                'title'       => _('Time schedule'),
                'link'        => 'UnavailabilityPlanning.php',
                'description' => _('Weekly schedule'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_ADMIN_WITHOUT_CASHFLOW,UserAccount::PROFILE_AERO_ADMIN_VENTES,UserAccount::PROFILE_AERO_CUSTOMER,UserAccount::PROFILE_AERO_OPERATOR,UserAccount::PROFILE_AERO_INSTRUCTOR),
                'restrict_to_context' => array('aero')
            )
        )
    ),
    // }}}
    // EDM {{{
    array(
        'title'       => _('EDM'),
        'description' => _('Electronic document management'),
        'link'        => 'home.php?help_page=EDM',
        'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_GED_PROJECT_MANAGER),
        'restrict_to_context' => array('consulting'),
        'children'    => array(
            array(
                'title'       => _('Documents'),
                'link'        => 'dispatcher.php?entity=UploadedDocument',
                'description' => _('List of uploaded documents'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN,UserAccount::PROFILE_GED_PROJECT_MANAGER),
                'restrict_to_context' => array('consulting')
            ),
            array(
                'title'       => _('Document Types'),
                'link'        => 'dispatcher.php?entity=UploadedDocumentType',
                'description' => _('List of document types'),
                'restrict_to' => array(UserAccount::PROFILE_ADMIN),
                'restrict_to_context' => array('consulting')
            ),
        )
    ),
    // }}}
);

?>
