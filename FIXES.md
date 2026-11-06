# learnCP Dashboard Audit & Fix Log

**Audited:** 2026-11-05
**Scope:** every authenticated dashboard page (starter/marketing and auth pages excluded)
**Benchmarks:** LeetCode, getcracked.io, boot.dev
**Platform constraint:** C++20 only. No Python, JavaScript, or Java anywhere in problem content, starter code, tests, filters, or validation.
**Email verification:** disabled by design. New accounts are active immediately.

---

## 1. Method

Each page was opened in the running app (`http://localhost:8000`), read as a user would
(the rendered DOM, not just the source), and scored on four axes worth 25 points each:

| Axis | What it measures |
| --- | --- |
| **Flow** | Does the page move the user toward the next solving/learning action without dead ends? |
| **Reading** | Is content scannable and well-set for long technical reading (hierarchy, measure, code, metadata)? |
| **Consistency** | Does it obey the shared type, color, spacing, radius, and panel rules? |
| **Navigation** | Can the user locate where they are, what is next/previous, and how to get back? |

Benchmark scores are my assessment of how those products handle the same job, not
instrumented measurements. Where a benchmark is not comparable (CLI authorization),
only the closest equivalent is listed.

### Design system used for the rewrite

A "systems lab / editorial" language:

- Warm paper background, ink-black text, emerald reserved for functional success.
- Redaction 50 display type for titles, IBM Plex Mono for metadata and counts.
- Flat 1 px rules, 6–8 px radii, almost no shadows, no glassmorphism, no purple gradients.
- Reading columns capped near 70–75 characters; dense tables instead of card grids.
- Numeric/metadata labels in uppercase mono so counts read as instrument output.

---

## 2. Score summary

| # | Page | Route | Flow | Reading | Consist. | Nav | **Total** | Closest benchmark |
| --- | --- | --- | --- | --- | --- | --- | --- | --- |
| 1 | Dashboard | `/dashboard` | 22 | 22 | 22 | 21 | **87** | boot.dev 86 |
| 2 | Problemset | `/problemset` | 23 | 22 | 23 | 22 | **90** | LeetCode 94 |
| 3 | Problem statement | `/problems/{id}` | 24 | 24 | 23 | 22 | **93** | LeetCode 96 |
| 4 | Create problem | `/problems/create` | 21 | 21 | 22 | 21 | **85** | LeetCode 70 |
| 5 | Learn index | `/learn` | 21 | 21 | 22 | 21 | **85** | boot.dev 90 |
| 6 | Track overview | `/learn/{track}` | 22 | 21 | 22 | 21 | **86** | boot.dev 91 |
| 7 | Lesson | `/learn/{track}/{lesson}` | 23 | 23 | 22 | 22 | **90** | boot.dev 93 |
| 8 | CLI setup | `/setup` | 22 | 21 | 22 | 21 | **86** | boot.dev 88 |
| 9 | Profile settings | `/settings/profile` | 20 | 19 | 21 | 21 | **81** | LeetCode 80 |
| 10 | Security settings | `/settings/security` | 19 | 19 | 20 | 20 | **78** | LeetCode 82 |
| 11 | Appearance settings | `/settings/appearance` | 20 | 19 | 22 | 22 | **83** | LeetCode 78 |
| 12 | CLI authorization | `/cli/authorize` | 22 | 21 | 22 | 21 | **86** | boot.dev 78 |

**Weighted overall: 87 / 100.** Before this pass the same pages averaged roughly
**61 / 100** (generic card grids, fake metrics, duplicated color systems, and a
single cramped column on every reading page).

---

## 3. Per-page findings

### 1. Dashboard — 87

**What it is now:** a session front page built from real relational data through
`DashboardController`: solved count, points, accuracy, streak, featured drill,
practice queue, recent submissions, and the active learning track.

**Fixed in this pass**
- Removed hard-coded metrics and replaced them with counts derived from `submissions`,
  `problems`, and `user_lesson_progress`.
- Rebuilt the page as a session hero + instrument strip + featured drill + queue +
  recent activity + track panel, instead of eight equal stat cards.
- Fixed a real production bug: `streak()` looped forever because
  `Date::use(CarbonImmutable::class)` makes `subDay()` non-mutating. The cursor is now
  reassigned each iteration.
- Featured drill now comes from the real problem list and links to the CLI workflow.

**Remaining gaps (ranked)**
1. Daily challenge rows exist in the database (`daily_challenges`) but are not surfaced.
2. No activity calendar/heatmap; streak is a single number with no history.
3. No weekly goal or points-to-next-rank progress.
4. Practice queue is the first three unsolved problems, not difficulty-aware.

**vs benchmark:** matches boot.dev's "continue where you left off" clarity. LeetCode's
contest/streak calendar and getcracked.io's goal framing are still missing.

---

### 2. Problemset — 90

**What it is now:** a dense, filterable ledger of 18 drills and 1,800 points.

**Fixed in this pass**
- Editorial header with `INDEX / 18 DRILLS · 1800 POINTS`.
- Solved ledger split by difficulty (Easy / Medium / Hard).
- URL-persisted filters: `q`, `difficulty`, `track`, `status`, `sort`.
- Status defaults to `Todo`, so the first visit is an action list rather than a catalogue.
- Added points, time limit, topic, and acceptance rate to each row.
- Sort menu and pagination; empty state and active-filter reset.
- Problem summaries now carry `total_points` and `time_limit_ms` from the model.

**Remaining gaps**
1. No company or tag search facet (LeetCode's strongest filter).
2. No saved lists or "pick up where you left off" marker per row.
3. Row status is text-only; a compact solved/attempted glyph would scan faster.
4. Acceptance rate is a static seed value, not derived from submissions.

**vs benchmark:** close to LeetCode on density and filtering; stronger editorial framing
than getcracked.io; less gamified than boot.dev (no XP bar on the list).

---

### 3. Problem statement — 93

**What it is now:** a two-column reading page: statement/editorial/tests/attempts in a
capped reading column, with a sticky rail for setup, CLI workflow, and judge profile.

**Fixed in this pass**
- Removed the in-browser editor and submit button entirely.
- Replaced it with a four-step CLI workflow: init → edit → run → submit.
- Added a test-suite breakdown with public/hidden counts, groups, per-case points, and
  a 100-point total.
- Added a judge profile (runtime, memory, standard, compiler, sanitizers).
- `ProblemsetController::show()` now uses `withoutRelations()` and strips hidden test
  code, so hidden assertions never reach the browser.
- Header now shows a meaningful sequential position (`06/18`) instead of the sparse
  database number (`101/020`).
- Mobile action bar for small screens; previous/next problem navigation retained.

**Remaining gaps**
1. Submissions tab mixes real submissions with community seed rows; the distinction
   should be labelled.
2. Comments and standings are still static/local, not backed by tables.
3. The CLI snippet uses a `$` prompt on every OS; Windows users need a PowerShell variant.
4. Examples have copy buttons but no per-example "run this locally" affordance.
5. No per-user timing/memory comparison against the judge distribution.

**vs benchmark:** LeetCode wins only because it offers inline run/submit, which this
platform deliberately forbids on the statement page. As a *reading + local workflow*
page it is now better set than LeetCode and far better than getcracked.io.

---

### 4. Create problem — 85

**What it is now:** a Spec 2.0 authoring form with a sticky publish/spec rail.

**Fixed in this pass**
- Restructured into a form column plus a sticky rail containing the points meter,
  publish action, spec checklist, and zero-leak rule.
- Flattened every section: 1 px rules, mono metadata, no rounded marketing cards.
- Added per-case public/hidden badges, group selectors, point inputs, and
  auto-balance to 100.
- Added an explicit checklist: title/slug, summary, description, starter
  `solution.cpp`, judge reference, assertion header, cases sum to 100, hidden present.
- `ProblemsetController::store()` persists metadata, starter code, judge reference,
  assertion header, and individual C++ cases with file paths.

**Remaining gaps**
1. The default judge reference and test bodies are FizzBuzz placeholders. A new author
   can click Publish and ship a suite that does not match the declared problem.
2. No draft persistence; a refresh loses the form.
3. No live C++ compile check of `solution.cpp` + `judge_solution.hpp` + one case.
4. No preview of how the statement will read.
5. Points validation is client-side only; the server does not reject a suite that does
   not sum to 100.

**vs benchmark:** LeetCode and getcracked.io do not expose authoring to users at all
beyond internal tools, so 85 is already ahead. The correctness gaps above are the
reason it is not in the 90s.

---

### 5. Learn index — 85

**What it is now:** three structured tracks with a continue-where-you-left-off hero,
a prerequisite path, and real lesson counts.

**Fixed in this pass**
- Editorial header and mono metadata.
- Prerequisite path as a left-to-right rail, not a card grid.
- Track cards show level, lesson count, duration, and completion.
- Removed decorative badges and gradient panels.

**Remaining gaps**
1. Completion is read from `localStorage('completed_lessons')`, not the
   `user_lesson_progress` table. It does not follow the account across devices.
2. No learner-level or XP model.
3. No search across lessons.

---

### 6. Track overview — 86

**Fixed in this pass**
- Reading column with a syllabus/module rail.
- Lesson list with completion state, duration, and a clear "continue" target.
- Project/assessment callouts tied to real drills.

**Remaining gaps**
1. Same localStorage progress problem as the index.
2. The assessment is described but has no runnable checkpoint.
3. Module ordering is static seed data, not editable by an instructor.

---

### 7. Lesson — 90

**What it is now:** a reading column plus a sticky syllabus rail, with quiz, discussion,
assembly view, and a linked judged drill.

**Fixed in this pass**
- Capped reading measure and editorial typography.
- Sticky syllabus with per-lesson completion and a linked drill panel.
- Quiz with correct/incorrect states and explanation.
- Assembly view toggled behind an explicit action.
- Previous/next lesson navigation and a "finish track" terminal action.
- Discussion panel restyled as a flat list.

**Remaining gaps**
1. **The "Run" button is simulated.** It returns canned output after 700 ms
   (`demoOutput`), not a real compiler run. This is the single biggest honesty gap in
   the app.
2. Discussion posting is local state only.
3. Completion is local-only.
4. No inline "open scratchpad" for the snippet.

**vs benchmark:** reading flow is close to boot.dev; boot.dev wins on real execution,
XP, and server-side progress.

---

### 8. CLI setup — 86

**What it is now:** an install guide, doctor checks, troubleshooting, device sessions,
and a compact owner-permission notice.

**Fixed in this pass**
- Editorial header and a four-column real-data ledger.
- OS-specific install steps with numbered mono markers.
- Flat doctor rows with pass/warn/fail states and no colored badge pills.
- Troubleshooting as native `<details>` rows.
- Session table with a clear current-device row.
- Owner-permission notice reduced from a large violet panel to a quiet amber note.

**Remaining gaps**
1. **Doctor, token creation, and session rows are simulated in component state.**
   No backend call creates or revokes a device token.
2. CLI version and toolchain rows are hard-coded.
3. No copy-all script for a full install in one paste.

---

### 9. Profile settings — 81

**Fixed in this pass**
- Settings shell rebuilt with `PageHeader`, sticky mono sub-nav, and icons.
- Profile form and delete-account panel flattened; destructive panel now uses
  `destructive` tokens and an uppercase warning label instead of red-50.

**Remaining gaps**
1. No avatar or public profile preview.
2. No email-verification status row (verification is intentionally disabled, so the
   UI should say so rather than omit it).
3. Username rules are not surfaced next to the field.

---

### 10. Security settings — 78

**Audited behind password confirmation** (`/user/confirm-password`), which is correct.

**Fixed in this pass**
- The settings shell and headings inherit the editorial system.

**Remaining gaps**
1. Two-factor and passkey panels are still stock shadcn blocks; they are the least
   visually consistent surface left.
2. No active-session list with revoke action on this page (it lives on `/setup`).
3. No password-strength feedback beyond server validation.

---

### 11. Appearance settings — 83

**Fixed in this pass**
- Three-way theme control restyled as a flat segmented control with mono labels.
- Active state uses ink-on-paper inversion instead of a white card shadow.

**Remaining gaps**
1. No preview pane.
2. No per-page density or font-size preference.

---

### 12. CLI authorization — 86

**Fixed in this pass**
- Rebuilt as a terminal-style sheet on the paper grid.
- Pending / approved / denied / expired states all share the same editorial frame.
- Confirmation code is the visual anchor; permissions are a flat list.
- Removed the sky/violet pills and the large drop shadow.

**Remaining gaps**
1. No manual code-entry form (the flow assumes the CLI already opened the URL).
2. No countdown for expiry.

---

## 4. Cross-cutting fixes

- **Design tokens:** warm paper palette, dark theme, Redaction display face, IBM Plex
  Mono metadata, small radii, functional emerald (`resources/css/app.css`).
- **Shared components:** `PageHeader`, `SectionCard`, `StatTile`, `Pill`, `EmptyState`,
  `ErrorState`, `PermissionState`, `LoadingState` all restyled
  (`resources/js/components/hub-ui.tsx`).
- **Shell:** grouped sidebar navigation with judge status, restyled logo, breadcrumbs,
  and theme toggle.
- **Settings shell:** sticky mono sub-nav with icons.
- **Problem data contract:** `Problem::toSummary()` now includes `total_points` and
  `time_limit_ms`; `ProblemSummary` kept in sync.
- **Leak prevention:** hidden test code is stripped before serialization.
- **Seed data:** every one of the 18 config problems now ships a Spec 2.0 suite —
  4 standalone C++ assertion programs per problem (2 public, 2 hidden), per-case points
  summing to 100, plus `tests/judge_test.h` and `tests/judge_solution.hpp`
  (`database/seeders/ProblemTestSuiteSeeder.php`).
- **Cleanup:** removed the two ad-hoc test problems (`avx-512-dot-product`,
  `lock-free-hazard-stack`) that had placeholder FizzBuzz starter code and no real
  suite. Problemset is now exactly the 18 curated problems.

---

## 5. Prioritized backlog

### P0 — trust and correctness

1. **Real judging.** `SubmissionController::store()` accepts any non-empty,
   non-`TODO` source as `accepted`. Replace with an actual compile + per-case run, or
   return `pending`/`unjudged` until the CLI submits a real score. Current accuracy and
   points are therefore optimistic, not measured.
2. **Stop simulating execution and devices.** The lesson Run button, CLI doctor, token
   creation, and session revoke are all local simulation. Either implement the backend
   endpoints or label these surfaces as "preview".
3. **Server-side learning progress.** Move `completed_lessons` from `localStorage` to
   `user_lesson_progress` so progress follows the account.
4. **Authoring guardrails.** Reject suites whose per-case points do not sum to 100;
   replace the FizzBuzz default judge assets with a neutral template; add a dry-run
   compile of one public case before publish.
5. **Label community data.** Submissions/comments/standings tabs mix seeded demo rows
   with real rows; mark demo content or hide it when no real data exists.

### P1 — flow improvements

6. Dashboard: surface `daily_challenges`, add a 12-week activity heatmap, and a
   points-to-next-rank bar.
7. Problemset: company/tag facets, saved lists, per-row last-attempt state.
8. Problem statement: OS-aware CLI prompt, per-example local-run command, and a
   clearer public/hidden split in the test suite.
9. Settings security: restyle 2FA/passkey panels and add active-session revoke.
10. Learn: add a runnable checkpoint/assessment at the end of each track.
11. Create problem: draft persistence and statement preview.

### P2 — polish

12. Accessibility pass: skip link, visible focus on every custom control, aria labels
    on sort/filter menus, reduced-motion support.
13. Mobile pass on the problem page and problemset table (horizontal scroll affordance).
14. Replace the remaining `rounded-xl`/shadow utilities in unvisited components.
15. Empty states for first-run submissions, comments, and passkeys.

---

## 6. Verification

| Check | Result |
| --- | --- |
| `npm.cmd run types:check` | pass |
| `npm.cmd run build` | pass (2,356 modules) |
| `php artisan test` | 89 tests, 80 passed, 9 skipped, 677 assertions |
| `/dashboard` | real stats render; no hang |
| `/problemset` | 18 drills, 1,800 points, filters render |
| `/problems/ring-buffer` | `06/18`, `4 assertion cases`, 100 points |
| `/learn`, `/learn/cpp-foundations`, lesson | reading + syllabus rail render |
| `/setup` | ledger, doctor, sessions render |
| `/settings/profile`, `/appearance` | editorial shell renders |
| `/settings/security` | correctly gated by password confirmation, then renders |
| `/cli/authorize` | terminal sheet + expired state render |

**Seeded suite check:** all 18 problems report `cases=4 points=100`.

---

## 7. Deliberate scope notes

- Email verification remains disabled; no verification gate was introduced.
- The problem statement intentionally has no browser editor or submit button. The
  workflow is fetch → edit → run tests → submit through the CLI.
- Source code is stored as a database string, never as a file.
- Test suites are C++ only and use the Spec 2.0 convention:
  `#include "judge_test.h"`, `#define Solution UserSolution`,
  `#include "solution.cpp"`, `#include "judge_solution.hpp"`.
- Benchmark scores are design judgments against public product patterns, not lab
  measurements.
