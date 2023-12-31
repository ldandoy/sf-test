<?php

namespace App\Controller;

use App\Entity\Post\Comment;
use App\Form\CommentType;
use App\Repository\PostRepository;
use App\Entity\Post\Post;
use App\Form\SearchType;
use App\Model\SearchData;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Doctrine\ORM\EntityManagerInterface;

class PostController extends AbstractController
{
    #[Route('/', name: 'post.index', methods: ['GET'])]
    public function index(
        PostRepository $postRepository,
        Request $request
    ): Response
    {
        $posts = $postRepository->findPublished($request->query->getInt('page', 1));

        $searchData = new SearchData();
        $form = $this->createForm(SearchType::class, $searchData);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $searchData->page = $request->query->getInt('page', 1);
            $posts = $postRepository->findBySearch($searchData);

            return $this->render('posts/index.html.twig', [
                'form' => $form->createView(),
                'posts' => $posts
            ]);
        }

        
        return $this->render('posts/index.html.twig', [
            'form' => $form->createView(),
            'posts' => $posts
        ]);
    }

    #[Route('/article/{slug}', name: 'post.show', methods: ['GET', 'POST'])]
    public function show(Post $post, Request $request, EntityManagerInterface $em): Response
    {
        $comment = new Comment();
        $comment->setPost($post);
        if($this->getUser()) {
            $comment->setAuthor($this->getUser());
        }

        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()) {
            $em->persist($comment);
            $em->flush();

            $this->addFlash('success', 'Votre commentaire a bien été enregistré. Il sera soumis à modération dans les plus brefs délais.');

            return $this->redirectToRoute('post.show', ['slug' => $post->getSlug()]);
        }

        return $this->render('posts/show.html.twig', [
            'post' => $post,
            'form' => $form->createView()
        ]);


    }

}