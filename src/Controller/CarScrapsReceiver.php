<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;


class CarScrapsReceiver {
  /**
   * @Route("/scrape")
   */
  public function scrape() {
    return new Response("This is a test");
  }
}
