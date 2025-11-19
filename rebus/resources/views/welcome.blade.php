<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Crossword</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rajdhani:wght@400;500;600;700&family=Orbitron:wght@400;700;900&display=swap" rel="stylesheet">
    <style>
        :root {
            --size: 48px;
            --f1-red: #E10600;
            --f1-red-glow: rgba(225, 6, 0, 0.4);
            --dark-bg: #0a0e27;
            --dark-card: #151932;
            --dark-card-hover: #1a1f3a;
            --text-primary: #ffffff;
            --text-secondary: #94a3b8;
            --border-color: rgba(255, 255, 255, 0.1);
            --success: #10b981;
            --error: #ef4444;
            --input-bg: rgba(21, 25, 50, 0.6);
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Rajdhani', sans-serif;
            background: var(--dark-bg);
            color: var(--text-primary);
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
            padding-bottom: 60px;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background:
                radial-gradient(circle at 20% 50%, rgba(225, 6, 0, 0.15) 0%, transparent 50%),
                radial-gradient(circle at 80% 80%, rgba(59, 130, 246, 0.12) 0%, transparent 50%),
                radial-gradient(circle at 40% 20%, rgba(139, 92, 246, 0.1) 0%, transparent 50%);
            animation: bgShift 15s ease-in-out infinite;
            z-index: -1;
        }

        @keyframes bgShift {
            0%, 100% { opacity: 1; transform: scale(1); }
            50% { opacity: 0.8; transform: scale(1.1); }
        }

        body::after {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image:
                linear-gradient(rgba(255, 255, 255, 0.02) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.02) 1px, transparent 1px);
            background-size: 50px 50px;
            z-index: -1;
            pointer-events: none;
        }

        h1 {
            font-family: 'Orbitron', sans-serif;
            font-size: clamp(32px, 6vw, 56px);
            font-weight: 900;
            text-align: center;
            margin: 32px 0 40px;
            background: linear-gradient(135deg, #ffffff 0%, var(--f1-red) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            letter-spacing: 4px;
            text-transform: uppercase;
            position: relative;
        }

        h1::after {
            content: '';
            position: absolute;
            bottom: -16px;
            left: 50%;
            transform: translateX(-50%);
            width: 120px;
            height: 4px;
            background: linear-gradient(90deg, transparent 0%, var(--f1-red) 50%, transparent 100%);
            border-radius: 2px;
            box-shadow: 0 0 20px var(--f1-red-glow);
        }

        .cw-wrap {
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 24px 32px;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 48px;
        }

        /* Crossword Container */
        .crossword-container {
            width: 100%;
            display: flex;
            justify-content: center;
        }

        .crossword {
            display: grid;
            grid-template-columns: repeat(var(--cols), var(--size));
            gap: 3px;
            padding: 20px;
            background: rgba(21, 25, 50, 0.6);
            backdrop-filter: blur(20px);
            border: 2px solid var(--border-color);
            border-radius: 24px;
            box-shadow:
                0 8px 32px rgba(0, 0, 0, 0.4),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
            position: relative;
        }

        .crossword::before {
            content: '';
            position: absolute;
            inset: 0;
            border-radius: 22px;
            padding: 2px;
            background: linear-gradient(135deg, var(--f1-red) 0%, transparent 50%, rgba(59, 130, 246, 0.3) 100%);
            -webkit-mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
            -webkit-mask-composite: xor;
            mask-composite: exclude;
            opacity: 0;
            transition: opacity 0.3s ease;
            pointer-events: none;
        }

        .crossword:hover::before {
            opacity: 1;
        }

        .cell {
            width: var(--size);
            height: var(--size);
            background: rgba(21, 25, 50, 0.9);
            position: relative;
            border: 2px solid var(--border-color);
            border-radius: 8px;
            transition: all 0.2s ease;
            overflow: visible;
        }

        .cell:not(.block):hover {
            background: var(--dark-card-hover);
            border-color: var(--f1-red);
            transform: scale(1.08);
            box-shadow: 0 0 25px var(--f1-red-glow);
            z-index: 100;
        }

        .cell.block {
            background: rgba(10, 14, 39, 0.8);
            border-color: rgba(255, 255, 255, 0.05);
        }

        .cell input {
            position: absolute;
            inset: 0;
            border: none;
            background: transparent;
            text-align: center;
            font-family: 'Orbitron', sans-serif;
            font-size: 22px;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--text-primary);
            outline: none;
            caret-color: var(--f1-red);
            transition: color 0.2s ease;
        }

        .cell input:focus {
            color: var(--f1-red);
        }

        .cell .number {
            position: absolute;
            top: -18px;
            left: 4px;
            font-family: 'Orbitron', sans-serif;
            font-size: 12px;
            font-weight: 700;
            color: var(--f1-red);
            text-shadow: 0 0 10px var(--f1-red-glow);
            z-index: 10;
        }

        /* Bottom Section - Questions and Form */
        .bottom-section {
            width: 100%;
            max-width: 1200px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 32px;
        }

        @media (max-width: 968px) {
            .bottom-section {
                grid-template-columns: 1fr;
            }
        }

        .clues {
            background: rgba(21, 25, 50, 0.6);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 32px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
        }

        .clues h2 {
            font-family: 'Orbitron', sans-serif;
            font-size: 28px;
            font-weight: 700;
            color: var(--f1-red);
            margin: 0 0 24px 0;
            text-transform: uppercase;
            letter-spacing: 2px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .clues h2::before {
            content: '🏁';
            font-size: 32px;
        }

        .clues ol {
            list-style: none;
            counter-reset: question;
            margin: 0;
            padding: 0;
        }

        .clues li {
            counter-increment: question;
            padding: 14px 16px;
            margin-bottom: 12px;
            background: rgba(10, 14, 39, 0.4);
            border-radius: 12px;
            border-left: 3px solid transparent;
            transition: all 0.3s ease;
            font-size: 16px;
            line-height: 1.6;
            position: relative;
            padding-left: 50px;
        }

        .clues li::before {
            content: counter(question);
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            width: 26px;
            height: 26px;
            background: var(--f1-red);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Orbitron', sans-serif;
            font-size: 13px;
            font-weight: 700;
            color: white;
            box-shadow: 0 0 10px var(--f1-red-glow);
        }

        .clues li:hover {
            background: rgba(21, 25, 50, 0.8);
            border-left-color: var(--f1-red);
            transform: translateX(4px);
            box-shadow: 0 4px 12px rgba(225, 6, 0, 0.2);
        }

        .clues em {
            color: var(--text-secondary);
            font-style: normal;
            font-size: 0.9em;
        }

        /* Form Section */
        .form-section {
            background: rgba(21, 25, 50, 0.6);
            backdrop-filter: blur(20px);
            border: 1px solid var(--border-color);
            border-radius: 24px;
            padding: 32px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
        }

        .form-section h2 {
            font-family: 'Orbitron', sans-serif;
            font-size: 28px;
            font-weight: 700;
            color: var(--f1-red);
            margin: 0 0 24px 0;
            text-transform: uppercase;
            letter-spacing: 2px;
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .form-section h2::before {
            content: '📝';
            font-size: 32px;
        }

        .ae-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
            margin: 18px 0;
        }

        @media (max-width: 640px) {
            .ae-row {
                grid-template-columns: 1fr;
            }
        }

        .ae-field {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .ae-field label {
            font-weight: 700;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: var(--text-secondary);
        }

        .ae-input {
            width: 100%;
            height: 56px;
            border: 2px solid var(--border-color);
            border-radius: 12px;
            background: var(--input-bg);
            backdrop-filter: blur(10px);
            padding: 14px 16px;
            font-family: 'Rajdhani', sans-serif;
            font-size: 16px;
            font-weight: 600;
            line-height: 1.2;
            color: var(--text-primary);
            outline: none;
            transition: all 0.3s ease;
        }

        .ae-input::placeholder {
            color: rgba(148, 163, 184, 0.5);
        }

        .ae-input:focus {
            border-color: var(--f1-red);
            box-shadow: 0 0 0 4px rgba(225, 6, 0, 0.15);
            background: rgba(21, 25, 50, 0.8);
        }

        .ae-err {
            color: var(--error);
            font-size: 13px;
            font-weight: 600;
            margin-top: -4px;
        }

        .ae-submit {
            margin-top: 24px;
            display: flex;
            gap: 12px;
        }

        .ae-btn {
            width: 100%;
            padding: 18px 32px;
            border-radius: 12px;
            border: 0;
            background: var(--f1-red);
            color: #fff;
            font-family: 'Orbitron', sans-serif;
            font-weight: 700;
            font-size: 18px;
            text-transform: uppercase;
            letter-spacing: 2px;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            box-shadow: 0 4px 20px var(--f1-red-glow);
        }

        .ae-btn::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .ae-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 30px var(--f1-red-glow);
        }

        .ae-btn:hover::before {
            width: 300px;
            height: 300px;
        }

        .ae-btn:active {
            transform: translateY(0);
        }

        .notice {
            margin: 16px 0;
            padding: 14px 18px;
            border-radius: 12px;
            font-weight: 700;
            text-align: center;
            background: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.3);
            color: #60a5fa;
        }

        #flash-below-form {
            max-width: 1400px;
            margin: 24px auto 0;
            padding: 0 24px;
        }

        #flash-below-form > div {
            margin: 12px 0;
            padding: 16px 20px;
            border-radius: 12px;
            font-weight: 700;
            text-align: center;
            backdrop-filter: blur(10px);
        }

        .flash.success,
        #flash-below-form div[style*="background:#ecfdf5"] {
            background: rgba(16, 185, 129, 0.1) !important;
            color: var(--success) !important;
            border: 1px solid var(--success) !important;
            box-shadow: 0 4px 12px rgba(16, 185, 129, 0.2);
        }

        .flash.error,
        #flash-below-form div[style*="background:#fef2f2"] {
            background: rgba(239, 68, 68, 0.1) !important;
            color: var(--error) !important;
            border: 1px solid var(--error) !important;
            box-shadow: 0 4px 12px rgba(239, 68, 68, 0.2);
        }

        @media (max-width: 768px) {
            h1 {
                font-size: 32px;
                margin: 24px 0 32px;
            }

            .cw-wrap {
                padding: 0 16px 24px;
                gap: 32px;
            }

            .crossword {
                padding: 12px;
                gap: 2px;
            }

            .clues, .form-section {
                padding: 24px;
            }

            .clues h2, .form-section h2 {
                font-size: 24px;
            }

            :root {
                --size: 40px;
            }
        }

        @keyframes cellAppear {
            from {
                opacity: 0;
                transform: scale(0.8);
            }
            to {
                opacity: 1;
                transform: scale(1);
            }
        }

        .cell {
            animation: cellAppear 0.3s ease forwards;
        }

        ::-webkit-scrollbar {
            width: 10px;
        }

        ::-webkit-scrollbar-track {
            background: rgba(10, 14, 39, 0.5);
        }

        ::-webkit-scrollbar-thumb {
            background: var(--f1-red);
            border-radius: 5px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #ff0800;
        }
    </style>
</head>
<body>
<h1>🏎️ Rebus F1</h1>

@php
    $minR   = isset($minR) ? (int)$minR : 0;
    $minC   = isset($minC) ? (int)$minC : 0;
    $values = old('cell', $values ?? []);
@endphp

<form method="post" action="{{ route('crossword.submit') }}" id="cw-form" novalidate>
    @csrf

    <div class="cw-wrap">
        <!-- Crossword Grid -->
        <div class="crossword-container">
            <div class="crossword" style="--cols: {{ $cols }}">
                @for ($r = 0; $r < $rows; $r++)
                    @for ($c = 0; $c < $cols; $c++)
                        @php
                            $cell  = $grid[$r][$c];
                            $keyId = "$r-$c";
                            $key   = ($r+$minR).','.($c+$minC);
                            $links = $nav[$keyId] ?? ['aNext'=>null,'aPrev'=>null,'dNext'=>null,'dPrev'=>null];
                            $val   = $values[$key] ?? '';
                        @endphp

                        @if ($cell['ch'])
                            <div class="cell">
                                <input
                                    id="cell-{{ $keyId }}"
                                    name="cell[{{ $key }}]"
                                    type="text"
                                    maxlength="1"
                                    autocomplete="off"
                                    spellcheck="false"
                                    value="{{ $val }}"
                                    data-anext="{{ $links['aNext'] ?? '' }}"
                                    data-aprev="{{ $links['aPrev'] ?? '' }}"
                                    data-dnext="{{ $links['dNext'] ?? '' }}"
                                    data-dprev="{{ $links['dPrev'] ?? '' }}"
                                    data-dir=""
                                >
                                @if ($cell['num'])
                                    <span class="number">{{ $cell['num'] }}</span>
                                @endif
                            </div>
                        @else
                            <div class="cell block"></div>
                        @endif
                    @endfor
                @endfor
            </div>
        </div>

        <!-- Bottom Section: Questions and Form -->
        <div class="bottom-section">
            <!-- Questions -->
            <div class="clues">
                <h2>Întrebări</h2>
                <ol>
                    @foreach ($words as $w)
                        <li>{{ $w['clue'] }}</li>
                    @endforeach
                </ol>

                @if (session('neutral'))
                    <p class="notice">{{ session('neutral') }}</p>
                @endif
            </div>

            <!-- Form -->
            <div class="form-section">
                <h2>Trimite</h2>

                <div class="ae-row">
                    <div class="ae-field">
                        <label for="first_name">Prenume</label>
                        <input id="first_name" name="first_name" type="text"
                               class="ae-input" placeholder="Ex: Andrei"
                               value="{{ old('first_name') }}" required>
                        @error('first_name') <p class="ae-err">{{ $message }}</p> @enderror
                    </div>

                    <div class="ae-field">
                        <label for="last_name">Nume</label>
                        <input id="last_name" name="last_name" type="text"
                               class="ae-input" placeholder="Ex: Popescu"
                               value="{{ old('last_name') }}" required>
                        @error('last_name') <p class="ae-err">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="ae-row">
                    <div class="ae-field" style="grid-column: 1 / -1;">
                        <label for="email">Email</label>
                        <input id="email" name="email" type="email"
                               class="ae-input" placeholder="nume@exemplu.ro"
                               value="{{ old('email') }}" required>
                        @error('email') <p class="ae-err">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="ae-submit">
                    <button type="submit" class="ae-btn">Trimite Soluția</button>
                </div>
            </div>
        </div>
    </div>
</form>

<div id="flash-below-form">
    @if ($errors->has('grid'))
        <div
            style="margin:12px 0;padding:14px 16px;border-radius:10px;
             font-weight:800;text-align:center;
             background:#fef2f2;color:#991b1b;border:1px solid #fecaca;">
            {{ $errors->first('grid') }}
        </div>
    @endif

    @if (session('success'))
        <div
            style="margin:12px 0;padding:14px 16px;border-radius:10px;
             font-weight:800;text-align:center;
             background:#ecfdf5;color:#065f46;border:1px solid #a7f3d0;">
            {{ session('success') }}
        </div>
    @endif
</div>

<script>
    (() => {
        function byKey(key) {
            return key ? document.getElementById('cell-'+key) : null;
        }

        function decideDir(inp) {
            const an = inp.dataset.anext, ap = inp.dataset.aprev;
            const dn = inp.dataset.dnext, dp = inp.dataset.dprev;
            if (an) return 'across';
            if (dn) return 'down';
            if (ap) return 'across';
            if (dp) return 'down';
            return '';
        }

        function ensureDir(inp) {
            if (!inp.dataset.dir) inp.dataset.dir = decideDir(inp);
            return inp.dataset.dir;
        }

        function move(inp, backwards=false) {
            const dir = ensureDir(inp);
            if (!dir) return;
            let nextKey;
            if (!backwards) {
                nextKey = (dir==='across') ? inp.dataset.anext : inp.dataset.dnext;
                if (!nextKey) {
                    const canOther = (dir==='across') ? !!inp.dataset.dnext : !!inp.dataset.anext;
                    if (canOther) {
                        inp.dataset.dir = (dir==='across') ? 'down' : 'across';
                        nextKey = (inp.dataset.dir==='across') ? inp.dataset.anext : inp.dataset.dnext;
                    }
                }
            } else {
                nextKey = (dir==='across') ? inp.dataset.aprev : inp.dataset.dprev;
                if (!nextKey) {
                    const canOther = (dir==='across') ? !!inp.dataset.dprev : !!inp.dataset.aprev;
                    if (canOther) {
                        inp.dataset.dir = (dir==='across') ? 'down' : 'across';
                        nextKey = (inp.dataset.dir==='across') ? inp.dataset.aprev : inp.dataset.dprev;
                    }
                }
            }
            const nxt = byKey(nextKey); if (nxt){
                nxt.dataset.dir = inp.dataset.dir; nxt.focus(); nxt.select();
            }
        }

        document.querySelectorAll('.cell input').forEach(inp => {
            inp.addEventListener('focus', () => { ensureDir(inp); });
            inp.addEventListener('input', () => {
                const v = inp.value.trim().toUpperCase().slice(0,1);
                inp.value = v;
                if (v) move(inp, false);
            });
            inp.addEventListener('keydown', (e) => {
                if (e.key === 'Backspace' && inp.value === '') {
                    e.preventDefault(); move(inp, true);
                }
            });
        });
    })();
</script>
</body>
</html>
