<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class offers API endpoints related to receiving requests and scraping
 * requested URLs. Currently only supports a handful of Slack App events.
 */
class CarScrapsReceiver {
  /**
   * @Route("/api/v1/slack", methods={"POST"})
   */
  public function slack() {
    $request = Request::createFromGlobals();
    $input = json_decode($request->getContent(), true);

    // Bail if there was no request object from Slack.
    if (empty($input)) {
      return new Response('Bad request: request payload was empty', 400);
    }

    switch ($input['type']) {
      // Slack Apps are subject to URL verification. For this test, just return the challenge string.
      case "url_verification":
        return new Response($input['challenge']);
        break;
    }
  }
}
