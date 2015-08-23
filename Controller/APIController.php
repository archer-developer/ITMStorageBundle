<?php
/**
 * Created by PhpStorm.
 * User: archer
 * Date: 23.8.15
 * Time: 17.33
 */

namespace ITM\StorageBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use ITM\StorageBundle\Util\JsonAPITrait;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/api")
 */
class APIController extends Controller
{
    use JsonAPITrait;

    /**
     * @Route("/")
     * @Template()
     */
    public function helloAction()
    {
        return $this->response('ITM Storage API v.1.0');
    }
}