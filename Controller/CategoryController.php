<?php

namespace Black\Bundle\CategoryBundle\Controller;

use Psr\Log\InvalidArgumentException;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Exception;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * Controller managing the categories`
 *
 * @Route("/category")
 */
class CategoryController extends Controller
{
    /**
     * Get a category (embed action)
     * 
     * @Method("GET")
     * @Route("/", name="_find_category")
     * @Template()
     * 
     * @return Template
     */
    public function categoryAction()
    {
        $documentManager    = $this->getManager();
        $documentRepository = $documentManager->getRepository();

        $tree = $documentRepository->childrenHierarchy(null, false, array(
                'decorate'  => true,
                'representationField' => 'slug',
                'html'  => true,
                'rootOpen' => function($tree) {
                    if(count($tree) && ($tree[0]['level'] == 1)){
                        return '<ul class="dropdown-menu" role="menu" aria-labelledby="dLabel">';
                    }
                },
                'rootClose' => function($child) {
                    if(count($child) && ($child[0]['level'] == 1)){
                        return '</ul>';
                    }
                },
                'nodeDecorator' => function($node) use (&$controller) {
                    return '<a href="/articles/category/'.$node['slug'].'.html">'.$node['slug'].'</a>';
                }
            ));

        if (!$tree) {
            $tree = array('items' => array());
        }

        return array(
            'document' => $tree,
        );
    }

    /**
     * Returns the DocumentManager
     *
     * @return DocumentManager
     */
    protected function getManager()
    {
        return $this->get('black_category.manager.category');
    }
}
