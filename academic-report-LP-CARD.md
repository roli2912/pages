# Academic Internship Report - LP-CARD Project

## 1. Project Description

### 1.1 Assignment Overview
The LP-CARD project is an interactive web-based memory card game integrated with a contest registration system, developed for a media/entertainment company (Antena 1). The application gamifies user engagement while collecting participant data for marketing campaigns related to a TV show called "Jocul Perechilor" (The Couples Game).

### 1.2 Business Context
The application serves as a landing page for a weekly contest where users:
- Play a memory matching game to find the correct couple from a TV show
- Answer a creative question about the show
- Register to win prizes (weekly: portable speaker, grand prize: Thailand travel voucher)

### 1.3 Personal Tasks and Goals

**Primary Responsibilities:**
1. Design and implement the complete database schema for participant tracking
2. Develop the game logic service using object-oriented principles
3. Create RESTful API endpoints for real-time game interactions
4. Implement comprehensive form validation with localized error messages
5. Build a responsive, visually appealing frontend interface
6. Ensure data integrity with proper constraints and indexing
7. Create an administrative view for participant management

**Learning Objectives:**
- Apply software design patterns in a real-world scenario
- Implement state management for interactive web applications
- Practice secure form handling and input validation
- Gain experience with the Laravel framework ecosystem

---

## 2. Applying Academic Knowledge and Skills

### 2.1 Object-Oriented Programming

#### Service Pattern Implementation
The core game logic is encapsulated in a dedicated `GameService` class (`app/Services/GameService.php`), demonstrating the Service Pattern:

```php
class GameService
{
    private $sessionKey = 'contest_game';

    public function initializeGame() { ... }
    public function getGameState() { ... }
    public function resetGame() { ... }
    public function flipCard($cardIndex) { ... }
    private function checkPair(&$gameState) { ... }
}
```

**Benefits achieved:**
- **Separation of Concerns:** Game logic isolated from HTTP handling
- **Testability:** Service can be unit tested independently
- **Reusability:** Logic can be reused across different controllers
- **Maintainability:** Changes to game rules don't affect controller code

#### MVC Architecture
The project strictly follows the Model-View-Controller pattern:
- **Model:** `Participant` class with business logic methods
- **View:** Blade templates for UI rendering
- **Controller:** `ContestController` handling HTTP requests

#### Encapsulation
Private methods hide implementation details:
- `checkPair()` - Internal pair validation logic
- `getWeeklyQuestion()` - Question rotation logic

### 2.2 Database Design

#### Schema Design
The `participants` table demonstrates normalized database design:

```php
Schema::create('participants', function (Blueprint $table) {
    $table->id();
    $table->string('first_name');
    $table->string('last_name');
    $table->string('email');
    $table->string('phone');
    $table->text('creative_answer');
    $table->boolean('newsletter_subscription')->default(false);
    $table->string('week_identifier');
    $table->timestamps();

    $table->unique(['email', 'week_identifier']);
    $table->index('week_identifier');
    $table->index('created_at');
});
```

**Design Decisions:**

1. **Composite Unique Constraint:** `unique(['email', 'week_identifier'])` allows the same user to participate in different weeks while preventing duplicate entries within a week.

2. **Strategic Indexing:**
   - `week_identifier` index for fast weekly participant filtering
   - `created_at` index for chronological sorting in admin view

3. **Week Identifier Format:** Using `date('Y-W')` (e.g., "2025-47") provides:
   - Year-aware identification
   - Easy querying by week
   - Natural ordering

### 2.3 Web Development Concepts

#### RESTful API Design
Routes follow REST principles for game interactions:

| Method | URI | Action | Purpose |
|--------|-----|--------|---------|
| GET | / | index | Display game |
| POST | /flip-card | flipCard | Update game state |
| POST | /reset | resetGame | Reset game |
| POST | /register | register | Submit entry |

#### Session Management
Game state is maintained using Laravel's session system:

```php
public function getGameState()
{
    $state = session($this->sessionKey);
    if (!$state) {
        return $this->initializeGame();
    }
    return $state;
}
```

This approach is appropriate because:
- Games are short-lived (single session)
- No need for database persistence
- Fast read/write operations
- Automatic cleanup on session expiry

#### AJAX Communication
The frontend uses asynchronous requests for smooth gameplay:

```javascript
const response = await fetch('{{ route("flip-card") }}', {
    method: 'POST',
    headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
    },
    body: JSON.stringify({ card_index: cardIndex })
});
```

### 2.4 Algorithms and Data Structures

#### State Machine Pattern
The game uses a finite state machine to manage transitions:

**States:**
- `waiting` - Ready for first card flip
- `first_card` - One card flipped, waiting for second
- `second_card` - Two cards flipped, evaluating
- `won` - Correct pair found
- `blocked` - Incorrect pair locked
- `no_match` - Cards don't match

**State Transitions:**
```
waiting -> first_card (flip first card)
first_card -> second_card (flip second card)
second_card -> won | blocked | no_match (evaluate pair)
won -> END (show form)
blocked | no_match -> waiting (continue game)
```

#### Card Shuffling Algorithm
Cards are randomized using PHP's Fisher-Yates shuffle:

```php
$cardTypes = [1, 1, 2, 2, 3, 3, 4, 5, 6];
shuffle($cardTypes);
```

**Card Type Distribution:**
- Type 1: Correct pair (2 cards)
- Types 2, 3: Incorrect pairs (2 cards each)
- Types 4, 5, 6: Single cards (decoys)

#### Array Operations
Extensive use of PHP array functions for state management:
- `array_merge()` - Combining matched/blocked arrays
- `array_diff()` - Removing unmatched cards
- `in_array()` - Checking card states

### 2.5 Software Engineering Practices

#### Dependency Injection
The controller receives the service through constructor injection:

```php
class ContestController extends Controller
{
    protected $gameService;

    public function __construct(GameService $gameService)
    {
        $this->gameService = $gameService;
    }
}
```

**Benefits:**
- Loose coupling between controller and service
- Easy to mock for testing
- Follows SOLID principles (Dependency Inversion)

#### Comprehensive Validation
Form validation with custom Romanian messages:

```php
$validated = $request->validate([
    'first_name' => 'required|string|max:255',
    'phone' => 'required|string|min:10|max:15|regex:/^[0-9]+$/',
    'creative_answer' => 'required|string|min:10',
    'terms_accepted' => 'required|accepted'
], [
    'phone.regex' => 'Telefonul trebuie să conțină doar cifre.',
    'creative_answer.min' => 'Răspunsul trebuie să aibă cel puțin 10 caractere.',
]);
```

#### Error Handling and Logging
Comprehensive exception handling with logging:

```php
try {
    // Registration logic
} catch (ValidationException $e) {
    session()->flash('show_form', true);
    return back()->withErrors($e->errors())->withInput();
} catch (\Exception $e) {
    \Log::error('Registration error', [
        'message' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
    return back()->with('error', 'A apărut o eroare: ' . $e->getMessage());
}
```

---

## 3. Method and Results

### 3.1 Development Methodology

**Approach:** Iterative development following Laravel conventions and best practices.

#### Phase 1: Planning and Design (Week 1)
- Analyzed business requirements and game mechanics
- Designed database schema with constraints
- Planned API endpoints and state machine
- Created wireframes for UI layout

#### Phase 2: Backend Development (Weeks 2-3)
- Set up Laravel project with required dependencies
- Created database migrations
- Implemented Eloquent models with business logic
- Developed GameService with state management
- Built controller actions with validation
- Added logging for debugging

#### Phase 3: Frontend Development (Week 4)
- Designed glassmorphism UI with CSS animations
- Implemented responsive 3x3 card grid
- Connected views to controllers using Blade
- Added AJAX calls for card flipping
- Implemented visual feedback (success/error messages)
- Created card flip animations with CSS transforms

#### Phase 4: Testing and Refinement (Week 5)
- Manual testing of all game flows
- Edge case handling (rapid clicks, duplicate registrations)
- Performance optimization (database indexes)
- Cross-browser testing
- Mobile responsiveness adjustments

### 3.2 Technical Implementation Details

#### Game State Structure
```php
$gameState = [
    'cards' => [1, 1, 2, 2, 3, 3, 4, 5, 6], // Shuffled
    'flipped' => [],           // Currently visible
    'blocked' => [],           // Locked incorrect pairs
    'matched' => [],           // Correct pair found
    'current_flipped' => [],   // Current attempt
    'game_won' => false,
    'message' => '',
    'message_type' => '',      // 'success' | 'error'
    'correct_pair' => 1,       // Winning type
    'incorrect_pairs' => [2, 3],
    'can_flip' => true,
    'step' => 'waiting'        // State machine state
];
```

#### Frontend Architecture
The JavaScript `ContestGame` class manages client-side state:

```javascript
class ContestGame {
    constructor() {
        this.cards = document.querySelectorAll('.card');
        this.canFlip = true;
        this.flippedCards = [];
        this.init();
    }

    async handleCardClick(e) { ... }
    updateGameState(gameState) { ... }
    processResult(gameState) { ... }
    showMessage(text, type) { ... }
    async resetGame() { ... }
}
```

#### CSS Animations
Card flip effect using 3D transforms:

```css
.card {
    transform-style: preserve-3d;
    transition: transform 0.6s cubic-bezier(0.4, 0.0, 0.2, 1);
}

.card.flipped {
    transform: rotateY(180deg);
}

.card-face {
    backface-visibility: hidden;
}

.card-front {
    transform: rotateY(180deg);
}
```

### 3.3 Results and Metrics

#### Code Statistics
- **Total PHP Lines:** ~450 lines
- **JavaScript Lines:** ~150 lines
- **CSS Lines:** ~700 lines
- **Controllers:** 2 (ContestController, ParticipantsViewController)
- **Models:** 1 (Participant)
- **Services:** 1 (GameService)
- **Views:** 4 (index, result, participants-view, app layout)
- **Routes:** 6 endpoints
- **Validation Rules:** 7 fields with 11 total rules

#### Database Design
- **Tables:** 1 (participants)
- **Columns:** 8
- **Indexes:** 3 (primary key, composite unique, 2 single-column)

#### Features Delivered
1. Interactive 3x3 memory card game
2. Real-time game state synchronization
3. Weekly question rotation system
4. Comprehensive form validation
5. Duplicate registration prevention
6. Newsletter subscription option
7. Administrative participant view with statistics
8. Responsive design for mobile/desktop
9. Animated UI with glassmorphism effects

---

## 4. Technical Deliverables (Appendix)

### A1. Complete Database Schema

```sql
CREATE TABLE `participants` (
    `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `first_name` VARCHAR(255) NOT NULL,
    `last_name` VARCHAR(255) NOT NULL,
    `email` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(255) NOT NULL,
    `creative_answer` TEXT NOT NULL,
    `newsletter_subscription` TINYINT(1) NOT NULL DEFAULT 0,
    `week_identifier` VARCHAR(255) NOT NULL,
    `created_at` TIMESTAMP NULL,
    `updated_at` TIMESTAMP NULL,
    UNIQUE KEY `participants_email_week_identifier_unique` (`email`, `week_identifier`),
    KEY `participants_week_identifier_index` (`week_identifier`),
    KEY `participants_created_at_index` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### A2. Class Diagram

```
┌─────────────────────────┐
│   ContestController     │
├─────────────────────────┤
│ - gameService: GameService
├─────────────────────────┤
│ + __construct(GameService)
│ + index(): View
│ + flipCard(Request): JSON
│ + resetGame(): JSON
│ + register(Request): Redirect
│ - getWeeklyQuestion(): string
└─────────────────────────┘
            │
            │ uses
            ▼
┌─────────────────────────┐
│      GameService        │
├─────────────────────────┤
│ - sessionKey: string    │
├─────────────────────────┤
│ + initializeGame(): array
│ + getGameState(): array
│ + resetGame(): array
│ + flipCard(int): array
│ + continueGame(): array
│ + getCardImages(): array
│ - checkPair(&array): void
└─────────────────────────┘

┌─────────────────────────┐
│      Participant        │
├─────────────────────────┤
│ + first_name: string    │
│ + last_name: string     │
│ + email: string         │
│ + phone: string         │
│ + creative_answer: text │
│ + newsletter_subscription: bool
│ + week_identifier: string
├─────────────────────────┤
│ + getCurrentWeekIdentifier(): string
│ + canRegister(email): bool
└─────────────────────────┘

┌─────────────────────────┐
│ ParticipantsViewController
├─────────────────────────┤
│ + index(): View         │
└─────────────────────────┘
```

### A3. API Documentation

#### GET /
**Description:** Display the game interface
**Response:** HTML view with game board

#### POST /flip-card
**Description:** Flip a card and update game state
**Request Body:**
```json
{
    "card_index": 0-8
}
```
**Response:**
```json
{
    "success": true,
    "gameState": {
        "cards": [1, 2, 3, ...],
        "flipped": [0, 5],
        "blocked": [],
        "matched": [],
        "game_won": false,
        "message": "",
        "step": "first_card",
        "can_flip": true
    }
}
```

#### POST /reset
**Description:** Reset the game to initial state
**Response:**
```json
{
    "success": true,
    "gameState": { ... }
}
```

#### POST /register
**Description:** Submit contest registration
**Request Body:** Form data with participant fields
**Response:** Redirect with success/error message

### A4. Validation Rules Documentation

| Field | Rules | Custom Message |
|-------|-------|----------------|
| first_name | required, string, max:255 | Numele este obligatoriu |
| last_name | required, string, max:255 | Prenumele este obligatoriu |
| email | required, email, max:255 | Email-ul trebuie să fie valid |
| phone | required, min:10, max:15, regex:/^[0-9]+$/ | Telefonul trebuie să conțină doar cifre |
| creative_answer | required, string, min:10 | Răspunsul trebuie să aibă cel puțin 10 caractere |
| newsletter_subscription | nullable, boolean | - |
| terms_accepted | required, accepted | Trebuie să accepți regulamentul concursului |

### A5. State Machine Diagram

```
                    ┌─────────┐
                    │  START  │
                    └────┬────┘
                         │
                         ▼
                  ┌─────────────┐
          ┌──────│   waiting   │◄────────────┐
          │      └──────┬──────┘             │
          │             │ flip card          │
          │             ▼                    │
          │      ┌─────────────┐             │
          │      │ first_card  │             │
          │      └──────┬──────┘             │
          │             │ flip card          │
          │             ▼                    │
          │      ┌─────────────┐             │
          │      │ second_card │             │
          │      └──────┬──────┘             │
          │             │ evaluate           │
          │     ┌───────┼───────┐            │
          │     ▼       ▼       ▼            │
    ┌─────────┐ ┌─────────┐ ┌──────────┐     │
    │   won   │ │ blocked │ │ no_match │     │
    └────┬────┘ └────┬────┘ └────┬─────┘     │
         │           │           │           │
         │           └───────────┴───────────┘
         ▼
   ┌───────────┐
   │ Show Form │
   └───────────┘
```

---

## 5. Critical Thinking

### 5.1 Technical Challenges and Solutions

#### Challenge 1: Race Conditions in Card Flipping
**Problem:** Users could rapidly click multiple cards, causing inconsistent game state.

**Solution:** Implemented a multi-layered protection system:
1. `can_flip` server-side flag controls when flipping is allowed
2. `current_flipped` array limits to exactly 2 cards per attempt
3. Client-side `this.flippedCards` array prevents multiple sends
4. CSS `pointer-events: none` on disabled cards

**Code Implementation:**
```php
if (!$gameState['can_flip'] ||
    in_array($cardIndex, $gameState['flipped']) ||
    in_array($cardIndex, $gameState['blocked']) ||
    in_array($cardIndex, $gameState['matched'])) {
    return $gameState;
}

if (count($gameState['current_flipped']) >= 2) {
    return $gameState;
}
```

#### Challenge 2: Weekly Registration Limits
**Problem:** Preventing duplicate entries while allowing repeat participation in different weeks.

**Solution:** Combined database constraints with application-level checks:

1. **Database Level:** Composite unique constraint
```php
$table->unique(['email', 'week_identifier']);
```

2. **Application Level:** Pre-check before insert
```php
public static function canRegister($email)
{
    return !self::where('email', $email)
        ->where('week_identifier', self::getCurrentWeekIdentifier())
        ->exists();
}
```

3. **User Feedback:** Clear Romanian error message
```php
return back()->withErrors(['email' => 'Acest email s-a înscris deja în această săptămână!']);
```

#### Challenge 3: Form State Persistence
**Problem:** Users losing form data after validation errors.

**Solution:** Multiple mechanisms for state preservation:
1. `withInput()` preserves form values
2. `session()->flash('show_form', true)` keeps form visible
3. Blade directive checks: `{{ old('field_name') }}`

### 5.2 Design Decisions and Rationale

#### Why Session-Based Game State?
**Decision:** Store game state in PHP sessions rather than database.

**Rationale:**
- Games are ephemeral (single session duration)
- Fast read/write without database overhead
- No need for cross-device continuity
- Automatic cleanup on session expiration
- Simpler implementation

**Trade-off:** Cannot track game analytics or abandoned games.

#### Why Service Pattern?
**Decision:** Extract game logic into dedicated GameService class.

**Rationale:**
- Single Responsibility: Controller handles HTTP, service handles logic
- Testability: Can unit test game logic without HTTP context
- Reusability: Same service could power API or different frontend
- Maintainability: Game rule changes isolated from routing

#### Why Glassmorphism UI?
**Decision:** Modern frosted glass aesthetic with animations.

**Rationale:**
- Visually engaging for entertainment context
- Aligns with current design trends
- Creates depth without heavy graphics
- Works well across devices

### 5.3 Areas for Improvement

#### 1. Automated Testing
**Current State:** No automated tests
**Improvement:** Add PHPUnit tests for GameService:
```php
public function test_correct_pair_wins_game()
{
    $service = new GameService();
    // Setup game with known card positions
    // Assert game_won becomes true on correct match
}
```

#### 2. Security Enhancements
- **Rate Limiting:** Prevent registration spam
```php
Route::post('/register', ...)->middleware('throttle:5,1');
```
- **CAPTCHA:** Bot prevention on registration
- **Email Verification:** Reduce fake entries

#### 3. Performance Optimizations
- **Question Caching:** Cache weekly questions
```php
return Cache::remember('weekly_question', 3600, function () {
    return $this->calculateWeeklyQuestion();
});
```
- **Redis Sessions:** For high-traffic scenarios

#### 4. Analytics and Insights
- Track game completion rates
- Average attempts to win
- Popular wrong choices
- Time to completion

#### 5. Accessibility
- ARIA labels for screen readers
- Keyboard navigation for cards
- High contrast mode option
- Focus indicators

#### 6. Code Quality
- Extract validation to Form Request class
- Add TypeScript for frontend type safety
- Implement comprehensive logging strategy

### 5.4 Lessons Learned

1. **State Management Complexity:** Even simple games require careful state machine design
2. **User Experience Priority:** Animation timing crucial for intuitive gameplay
3. **Defensive Programming:** Never trust client-side validation alone
4. **Localization Matters:** Romanian error messages significantly improve UX
5. **Database Design First:** Well-designed schema prevents many application-level problems

---

## 6. Conclusion

### 6.1 Summary of Achievements

The LP-CARD project successfully delivered a complete, production-ready interactive web application that demonstrates proficiency in:

- **Full-stack Laravel Development:** Backend services, Eloquent ORM, Blade templating
- **Database Design:** Normalized schema with strategic indexing and constraints
- **State Management:** Complex game state with session persistence
- **Frontend Development:** Responsive UI with CSS animations and AJAX integration
- **Security:** CSRF protection, input validation, SQL injection prevention
- **User Experience:** Intuitive gameplay, clear feedback, localized messages

### 6.2 Technical Competencies Demonstrated

| Area | Technologies/Concepts |
|------|----------------------|
| Backend | PHP 8.x, Laravel 11, Service Pattern |
| Database | MySQL, Eloquent ORM, Migrations |
| Frontend | HTML5, CSS3, JavaScript ES6+, Blade |
| Patterns | MVC, State Machine, Dependency Injection |
| Security | CSRF, Input Validation, SQL Injection Prevention |

### 6.3 Business Value Delivered

The application successfully meets its marketing objectives:
- **User Engagement:** Gamification increases time-on-site and interaction
- **Lead Generation:** Collects quality leads with creative answers for segmentation
- **Newsletter Growth:** Opt-in subscription builds marketing database
- **Brand Association:** TV show tie-in strengthens viewer connection
- **Repeat Visits:** Weekly contests encourage return traffic

### 6.4 Learning Outcomes

1. **Practical Pattern Application:** Implemented Service and State Machine patterns in real-world context
2. **Laravel Ecosystem Mastery:** Deep understanding of framework conventions and best practices
3. **Security Awareness:** Hands-on experience with common web vulnerabilities and mitigations
4. **Iterative Development:** Practiced building features incrementally with continuous refinement
5. **User-Centric Design:** Learned importance of feedback, error handling, and localization

The LP-CARD project represents a successful application of computer science fundamentals to create a commercially viable, maintainable web application that balances technical excellence with business requirements.
