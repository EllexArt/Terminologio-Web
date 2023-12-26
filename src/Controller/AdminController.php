<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\ComponentName;
use App\Entity\Concept;
use App\Entity\Language;
use App\Entity\User;
use App\Form\CreationCategoryType;
use App\Form\CreationLanguageType;
use App\Form\RegistrationFormType;
use App\Repository\CategoryRepository;
use App\Repository\ComponentNameRepository;
use App\Repository\ConceptRepository;
use App\Repository\LanguageRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    #[Route('/admin', name: 'app_admin')]
    public function index(UserRepository $userRepository, ConceptRepository $conceptRepository,
                          LanguageRepository $languageRepository, CategoryRepository $categoryRepository): Response
    {
        return $this->render('admin/admin_management.html.twig', [
            'users' => $userRepository->findByRoleUser(),
            'concepts' => $conceptRepository->findAll(),
            'languages' => $languageRepository->findAll(),
            'categories' => $categoryRepository->findAll(),
            'success' => false,
        ]);
    }

    #[Route('/delete/user/{id}', name: 'app_delete_user')]
    public function deleteUser(EntityManagerInterface $entityManager, User $user) : Response
    {
        $entityManager->remove($user);
        $entityManager->flush();
        return $this->redirectToRoute('app_admin');
    }

    #[Route('/delete/concept/{id}', name: 'app_delete_concept')]
    public function deleteConcept(EntityManagerInterface $entityManager, Concept $concept) : Response
    {
        $entityManager->remove($concept);
        $entityManager->flush();
        return $this->redirectToRoute('app_admin');
    }

    #[Route('/add/user', name:'app_add_user')]
    public function addUser(Request $request, UserPasswordHasherInterface $userPasswordHasher,
                            EntityManagerInterface $entityManager,
                            UserRepository $userRepository,
                            ConceptRepository $conceptRepository,
                            LanguageRepository $languageRepository,
                            CategoryRepository$categoryRepository) : Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setUsername($form->get('username')->getData());
            $user->setEmail($form->get('email')->getData());
            $user->setRoles($user->getRoles());
            $entityManager->persist($user);
            $entityManager->flush();
            return $this->render('admin/admin_management.html.twig', [
                'users' => $userRepository->findByRoleUser(),
                'concepts' => $conceptRepository->findAll(),
                'languages' => $languageRepository->findAll(),
                'categories' => $categoryRepository->findAll(),
                'success' => true,
                'object' => 'user',
            ]);
        }
        return $this->render('admin/admin_add_user.html.twig',[
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/delete/language/{id}', name: 'app_delete_language')]
    public function deleteLanguage(EntityManagerInterface $entityManager, ComponentNameRepository $componentNameRepository,
                                   ConceptRepository      $conceptRepository, Language $language) : Response
    {
        $conceptsDefaultLanguage = $conceptRepository->findByDefaultLanguage($language);
        foreach($conceptsDefaultLanguage as $concept) {
            $entityManager->remove($concept);
        }
        $translations = $componentNameRepository->findByLanguage($language);
        foreach($translations as $translation) {
            $entityManager->remove($translation);
        }
        $entityManager->remove($language);
        $entityManager->flush();
        return $this->redirectToRoute('app_admin');
    }

    #[Route('/add/language', name:'app_add_language')]
    public function addLanguage(Request $request, EntityManagerInterface $entityManager,
                                UserRepository $userRepository,
                                ConceptRepository $conceptRepository,
                                LanguageRepository $languageRepository,
                                CategoryRepository$categoryRepository) : Response
    {
        $language = new Language();
        $form = $this->createForm(CreationLanguageType::class, $language);
        $result = $this->formProcessing($form, $request, $entityManager, $userRepository, $conceptRepository,
            $languageRepository, $categoryRepository, $language, 'language');
        return $result == null ?$this->render('admin/admin_add_language_or_category.html.twig',[
            'object' => 'language',
            'creationForm' => $form->createView(),
        ]) : $result;
    }

    #[Route('/delete/category/{id}', name: 'app_delete_category')]
    public function deleteCategory(EntityManagerInterface $entityManager, CategoryRepository $categoryRepository,
                                   ConceptRepository $conceptRepository, Category $category) : Response
    {
        $categoryOther = $categoryRepository->findById(0);
        if($categoryOther != $category) {
            $conceptsCategory = $conceptRepository->findByCategory($category);
            foreach($conceptsCategory as $concept) {
                $concept->setCategory($categoryOther);
            }
            $entityManager->remove($category);
            $entityManager->flush();
        }
        return $this->redirectToRoute('app_admin');
    }

    #[Route('/add/category', name:'app_add_category')]
    public function addCategory(Request $request, EntityManagerInterface $entityManager,
                                UserRepository $userRepository,
                                ConceptRepository $conceptRepository,
                                LanguageRepository $languageRepository,
                                CategoryRepository$categoryRepository) : Response
    {
        $category = new Category();
        $form = $this->createForm(CreationCategoryType::class, $category);
        $result = $this->formProcessing($form, $request, $entityManager, $userRepository, $conceptRepository,
            $languageRepository, $categoryRepository, $category, 'category');
        return $result == null ? $this->render('admin/admin_add_language_or_category.html.twig',[
            'object' => 'category',
            'creationForm' => $form->createView(),
        ]) : $result;
    }

    private function formProcessing($form, $request, $entityManager, $userRepository, $conceptRepository,
                                    $languageRepository, $categoryRepository,$value, $stringValue) {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $value->setName($form->get('name')->getData());
            $entityManager->persist($value);
            $entityManager->flush();
            return $this->render('admin/admin_management.html.twig', [
                'users' => $userRepository->findByRoleUser(),
                'concepts' => $conceptRepository->findAll(),
                'languages' => $languageRepository->findAll(),
                'categories' => $categoryRepository->findAll(),
                'success' => true,
                'object' => $stringValue,
            ]);
        }
        return null;
    }
}
