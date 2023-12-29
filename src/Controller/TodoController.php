<?php

namespace App\Controller;

use App\Entity\Todo;
use App\Form\TodoType;
use App\Repository\TodoRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/')]
class TodoController extends AbstractController
{
    #[Route('/', name: 'app_todo_index', methods: ['GET'])]
    public function index(TodoRepository $todoRepository): Response
    {
        $todos = $todoRepository->findAll();
        $completedTodos = array_filter($todos, function ($todo) {
            return $todo->isCompleted();
        });

        $remainingTodosCount = count($todos) - count($completedTodos);
    
        return $this->render('todo/index.html.twig', [
            'todos' => $todos,
            'completedTodosCount' => count($completedTodos),
            'totalTodosCount' => count($todos),
            'remainingTodosCount' => $remainingTodosCount,
        ]);
    }

    #[Route('/new', name: 'app_todo_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $todo = new Todo();
        $form = $this->createForm(TodoType::class, $todo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($todo);
            $entityManager->flush();

            return $this->redirectToRoute('app_todo_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('todo/new.html.twig', [
            'todo' => $todo,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_todo_show', methods: ['GET'])]
    public function show(Todo $todo): Response
    {
        return $this->render('todo/show.html.twig', [
            'todo' => $todo,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_todo_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Todo $todo, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(TodoType::class, $todo);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_todo_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('todo/edit.html.twig', [
            'todo' => $todo,
            'form' => $form,
        ]);
    }

    #[Route('/delete-completed', name: 'app_todo_delete_completed', methods: ['POST'])]
    public function deleteCompleted(Request $request, EntityManagerInterface $entityManager, TodoRepository $todoRepository): Response
    {
        // requête est une méthode POST ?
        if ($request->isMethod('POST')) {
            // Récupère toutes les todos complétées depuis le repository
            $completedTodos = $todoRepository->findBy(['completed' => true]);
    
            // Parcourt chaque todo check et les supprime de la base de données
            foreach ($completedTodos as $todo) {
                $entityManager->remove($todo);
            }
    
            $entityManager->flush();
    
            // Redirection
            return $this->redirectToRoute('app_todo_index', [], Response::HTTP_SEE_OTHER);
        }
    
        // Si la requête n'est pas de type POST, retourne une réponse JSON avec un message d'erreur
        return new JsonResponse(['message' => 'Invalid request method'], JsonResponse::HTTP_BAD_REQUEST);
    }
}
