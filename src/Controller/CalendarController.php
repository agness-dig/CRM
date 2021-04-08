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
        //importante
        $events = $calendarRepository->findAllByUser($this->getUser()->getId());

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
            $calendar->setUser($this->getUser());
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

        /** @var Calendar $calendar */
        $calendar = $calendarRepository->find($id);

        if ($request->getMethod() == "POST") {
            //title
            if ($request->request->get('title2')) {
                $calendar->setTitle($request->request->get('title2'));
            }

            //start
            if ($request->request->get('start')) {
                $calendar->setStart(new \DateTime($request->request->get('start')." ".$request->request->get('start_time')));
            }


            //end

            if ($request->request->get('end')) {
                $calendar->setEnd(new \DateTime($request->request->get('end')." ".$request->request->get('end_time')));
            }

            //description
            if ($request->request->get('descr')) {
                $calendar->setDescription($request->request->get('descr'));
            }
           // dd($request->request);

            //$calendar
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($calendar);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_calendar');

    }

    /**
     * @Route("/user/calendar/delete/{id}", name="calendar_delete", methods={"POST"})
     * @param Request $request
     * @param Calendar $calendar
     * @return Response
     */
    public function delete(Request $request, Calendar $calendar): Response
    {
        if ($request->getMethod()=="POST" && $calendar) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($calendar);
            $entityManager->flush();
        }

        return $this->redirectToRoute('user_dashboard');
    }

}
