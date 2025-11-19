@extends('layouts.app')

@section('title', 'Rezultat - A1 Jocul Perechilor')

@section('content')
    <div class="contest-container">
        <!-- Contest Info -->
        <div class="contest-info">
            <h2>Rezultat</h2>
            @if($gameState['message'])
                <div class="game-message message {{ $gameState['message_type'] }}">
                    {{ $gameState['message'] }}
                </div>
            @endif
        </div>

        <div class="game-board">
            <div class="cards-grid">
                @foreach($gameState['cards'] as $index => $cardType)
                    <div class="card
                    @if(in_array($index, $gameState['flipped'])) flipped @endif
                    @if(in_array($index, $gameState['matched'])) matched @endif
                    @if(in_array($index, $gameState['blocked'])) blocked @endif">
                        <div class="card-face card-back">A1</div>
                        <div class="card-face card-front">
                            <img src="{{ $cardImages['cards'][$cardType] }}" alt="Card {{ $cardType }}"
                                 onerror="this.src='data:image/svg+xml,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' width=\'100\' height=\'100\' viewBox=\'0 0 100 100\'%3E%3Crect width=\'100\' height=\'100\' fill=\'%23{{ ['', 'ff4081', 'e91e63', '9c27b0', '673ab7', '3f51b5', '2196f3', '03a9f4'][$cardType] ?? 'cccccc' }}\'/%3E%3Ctext x=\'50\' y=\'55\' text-anchor=\'middle\' fill=\'white\' font-size=\'20\' font-weight=\'bold\'%3E{{ $cardType }}%3C/text%3E%3C/svg%3E'">
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="game-controls">
                @if($gameState['game_won'])
                    <a href="{{ route('index') }}" class="btn btn-primary">Completează formularul</a>
                    <form method="POST" action="{{ route('reset') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-secondary">Joc nou</button>
                    </form>
                @else
                    <form method="POST" action="{{ route('continue') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-primary">Continuă jocul</button>
                    </form>
                    <form method="POST" action="{{ route('reset') }}" style="display: inline;">
                        @csrf
                        <button type="submit" class="btn btn-secondary">Resetează jocul</button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <style>
        /* Use same styles as index.blade.php */
        .contest-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .contest-info {
            background: rgba(255,255,255,0.95);
            border-radius: 20px;
            padding: 30px;
            margin-bottom: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            text-align: center;
        }

        .contest-info h2 {
            color: #e91e63;
            margin-bottom: 20px;
            font-size: 28px;
        }

        .game-board {
            background: rgba(255,255,255,0.95);
            border-radius: 20px;
            padding: 30px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        .game-message {
            text-align: center;
            margin-bottom: 20px;
            padding: 15px;
            border-radius: 10px;
            font-weight: bold;
        }

        .cards-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            max-width: 500px;
            margin: 0 auto 20px;
        }

        .card {
            width: 100%;
            aspect-ratio: 1;
            border-radius: 15px;
            position: relative;
            transform-style: preserve-3d;
            transition: transform 0.6s ease;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
        }

        .card.flipped {
            transform: rotateY(180deg);
        }

        .card.matched {
            opacity: 0.7;
        }

        .card.blocked {
            opacity: 0.5;
            border: 3px solid #ff1744;
        }

        .card-face {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .card-back {
            background: linear-gradient(135deg, #ff4081, #e91e63);
            color: white;
            font-size: 24px;
            font-weight: bold;
        }

        .card-front {
            background: white;
            transform: rotateY(180deg);
            border: 3px solid #e91e63;
        }

        .card-front img {
            width: 80%;
            height: 80%;
            object-fit: cover;
            border-radius: 10px;
        }

        .game-controls {
            text-align: center;
            margin-top: 20px;
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            margin: 0 10px;
            transition: transform 0.2s;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn-primary {
            background: linear-gradient(135deg, #ff4081, #e91e63);
            color: white;
        }

        .btn-secondary {
            background: #6c757d;
            color: white;
        }

        .message {
            text-align: center;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 20px;
            font-weight: bold;
        }

        .message.success {
            background: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .message.error {
            background: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        @media (max-width: 768px) {
            .contest-container {
                padding: 15px;
            }

            .cards-grid {
                gap: 10px;
                max-width: 300px;
            }

            .contest-info, .game-board {
                padding: 20px;
            }

            .btn {
                margin: 5px;
                font-size: 14px;
                padding: 10px 16px;
            }
        }
    </style>
@endsection
