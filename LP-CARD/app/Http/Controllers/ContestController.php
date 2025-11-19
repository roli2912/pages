<?php
// app/Http/Controllers/ContestController.php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Services\GameService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class ContestController extends Controller
{
    protected $gameService;

    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }

    public function index()
    {
        $weeklyQuestion = $this->getWeeklyQuestion();
        $gameState = $this->gameService->getGameState();
        $cardImages = $this->gameService->getCardImages();

        return view('contest.index', compact('weeklyQuestion', 'gameState', 'cardImages'));
    }

    public function flipCard(Request $request)
    {
        $cardIndex = $request->input('card_index');

        if ($cardIndex === null || $cardIndex < 0 || $cardIndex > 8) {
            return response()->json([
                'success' => false,
                'message' => 'Card invalid!'
            ], 400);
        }

        $gameState = $this->gameService->flipCard($cardIndex);

        return response()->json([
            'success' => true,
            'gameState' => $gameState
        ]);
    }

    public function resetGame()
    {
        $gameState = $this->gameService->resetGame();

        return response()->json([
            'success' => true,
            'gameState' => $gameState
        ]);
    }

    public function register(Request $request)
    {
        try {
            $validated = $request->validate([
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|min:10|max:15|regex:/^[0-9]+$/', // Doar cifre
                'creative_answer' => 'required|string|min:10',
                'newsletter_subscription' => 'nullable|boolean',
                'terms_accepted' => 'required|accepted'
            ], [
                'first_name.required' => 'Numele este obligatoriu.',
                'last_name.required' => 'Prenumele este obligatoriu.',
                'email.required' => 'Email-ul este obligatoriu.',
                'email.email' => 'Email-ul trebuie să fie valid (ex: nume@domeniu.com).',
                'phone.required' => 'Telefonul este obligatoriu.',
                'phone.min' => 'Telefonul trebuie să aibă cel puțin 10 cifre.',
                'phone.max' => 'Telefonul poate avea maximum 15 cifre.',
                'phone.regex' => 'Telefonul trebuie să conțină doar cifre.',
                'creative_answer.required' => 'Răspunsul la întrebare este obligatoriu.',
                'creative_answer.min' => 'Răspunsul trebuie să aibă cel puțin 10 caractere.',
                'terms_accepted.required' => 'Trebuie să accepți regulamentul concursului.',
                'terms_accepted.accepted' => 'Trebuie să accepți regulamentul concursului.',
            ]);

            if (!Participant::canRegister($validated['email'])) {
                session()->flash('show_form', true);
                return back()->withErrors(['email' => 'Acest email s-a înscris deja în această săptămână!'])
                    ->withInput();
            }

            Participant::create([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'creative_answer' => $validated['creative_answer'],
                'newsletter_subscription' => isset($validated['newsletter_subscription']) && $validated['newsletter_subscription'] == '1',
                'week_identifier' => Participant::getCurrentWeekIdentifier()
            ]);

            $this->gameService->resetGame();

            return redirect()->route('index')->with('success', 'Înregistrarea a fost realizată cu succes! Mult noroc!');

        } catch (ValidationException $e) {
            session()->flash('show_form', true);
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            \Log::error('Registration error', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            session()->flash('show_form', true);
            return back()->with('error', 'A apărut o eroare: ' . $e->getMessage())->withInput();
        }
    }

    private function getWeeklyQuestion()
    {
        $questions = [
            'Care cuplu a participat la ceremonia focului în avans?',
        ];

        $weekNumber = (int)date('W');
        return $questions[($weekNumber - 1) % count($questions)];
    }
}
