# Gestionnaire de Todos avec Symfony

## Fonctionnalités Implémentées

1. **Page d'accueil et Nouvelle Todo**

   - Modification du Header :
     - Suppression des liens inutiles pour ne conserver que "Accueil" et "Nouvelle Todo".

   **Code associé :**

   ~~~twig
   <!-- base.html.twig -->
   <header class="d-flex py-3">
       <ul class="nav nav-pills">
           <li class="nav-item"><a href="{{ path('app_todo_index') }}" class="nav-link active" aria-current="page">Accueil</a></li>
           <li class="nav-item"><a href="{{ path('app_todo_new') }}" class="nav-link">Nouvelle Todo</a></li>
       </ul>
   </header>
   ~~~

2. **Stylisation avec Bootstrap**

   - Utilisation de Composants Bootstrap :
     - Intégration des classes Bootstrap pour améliorer le style global.
     - Personnalisation des styles pour une expérience utilisateur plus moderne.

   **Code associé :**

**fichier `_form.html.twig`**
  ~~~twig 
   <!-- Exemple : Stylisation du formulaire -->
   {{ form_start(form, {'attr': {'class': 'custom-form'}}) }}
     <div class="mb-3">
      <!-- ... -->
   {{ form_end(form) }}
  ~~~

**fichier `new.html.twig`**
   ~~~twig 
   {% block stylesheets %}
    <style>
      /* Add your custom styles here */
      .custom-form {
          max-width: 300px;
          margin: 10px auto;
          padding: 10px 20px;
          background: #f4f7f8;
          border-radius: 8px;
      }
  
      .custom-form h1 {
          margin: 0 0 30px 0;
          text-align: center;
      }

      /* Add the rest of your custom styles */
    </style>
    {% endblock %}
   ~~~

3. **Bouton de Complétion Rapide**

   - Ajout d'un Bouton dans la Liste :
     - Inclusion d'un bouton pour compléter rapidement une tâche depuis la liste.

   **Code associé :**

   ```twig
   <!-- Liste des Todos -->
   {% for todo in todos %}
      <!-- ... -->
      <td><a href="{{ path('app_todo_complete', {'id': todo.id}) }}" class="btn btn-success btn-sm">Complete</a></td>
   {% endfor %}
   ```
   ```php
    use Symfony\Component\HttpFoundation\RedirectResponse;
   
    #[Route('/complete/{id}', name: 'app_todo_complete', methods: ['POST'])]
    public function completeTodo(Request $request, Todo $todo, EntityManagerInterface $entityManager): RedirectResponse
    {
        if ($request->isMethod('POST')) {
            // Mettre à jour la propriété 'completed' de l'objet Todo
            $todo->setCompleted(true);
            $entityManager->flush();
    
            // Rediriger vers la page d'index des todos
            return $this->redirectToRoute('app_todo_index', [], Response::HTTP_SEE_OTHER);
        }
    
        // Si la requête n'est pas de type POST, retourner une réponse JSON avec un message d'erreur
        return new JsonResponse(['message' => 'Invalid request method'], JsonResponse::HTTP_BAD_REQUEST);
    }
   ```

4. **Statistiques des Todos / Message d'Encouragement**

   - Affichage des Statistiques :
     - Présentation du nombre de Todos complétées et du total au-dessus de la liste.
     - Message encourageant lorsque moins de la moitié des Todos restent à faire.

   **Code associé :**

   ~~~twig
   <!-- Statistiques des Todos -->
   <div class="mb-3">
       {{ completedTodosCount }} / {{ totalTodosCount }} Todos complétées
       {% if remainingTodosCount < totalTodosCount / 2 %}
           <p class="text-success">Continuez ainsi ! Vous êtes sur la bonne voie.</p>
       {% endif %}
   </div>
   ~~~

5. **Bouton de Suppression des Todos Complétées**

   - Bouton pour Supprimer les Todos Complétées :
     - Ajout d'un bouton pour supprimer toutes les Todos complétées en un clic.

   **Code associé :**

   ~~~twig
   <!-- Bouton de Suppression des Todos Complétées -->
   <form method="post" action="{{ path('app_todo_delete_completed') }}">
       <button class="btn btn-danger">Supprimer les Todos complétées</button>
   </form>
   ~~~

   - Dans le contrôleur (`TodoController`), la méthode associée à la route `/delete-completed` vérifie si la requête est de type POST.
   - Si c'est le cas, elle utilise le `TodoRepository` pour récupérer toutes les Todos complétées.
   - Ensuite, elle parcourt chaque Todo complétée et les supprime de la base de données en utilisant l'`EntityManager`.
   - Après avoir supprimé les Todos complétées, la méthode applique les changements dans la base de données avec `$entityManager->flush()`.
   - Enfin, l'utilisateur est redirigé vers la page d'accueil (`app_todo_index`). Si la requête n'est pas de type POST, une réponse JSON avec un message d'erreur est renvoyée.

  **Code associé :**

   ~~~twig
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
   ~~~

## Améliorations Esthétiques

   - Utilisation de Classes Bootstrap :
     - Personnalisation des formulaires, boutons et autres éléments avec les classes Bootstrap.

   **Code associé :**

   ~~~twig
   <!-- Exemple : Personnalisation du formulaire -->
   {{ form_start(form, {'attr': {'class': 'custom-form'}}) }}
      <!-- ... -->
   {{ form_end(form) }}
   ~~~

# Temps Passé

Le projet a été complété en environ 3 heures, incluant la recherche, le développement, la rédaction du compte-rendu au sein de ce Read Me.

## Problèmes Rencontrés

   - Aucun problème majeur n'a été rencontré.
   - Ce projet a tout de même nécessité une redécouverte légère de PHP, notamment pour les boutons de suppression de toutes les choses faites.

---

**Note :** Pour plus de détails, consultez le code source, les fichiers de configuration et les commits du projet. Suivez les instructions du Readme pour installer et exécuter l'application.
