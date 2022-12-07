<?php

namespace App\Controller\Gallerie;

use App\Entity\Personne;
use App\Repository\ReservationRepository;
use CalendarBundle\CalendarEvents;
use CalendarBundle\Entity\Event;
use CalendarBundle\Event\CalendarEvent;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\Session;

class CalendarSubscriber implements EventSubscriberInterface
{

    private ReservationRepository $reservationRepository;
    private Session $session;

    public function __construct(ReservationRepository $reservationRepository)
    {
        $this->reservationRepository = $reservationRepository;
        $this->session = new Session();
    }

    public static function getSubscribedEvents()
    {
        return [
            CalendarEvents::SET_DATA => 'onCalendarSetData',
        ];
    }

    public function onCalendarSetData(CalendarEvent $calendar, $id)
    {
        $start = $calendar->getStart();
        $end = $calendar->getEnd();
        $filters = $calendar->getFilters();


        $listReservation = $this->reservationRepository->findBy(["gallerie"=>$this->session->get('id')]);

        for ($i = 0; $i < count($listReservation) ; $i++) {

            $bookingEvent = new Event(
                "NON DISPONIBLE",
                $listReservation[$i]->getDateDebut(),
                $listReservation[$i]->getDateFin()
            );

            $bookingEvent->setOptions([
                'backgroundColor' => 'red',
                'borderColor' => 'red',
            ]);

            // finally, add the event to the CalendarEvent to fill the calendar
            $calendar->addEvent($bookingEvent);
        }
    }
}
