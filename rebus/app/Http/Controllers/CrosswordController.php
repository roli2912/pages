<?php

namespace App\Http\Controllers;

use App\Models\CrosswordWinner;
use Illuminate\Http\Request;

class CrosswordController extends Controller
{
    private function words(): array
    {
        return [
            ['num'=>1,'answer'=>'FERRARI',         'row'=>1,'col'=>7,'dir'=>'across','clue'=>'Ce echipă reprezintă Lewis Hamilton?'],
            ['num'=>2,'answer'=>'OSCAR PIASTRI',   'row'=>2,'col'=>7,'dir'=>'across','clue'=>'Numele pilotului F1 cu cele mai multe puncte la debut, sezonul 2025'],
            ['num'=>3,'answer'=>'AUSTRALIA',       'row'=>3,'col'=>3,'dir'=>'across','clue'=>'Unde a avut loc prima cursă F1 a sezonului 2025?'],
            ['num'=>4,'answer'=>'MAX VERSTAPPEN',  'row'=>4,'col'=>7,'dir'=>'across','clue'=>'Cine a câștigat Marele Premiu la Imola (Emilia-Romagna) 16–18 mai 2025?'],
            ['num'=>5,'answer'=>'RED BULL',        'row'=>5,'col'=>2,'dir'=>'across','clue'=>'Christian Horner este șeful echipei…?'],
            ['num'=>6,'answer'=>'LEWIS HAMILTON',  'row'=>6,'col'=>7,'dir'=>'across','clue'=>'Pilot cu 7 titluri mondiale'],
            ['num'=>7,'answer'=>'ABU DHABI',       'row'=>7,'col'=>7,'dir'=>'across','clue'=>'Unde va avea loc ultima cursă F1 a sezonului acesta?'],
            ['num'=>8,'answer'=>'ANTENA1',         'row'=>8,'col'=>1,'dir'=>'across','clue'=>'Unde poți urmări show-uri fenomen?'],
        ];
    }

    public function show()
    {
        $words = $this->words();

        $minR = PHP_INT_MAX;
        $minC = PHP_INT_MAX;
        $maxR = 0;
        $maxC = 0;

        foreach ($words as $w) {
            $len  = mb_strlen($w['answer'], 'UTF-8');
            $endR = $w['row'] + ($w['dir'] === 'down'   ? $len-1 : 0);
            $endC = $w['col'] + ($w['dir'] === 'across' ? $len-1 : 0);
            $minR = min($minR, $w['row']);
            $minC = min($minC, $w['col']);
            $maxR = max($maxR, $endR);
            $maxC = max($maxC, $endC);
        }
        $rows = $maxR - $minR + 1;
        $cols = $maxC - $minC + 1;

        $grid = array_fill(0, $rows, array_fill(0, $cols, ['ch'=>null,'num'=>null]));

        foreach ($words as $w) {
            $r0 = $w['row'] - $minR;
            $c0 = $w['col'] - $minC;

            if (!$grid[$r0][$c0]['num']) $grid[$r0][$c0]['num'] = $w['num'];

            $chars = preg_split('//u', mb_strtoupper($w['answer'],'UTF-8'), -1, PREG_SPLIT_NO_EMPTY);
            foreach ($chars as $i => $ch) {
                $r = $r0 + ($w['dir'] === 'down'   ? $i : 0);
                $c = $c0 + ($w['dir'] === 'across' ? $i : 0);
                if ($ch === ' ') continue;
                if ($grid[$r][$c]['ch'] && $grid[$r][$c]['ch'] !== $ch) {
                    throw new \RuntimeException("Conflict at ($r,$c).");
                }
                $grid[$r][$c]['ch'] = $ch;
            }
        }

        $nav = [];
        $addChain = function(array $coords, string $dir) use (&$nav) {
            $n = count($coords);
            for ($i = 0; $i < $n; $i++) {
                [$r,$c] = $coords[$i];
                $key = "$r-$c";
                $nav[$key] = $nav[$key] ?? ['aNext'=>null,'aPrev'=>null,'dNext'=>null,'dPrev'=>null];
                if ($dir === 'across') {
                    if ($i > 0)   {
                        [$pr,$pc] = $coords[$i-1];
                        $nav[$key]['aPrev'] = "$pr-$pc";
                    }
                    if ($i < $n-1){
                        [$nr,$nc] = $coords[$i+1];
                        $nav[$key]['aNext'] = "$nr-$nc";
                    }
                } else {
                    if ($i > 0)   {
                        [$pr,$pc] = $coords[$i-1];
                        $nav[$key]['dPrev'] = "$pr-$pc";
                    }
                    if ($i < $n-1){
                        [$nr,$nc] = $coords[$i+1];
                        $nav[$key]['dNext'] = "$nr-$nc";
                    }
                }
            }
        };
        foreach ($words as $w) {
            $r0 = $w['row'] - $minR;
            $c0 = $w['col'] - $minC;
            $chars = preg_split('//u', mb_strtoupper($w['answer'],'UTF-8'), -1, PREG_SPLIT_NO_EMPTY);

            $coords = [];
            foreach ($chars as $i => $ch) {
                if ($ch === ' ') continue;
                $coords[] = [
                    $r0 + ($w['dir'] === 'down'   ? $i : 0),
                    $c0 + ($w['dir'] === 'across' ? $i : 0),
                ];
            }
            $addChain($coords, $w['dir']);
        }

        $values = (array) old('cell', []);

        return view('welcome', compact('rows','cols','grid','words','nav','values','minR','minC'));
    }

    public function submit(Request $request)
    {
        $data = $request->validate(
            [
                'first_name' => ['required','string','max:120'],
                'last_name'  => ['required','string','max:120'],
                'email'      => ['required','email','max:190'],
            ],
            [
                'first_name.required' => 'Acest câmp trebuie completat.',
                'last_name.required'  => 'Acest câmp trebuie completat.',
                'email.required'      => 'Acest câmp trebuie completat.',
                'email.email'         => 'Introduceți un email valid.',
            ]
        );

        $correct = [];
        foreach ($this->words() as $w) {
            $dir  = $w['dir'] === 'down' ? 'D' : 'A';
            $r0   = (int)$w['row'];
            $c0   = (int)$w['col'];
            $chars = preg_split('//u', mb_strtoupper($w['answer'],'UTF-8'), -1, PREG_SPLIT_NO_EMPTY);
            foreach ($chars as $i => $ch) {
                if ($ch === ' ') continue;
                $r = $r0 + ($dir==='D' ? $i : 0);
                $c = $c0 + ($dir==='A' ? $i : 0);
                $correct["$r,$c"] = $ch;
            }
        }

        $secretCol  = 7;
        $secretKeys = [];
        foreach ($correct as $k => $ch) {
            [$r,$c] = array_map('intval', explode(',', $k));
            if ($c === $secretCol) $secretKeys[$r] = $k;
        }
        ksort($secretKeys);
        $secretKeys = array_values($secretKeys);

        $posted = [];
        foreach ((array)$request->input('cell', []) as $k => $v) {
            $posted[$k] = mb_strtoupper((string)$v, 'UTF-8');
        }

        foreach ($secretKeys as $k) {
            $v = trim($posted[$k] ?? '');
            if ($v === '' || mb_strlen($v, 'UTF-8') !== 1 || !preg_match('/^[\p{L}\p{N}]$/u', $v)) {
                return redirect()->route('crossword.show')
                    ->withErrors(['grid' => 'Te rugăm să completezi toate căsuțele din rebus.'])
                    ->withInput();
            }
        }

        $target = 'FORMULA1';
        $have   = implode('', array_map(fn($k) => $posted[$k] ?? '', $secretKeys));
        $allCorrect = hash_equals($target, $have);

        if ($allCorrect) {
            CrosswordWinner::updateOrCreate(
                ['email' => $data['email']],
                ['name'  => trim($data['first_name'].' '.$data['last_name'])]
            );
        }

        return redirect()->route('crossword.show')
            ->with('success', 'Mulțumim pentru soluție!')
            ->withInput();
    }

    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $entries = CrosswordWinner::query()
            ->when($q, fn($sql) =>
            $sql->where(fn($w) =>
            $w->where('name','like',"%$q%")
                ->orWhere('email','like',"%$q%")
            )
            )
            ->orderByDesc('id')
            ->paginate(25)
            ->withQueryString();

        return view('entries', compact('entries','q'));
    }
}
