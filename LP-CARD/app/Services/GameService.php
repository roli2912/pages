<?php

namespace App\Services;

class GameService
{
    private $sessionKey = 'contest_game';

    public function initializeGame()
    {
        $cardTypes = [1, 1, 2, 2, 3, 3, 4, 5, 6];
        shuffle($cardTypes);

        $gameState = [
            'cards' => $cardTypes,
            'flipped' => [],
            'blocked' => [],
            'matched' => [],
            'current_flipped' => [],
            'game_won' => false,
            'message' => '',
            'message_type' => '',
            'correct_pair' => 1, // Maria_si_Andrei.jpg
            'incorrect_pairs' => [2, 3], // Cristina și Daiana
            'can_flip' => true,
            'step' => 'waiting'
        ];

        session([$this->sessionKey => $gameState]);
        return $gameState;
    }

    public function getGameState()
    {
        $state = session($this->sessionKey);

        if (!$state) {
            return $this->initializeGame();
        }

        return $state;
    }

    public function resetGame()
    {
        session()->forget($this->sessionKey);
        return $this->initializeGame();
    }

    public function flipCard($cardIndex)
    {
        $cardIndex = (int)$cardIndex;

        $gameState = $this->getGameState();

        if ($gameState['game_won']) {
            return $gameState;
        }

        if (!$gameState['can_flip'] ||
            in_array($cardIndex, $gameState['flipped']) ||
            in_array($cardIndex, $gameState['blocked']) ||
            in_array($cardIndex, $gameState['matched'])) {
            return $gameState;
        }

        if (count($gameState['current_flipped']) >= 2) {
            return $gameState;
        }

        $gameState['flipped'][] = $cardIndex;
        $gameState['current_flipped'][] = $cardIndex;
        $gameState['message'] = '';

        $currentFlippedCount = count($gameState['current_flipped']);

        if ($currentFlippedCount == 1) {
            $gameState['step'] = 'first_card';
            $gameState['can_flip'] = true;
        } elseif ($currentFlippedCount == 2) {
            $gameState['step'] = 'second_card';
            $gameState['can_flip'] = false;
            $this->checkPair($gameState);
        } else {
            $gameState['step'] = 'waiting';
            $gameState['can_flip'] = true;
        }

        session()->put($this->sessionKey, $gameState);
        session()->save();

        return $gameState;
    }

    private function checkPair(&$gameState)
    {
        if (count($gameState['current_flipped']) !== 2) {
            \Log::warning('checkPair called without exactly 2 cards', [
                'current_flipped' => $gameState['current_flipped']
            ]);
            return;
        }

        $card1Index = $gameState['current_flipped'][0];
        $card2Index = $gameState['current_flipped'][1];
        $card1Type = $gameState['cards'][$card1Index];
        $card2Type = $gameState['cards'][$card2Index];

        \Log::info('Checking pair', [
            'card1' => ['index' => $card1Index, 'type' => $card1Type],
            'card2' => ['index' => $card2Index, 'type' => $card2Type],
        ]);

        if ($card1Type === $card2Type) {
            if ($card1Type === $gameState['correct_pair']) {
                $gameState['matched'] = array_merge($gameState['matched'], $gameState['current_flipped']);
                $gameState['game_won'] = true;
                $gameState['message'] = 'Felicitări, ai descoperit perechea corectă!';
                $gameState['message_type'] = 'success';
                $gameState['step'] = 'won';
            } elseif (in_array($card1Type, $gameState['incorrect_pairs'])) {
                $gameState['blocked'] = array_merge($gameState['blocked'], $gameState['current_flipped']);
                $gameState['message'] = 'Pereche incorectă! Aceste cărți sunt blocate.';
                $gameState['message_type'] = 'error';
                $gameState['step'] = 'blocked';
                $gameState['can_flip'] = true;
            } else {
                $gameState['flipped'] = array_diff($gameState['flipped'], $gameState['current_flipped']);
                $gameState['step'] = 'no_match';
                $gameState['can_flip'] = true;
            }
        } else {
            $gameState['flipped'] = array_diff($gameState['flipped'], $gameState['current_flipped']);
            $gameState['step'] = 'no_match';
            $gameState['can_flip'] = true;
        }

        $gameState['current_flipped'] = [];
    }

    public function continueGame()
    {
        $gameState = $this->getGameState();
        $gameState['message'] = '';
        $gameState['message_type'] = '';
        $gameState['step'] = 'waiting';
        $gameState['can_flip'] = true;

        session()->put($this->sessionKey, $gameState);
        session()->save();

        return $gameState;
    }

    public function getCardImages()
    {
        return [
            'back' => asset('storage/contest/cards/lightning.png'),
            'cards' => [
                1 => asset('storage/contest/cards/Maria_si_Andrei.jpg'), // CORECT
                2 => asset('storage/contest/cards/Cristina_si_Andrei_2.jpg'), // INCORECT
                3 => asset('storage/contest/cards/Daiana_si_Rares_2.JPG'), // INCORECT
                4 => asset('storage/contest/cards/radu.JPG'), // SINGLE
                5 => asset('storage/contest/cards/radu.JPG'), // SINGLE
                6 => asset('storage/contest/cards/radu.JPG'), // SINGLE
            ]
        ];
    }
}
