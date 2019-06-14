<?php

namespace App\Service;

use App\Entity\Car;
use App\Repository\CarRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

class CarScrapsFetcher {
  public function __construct(CarRepository $carRepo, EntityManagerInterface $entityManager) {
    $this->carRepository = $carRepo;
    $this->entityManager = $entityManager;
  }

  /**
   * Returns the count of cars that haven't been mirrored
   */
  public function countUnfetchedCars() {
    return count($this->carRepository->getUnfetchedCars());
  }

  /**
   * Mirror the Craigslist post of a car
   */
  public function fetchCar(Car $car) {
    if ($car->getMirrored()) {
      // This has already been (marked as?) fetched, sooooooo
      return false;
    }

    if (empty($car->getUrl())) {
      // This post has no URL, that's bad
      return false;
    }

    $process = new Process([dirname(__FILE__, 3) . '/bin/fetch.sh', $car->getUrl()]);
    $process->run();

    if (!$process->isSuccessful()) {
      throw new ProcessFailedException($process);
    }

    // The bash script is written to echo the title from the page.
    // @TODO outta do somethin' with that...
    $car->setTitle(trim($process->getOutput()) ?: "(Unknown title)");
    $car->setMirrored(true);

    $this->entityManager->persist($car);
    $this->entityManager->flush();
  }
}
