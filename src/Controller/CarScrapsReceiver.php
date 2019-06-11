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

      // Slack Apps Events API is notifying of a subscribed event:
      case "event_callback":
        $event = $input['event'];

        switch ($event['type']) {
          case "link_shared":

            // Capture links that Slack is giving us.
            $links = array();

            foreach($event['links'] as $link) {
              $links[] = $link['url'];
            }

            $this->enqueue($links);

            return new Response("Links captured", 200);
            break;
        }
        break;
    }
  }

  /**
   * Receiving an array of links, they need to be enqueued for mirroring.
   */
  public function enqueue($links = NULL) {
    try {
      file_put_contents(__DIR__ . "/../../public/queue.txt", implode(PHP_EOL, $links) . PHP_EOL, FILE_APPEND | LOCK_EX);
    } catch (Exception $e) {
      return new Response($e->getMessage(), 500);
    }
  }
}
