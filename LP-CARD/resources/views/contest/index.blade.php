@extends('layouts.app')

@section('title', 'A1 - Jocul Perechilor')

@section('content')
    <div class="contest-container">
        @if(session('success'))
            <div class="message success">{{ session('success') }}</div>
        @endif

        <div class="contest-info">
            <h2>Jocul Perechilor</h2>
            <p>Găsește cele 2 cartonașe identice care dau răspunsul corect la întrebare, completează formularul și înscrie-te în concursul "JOCUL PERECHILOR".</p>
            <p>Nu rata șansa de a câștiga săptămânal o boxă portabilă și marele premiu, un voucher de călătorie în Thailanda.</p>
        </div>

        <div class="question">
            {{ $weeklyQuestion }}
        </div>

        <div class="game-board">
            <div id="gameMessage" class="game-message"></div>

            <div class="cards-grid" id="cardsGrid">
                @foreach($gameState['cards'] as $index => $cardType)
                    <div class="card-wrapper" data-index="{{ $index }}">
                        <div class="card"
                             data-card-index="{{ $index }}"
                             data-card-type="{{ $cardType }}">
                            <div class="card-face card-back">
                                <img src="{{ $cardImages['back'] }}" alt="Card back">
                            </div>
                            <div class="card-face card-front">
                                <img src="{{ $cardImages['cards'][$cardType] }}" alt="Card {{ $cardType }}">
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="game-controls">
                <button id="resetBtn" class="btn btn-secondary">Resetează jocul</button>
            </div>
        </div>

        <div class="form-container {{ ($gameState['game_won'] || session('show_form') || $errors->any()) ? 'show' : '' }}" id="registrationForm">
            <h3>Completează formularul pentru a te înscrie în concurs</h3>

            @if(session('error'))
                <div class="message error">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="message error">
                    <strong>Te rugăm să corectezi următoarele erori:</strong>
                    <ul style="margin-top: 10px; padding-left: 20px;">
                        @foreach($errors->all() as $error)
                            <li style="margin-bottom: 5px;">{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}" novalidate>
                @csrf

                <div class="form-group">
                    <label for="creative_answer">Care-i cuplul în care ai încredere că va rezista tentației și de ce? *</label>
                    <textarea name="creative_answer" id="creative_answer" required placeholder="Scrie răspunsul tău aici..." class="{{ $errors->has('creative_answer') ? 'error-input' : '' }}">{{ old('creative_answer') }}</textarea>
                    @error('creative_answer')
                    <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="first_name">Nume *</label>
                        <input type="text" name="first_name" id="first_name" value="{{ old('first_name') }}" required class="{{ $errors->has('first_name') ? 'error-input' : '' }}">
                        @error('first_name')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="last_name">Prenume *</label>
                        <input type="text" name="last_name" id="last_name" value="{{ old('last_name') }}" required class="{{ $errors->has('last_name') ? 'error-input' : '' }}">
                        @error('last_name')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="email">Email *</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}" class="{{ $errors->has('email') ? 'error-input' : '' }}">
                        @error('email')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        <label for="phone">Telefon *</label>
                        <input type="tel" name="phone" id="phone" value="{{ old('phone') }}" class="{{ $errors->has('phone') ? 'error-input' : '' }}">
                        @error('phone')
                        <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" name="terms_accepted" id="terms_accepted" value="1" {{ old('terms_accepted') ? 'checked' : '' }} required>
                    <label for="terms_accepted">Accept regulamentul concursului *</label>
                </div>
                @error('terms_accepted')
                <span class="error-message" style="display: block; margin-top: -15px; margin-bottom: 15px;">{{ $message }}</span>
                @enderror

                <div class="checkbox-group">
                    <input type="checkbox" name="newsletter_subscription" id="newsletter_subscription" value="1" {{ old('newsletter_subscription') ? 'checked' : '' }}>
                    <label for="newsletter_subscription">Doresc să mă abonez la newsletter</label>
                </div>

                <button type="submit" class="submit-btn">Înscrie-te în concurs</button>
            </form>
        </div>

            <style>
                @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800&display=swap');

                * {
                    margin: 0;
                    padding: 0;
                    box-sizing: border-box;
                }

                body {
                    font-family: 'Inter', sans-serif;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 25%, #f093fb 50%, #4facfe 75%, #00f2fe 100%);
                    background-size: 400% 400%;
                    animation: gradientShift 15s ease infinite;
                    min-height: 100vh;
                    position: relative;
                    overflow-x: hidden;
                }

                @keyframes gradientShift {
                    0% { background-position: 0% 50%; }
                    50% { background-position: 100% 50%; }
                    100% { background-position: 0% 50%; }
                }

                /* Animated background particles */
                body::before {
                    content: '';
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    background: radial-gradient(circle at 20% 50%, rgba(255, 255, 255, 0.1) 0%, transparent 50%),
                    radial-gradient(circle at 80% 80%, rgba(255, 255, 255, 0.1) 0%, transparent 50%);
                    pointer-events: none;
                    z-index: 0;
                }

                /* Contest Container */
                .contest-container {
                    max-width: 1200px;
                    margin: 0 auto;
                    padding: 40px 20px;
                    position: relative;
                    z-index: 1;
                }

                .contest-banner {
                    text-align: center;
                    margin-bottom: 40px;
                    animation: fadeInDown 0.8s ease-out;
                    max-width: 1200px; /* Aceeași max-width ca container-ul */
                    margin-left: auto;
                    margin-right: auto;
                    padding: 0 40px; /* Aceeași padding ca celelalte secțiuni */
                }

                @keyframes fadeInDown {
                    from {
                        opacity: 0;
                        transform: translateY(-30px);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }

                .banner-image {
                    width: 100%;
                    height: 200px;
                    object-fit: cover;
                    border-radius: 20px;
                    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3),
                    0 0 60px rgba(255, 255, 255, 0.2);
                    transition: transform 0.3s ease, box-shadow 0.3s ease;
                }

                .banner-image:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.4),
                    0 0 80px rgba(255, 255, 255, 0.3);
                }

                /* Glass Card Effect */
                .contest-info,
                .game-board,
                .form-container {
                    background: rgba(255, 255, 255, 0.1);
                    backdrop-filter: blur(20px);
                    -webkit-backdrop-filter: blur(20px);
                    border-radius: 30px;
                    padding: 40px;
                    margin-bottom: 40px;
                    border: 1px solid rgba(255, 255, 255, 0.2);
                    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1),
                    inset 0 1px 0 rgba(255, 255, 255, 0.2);
                    animation: fadeInUp 0.8s ease-out;
                    transition: transform 0.3s ease, box-shadow 0.3s ease;
                }

                @keyframes fadeInUp {
                    from {
                        opacity: 0;
                        transform: translateY(30px);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }

                .contest-info:hover,
                .game-board:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.2),
                    inset 0 1px 0 rgba(255, 255, 255, 0.3);
                }

                .contest-info h2 {
                    color: #ffffff;
                    margin-bottom: 20px;
                    font-size: 36px;
                    text-align: center;
                    font-weight: 800;
                    text-shadow: 0 2px 20px rgba(0, 0, 0, 0.3);
                    letter-spacing: -0.5px;
                }

                .contest-info p {
                    font-size: 18px;
                    line-height: 1.8;
                    color: rgba(255, 255, 255, 0.95);
                    margin-bottom: 15px;
                    text-align: center;
                    font-weight: 400;
                }

                /* Question Box */
                .question {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    padding: 30px 40px;
                    border-radius: 25px;
                    text-align: center;
                    font-size: 22px;
                    font-weight: 700;
                    margin-bottom: 40px;
                    box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4),
                    0 5px 15px rgba(0, 0, 0, 0.1);
                    position: relative;
                    overflow: hidden;
                    animation: pulse 2s ease-in-out infinite;
                }

                @keyframes pulse {
                    0%, 100% {
                        transform: scale(1);
                        box-shadow: 0 15px 35px rgba(102, 126, 234, 0.4);
                    }
                    50% {
                        transform: scale(1.02);
                        box-shadow: 0 20px 45px rgba(102, 126, 234, 0.6);
                    }
                }

                .question::before {
                    content: '';
                    position: absolute;
                    top: -50%;
                    left: -50%;
                    width: 200%;
                    height: 200%;
                    background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
                    animation: shine 3s infinite;
                }

                @keyframes shine {
                    0% { transform: translateX(-100%) translateY(-100%) rotate(45deg); }
                    100% { transform: translateX(100%) translateY(100%) rotate(45deg); }
                }

                /* Game Message */
                .game-message {
                    text-align: center;
                    margin-bottom: 25px;
                    padding: 20px;
                    border-radius: 20px;
                    font-weight: 600;
                    font-size: 16px;
                    display: none;
                    animation: slideInDown 0.5s ease-out;
                }

                @keyframes slideInDown {
                    from {
                        opacity: 0;
                        transform: translateY(-20px);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }

                .game-message.show {
                    display: block;
                }

                .game-message.success {
                    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
                    color: white;
                    box-shadow: 0 10px 25px rgba(17, 153, 142, 0.3);
                }

                .game-message.error {
                    background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
                    color: white;
                    box-shadow: 0 10px 25px rgba(235, 51, 73, 0.3);
                }

                /* Cards Grid */
                .cards-grid {
                    display: grid;
                    grid-template-columns: repeat(3, 1fr);
                    gap: 20px;
                    max-width: 550px;
                    margin: 0 auto 30px;
                    perspective: 1000px;
                }

                .card-wrapper {
                    position: relative;
                }

                .card {
                    width: 100%;
                    aspect-ratio: 1;
                    border-radius: 20px;
                    position: relative;
                    transform-style: preserve-3d;
                    transition: transform 0.6s cubic-bezier(0.4, 0.0, 0.2, 1);
                    cursor: pointer;
                    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
                }

                .card:hover:not(.flipped):not(.blocked):not(.matched) {
                    transform: scale(1.08) translateY(-5px);
                    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
                }

                .card.flipped {
                    transform: rotateY(180deg);
                }

                .card.matched {
                    opacity: 0.6;
                    cursor: not-allowed;
                    filter: saturate(0.5);
                }

                .card.blocked {
                    opacity: 0.4;
                    cursor: not-allowed;
                    border: 3px solid #ff1744;
                    animation: shake 0.5s;
                }

                @keyframes shake {
                    0%, 100% { transform: translateX(0); }
                    25% { transform: translateX(-10px); }
                    75% { transform: translateX(10px); }
                }

                .card.disabled {
                    opacity: 0.5;
                    cursor: not-allowed;
                    pointer-events: none;
                }

                .card-face {
                    position: absolute;
                    width: 100%;
                    height: 100%;
                    backface-visibility: hidden;
                    border-radius: 20px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    overflow: hidden;
                }

                .card-face img {
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    border-radius: 20px;
                }

                .card-back {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    box-shadow: inset 0 0 20px rgba(0, 0, 0, 0.2);
                }

                .card-front {
                    background: white;
                    transform: rotateY(180deg);
                    box-shadow: 0 0 20px rgba(255, 255, 255, 0.2);
                }

                /* Game Controls */
                .game-controls {
                    text-align: center;
                    margin-top: 30px;
                }

                /* Buttons */
                .btn {
                    padding: 15px 35px;
                    border: none;
                    border-radius: 50px;
                    font-size: 16px;
                    font-weight: 700;
                    cursor: pointer;
                    text-decoration: none;
                    display: inline-block;
                    transition: all 0.3s cubic-bezier(0.4, 0.0, 0.2, 1);
                    position: relative;
                    overflow: hidden;
                    letter-spacing: 0.5px;
                    text-transform: uppercase;
                }

                .btn::before {
                    content: '';
                    position: absolute;
                    top: 50%;
                    left: 50%;
                    width: 0;
                    height: 0;
                    border-radius: 50%;
                    background: rgba(255, 255, 255, 0.3);
                    transform: translate(-50%, -50%);
                    transition: width 0.6s, height 0.6s;
                }

                .btn:hover::before {
                    width: 300px;
                    height: 300px;
                }

                .btn:hover {
                    transform: translateY(-3px);
                    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
                }

                .btn-primary {
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
                }

                .btn-secondary {
                    background: linear-gradient(135deg, #868f96 0%, #596164 100%);
                    color: white;
                    box-shadow: 0 8px 20px rgba(134, 143, 150, 0.4);
                }

                /* Form Container */
                .form-container {
                    display: none;
                    animation: scaleIn 0.5s ease-out;
                }

                @keyframes scaleIn {
                    from {
                        opacity: 0;
                        transform: scale(0.9);
                    }
                    to {
                        opacity: 1;
                        transform: scale(1);
                    }
                }

                .form-container.show {
                    display: block;
                }

                .form-container h3 {
                    color: #ffffff;
                    text-align: center;
                    margin-bottom: 35px;
                    font-size: 32px;
                    font-weight: 800;
                    text-shadow: 0 2px 20px rgba(0, 0, 0, 0.3);
                }

                .form-row {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 25px;
                }

                .form-group {
                    margin-bottom: 25px;
                }

                .form-group label {
                    display: block;
                    margin-bottom: 10px;
                    font-weight: 600;
                    color: rgba(255, 255, 255, 0.95);
                    font-size: 15px;
                    letter-spacing: 0.3px;
                }

                .form-group input,
                .form-group textarea {
                    width: 100%;
                    padding: 18px 20px;
                    border: 2px solid rgba(255, 255, 255, 0.2);
                    background: rgba(255, 255, 255, 0.1);
                    backdrop-filter: blur(10px);
                    border-radius: 15px;
                    font-size: 16px;
                    transition: all 0.3s ease;
                    font-family: inherit;
                    color: #ffffff;
                }

                .form-group input::placeholder,
                .form-group textarea::placeholder {
                    color: rgba(255, 255, 255, 0.5);
                }

                .form-group input:focus,
                .form-group textarea:focus {
                    outline: none;
                    border-color: rgba(255, 255, 255, 0.5);
                    background: rgba(255, 255, 255, 0.15);
                    box-shadow: 0 0 20px rgba(255, 255, 255, 0.2);
                    transform: translateY(-2px);
                }

                .form-group textarea {
                    height: 140px;
                    resize: vertical;
                }

                .error-input {
                    border-color: #ff1744 !important;
                    background: rgba(255, 23, 68, 0.1) !important;
                    animation: errorShake 0.5s;
                }

                @keyframes errorShake {
                    0%, 100% { transform: translateX(0); }
                    25% { transform: translateX(-5px); }
                    75% { transform: translateX(5px); }
                }

                /* Checkbox */
                .checkbox-group {
                    display: flex;
                    align-items: flex-start;
                    gap: 12px;
                    margin-bottom: 20px;
                }

                .checkbox-group input[type="checkbox"] {
                    width: 22px;
                    height: 22px;
                    margin: 0;
                    margin-top: 2px;
                    cursor: pointer;
                    accent-color: #667eea;
                }

                .checkbox-group label {
                    margin: 0;
                    font-weight: 400;
                    line-height: 1.6;
                    flex: 1;
                    color: rgba(255, 255, 255, 0.9);
                    font-size: 15px;
                }

                .checkbox-group a {
                    color: #ffffff;
                    text-decoration: underline;
                    font-weight: 600;
                }

                .checkbox-group a:hover {
                    color: #667eea;
                }

                /* Error Messages */
                .error-message {
                    color: #ff1744;
                    font-size: 14px;
                    display: block;
                    margin-top: 8px;
                    font-weight: 600;
                    text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
                }

                /* Submit Button */
                .submit-btn {
                    width: 100%;
                    padding: 20px;
                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                    color: white;
                    border: none;
                    border-radius: 15px;
                    font-size: 18px;
                    font-weight: 700;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    position: relative;
                    overflow: hidden;
                    text-transform: uppercase;
                    letter-spacing: 1px;
                    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
                }

                .submit-btn::before {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: -100%;
                    width: 100%;
                    height: 100%;
                    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
                    transition: left 0.5s;
                }

                .submit-btn:hover::before {
                    left: 100%;
                }

                .submit-btn:hover {
                    transform: translateY(-3px);
                    box-shadow: 0 15px 40px rgba(102, 126, 234, 0.6);
                }

                /* Message Box */
                .message {
                    text-align: center;
                    padding: 20px 25px;
                    border-radius: 20px;
                    margin-bottom: 25px;
                    font-weight: 600;
                    font-size: 16px;
                    animation: slideInDown 0.5s ease-out;
                }

                .message.success {
                    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
                    color: white;
                    box-shadow: 0 10px 25px rgba(17, 153, 142, 0.3);
                }

                .message.error {
                    background: linear-gradient(135deg, #eb3349 0%, #f45c43 100%);
                    color: white;
                    box-shadow: 0 10px 25px rgba(235, 51, 73, 0.3);
                }

                .message ul {
                    list-style: none;
                    padding: 0;
                    margin: 10px 0 0 0;
                }

                .message li {
                    margin-bottom: 8px;
                    padding-left: 25px;
                    position: relative;
                }

                .message li::before {
                    content: '•';
                    position: absolute;
                    left: 0;
                    font-size: 20px;
                }

                /* Responsive Design */
                @media (max-width: 768px) {
                    .contest-container {
                        padding: 20px 15px;
                    }

                    .contest-info h2 {
                        font-size: 28px;
                    }

                    .contest-info p {
                        font-size: 16px;
                    }

                    .question {
                        font-size: 18px;
                        padding: 20px;
                    }

                    .form-row {
                        grid-template-columns: 1fr;
                        gap: 0;
                    }

                    .cards-grid {
                        gap: 12px;
                        max-width: 320px;
                    }

                    .contest-info,
                    .game-board,
                    .form-container {
                        padding: 25px;
                    }

                    .btn {
                        padding: 12px 25px;
                        font-size: 14px;
                    }

                    .form-container h3 {
                        font-size: 24px;
                    }

                    .contest-banner {
                        padding: 0 20px; /* Aceeași padding ca celelalte pe mobile */
                    }

                    .banner-image {
                        height: 120px;
                        border-radius: 12px;
                    }
                }

                /* Additional Animations */
                @keyframes float {
                    0%, 100% {
                        transform: translateY(0);
                    }
                    50% {
                        transform: translateY(-10px);
                    }
                }

                .contest-banner {
                    animation: float 3s ease-in-out infinite;
                }
            </style>

    <script>
        class ContestGame {
            constructor() {
                this.cards = document.querySelectorAll('.card');
                this.gameMessage = document.getElementById('gameMessage');
                this.registrationForm = document.getElementById('registrationForm');
                this.resetBtn = document.getElementById('resetBtn');
                this.canFlip = true;
                this.flippedCards = [];

                this.init();
            }

            init() {
                this.cards.forEach(card => {
                    card.addEventListener('click', (e) => this.handleCardClick(e));
                });

                this.resetBtn.addEventListener('click', () => this.resetGame());

                @if(session('show_form') || $errors->any())
                    this.registrationForm.classList.add('show');
                this.registrationForm.scrollIntoView({ behavior: 'smooth' });
                @endif
            }

            async syncWithServer() {
                // Get initial game state (already loaded on page)
                // Cards are already rendered with initial state
            }

            async handleCardClick(e) {
                const card = e.currentTarget;
                const cardIndex = parseInt(card.dataset.cardIndex);

                if (!this.canFlip ||
                    card.classList.contains('flipped') ||
                    card.classList.contains('blocked') ||
                    card.classList.contains('matched') ||
                    this.flippedCards.length >= 2) {
                    return;
                }

                card.classList.add('flipped');
                this.flippedCards.push(cardIndex);

                try {
                    const response = await fetch('{{ route("flip-card") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ card_index: cardIndex })
                    });

                    const data = await response.json();

                    if (data.success) {
                        this.updateGameState(data.gameState);
                    }
                } catch (error) {
                    console.error('Error:', error);
                    this.showMessage('A apărut o eroare. Te rugăm să reîncarci pagina.', 'error');
                }
            }

            updateGameState(gameState) {
                if (gameState.step === 'first_card') {
                    return;
                }

                if (gameState.step === 'second_card' || gameState.step === 'won' || gameState.step === 'blocked' || gameState.step === 'no_match') {
                    setTimeout(() => {
                        this.processResult(gameState);
                    }, 600);
                }
            }

            processResult(gameState) {
                // Show message if exists
                if (gameState.message) {
                    this.showMessage(gameState.message, gameState.message_type);
                }

                // Update all cards
                this.cards.forEach((card, index) => {
                    card.classList.remove('flipped', 'blocked', 'matched');

                    if (gameState.flipped.includes(index)) {
                        card.classList.add('flipped');
                    }
                    if (gameState.blocked.includes(index)) {
                        card.classList.add('blocked');
                    }
                    if (gameState.matched.includes(index)) {
                        card.classList.add('matched');
                    }
                });

                if (gameState.game_won) {
                    setTimeout(() => {
                        this.registrationForm.classList.add('show');
                        this.registrationForm.scrollIntoView({ behavior: 'smooth' });
                    }, 1500);
                }

                this.flippedCards = [];
                this.canFlip = gameState.can_flip;
            }

            showMessage(text, type) {
                this.gameMessage.textContent = text;
                this.gameMessage.className = `game-message ${type} show`;

                setTimeout(() => {
                    this.gameMessage.classList.remove('show');
                }, 3000);
            }

            async resetGame() {
                try {
                    const response = await fetch('{{ route("reset") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        }
                    });

                    const data = await response.json();

                    if (data.success) {
                        window.location.reload();
                    }
                } catch (error) {
                    console.error('Error:', error);
                }
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            new ContestGame();
        });
    </script>
@endsection
