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
    
    /**
     * @Route("/cascadeselect/test/entity", name="cascadeselect-entity")
     * @Template("SDLabSmartUtilsBundle:Test:Base/entity.html.twig")
     */
    public function entityAction()
    {
        $em = $this->get('doctrine')->getManager();
        $qb = $em->getRepository('SDLabSmartUtilsBundle:LevelA')->createQueryBuilder('a');
        $qb ->select('a', 'b', 'c')
            ->leftJoin('a.children', 'b')
            ->leftJoin('b.children', 'c')
        ;
        
        $data = $qb->getQuery()->getResult();
                 
        $cascadeData = $this->get('cascade_select_manager')->manage($data, array(
            1 => array(
                'nextLevelAccessor' => 'children',
                'valueAccessor' => 'id',
                'labelAccessor' => 'name'
            ),
            2 => array(
                'nextLevelAccessor' => 'children',
                'valueAccessor' => 'id',
                'labelAccessor' => 'name'
            ),
            3 => array(
                'valueAccessor' => 'id',
                'labelAccessor' => 'name'
            )
        ));
        
        return array(
            'cascadeData' => $cascadeData
        );
    }
    
    /**
     * @Route("/cascadeselect/test/group", name="cascadeselect-group")
     * @Template("SDLabSmartUtilsBundle:Test:Base/group.html.twig")
     */
    public function groupAction()
    {
        return array();
    }
    
    /**
     * @Route("/cascadeselect/test/select2", name="cascadeselect-select2")
     * @Template("SDLabSmartUtilsBundle:Test:Base/select2.html.twig")
     */
    public function select2Action()
    {
        return array();
    }
    
    /**
     * @Route("/cascadeselect/test/entity-select2", name="cascadeselect-entity-select2")
     * @Template("SDLabSmartUtilsBundle:Test:Base/entitySelect2.html.twig")
     */
    public function entitySelect2Action()
    {
        $em = $this->get('doctrine')->getManager();
        $qb = $em->getRepository('SDLabSmartUtilsBundle:LevelA')->createQueryBuilder('a');
        $qb ->select('a', 'b', 'c')
            ->leftJoin('a.children', 'b')
            ->leftJoin('b.children', 'c')
        ;
        
        $data = $qb->getQuery()->getResult();
                 
        $cascadeData = $this->get('cascade_select_manager')->manage($data, array(
            1 => array(
                'nextLevelAccessor' => 'children',
                'valueAccessor' => 'id',
                'labelAccessor' => 'name'
            ),
            2 => array(
                'nextLevelAccessor' => 'children',
                'valueAccessor' => 'id',
                'labelAccessor' => 'name'
            ),
            3 => array(
                'valueAccessor' => 'id',
                'labelAccessor' => 'name',
                'groupAccessor' => 'group',
                'extraDataAccessor' => 'extraData'
            )
        ));
        
        return array(
            'cascadeData' => $cascadeData
        );
    }
    
}
