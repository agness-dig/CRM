<?php

namespace App\Controller;

use App\Entity\Calendar;
use App\Entity\Contact;
use App\Form\CalendarType;
use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CalendarController extends AbstractController
{
    /**
     * @Route("/user/calendar", name="user_calendar")
     */
    public function index(): Response
    {
        //entity manager
        $em = $this->getDoctrine()->getManager();
        $calendarRepository = $em->getRepository(Calendar::class);
        $events = $calendarRepository->findAll();

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
        return $this->render('user/calendar/index.html.twig', [
            'events' => json_encode($calendar_json),
        ]);
    }

    /**
     * @Route("/user/calendar/new", name="calendar_new")
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $calendar = new Calendar();
        $form = $this->createForm(CalendarType::class, $calendar);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($calendar);
            $entityManager->flush();

            return $this->redirectToRoute('user_calendar');
        }

        return $this->render('user/calendar/new.html.twig', [
            'calendar' => $calendar,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/user/calendar/edit/{id}", name="calendar_edit", methods={"POST"})
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function edit(int $id, Request $request): Response
    {
        $em = $this->getDoctrine()->getManager();
        $calendarRepository = $em->getRepository(Calendar::class);
        $calendar = $calendarRepository->find($id);

        $formular_js = $request->getContent() ;
        dd($formular_js);

        if ($request->getMethod() == "POST") {
            //update calendar
            //$calendar
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($calendar);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_calendar');

    }


}
