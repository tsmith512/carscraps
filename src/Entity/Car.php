<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\CarRepository")
 */
class Car
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $url;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $user;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $channel;

    /**
     * @ORM\Column(type="decimal", precision=20, scale=6)
     */
    private $timestamp;

    /**
     * @ORM\Column(type="boolean")
     */
    private $mirrored;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $slackts;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    /**
     * Return the URL to the locally mirrored post instead of the stored
     * Craigslist ad URL.
     */
    public function getMirrorUrl(): ?string
    {
        return preg_replace('/https?:\/\//', '/mirror/', $this->url);
    }

    public function setUrl(string $url): self
    {
        $this->url = $url;

        return $this;
    }

    public function getUser(): ?string
    {
        return $this->user;
    }

    public function setUser(?string $user): self
    {
        $this->user = $user;

        return $this;
    }

    public function getChannel(): ?string
    {
        return $this->channel;
    }

    public function setChannel(?string $channel): self
    {
        $this->channel = $channel;

        return $this;
    }

    public function getTimestamp()
    {
        return $this->timestamp;
    }

    public function setTimestamp($timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getMirrored(): ?bool
    {
        return $this->mirrored;
    }

    public function setMirrored(bool $mirrored): self
    {
        $this->mirrored = $mirrored;

        return $this;
    }

    public function getTitle(): ?string
    {
        return preg_replace('/ - cars &amp; trucks.+/', '', $this->title);
    }

    public function setTitle(?string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSlackts(): ?string
    {
        return $this->slackts;
    }

    public function setSlackts(?string $slackts): self
    {
        $this->slackts = $slackts;

        return $this;
    }

    /**
     * Pull the Craiglist region/city from the subdomain of the post and return
     */
    public function getCity(): ?string
    {
        $matches = array();
        preg_match('/\/\/(\w+)\./', $this->url, $matches);
        return isset($matches[1]) ? $matches[1] : NULL;
    }
}
