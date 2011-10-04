<?php

namespace Sfby\StaticBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\HttpFoundation\Request;
use \Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sfby\StaticBundle\Entity\Product;
use Sfby\StaticBundle\Entity\Category;
use Sfby\StaticBundle\Form\ProductType;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use JMS\SecurityExtraBundle\Annotation\Secure;


class ContentController extends Controller
{

    /**
     * @Route("/", name="homepage")
     * @Template
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/login", name="login")
     * @Template
     */
    public function loginAction()
    {
        $request = $this->getRequest();
        $session = $request->getSession();

        // get the login error if there is one
        if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR))
        {
            $error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
        }
        else
        {
            $error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
        }

        return array(
            'last_username' => $session->get(SecurityContext::LAST_USERNAME),
            'error' => $error,
        );
    }

    /**
     * @Route("/test", name="test")
     */
    public function testAction()
    {
        if (false === $this->get('security.context')->isGranted('ROLE_ADMIN'))
        {
            throw new AccessDeniedException();
        }

        return new Response('Open');
    }

    /**
     * @Template
     */
    public function menuAction($active)
    {
        return array(
            'active' => $active, 
            'user' => $user = $this->get('security.context')->getToken()->getUser()
        );
    }

    /**
     * @Route("/create", name="create")
     * @Secure(roles="ROLE_ADMIN")
     */
    public function createAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        for ($a = 0; $a < 10; $a++)
        {
            $c = new Category();
            $c->setName('Categiry ' . $a);
            $em->persist($c);

            $p = new Product();
            $p->setName('test ' . $a);
            $p->setPrice(10 * $a);
            $p->setDescription('Lorem ipsum dolor');
            $p->setCategory($c);
//            $p->setCategoryId(1);
            $em->persist($p);
        }
        $em->flush();
        return $this->forward('SfbyStaticBundle:Content:list');
    }

    /**
     * @Route("/list", name="list")
     * @Route("/{_locale}/list", name="list_loc", defaults={"_locale"="0"}, requirements={"_locale"="en|ru"})
     * @Template
     */
    public function listAction()
    {
        $r = $this->getRequest();
        if ($r->get('_locale')) $this->get('session')->setLocale($r->get('_locale'));
        
        $dql = "SELECT p,c FROM SfbyStaticBundle:Product p JOIN p.category c ";
        $products = $this->getDoctrine()->getEntityManager()->createQuery($dql);
        $products->setMaxResults(10);
        if ($this->get('security.context')->isGranted('ROLE_ADMIN'))
        {
            $products->setMaxResults(30);
        }
//        $products = $this->getDoctrine()->getRepository('SfbyStaticBundle:Product')
//                ->createQueryBuilder('p')
////                ->innerJoin('p', 'Category', 'c', 'p.category_id=c.id')
//                ->setMaxResults(20)
//                ->getQuery()->getResult();

        return array(
            'products' => $products->getResult(),
            'service' => $this->get('test')->get()
        );
    }

    /**
     * @Route("/edit/{id}", name="edit")
     * @Secure(roles="ROLE_ADMIN")
     * @Template
     */
    public function editAction(Request $request)
    {
        $id = $request->get('id');
        $em = $this->getDoctrine()->getEntityManager();
        $product = $em->getRepository('SfbyStaticBundle:Product')->find($id);

        if (!$product)
        {
            throw $this->createNotFoundException('No product found for id ' . $id);
        }

        $form = $this->createForm(new ProductType(), $product);

        if ($request->getMethod() == 'POST')
        {
            $form->bindRequest($request);
            if ($form->isValid())
            {
//                $em->persist($task);
                $em->flush();
                return $this->redirect($this->generateUrl('list'));
            }
        }

        return array('form' => $form->createView(),
            'error' => $form->getErrors(),
            'product' => $product,
        );
    }

    /**
     * @Route("/delete/{id}", name="delete")
     * @Secure(roles="ROLE_ADMIN")
     */
    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $product = $em->getRepository('SfbyStaticBundle:Product')->find($id);

        if (!$product)
        {
            throw $this->createNotFoundException('No product found for id ' . $id);
        }

        $em->remove($product);
        $em->flush();

        return $this->forward('SfbyStaticBundle:Content:list');
    }

    /**
     * @Route("/deleteAll", name="deleteAll")
     * @Secure(roles="ROLE_ADMIN")
     */
    public function deleteAllAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $arr = $em->getRepository('SfbyStaticBundle:Product')->findAll();
        foreach ($arr as $product)
        {
            $em->remove($product);
        }

        $arr = $em->getRepository('SfbyStaticBundle:Category')->findAll();
        foreach ($arr as $category)
        {
            $em->remove($category);
        }
        $em->flush();

        return $this->forward('SfbyStaticBundle:Content:list');
    }

    /**
     * @Route("/details/{id}", name="details")
     * @Template
     */
    public function detailsAction($id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $cat = $em->getRepository('SfbyStaticBundle:Category')->find($id);

        $products = $cat->getProducts();

        return array('products' => $products);
    }

}
