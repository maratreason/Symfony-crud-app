<?php

namespace App\Controller;

use App\Entity\Post;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class PostController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->render("index.html.twig");
    }

    /**
     * @Route("/posts", name="posts")
     */
    public function viewPostsAction(): Response
    {
        $posts = $this->getDoctrine()->getRepository(Post::class)->findAll();
        return $this->render("post/index.html.twig", compact("posts"));
    }

    /**
     * @Route("/post/create", name="create_post")
     */
    public function createPostAction(Request $request): Response
    {
        $post = new Post;
        $form = $this->createFormBuilder($post)
            ->add("title", TextType::class, [
                "attr" => ["class" => "form-control"] 
            ])
            ->add("description", TextareaType::class, [
                "attr" => ["class" => "form-control"] 
            ])
            ->add("category", TextType::class, [
                "attr" => ["class" => "form-control"] 
            ])
            ->add("save", SubmitType::class, [
                "label" => "Create Post",
                "attr" => ["class" => "btn btn-success mt-3 float-right"] 
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $title = $form["title"]->getData();
            $description = $form["description"]->getData();
            $category = $form["category"]->getData();

            $post->setTitle($title);
            $post->setDescription($description);
            $post->setCategory($category);

            $em = $this->getDoctrine()->getManager();
            $em->persist($post);
            $em->flush();

            $this->addFlash("success", "Post saved successfully!");

            return $this->redirect($this->generateUrl("posts"));
        }
        $createForm = $form->createView();
        
        return $this->render("post/create.html.twig", compact("createForm"));
    }

    /**
     * @Route("/post/update/{id}", name="update_post")
     */
    public function updatePostAction($id, Request $request): Response
    {
        $post = $this->getDoctrine()->getRepository(Post::class)->find($id);
        $post->setTitle($post->getTitle());
        $post->setDescription($post->getDescription());
        $post->setCategory($post->getCategory());

        $form = $this->createFormBuilder($post)
            ->add("title", TextType::class, [
                "attr" => ["class" => "form-control"] 
            ])
            ->add("description", TextareaType::class, [
                "attr" => ["class" => "form-control"] 
            ])
            ->add("category", TextType::class, [
                "attr" => ["class" => "form-control"] 
            ])
            ->add("save", SubmitType::class, [
                "label" => "Update Post",
                "attr" => ["class" => "btn btn-success mt-3 float-right"] 
            ])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $title = $form["title"]->getData();
            $description = $form["description"]->getData();
            $category = $form["category"]->getData();

            $em = $this->getDoctrine()->getManager();

            $post = $em->getRepository(Post::class)->find($id);
            $post->setTitle($title);
            $post->setDescription($description);
            $post->setCategory($category);

            $em->flush();

            $this->addFlash("success", "Post saved successfully!");

            return $this->redirect($this->generateUrl("posts"));
        }

        $createForm = $form->createView();
        return $this->render("post/update.html.twig", compact("createForm"));
    }

    /**
     * @Route("/post/view/{id}", name="show_post")
     */
    public function showPostAction($id, Request $request): Response
    {
        $post = $this->getDoctrine()->getRepository(Post::class)->find($id);
        return $this->render("post/view.html.twig", compact("post"));
    }

    /**
     * @Route("/post/delete/{id}", name="delete_post")
     */
    public function deletePostAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        $post = $em->getRepository(Post::class)->find($id);
        $em->remove($post);
        $em->flush();
        $this->addFlash("success", "Post deleted successfully!");

        return $this->redirect($this->generateUrl("posts"));
    }
}
