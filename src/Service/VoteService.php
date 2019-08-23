<?php

namespace App\Service;

class VoteService
{
    private $fileSystem;
    private $apiService;

    public function __construct(FileSystemService $fileSystemService, ApiService $apiService)
    {
        $this->fileSystem = $fileSystemService;
        $this->apiService = $apiService;
    }

    public function vote($gameId, $upvote = true)
    {
        if ($upvote) {
            $this->upvote($gameId);
        } else {
            $this->downvote($gameId);
        }
    }

    public function upvote($gameId)
    {
        $currentVotes = (int) $this->fileSystem->read($gameId);
        $currentVotes++;
        $this->fileSystem->write($gameId, $currentVotes);
    }

    public function downvote($gameId)
    {
        $currentVotes = (int) $this->fileSystem->read($gameId);
        $currentVotes = $currentVotes <= 0 ? 0 : --$currentVotes;
        $this->fileSystem->write($gameId, $currentVotes);
    }

    public function getVotes()
    {
        $games = $this->fileSystem->getVotes();
        $totalVotes = (int) $this->getTotalVotes();
        $array = [];

        foreach ($games as $gameId => $vote) {
            if ($vote == 0) {
                continue;
            }
            $array[$gameId]['details'] = $this->apiService->getNintendoGame($gameId);
            $array[$gameId]['votes'] = $vote;
            $array[$gameId]['percentage'] = $vote * 100 / $totalVotes;
        }

        usort($array, function ($item1, $item2) {
            return $item1['votes'] <= $item2['votes'];
        });

        return $array;
    }

    public function getTotalVotes()
    {
        $games = $this->fileSystem->getVotes();
        $total = 0;

        foreach ($games as $vote) {
            $total += $vote;
        }

        return $total;
    }
}
