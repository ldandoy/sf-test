<?php

namespace App\Controller;

use App\Repository\PostRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

class PostController extends AbstractController
{
    #[Route('/', name: 'post.index', methods: ['GET'])]
    public function index(
        PostRepository $postRepository,
        Request $request
    ): Response
    {
        $posts = $postRepository->findPublished($request->query->getInt('page', 1));

        // dd($posts);
        
        return $this->render('posts/index.html.twig', [
            'posts' => $posts
        ]);
    }

    #[Route('/article/{slug}', name: 'post.show', methods: ['GET', 'POST'])]
    public function show(Post $post, Request $request, EntityManagerInterface $em): Response
    {
        
    }

}