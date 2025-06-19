<?php

namespace App\Controller;

use KnpU\OAuth2ClientBundle\Client\ClientRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Annotation\Route;

class GoogleController extends AbstractController
{
    #[Route('/connect/google', name: 'connect_google')]
    public function connectAction(ClientRegistry $clientRegistry): Response
    {
        return $clientRegistry
            ->getClient('google')
            ->redirect([], [
                'email', 'profile' // the scopes you want to access
            ]);
    }

    #[Route('/connect/google/check', name: 'connect_google_check')]
    public function connectCheckAction(): Response|RedirectResponse
    {
        // This method will be handled by the OAuth2 authenticator
        if (!$this->getUser()) {
            return new Response('User not found!', Response::HTTP_FORBIDDEN);
        }

        return $this->redirectToRoute('homepage');
    }
}
