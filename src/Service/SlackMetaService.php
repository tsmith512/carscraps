<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\HttpClient\NativeHttpClient;
use Symfony\Contracts\Cache\ItemInterface;

class SlackMetaService {

  private $slackToken;

  public $allSlackChannels = array();

  public function __construct() {
    $this->slackToken = $_ENV['SLACK_TOKEN'] ?: FALSE;

    if (!$this->slackToken) {
      // @TODO: Need to fail out on this because the token is not set.
    }

    // $this->conversations = $cache->get('slack_meta_conversations', function (ItemInterface $item) {
      // $item->expiresAfter(60 * 60 * 6);

      $httpClient = new CurlHttpClient();
      $response = $httpClient->request('GET', 'https://slack.com/api/conversations.list', [
        'query' => [
          'token' => $this->slackToken,
          'exclude_archived' => TRUE,
          'limit' => 100,
          'types' => 'public_channel'
        ]
      ]);

      $data = json_decode($response->getContent());

      if (empty($data->channels)) {
        // @TODO: There aren't any. Uh oh.
      }

      foreach ($data->channels as $channel) {
        $this->allSlackChannels[$channel->id] = $channel->name;
      }
      var_dump($this->allSlackChannels);
    // });
  }
}
