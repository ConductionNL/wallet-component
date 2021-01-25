<?php

namespace App\Subscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Service\PointsService;
use Conduction\CommonGroundBundle\Service\CommonGroundService;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Serializer\SerializerInterface;

class ApplicationPointsSubscriber implements EventSubscriberInterface
{
    private $commonGroundService;
    private $serializer;
    private $pointService;

    public function __construct(CommongroundService $commonGroundService, SerializerInterface $serializer, PointsService $pointService)
    {
        $this->commonGroundService = $commonGroundService;
        $this->serializer = $serializer;
        $this->pointService = $pointService;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['applicationPoints', EventPriorities::PRE_VALIDATE],
        ];
    }

    public function applicationPoints(ViewEvent $event)
    {
        $result = $event->getControllerResult();

        if ($event->getRequest()->getMethod() == 'GET' && $event->getRequest()->get('_route') == 'api_authorizations_get_points_by_application_collection') {
            $id = $event->getRequest()->attributes->get('id');

            $points = [];
            $points['points'] = (int)$this->pointService->getPointsByApplication($id)[0]['points'];

            $json = $this->serializer->serialize(
                $points,
                'json'
            );

            $response = new Response(
                $json,
                Response::HTTP_OK,
                ['content-type' => 'application/json']
            );

            $event->setResponse($response);
        }

        return $result;
    }
}
