<?php

namespace App\Controller;

use App\Entity\Car;
use App\Repository\CarRepository;
use App\Service\SlackMetaService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class offers API endpoints related to receiving requests and scraping
 * requested URLs. Currently only supports a handful of Slack App events.
 */
class MirrorIndex extends AbstractController {
  public function __construct(CarRepository $carRepository, SlackMetaService $slackMetaService) {
    $this->carRepository = $carRepository;
    $this->slackMetaService = $slackMetaService;
  }
  /**
   * @Route("/", methods={"GET"})
   */
  public function index() {
    $cars = $this->carRepository->getFetchedCars();
    $queue = $this->carRepository->getUnfetchedCars();

    $index = array();

    if (!empty($cars)) {
      foreach ($cars as $car) {
        $timestamp = !empty($car->getSlackts()) ? explode('.', $car->getSlackts())[0] : false;
        $date = ($timestamp) ? date('D d M Y', $timestamp) : false;
        $index[] = array(
          'mirrorUrl' => preg_replace('/https?:\/\//', '/mirror/', $car->getUrl()),
          'title' => $car->getTitle() ?: '(Unknown Title)',
          'user' => $this->slackMetaService->getUserName($car->getUser()) ?: $car->getUser(),
          'channel' => $this->slackMetaService->getChannelName($car->getChannel()) ?: $car->getChannel(),
          'timestamp' => $date ? date('c', $timestamp) : false,
          'date' => $date,
        );
      }
    } else {
      // @TODO: Uh, this is not how to...
      echo "Error";
    }

    $queueCount = (!empty($queue)) ? count($queue) : false;

    return $this->render('mirrorIndex.html.twig', [
      'title' => 'Car Scraps Index',
      'cars' => $index,
      'queue' => $queueCount,
    ]);
  }
}
