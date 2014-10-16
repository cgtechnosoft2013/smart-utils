<?php

namespace SDLab\Bundle\SmartUtilsBundle\Test\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class BaseController extends Controller
{
    
    /**
     * @Route("/cascadeselect/test/base", name="cascadeselect-base")
     * @Template("SDLabSmartUtilsBundle:Test:Base/base.html.twig")
     */
    public function baseAction()
    {
        return array();
    }
    
    /**
     * @Route("/cascadeselect/test/multiple", name="cascadeselect-multiple")
     * @Template("SDLabSmartUtilsBundle:Test:Base/multiple.html.twig")
     */
    public function multipleAction()
    {
        return array();
    }
}
