# Academic Report - Project Sections

## Project Description

### Assignment Overview
During this internship, I developed two interactive web applications for a media/entertainment company: **LP-CARD** and **Rebus**. Both projects are Laravel-based landing pages designed to engage users through gamification while collecting participant data for marketing campaigns.

### LP-CARD Project
**Purpose:** A memory card matching game integrated with a contest registration system.

**Core Functionality:**
- Interactive card-flipping memory game with 9 cards (3x3 grid)
- Users must find the correct pair among multiple card types
- Game state management using PHP sessions
- Contest registration form with validation
- Weekly participation limits (users can only register once per week)
- Participant data collection (name, email, phone, creative answers)

### Rebus Project
**Purpose:** An F1-themed crossword puzzle application for user engagement.

**Core Functionality:**
- Dynamic crossword grid generation from word definitions
- 8 clues related to Formula 1 (2025 season)
- Secret word extraction from a specific column ("FORMULA1")
- Winner registration and tracking system
- Administrative view for managing entries

### Personal Tasks and Goals
1. Design and implement the database schema for participant tracking
2. Develop game logic services using object-oriented principles
3. Create RESTful API endpoints for game interactions
4. Implement form validation with custom error messages (Romanian)
5. Build responsive frontend interfaces
6. Ensure data integrity with unique constraints and indexes

---

## Applying Academic Knowledge and Skills

### Object-Oriented Programming
- **Service Pattern:** Implemented `GameService` class to encapsulate game logic, separating concerns from controllers
- **MVC Architecture:** Proper separation between Models (Participant, CrosswordWinner), Views (Blade templates), and Controllers
- **Encapsulation:** Private methods like `checkPair()` and `getWeeklyQuestion()` hide implementation details

### Database Design
- **Normalization:** Designed participants table with appropriate field types and constraints
- **Indexing:** Applied indexes on frequently queried columns (`week_identifier`, `created_at`)
- **Composite Unique Constraint:** `unique(['email', 'week_identifier'])` prevents duplicate registrations per week

### Web Development
- **HTTP Methods:** Proper use of GET/POST for different actions (REST principles)
- **Session Management:** Stateful game tracking using Laravel's session system
- **Form Validation:** Server-side validation with regex patterns and custom messages
- **Security:** Input sanitization, CSRF protection (Laravel built-in)

### Algorithms and Data Structures
- **Grid Generation Algorithm:** Crossword grid built dynamically from word definitions with conflict detection
- **State Machine Pattern:** Game states (waiting, first_card, second_card, won, blocked) manage transitions
- **Array Manipulation:** Card shuffling, pair matching, coordinate calculations

### Software Engineering Practices
- **Dependency Injection:** `GameService` injected into `ContestController` constructor
- **Single Responsibility:** Each controller method handles one specific action
- **Error Handling:** Try-catch blocks with logging for debugging

---

## Method and Results

### Development Methodology
**Approach:** Iterative development following Laravel conventions

**Phase 1 - Planning**
- Analyzed requirements for game mechanics
- Designed database schema
- Planned API endpoints and routes

**Phase 2 - Backend Development**
- Created migrations for database tables
- Implemented Eloquent models with business logic
- Developed service classes for game mechanics
- Built controller actions with validation

**Phase 3 - Frontend Integration**
- Connected views to controllers using Blade templating
- Implemented AJAX calls for card flipping
- Added user feedback (success/error messages)

**Phase 4 - Testing and Refinement**
- Manual testing of game flows
- Edge case handling (duplicate registrations, invalid inputs)
- Performance optimization (database indexes)

### Technical Results

#### LP-CARD
- **Routes:** 6 endpoints (index, flip-card, reset, register, clear, view-participants)
- **Database:** Participants table with 8 columns, 3 indexes
- **Game Logic:** 169 lines of service code managing card states
- **Validation:** 7 form fields with 11 validation rules

#### Rebus
- **Crossword:** 8 words, dynamically generated grid
- **Navigation System:** Cell-to-cell navigation for keyboard input
- **Answer Validation:** Hash comparison for security (`hash_equals`)
- **Admin Panel:** Paginated winner list with search functionality

### Key Metrics
- Clean code structure following PSR-12 standards
- Comprehensive error handling with user-friendly Romanian messages
- Scalable architecture allowing easy addition of new questions/puzzles

---

## Technical Deliverables (Appendix)

### A1. Database Schema - LP-CARD Participants
```sql
CREATE TABLE participants (
    id BIGINT PRIMARY KEY AUTO_INCREMENT,
    first_name VARCHAR(255),
    last_name VARCHAR(255),
    email VARCHAR(255),
    phone VARCHAR(255),
    creative_answer TEXT,
    newsletter_subscription BOOLEAN DEFAULT FALSE,
    week_identifier VARCHAR(255),
    created_at TIMESTAMP,
    updated_at TIMESTAMP,
    UNIQUE KEY (email, week_identifier),
    INDEX (week_identifier),
    INDEX (created_at)
);
```

### A2. Class Diagram - LP-CARD

```
+------------------+       +------------------+       +------------------+
| ContestController|------>|   GameService    |       |   Participant    |
+------------------+       +------------------+       +------------------+
| -gameService     |       | -sessionKey      |       | +first_name      |
+------------------+       +------------------+       | +last_name       |
| +index()         |       | +initializeGame()|       | +email           |
| +flipCard()      |       | +getGameState()  |       | +phone           |
| +resetGame()     |       | +resetGame()     |       | +creative_answer |
| +register()      |       | +flipCard()      |       | +newsletter_sub  |
| -getWeeklyQ()    |       | -checkPair()     |       | +week_identifier |
+------------------+       | +getCardImages() |       +------------------+
                           +------------------+       | +getCurrentWeek()|
                                                      | +canRegister()   |
                                                      +------------------+
```

### A3. Route Documentation

**LP-CARD Routes:**
| Method | URI | Action | Name |
|--------|-----|--------|------|
| GET | / | ContestController@index | index |
| POST | /flip-card | ContestController@flipCard | flip-card |
| POST | /reset | ContestController@resetGame | reset |
| POST | /register | ContestController@register | register |

**Rebus Routes:**
| Method | URI | Action | Name |
|--------|-----|--------|------|
| GET | / | CrosswordController@show | crossword.show |
| POST | /submit | CrosswordController@submit | crossword.submit |
| GET | /entries | CrosswordController@index | entries |

### A4. Game State Structure - LP-CARD
```php
$gameState = [
    'cards' => [1, 1, 2, 2, 3, 3, 4, 5, 6], // Shuffled card types
    'flipped' => [],      // Currently visible cards
    'blocked' => [],      // Incorrect pairs (locked)
    'matched' => [],      // Correct pairs found
    'current_flipped' => [], // Cards in current attempt
    'game_won' => false,
    'message' => '',
    'message_type' => '', // 'success' or 'error'
    'correct_pair' => 1,  // Winning card type
    'incorrect_pairs' => [2, 3],
    'can_flip' => true,
    'step' => 'waiting'   // State machine state
];
```

### A5. Crossword Algorithm - Key Code Snippet
```php
// Grid generation with conflict detection
foreach ($words as $w) {
    $chars = preg_split('//u', mb_strtoupper($w['answer'],'UTF-8'), -1, PREG_SPLIT_NO_EMPTY);
    foreach ($chars as $i => $ch) {
        $r = $r0 + ($w['dir'] === 'down' ? $i : 0);
        $c = $c0 + ($w['dir'] === 'across' ? $i : 0);
        if ($grid[$r][$c]['ch'] && $grid[$r][$c]['ch'] !== $ch) {
            throw new \RuntimeException("Conflict at ($r,$c).");
        }
        $grid[$r][$c]['ch'] = $ch;
    }
}
```

### A6. Validation Rules - Registration Form
```php
$validated = $request->validate([
    'first_name' => 'required|string|max:255',
    'last_name' => 'required|string|max:255',
    'email' => 'required|email|max:255',
    'phone' => 'required|string|min:10|max:15|regex:/^[0-9]+$/',
    'creative_answer' => 'required|string|min:10',
    'newsletter_subscription' => 'nullable|boolean',
    'terms_accepted' => 'required|accepted'
]);
```

---

## Critical Thinking

### Challenges and Solutions

**Challenge 1: Race Conditions in Game State**
- *Problem:* Multiple rapid clicks could corrupt game state
- *Solution:* Implemented `can_flip` flag and state machine to control flow

**Challenge 2: Weekly Registration Limits**
- *Problem:* Preventing users from registering multiple times per week
- *Solution:* Composite unique constraint on email + week_identifier, with application-level check via `canRegister()` method

**Challenge 3: Crossword Navigation**
- *Problem:* Users needed smooth keyboard navigation across cells
- *Solution:* Built navigation map with previous/next cell references for both directions

### Areas for Improvement

1. **Testing:** Both projects lack automated tests. Adding PHPUnit/Pest tests would improve reliability and enable CI/CD pipelines.

2. **Security Enhancements:**
   - Rate limiting on registration endpoints to prevent abuse
   - Email verification to reduce fake entries
   - CAPTCHA integration for bot prevention

3. **Performance:**
   - Caching weekly questions to reduce computation
   - Consider Redis for session storage in high-traffic scenarios

4. **Code Quality:**
   - Extract validation rules to Form Request classes
   - Add TypeScript types for frontend JavaScript
   - Implement logging for game analytics

5. **User Experience:**
   - Add game statistics (attempts, completion time)
   - Implement mobile-responsive design improvements
   - Add accessibility features (ARIA labels, keyboard focus indicators)

### Reflection on Design Decisions

The choice to use session-based game state was appropriate for this use case, as games are short-lived and don't need persistence. However, for analytics purposes, storing game attempts in the database could provide valuable insights.

The service pattern (`GameService`) proved valuable for isolating game logic, making the code easier to test and modify. This separation would allow swapping game types without changing controllers.

---

## Conclusion

### Summary of Achievements
During this internship, I successfully developed two interactive web applications that demonstrate proficiency in:
- Full-stack Laravel development
- Database design and optimization
- RESTful API design
- State management patterns
- Form validation and user input handling

### Technical Competencies Demonstrated
- **Backend:** PHP 8.x, Laravel 11, Eloquent ORM, Service Pattern
- **Database:** MySQL, migrations, indexes, constraints
- **Frontend:** Blade templating, JavaScript/AJAX, CSS
- **Software Engineering:** MVC architecture, dependency injection, error handling

### Learning Outcomes
1. Practical application of design patterns in real-world projects
2. Understanding of web security best practices
3. Experience with iterative development and client requirements
4. Skills in writing maintainable, documented code

### Value Delivered
Both applications are production-ready and serve their intended marketing purposes:
- **LP-CARD:** Engages users with gamification, collects quality leads with creative answers
- **Rebus:** Provides educational content (F1 knowledge) while building user database

These projects demonstrate the intersection of technical skills and business value, creating engaging user experiences that support marketing objectives while maintaining code quality and scalability.
