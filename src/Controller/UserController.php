<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;
use App\Repository\UserRepository;

class UserController extends AbstractController
{
    public function __construct(private readonly UserRepository $userRepository,)
    {
    }

    #[Route('/usersPage', name: 'app_users_page', methods: ['GET'])]
    public function index(Security $security, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $security->getUser();
        $user->setDateLastLogin(new \DateTime());
        $entityManager->persist($user);
        $entityManager->flush();
        return $this->render('user/index.html.twig', [
            'user_name' => $user->getName(),
            'users' => $this->userRepository->findAll(),
        ]);
    }
    #[Route('/blockUsers', name: 'block_users', methods: ['POST'])]
    public function blockUsers(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->redirectToRoute('app_users_page');
    }
    #[Route('/unblockUsers', name: 'unblock_users', methods: ['POST'])]
    public function unblockUsers(): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        return $this->redirectToRoute('app_users_page');
    }
    #[Route('/deleteUsers', name: 'delete_users', methods: ['POST'])]
    public function deleteUsers(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $actionInputDeleteData = $request->request->get('actionInputDelete');
        $deleteData = json_decode($actionInputDeleteData);
        return $this->redirectToRoute('app_users_page');
    }
}
