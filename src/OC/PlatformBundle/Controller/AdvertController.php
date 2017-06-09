<?php



namespace OC\PlatformBundle\Controller;

use OC\PlatformBundle\Entity\Advert;
use OC\PlatformBundle\Form\AdvertType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AdvertController extends Controller
{
    public function indexAction($page)
    {
        // ...

        // Notre liste d'annonce en dur
        $listAdverts = array(
            array(
                'title' => 'Recherche développpeur Symfony',
                'id' => 1,
                'author' => 'Alexandre',
                'content' => 'Nous recherchons un développeur Symfony débutant sur Lyon. Blabla…',
                'date' => new \Datetime()),
            array(
                'title' => 'Mission de webmaster',
                'id' => 2,
                'author' => 'Hugo',
                'content' => 'Nous recherchons un webmaster capable de maintenir notre site internet. Blabla…',
                'date' => new \Datetime()),
            array(
                'title' => 'Offre de stage webdesigner',
                'id' => 3,
                'author' => 'Mathieu',
                'content' => 'Nous proposons un poste pour webdesigner. Blabla…',
                'date' => new \Datetime())
        );

        // Et modifiez le 2nd argument pour injecter notre liste
        return $this->render('OCPlatformBundle:Advert:index.html.twig', array(
            'listAdverts' => array()
        ));
    }
    public function viewAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        // Pour récupérer une seule annonce, on utilise la méthode find($id)
        $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);

        // $advert est donc une instance de OC\PlatformBundle\Entity\Advert
        // ou null si l'id $id n'existe pas, d'où ce if :
        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
        }

        // Récupération de la liste des candidatures de l'annonce
        $listApplications = $em
            ->getRepository('OCPlatformBundle:Application')
            ->findBy(array('advert' => $advert))
        ;

        // Récupération des AdvertSkill de l'annonce
        $listAdvertSkills = $em
            ->getRepository('OCPlatformBundle:AdvertSkill')
            ->findBy(array('advert' => $advert))
        ;

        return $this->render('OCPlatformBundle:Advert:view.html.twig', array(
            'advert'           => $advert,
            'listApplications' => $listApplications,
            'listAdvertSkills' => $listAdvertSkills,
        ));
    }

    public function addAction(Request $request)
    {
        $advert= new advert();
        $form = $this->createForm(AdvertType::class, $advert);
        // On vérifie qu'elle est de type POST
        if ($request->getMethod() == 'POST') {
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($advert);
                $em->flush();
                // On définit un message flash
                $this->get('session')->getFlashBag()->add('info', 'advert bien ajouté');
                return $this->redirect($this->generateUrl('oc_platform_advert_adverts'));
            }
        }

        return $this->render('OCPlatformBundle:Advert:add.html.twig', array(
            'form' => $form->createView(),
        ));
    }

    public function editAction($id, Request $request)
    {
        // ki t7otih bil tari9a hathi symfony ya3ref il entity win et yemchi ijiblek il membre mil base de donnée, ya3ni 3ibara ya3mel
        $em = $this->getDoctrine()->getManager();
        // tnajmi it5arji im mebre mil bdd bil tari9a hathi si non fama tari9a o5ra
        $advert = $em->getRepository('OCPlatformBundle:Advert')->find($id);
        // ana taw ne5demlik bil tari9a il 3adiya 5ir bech tafhmi
        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id ".$id." n'existe pas.");
        }
        // Ici encore, il faudra mettre la gestion du formulaire
        // Zayed ta3mli fi formulaire lil edition, tnajmi tista3mli nafs il formulaire lil creation wil edition
        $form = $this->createForm(AdvertType::class, $advert);
        if ($request->isMethod('POST')) {
            // hna itchargi il formulaire avec les données du formulaire
            $form->handleRequest($request);
            $em->persist($advert);
            $em->flush();
            $request->getSession()->getFlashBag()->add('notice', 'advertbien modifiée.');

            return $this->redirect($this->generateUrl('oc_platform_advert_adverts'));
        }

        return $this->render('OCPlatformBundle:Advert:edit.html.twig', array(
            'form' => $form->createView(),
        ));
    }


    public function deleteAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        $advert= $em->getRepository('OCPlatformBundle:Advert')->find($id);

        if (null === $advert) {
            throw new NotFoundHttpException("L'annonce d'id " . $id . " n'existe pas.");
        }



        $em->remove($advert);
        $em->flush();
        $advertRepository = $em->getRepository('OCPlatformBundle:Advert');

        return $this->render('OCPlatformBundle:Advert:adverts.html.twig',array('adverts' => $advertRepository->findAll()));
    }

    public function menuAction()
    {
        // On fixe en dur une liste ici, bien entendu par la suite
        // on la récupérera depuis la BDD !
        $listAdverts = array(
            array('id' => 2, 'title' => 'Recherche développeur Symfony'),
            array('id' => 5, 'title' => 'Mission de webmaster'),
            array('id' => 9, 'title' => 'Offre de stage webdesigner')
        );

        return $this->render('OCPlatformBundle:Advert:menu.html.twig', array(
            // Tout l'intérêt est ici : le contrôleur passe
            // les variables nécessaires au template !
            'listAdverts' => $listAdverts
        ));
    }

    public function getAdverts()
    {
        $query = $this->createQueryBuilder('a')
            ->orderBy('a.date', 'DESC')
            ->getQuery()
        ;

        return $query->getResult();
    }
    public function advertsAction()
    {
        $em = $this->getDoctrine()->getEntityManager();
        $advertRepository = $em->getRepository('OCPlatformBundle:Advert');

        return $this->render('OCPlatformBundle:advert:adverts.html.twig', array('adverts' => $advertRepository->findAll()));
    }
}
