<?php
// src/Blogger/BlogBundle/Controller/CommentController.php

namespace Kunstmaan\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\MediaBundle\Form\GalleryType;
use Kunstmaan\MediaBundle\Form\SubGalleryType;
use Kunstmaan\MediaBundle\Entity\SlideGallery;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * slidegallery controller.
 *
 * @author Kristof Van Cauwenbergh
 */
class SlideGalleryController extends GalleryController
{
    /**
     * @Route("/", name="KunstmaanMediaBundle_slidegallery_new")
     */
    public function newAction(){
        $gallery = new SlideGallery();
        return $this->parentnewAction($gallery);
    }

    /**
     * @Route("/sub/{id}", requirements={"id" = "\d+"}, name="KunstmaanMediaBundle_slidegallery_subnew")
     */
    public function subnewAction($id){
        $gallery = new SlideGallery();
        return $this->parentsubnewAction($gallery,$id);
    }

    /**
     * @Route("/create", name="KunstmaanMediaBundle_slidegallery_create")
     * @Method({"POST"})
     */
    public function createAction(){
        $gallery = new SlideGallery();
        return $this->parentcreateAction($gallery, new \Kunstmaan\MediaBundle\Entity\Slide(), new \Kunstmaan\MediaBundle\Form\SlideType());
    }

    /**
     * @Route("/sub/create/{id}", requirements={"id" = "\d+"}, name="KunstmaanMediaBundle_slidegallery_subcreate")
     * @Method({"POST"})
     */
    public function subcreateAction($id){
        $gallery = new SlideGallery();
        return $this->parentsubcreateAction($gallery,$id, new \Kunstmaan\MediaBundle\Entity\Slide(), new \Kunstmaan\MediaBundle\Form\SlideType());
    }

}