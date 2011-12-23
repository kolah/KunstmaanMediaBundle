<?php
// src/Blogger/BlogBundle/Controller/CommentController.php

namespace Kunstmaan\MediaBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Kunstmaan\MediaBundle\Entity\ImageGallery;
use Kunstmaan\MediaBundle\Entity\SlideGallery;
use Kunstmaan\MediaBundle\Entity\VideoGallery;
use Kunstmaan\MediaBundle\Entity\FileGallery;
use Kunstmaan\MediaBundle\Form\GalleryType;
use Kunstmaan\MediaBundle\Form\SubGalleryType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;

/**
 * imagegallery controller.
 *
 * @author Kristof Van Cauwenbergh
 */
class GalleryController extends Controller
{
    /**
     * @Route("/{id}/{slug}", requirements={"id" = "\d+"}, name="KunstmaanMediaBundle_gallery_show")
     * @Template()
     */
    function showAction($id){
        $em = $this->getDoctrine()->getEntityManager();
        $gallery = $em->getRepository('KunstmaanMediaBundle:Gallery')->getGallery($id, $em);
        $galleries = $em->getRepository('KunstmaanMediaBundle:Gallery')
                                ->getAllGalleriesByType();

        $itemlist = "";
        $listconfigurator = $gallery->getStrategy()->getListConfigurator();
        if(isset($listconfigurator) && $listconfigurator != null){
            $itemlist = $this->get("adminlist.factory")->createList($listconfigurator, $em);
            $itemlist->bindRequest($this->getRequest());
        }

        $form = $this->createForm($gallery->getStrategy()->getFormType(), $gallery->getStrategy()->getFormHelper());
        $sub = $gallery->getStrategy()->getNewGallery();
        $sub->setParent($gallery);
        $subform = $this->createForm(new SubGalleryType(), $sub);
        $editform = $this->createForm($gallery->getFormType($gallery), $gallery);

        return array(
            'form'          => $form->createView(),
            'subform'       => $subform->createView(),
            'editform'      => $editform->createView(),
            'gallery'       => $gallery,
            'galleries'     => $galleries,
            'itemlist'      => $itemlist
        );
    }

    /**
     * @Route("/delete/{gallery_id}", requirements={"gallery_id" = "\d+"}, name="KunstmaanMediaBundle_gallery_delete")
     */
    public function deleteAction($gallery_id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $gallery = $em->getRepository('KunstmaanMediaBundle:Gallery')->getGallery($gallery_id, $em);
        
        $em->getRepository('KunstmaanMediaBundle:Gallery')->delete($gallery, $em);

        $galleries = $em->getRepository('KunstmaanMediaBundle:Gallery')
                        ->getAllGalleriesByType();

        return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_media_'.$gallery->getStrategy()->getType().'s'));
    }

    /**
     * @Route("/update/{gallery_id}", requirements={"gallery_id" = "\d+"}, name="KunstmaanMediaBundle_gallery_edit")
     * @Method({"GET", "POST"})
     * @Template()
     */
    public function editAction($gallery_id)
    {
        $em = $this->getDoctrine()->getEntityManager();
        $gallery = $em->getRepository('KunstmaanMediaBundle:Gallery')->getGallery($gallery_id, $em);
        
        $request = $this->getRequest();
        $form = $this->createForm($gallery->getFormType($gallery), $gallery);

            if ('POST' == $request->getMethod()) {
                $form->bindRequest($request);
                if ($form->isValid()){
                    $em->getRepository('KunstmaanMediaBundle:Gallery')->save($gallery_id, $em);

                    return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_gallery_show', array('id' => $gallery->getId(), 'slug' => $gallery->getSlug())));
                }
            }

            $galleries = $em->getRepository('KunstmaanMediaBundle:Gallery')
                                           ->getAllGalleriesByType();

            return array(
                'gallery' => $gallery,
                'form' => $form->createView(),
                'galleries'     => $galleries
            );
     }

    public function parentcreateAction($gallery)
    {
        $request = $this->getRequest();
        $form = $this->createForm($gallery->getFormType(), $gallery);

        if ('POST' == $request->getMethod()) {
            $form->bindRequest($request);
            if ($form->isValid()){
                $em = $this->getDoctrine()->getEntityManager();
                $em->getRepository('KunstmaanMediaBundle:Gallery')->save($gallery, $em);

                return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_gallery_show', array('id' => $gallery->getId(), 'slug' => $gallery->getSlug())));
            }
        }

        $em = $this->getDoctrine()->getEntityManager();
        $galleries = $em->getRepository('KunstmaanMediaBundle:Gallery')
                                       ->getAllGalleriesByType();

        return $this->render('KunstmaanMediaBundle:Gallery:create.html.twig', array(
            'gallery' => $gallery,
            'form' => $form->createView(),
            'galleries'     => $galleries
        ));
    }

    public function parentsubcreateAction($gallery,$id)
    {
            $request = $this->getRequest();

            $em = $this->getDoctrine()->getEntityManager();
            $parent = $em->getRepository('KunstmaanMediaBundle:Gallery')->getGallery($id, $em);

            $gallery->setParent($parent);
            $form = $this->createForm(new SubGalleryType(), $gallery);

            if ('POST' == $request->getMethod()) {
                $form->bindRequest($request);
                if ($form->isValid()){
                    $em = $this->getDoctrine()->getEntityManager();
                    $em->getRepository('KunstmaanMediaBundle:Gallery')->save($gallery, $em);

                    return new RedirectResponse($this->generateUrl('KunstmaanMediaBundle_gallery_show', array('id' => $gallery->getId(), 'slug' => $gallery->getSlug())));
                }
            }

            $galleries = $em->getRepository('KunstmaanMediaBundle:Gallery')
                                           ->getAllGalleriesByType();

            return $this->render('KunstmaanMediaBundle:Gallery:subcreate.html.twig', array(
                'subform' => $form->createView(),
                'galleries' => $galleries,
                'gallery' => $gallery,
                'parent' => $parent
            ));
    }
}