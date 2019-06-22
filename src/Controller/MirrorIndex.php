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

   // @TODO: This is not how to do this:
   ob_start();

    if (!empty($cars)) {
      echo "<ul>";
      foreach ($cars as $car) {
        $mirrorUrl = preg_replace('/https?:\/\//', '/mirror/', $car->getUrl());
        $title = $car->getTitle() ?: '(Unknown Title)';
        $user = $this->slackMetaService->getUserName($car->getUser()) ?: $car->getUser();
        $channel = $this->slackMetaService->getChannelName($car->getChannel()) ?: $car->getChannel();
        $timestamp = $car->getSlackts();
        echo "<li><a href='$mirrorUrl'>{$title}</a><br /><em>Posted by {$user} in {$channel} at {$timestamp}</em></li>";
      }
      echo "</ul>";
    } else {
      echo "Error";
    }

    if (!empty($queue)) {
      echo "There are " . count($queue) . " posts in the fetch queue.";
    }

    // @TODO: Still not how to do this.
    $content = ob_get_clean();

    // @TODO: This is still definitely not how to do this.
    return $this->render('mirrorIndex.html.twig', ['title' => 'Car Scraps Index', 'body' => $content]);
  }
}
