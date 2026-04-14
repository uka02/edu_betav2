# EduDev

EduDev is an accessible, integrated online learning platform designed for Mongolian learners. The system brings lesson delivery, assessment, learner progress monitoring, and certificate generation into one web application, and it is intended to serve as both a working educational platform and an academic implementation artifact.

## Abstract

This project addresses the need for a unified Mongolian-language e-learning environment where learners can study lessons, complete tests, track progress, and receive certificates without moving between separate systems. EduDev implements that environment as a Laravel-based web platform with segmented lessons, rich content blocks, timed exams, learner activity tracking, and per-exam certification. The repository demonstrates the software implementation and automated verification of the system; formal usability studies and experimental results should be documented alongside the thesis or research report.

## Research-Oriented Objectives

- Provide a single platform for lesson delivery, assessment, progress monitoring, and certification.
- Support Mongolian-language learning workflows with bilingual interface support (`mn`, `en`).
- Improve assessment trustworthiness through server-side grading and server-side time-limit enforcement.
- Measure learner progress from actual recorded learning activity instead of author-side publishing statistics.
- Produce verifiable certificates for successfully completed published exams.

## Implemented System Capabilities

- User authentication with email/password login and Google OAuth.
- Lesson creation and editing for `video`, `text`, and `document` lesson types.
- Segmented lesson structure with content sections and exam sections.
- Rich content blocks including text, subheadings, images, videos, files, quizzes, callouts, code, and dividers.
- Exam support for multiple choice, true/false, and short-answer questions.
- Server-side exam grading, attempt recording, passing-score checks, and time-limit enforcement.
- Progress tracking based on stored learner interaction data.
- Certificate issuance per exam segment for passed published lessons.
- Soft deletes, restore flows, and rate-limited lesson management endpoints.
- Locale switching with Mongolian and English language files.
- Accessibility-focused exam UI improvements including semantic answer controls, dialog roles, and live status messaging.

## System Architecture

### Main application layers

- `app/Http/Controllers`: web and API endpoints for authentication, lessons, progress, certificates, and locale switching.
- `app/Services`: focused business logic for lesson management, exam grading, and learner progress aggregation.
- `app/Models`: core entities such as `Lesson`, `LessonProgress`, `LessonExamAttempt`, and `Certificate`.
- `resources/views`: Blade templates for the dashboard, lesson builder, lesson viewer, and certificate pages.
- `lang/mn` and `lang/en`: bilingual interface strings.
- `tests/Feature`: end-to-end feature tests for the major academic workflows.

### Core workflow

1. An authenticated user creates a lesson with structured content and optional exam segments.
2. A learner opens the lesson and the system records progress from trackable items.
3. When the learner submits an exam, the server grades answers, validates the time limit, and stores the attempt.
4. If the learner passes a published exam, the system issues a certificate tied to that exam segment.
5. The dashboard aggregates actual learner progress, study time, streaks, and earned certificates.

## Technology Stack

- PHP 8.2
- Laravel 12
- Blade templates
- Vite
- Tailwind CSS tooling
- Laravel Socialite for Google authentication
- PHPUnit for automated testing
- SQLite by default in `.env.example` (other Laravel-supported databases can also be configured)

## Repository Structure

```text
app/
  Http/Controllers/      Web and API controllers
  Models/                Domain entities
  Services/              Business logic
database/migrations/     Schema changes
lang/mn/                 Mongolian translations
lang/en/                 English translations
resources/views/         User interface templates
routes/                  Web and API routes
tests/Feature/           Feature and workflow verification
```

## Setup

### 1. Install dependencies

```bash
composer install
npm install
```

### 2. Create environment file

```bash
cp .env.example .env
```

If you are using the default SQLite configuration, create the database file before migrating:

```bash
php -r "file_exists('database/database.sqlite') || touch('database/database.sqlite');"
```

### 3. Configure environment values

Generate the application key:

```bash
php artisan key:generate
```

Optional Google OAuth configuration:

```env
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
GOOGLE_REDIRECT_URI=http://127.0.0.1:8000/auth/google/callback
```

If Google sign-in is not needed, the standard email/password flow can still be used.

### 4. Run migrations

```bash
php artisan migrate
```

### 5. Start the application

For a full local development workflow:

```bash
composer run dev
```

Or start the backend and frontend separately:

```bash
php artisan serve
npm run dev
```

## Testing And Verification

Run the automated test suite with:

```bash
php artisan test
```

As of April 9, 2026, the project passes:

- 39 tests
- 237 assertions

The automated tests cover:

- authentication and page rendering
- locale switching
- lesson draft and publish workflows
- learner progress tracking
- dashboard learner metrics
- exam grading and certification
- certificate access control
- lesson API behavior
- supported video embedding behavior

## Academic Use Notes

This repository demonstrates the implemented platform and its software-level verification. It does not, by itself, constitute a full academic evaluation of usability or effectiveness. For thesis or publication use, the following should be documented separately:

- participant profile and sampling method
- experiment tasks and learning scenarios
- usability instruments, such as SUS or structured questionnaires
- effectiveness metrics, such as completion rate, score improvement, or task time
- analysis method and discussion of findings

In other words, the codebase is ready to support experiments, but the human-subject evaluation narrative should live in the thesis, appendix, or a dedicated research document.

## Related Documentation

- [EXPERIMENT_METHODOLOGY_MN.md](EXPERIMENT_METHODOLOGY_MN.md)
- [EXPERIMENT_METHODOLOGY.md](EXPERIMENT_METHODOLOGY.md)
- [API_DOCUMENTATION.md](API_DOCUMENTATION.md)
- [API_IMPLEMENTATION.md](API_IMPLEMENTATION.md)
- [IMPLEMENTATION_SUMMARY.md](IMPLEMENTATION_SUMMARY.md)
- [RATE_LIMITING.md](RATE_LIMITING.md)
- [SOFT_DELETES.md](SOFT_DELETES.md)

## Current Scope

The platform currently emphasizes:

- integrated lesson authoring and delivery
- learner-side progress visibility
- exam and certification workflows
- bilingual support for Mongolian and English
- academically cleaner system behavior around assessment validity and publishability

Future academic-facing improvements can focus on stronger documentation of experiment methodology, participant studies, and usability outcomes.
