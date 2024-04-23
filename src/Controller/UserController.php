<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\SecurityBundle\Security;
use App\Repository\UserRepository;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class UserController extends AbstractController
{
    public function __construct(
        private readonly UserRepository $userRepository,
        private readonly EntityManagerInterface $entityManager,
        private readonly TokenStorageInterface $tokenStorage,
    ) {
    }
    #[Route('/usersPage', name: 'app_users_page', methods: ['GET'])]
    public function index(Security $security, UrlGeneratorInterface $urlGenerator): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $user = $security->getUser();
        $user->setDateLastLogin(new \DateTime());
        $this->entityManager->persist($user);
        $this->entityManager->flush();
        if ($user->getStatus() === 'blocked') {
            $logoutUrl = $urlGenerator->generate('app_logout');
            return new RedirectResponse($logoutUrl);
        }
        return $this->render('user/index.html.twig', [
            'user_name' => $user->getName(),
            'users' => $this->userRepository->findAll(),
        ]);
    }
    #[Route('/blockUsers', name: 'block_users', methods: ['POST'])]
    public function blockUsers(Request $request, SessionInterface $session): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $actionInputBlockData = $request->request->get('actionInputBlock');
        $blockIds = json_decode($actionInputBlockData);
        foreach ($blockIds as $blockId) {
            $this->invalidUser($blockId, $session);
            $user = $this->entityManager->getReference(User::class, $blockId);
            if (!$user) {
                continue;
            }
            $user->setStatus('blocked');
            $this->entityManager->flush();
        }
        return $this->redirectToRoute('app_users_page');
    }
    #[Route('/unblockUsers', name: 'unblock_users', methods: ['POST'])]
    public function unblockUsers(Request $request): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $actionInputUnblockData = $request->request->get('actionInputUnblock');
        $unblockIds = json_decode($actionInputUnblockData);
        foreach ($unblockIds as $unblockId) {
            $user = $this->entityManager->getReference(User::class, $unblockId);
            if (!$user) {
                continue;
            }
            if ($user->getStatus() === 'blocked') {
                $user->setStatus('active');
            }
            $this->entityManager->flush();
        }
        return $this->redirectToRoute('app_users_page');
    }
    #[Route('/deleteUsers', name: 'delete_users', methods: ['POST'])]
    public function deleteUsers(Request $request, SessionInterface $session): Response
    {
        $this->denyAccessUnlessGranted('IS_AUTHENTICATED_FULLY');
        $actionInputDeleteData = $request->request->get('actionInputDelete');
        $deleteIds = json_decode($actionInputDeleteData);
        foreach ($deleteIds as $deleteId) {
            $this->invalidUser($deleteId, $session);
            $user = $this->entityManager->getReference(User::class, $deleteId);
            if (!$user) {
                continue;
            }
            $this->entityManager->remove($user);
        }
        $this->entityManager->flush();
        return $this->redirectToRoute('app_users_page');
    }
    private function invalidUser(int $id, SessionInterface $session): void
    {
        $currentUser = $this->getUser();
        if ($currentUser && $currentUser->getId() == $id) {
            $this->tokenStorage->setToken(null);
            $session->invalidate();
        }
    }
}
