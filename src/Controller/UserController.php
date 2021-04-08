<?php

namespace App\Controller;

use App\Entity\Calendar;
use App\Entity\Contact;
use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Annotation;

/**
 * @Route("/user")
 */
class UserController extends AbstractController
{
    /**
     * @Route("/", name="user_index", methods={"GET"})
     */
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    /**
     * @Route("/profile", name="user_profile", methods={"GET"})
     */
    public function profile( Request $request): Response
    {
        return $this->render('user/profile.html.twig', [
            'user' => $this->getUser(),
        ]);
    }

    /**
     * @Route("/dashboard", name="user_dashboard", methods={"GET"})
     * @return Response
     */
    public function dashboard(): Response
    {
        //entity manager
        $em = $this->getDoctrine()->getManager();
        $calendarRepository = $em->getRepository(Calendar::class);
        //seul events de cet utilisateur
        $events = $calendarRepository->findAllByUser($this->getUser()->getId());
        $contactsRepository = $em->getRepository(Contact::class);
        $contacts =$contactsRepository->findAll();
        $calendar_json = [];
        foreach ($events as $event) {
            $calendar_json [] = [
                'id'=>$event->getId(),
                'title'=>$event->getTitle(),
                'start'=>$event->getStart()->format('Y-m-d H:i'),
                'end'=>$event->getEnd()->format('Y-m-d H:i'),
                'description'=>$event->getDescription()
            ];
        }

        return $this->render('user/dashboard.html.twig',
            [
                'events_j'=>json_encode($calendar_json),
                'events'=>$events,
                'contacts'=>$contacts
            ]
        );
    }



    /**
     * @Route("/new", name="user_new", methods={"GET","POST"})
     */
    public function new(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('user_index');
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="user_show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="user_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, int $id): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('user_profile');
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
            'title' => "Personnaliser votre profil",
            'return' => "user",
            'label' => "Editer",
        ]);
    }

    /**
     * @Route("/{id}", name="user_delete", methods={"POST"})
     * @param Request $request
     * @param User $user
     * @return Response
     */
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_index');
    }




}
