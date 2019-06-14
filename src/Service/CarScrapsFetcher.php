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
  public function countUnfetchedCars() {
    return count($this->carRepository->getUnfetchedCars());
  }

  /**
   * Mirror the Craigslist post of a car
   */
  public function fetchCar(Car $car) {
    // @TODO:
    var_dump($car);
  }
}
