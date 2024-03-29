<?php

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\HttpClient\NativeHttpClient;
use Symfony\Contracts\Cache\ItemInterface;

class SlackMetaService {

  private $slackToken;

  public $allSlackChannels = array();
  public $allSlackUsers = array();

  public function __construct() {
    $cache = new FilesystemAdapter();
    $this->slackToken = $_ENV['SLACK_TOKEN'] ?: FALSE;

    if (!$this->slackToken) {
      // @TODO: Need to fail out on this because the token is not set.
    }

    $this->allSlackChannels = $cache->get('slack_meta_conversations', function (ItemInterface $item) {
      $item->expiresAfter(60 * 60 * 6);

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

      $channels = array();
      foreach ($data->channels as $channel) {
        $channels[$channel->id] = $channel->name;
      }

      return $channels;
    });

    $this->allSlackUsers = $cache->get('slack_meta_users', function (ItemInterface $item) {
      $item->expiresAfter(60 * 60 * 6);

      $httpClient = new CurlHttpClient();
      $response = $httpClient->request('GET', 'https://slack.com/api/users.list', [
        'query' => [
          'token' => $this->slackToken,
          'include_locale' => FALSE,
          'limit' => 50,
        ]
      ]);

      $data = json_decode($response->getContent());

      if (empty($data->members)) {
        // @TODO: There aren't any. Uh oh.
      }

      $users = array();
      foreach ($data->members as $user) {
        $users[$user->id] = array(
          'handle' => $user->name,
          'real_name' => !empty($user->real_name) ? $user->real_name : $user->name
        );
      }

      return $users;
    });
  }

  public function getChannelName($channel_id) {
    if (isset($this->allSlackChannels[$channel_id])) {
      return $this->allSlackChannels[$channel_id];
    } else {
      return false;
    }
  }

  public function getUserName($user_id) {
    if (isset($this->allSlackUsers[$user_id])) {
      return $this->allSlackUsers[$user_id]['real_name'];
    } else {
      return false;
    }
  }
}
