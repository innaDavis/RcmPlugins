<?php
/**
 * Created by PhpStorm.
 * User: rmcnew
 * Date: 4/16/14
 * Time: 4:13 PM
 */

namespace RcmInstanceConfig\Factory;


use RcmInstanceConfig\ViewHelper\RcmRichEdit;
use RcmInstanceConfig\ViewHelper\RcmTextEdit;
use Zend\ServiceManager\FactoryInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

class RcmTextEditFactory implements FactoryInterface
{
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
        $config = \HTMLPurifier_Config::createDefault();
        $htmlPurifier = new \HTMLPurifier($config);
        return new RcmTextEdit($htmlPurifier);
    }
} 