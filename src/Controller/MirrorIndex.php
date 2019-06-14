<?php

namespace App\Controller;

use App\Entity\Car;
use App\Repository\CarRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class offers API endpoints related to receiving requests and scraping
 * requested URLs. Currently only supports a handful of Slack App events.
 */
class MirrorIndex extends AbstractController {
  public function __construct(CarRepository $carRepository) {
    $this->carRepository = $carRepository;
  }
  /**
   * @Route("/", methods={"GET"})
   */
  public function index() {
    $cars = $this->carRepository->getFetchedCars();
    $queue = $this->carRepository->getUnfetchedCars();
    if (!empty($cars)) {
      echo "<ul>";
      foreach ($cars as $car) {
        $mirrorUrl = preg_replace('/https?:\/\//', '/mirror/', $car->getUrl());
        $title = $car->getTitle() ?: '(Unknown Title)';
        echo "<li><a href='$mirrorUrl'>{$title}</a><br /><em>Posted by {$car->getUser()} in {$car->getChannel()}</em></li>";
      }
      echo "</ul>";
    } else {
      echo "Error";
    }

    if (!empty($queue)) {
      echo "There are " . count($queue) . " posts in the fetch queue.";
    }

    return new Response('', 200);
  }
}
