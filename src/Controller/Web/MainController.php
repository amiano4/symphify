<?php

namespace App\Controller\Web;

use App\Service\SpotifyApiClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class MainController extends AbstractController
{
    #[Route('')]
    public function index(
        SpotifyApiClient $client,
        Request $request,
    ): Response {
        $offset = $request->get('offset', 0);
        $limit = $request->get('limit', 20);

        $albums = $client->apiRequest("browse/new-releases?offset=$offset&limit=$limit");

        // dd($albums);

        $items = $albums['albums']['items'];
        unset($albums['albums']['items']);

        return $this->render('home.html.twig', [
            'albums' => $items,
            'pagination' => $albums['albums']
        ]);
    }
}