<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Component;
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
use App\Service\UploadImageService;
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
            'concepts' => $conceptRepository->findBy(['isValidated' => true]),
            'languages' => $languageRepository->findAll(),
            'categories' => $categoryRepository->findAll()
        ]);
    }

    #[Route('/admin/delete/user/{id}', name: 'app_delete_user')]
    public function deleteUser(EntityManagerInterface $entityManager, User $user) : Response
    {
        foreach ($user->getConcepts() as $concept) {
            $concept->setAuthor(null);
            $entityManager->persist($concept);
        }
        $entityManager->remove($user);
        $entityManager->flush();
        return $this->redirectToRoute('app_admin');
    }

    #[Route('/admin/delete/concept/{id}/{redirectPath}', name: 'app_delete_concept')]
    public function deleteConcept(EntityManagerInterface $entityManager, Concept $concept, string $redirectPath,
                                  UploadImageService $uploadImageService) : Response
    {
        if(!$concept->isIsValidated()) {
            $this->addFlash('warning', 'Impossible to remove a draft');
            return $this->redirectToRoute($redirectPath);
        }
        $filename = $concept->getImage();
        $entityManager->remove($concept);
        $entityManager->flush();
        $uploadImageService->deleteImage($this->getParameter('image_directory'), $filename);
        return $this->redirectToRoute($redirectPath);
    }

    #[Route('/admin/add/user', name:'app_add_user')]
    public function addUser(Request $request, UserPasswordHasherInterface $userPasswordHasher,
                            EntityManagerInterface $entityManager) : Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);
        if($form->isSubmitted() && !$form->isValid()) {
            foreach ($form->getErrors(true, true) as $error) {
                $this->addFlash('warning', $error->getMessage());
            }
        }
        if ($form->isSubmitted() && $form->isValid()) {
            if($form->get('plainPassword')->getData() != $form->get('confirmPassword')->getData()) {
                $this->addFlash('warning', "Password confirmation failed, please retry");
                return $this->render('registration/register.html.twig', [
                    'registrationForm' => $form->createView(),
                ]);
            }
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );
            $user->setUsername($form->get('username')->getData());
            $user->setEmail($form->get('email')->getData());
            $user->addRole("ROLE_USER");
            $entityManager->persist($user);
            $entityManager->flush();
            $this->addFlash('notice', 'A new user has been created');
            return $this->redirectToRoute('app_admin');
        }
        return $this->render('admin/admin_add_user.html.twig',[
            'registrationForm' => $form->createView(),
        ]);
    }

    #[Route('/admin/delete/language/{id}', name: 'app_delete_language')]
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

    #[Route('/admin/add/language', name:'app_add_language')]
    public function addLanguage(Request $request, EntityManagerInterface $entityManager) : Response
    {
        $language = new Language();
        $form = $this->createForm(CreationLanguageType::class, $language);
        $result = $this->formProcessing($form, $request, $entityManager, $language, 'language');
        return $result == null ?$this->render('admin/admin_add_language_or_category.html.twig',[
            'object' => 'language',
            'creationForm' => $form->createView(),
        ]) : $result;
    }

    #[Route('/admin/delete/category/{id}', name: 'app_delete_category')]
    public function deleteCategory(EntityManagerInterface $entityManager, CategoryRepository $categoryRepository,
                                   ConceptRepository $conceptRepository, Category $category) : Response
    {
        $categoryOther = $categoryRepository->findOneBy(['id' => 1]);
        if($categoryOther->getId() !== $category->getId()) {
            $conceptsCategory = $conceptRepository->findByCategory($category);
            foreach($conceptsCategory as $concept) {
                $concept->setCategory($categoryOther);
            }
            $entityManager->remove($category);
            $entityManager->flush();
        }
        return $this->redirectToRoute('app_admin');
    }

    #[Route('/admin/add/category', name:'app_add_category')]
    public function addCategory(Request $request, EntityManagerInterface $entityManager,
                                UserRepository $userRepository,
                                ConceptRepository $conceptRepository,
                                LanguageRepository $languageRepository,
                                CategoryRepository$categoryRepository) : Response
    {
        $category = new Category();
        $form = $this->createForm(CreationCategoryType::class, $category);
        $result = $this->formProcessing($form, $request, $entityManager, $category, 'category');
        return $result == null ? $this->render('admin/admin_add_language_or_category.html.twig',[
            'object' => 'category',
            'creationForm' => $form->createView(),
        ]) : $result;
    }

    private function formProcessing($form, $request, $entityManager, $value, $typeValue) {
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $value->setName($form->get('name')->getData());
            $entityManager->persist($value);
            $entityManager->flush();
            $this->addFlash('notice', 'A new '.$typeValue. ' has been created');
            return $this->redirectToRoute('app_admin');
        }
        return null;
    }
}
