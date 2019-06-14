<?php

namespace App\Service;

use App\Entity\Car;
use App\Repository\CarRepository;

class CarScrapsFetcher {
  public function __construct(CarRepository $carRepo) {
    $this->carRepository = $carRepo;
  }

  /**
   * Returns the count of cars that haven't been mirrored
   */
  public function unfetchedCars() {
    return count($this->carRepository->getUnfetchedCars());
  }
}
