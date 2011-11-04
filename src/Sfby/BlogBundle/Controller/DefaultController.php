<?php

namespace Sfby\BlogBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

use Sfby\BlogBundle\Entity\Category;
use Sfby\BlogBundle\Entity\Blog;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="blog_index")
     * @Template()
     */
    public function indexAction()
    {
        $rep = $this->getDoctrine()->getRepository('Sfby\BlogBundle\Entity\Blog');
        return array(
            'blogs' => $rep->findAll(),
        );
    }
    
    /**
     * @Route("/{slug}", name="blog_category")
     * @Template("SfbyBlogBundle:Default:index.html.twig")
     */

    public function categoryAction(Category $category)
    {
        return array(
            'blogs' => $category->getBlogs(),
        );
    }
    
    /**
     * component
     * 
     * @Template
     */
    public function submenuAction()
    {
        $rep = $this->getDoctrine()->getRepository('Sfby\BlogBundle\Entity\Category');
        
        return array(
            'categories' => $rep->findAll(),
            'active' => $this->getRequest()->get('active'),
        );
    }
    
    /**
     * component
     * 
     * @Template("SfbyBlogBundle:Default:list.html.twig")
     */
    public function recentAction()
    {
        $rep = $this->getDoctrine()->getRepository('Sfby\BlogBundle\Entity\Blog');
        return array(
            'blogs' => $rep->findAll(),
        );
    }
    
    /**
     * component
     * 
     * @Template
     */
    public function tagCloudAction()
    {
        return array();
    }
    
    /**
     * component
     * 
     * @Template
     */
    public function newUsersAction()
    {
        return array();
    }
    
    /**
     * component
     * 
     * @Template
     */
    public function lastCommentsAction()
    {
        return array();
    }
    
    /**
     * component
     * 
     * @Template
     */
    public function userBlogsAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        return array(
            
        );
    }
    
    /**
     * component
     * 
     * @Template
     */
    public function userCommentsAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        return array(
            
        );
    }
    
    /**
     * component
     * 
     * @Template
     */
    public function userRatesAction()
    {
        $user = $this->get('security.context')->getToken()->getUser();
        return array(
            
        );
    }
}