<?php

namespace App\Controller;

use App\Service\ApiService;
use App\Service\VoteService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Component\HttpFoundation\Request;

class GamesController extends AbstractController
{
    /**
     * @Route("/games/list", name="game_list"), methods={"GET"}
     */
    public function list(ApiService $apiService)
    {
        $nintendoGames = $apiService->getNintendoGames();
        return $this->json($nintendoGames);
    }

    /**
     * @Route("/games/vote", name="game_vote"), methods={"POST"}
     * @param Request $request
     * @param VoteService $voteService
     * @return JsonResponse
     */
    public function vote(Request $request, VoteService $voteService)
    {
        $game = json_decode($request->getContent(), true);
        if (!empty($game['id'])) {
            $voteService->vote($game['id'], $game['voted']);
        }

        return $this->json([$game], 201);
    }

    /**
     * @Route("/votes", name="votes"), methods={"GET"}
     * @return JsonResponse
     */
    public function getVotes(VoteService $voteService)
    {
        return $this->json($voteService->getVotes());
    }
}
