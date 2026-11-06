# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users

Software engineers, quantitative developers, systems engineers, and students preparing for competitive programming, low-latency technical interviews, and modern C++ mastery.

## Product Purpose

A high-performance algorithmic and systems engineering learning and judge platform. Users solve curated coding challenges across C++, Python, and JavaScript, test implementations against automated test suites, analyze time and memory complexity, and master systems programming through structured tracks.

## Positioning

Combines traditional LeetCode-style algorithmic problem solving with low-latency systems engineering drills (ring buffers, lock-free stacks, cache-aware kernels, move semantics, and C++20 concepts) backed by compiler sanitizer profiles and CLI integration.

## Operating Context

Desktop and tablet web browsers. Fixed application container with internal scroll, sidebar navigation, light and dark themes, online code submissions, and companion CLI workflow (`arena CLI`) for local development and test runs.

## Capabilities and Constraints

- 18 curated algorithmic and systems challenges categorized by difficulty (Easy, Medium, Hard).
- Multi-language starter code and testing (C++20, Python 3.12, JavaScript Node 22).
- Automatic judge simulation and submission persistence with runtime and memory recording.
- Daily featured challenge with streak tracking and midnight countdown.
- Learning tracks with interactive quizzes, syllabus tracking, and lesson progress.
- Passkey and two-factor authentication support.

## Brand Commitments

- Visual Identity: Warm cream (`#F7F5EC`) background in light mode, deep dark mode, Instrument Sans body typography, Redaction 50 headers, IBM Plex Mono code font, and emerald accents.
- Voice: Technical, restrained, concise, and focused on systems correctness and performance.

## Evidence on Hand

- Database-seeded problem set with comprehensive test specifications in `config/problems.php`.
- Complete learning tracks and syllabi in `config/learning.php`.
- Fully implemented Laravel REST API endpoints under `/api`.

## Product Principles

1. **Systems-First Rigor**: Emphasize memory boundaries, cache locality, and language semantics over generic tricks.
2. **Distraction-Free Focus**: High-contrast, scannable layouts with fixed container framing and smooth internal scrolling.
3. **Continuous Mastery**: Daily challenges, streak incentives, and structured syllabus progression keep engineers consistent.

## Accessibility & Inclusion

- WCAG AA contrast compliance across light and dark themes.
- Full keyboard navigation and visible focus rings.
- Semantic HTML headers, landmarks, and ARIA labels on all interactive controls.
