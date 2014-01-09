<?php

/**
 * Module Config For ZF2
 *
 * PHP version 5.4
 *
 * LICENSE: New BSD License
 *
 * @category  Reliv
 * @package   RcmNumberFormat
 * @author    Rod McNew <rmcnew@relivinc.com>
 * @copyright 2013 Reliv International
 * @license   License.txt New BSD License
 * @version   GIT: <git_id>
 * @link      http://ci.reliv.com/confluence
 */

namespace RcmNumberFormat;

use RcmNumberFormat\Controller\NumberFormatController;

/**
 * ZF2 Module Config.  Required by ZF2
 *
 * ZF2 requires a Module.php file to load up all the Module Dependencies.  This
 * file has been included as part of ZF2 standards.
 *
 * @category  Reliv
 * @package   RcmPlugins\RcmHtmlArea
 * @author    Rod McNew <rmcnew@relivinc.com>
 * @copyright 2012 Reliv Inernational
 * @license   License.txt New BSD License
 * @version   Release: 1.0
 */
class Module
{

    /**
     * Returns ZF2 controller config for this module
     * @return array
     */
    function getControllerConfig()
    {
        return array(
            'factories' => array(
                'rcmNumberFormatController' => function ($controllerMgr) {
                        $serviceMgr = $controllerMgr->getServiceLocator();
                        $controller = new NumberFormatController(
                            $serviceMgr->get('rcmNumberFormatter'),
                            $serviceMgr->get('rcmCurrencyFormatter')
                        );
                        return $controller;
                    }
            )
        );
    }

    /**
     * Returns ZF2 auto-loader config
     * @return array
     */
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    /**
     * Returns ZF2 config for this module
     * @return mixed
     */
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
}