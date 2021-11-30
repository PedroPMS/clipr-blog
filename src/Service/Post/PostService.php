<?php

namespace App\Service\Post;
libxml_use_internal_errors(true);

use App\Entity\Post;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use FOS\RestBundle\View\View;
use Symfony\Component\DomCrawler\Crawler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PostService
{
    private EntityManagerInterface $entityManager;
    private HttpClientInterface $client;

    public function __construct(EntityManagerInterface $entityManager, HttpClientInterface $client)
    {
        $this->entityManager = $entityManager;
        $this->client = $client;
    }

    public function checkUserOfPost(UserInterface $user, Post $post): bool
    {
        return $user->getId() == $post->getUser()->getId();
    }

    public function createFromReddit(UserInterface $user, string $url): View
    {
        [$title, $content] = $this->getContent($url);
        if (!$title || !$content) {
            return View::create(['message' => 'Unable to extract data from the passed URL'], Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $post = new Post();
        $post->setUser($user);
        $post->setTitle($title);
        $post->setContent($content);

        $this->entityManager->persist($post);
        $this->entityManager->flush();

        return View::create($post, Response::HTTP_CREATED);
    }

    private function getContent(string $parseUrl): array
    {
        try {
            $content = file_get_contents($parseUrl);
            $crawler = new Crawler($content);
            $title = $crawler->filter('h1')->getNode(0)->textContent;
            $postContent = $crawler->filterXpath("//div[contains(@class, '_3xX726aBn29LDbsDtzr_6E')]")->text();
            return [$title, $postContent];
        } catch (Exception $exception) {
            return [null, null];
        }
    }
}
