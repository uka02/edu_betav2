@php
    $lessonDeletionNotifications = $lessonDeletionNotifications ?? collect();
@endphp

@if($lessonDeletionNotifications->isNotEmpty())
    @once
        <style>
            .lesson-admin-notice-stack {
                display: grid;
                gap: 12px;
                margin-bottom: 18px;
            }

            .lesson-admin-notice-card {
                background: color-mix(in srgb, var(--amber-soft) 70%, var(--s1));
                border: 1px solid color-mix(in srgb, var(--amber) 22%, transparent);
                border-radius: 16px;
                padding: 18px 20px;
                box-shadow: 0 8px 22px rgba(15, 23, 42, 0.08);
            }

            .lesson-admin-notice-head {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
                margin-bottom: 8px;
                flex-wrap: wrap;
            }

            .lesson-admin-notice-kicker {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                font-size: 11px;
                font-weight: 800;
                letter-spacing: .08em;
                text-transform: uppercase;
                color: var(--amber);
            }

            .lesson-admin-notice-title {
                font-size: 18px;
                font-weight: 800;
                color: var(--tx);
                letter-spacing: -.03em;
            }

            .lesson-admin-notice-copy {
                color: var(--tx2);
                font-size: 13px;
                line-height: 1.7;
                margin-bottom: 14px;
            }

            .lesson-admin-notice-grid {
                display: grid;
                grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
                gap: 10px;
            }

            .lesson-admin-notice-item {
                padding: 12px 14px;
                border-radius: 12px;
                background: var(--s1);
                border: 1px solid var(--b0);
            }

            .lesson-admin-notice-label {
                font-size: 11px;
                font-weight: 700;
                letter-spacing: .07em;
                text-transform: uppercase;
                color: var(--muted);
                margin-bottom: 6px;
            }

            .lesson-admin-notice-value {
                color: var(--tx);
                font-size: 13px;
                line-height: 1.7;
                word-break: break-word;
            }

            .lesson-admin-notice-time {
                font-size: 12px;
                color: var(--muted);
            }

            [data-theme="light"] .lesson-admin-notice-card {
                background: linear-gradient(180deg, rgba(217, 119, 6, 0.10), rgba(255, 255, 255, 0.98));
                border-color: rgba(217, 119, 6, 0.16);
            }

            [data-theme="light"] .lesson-admin-notice-item {
                background: #ffffff;
                border-color: rgba(37, 99, 235, 0.10);
            }
        </style>
    @endonce

    <section class="lesson-admin-notice-stack">
        <article class="lesson-admin-notice-card">
            <div class="lesson-admin-notice-head">
                <div>
                    <div class="lesson-admin-notice-kicker">{{ __('lessons.recent_admin_notices') }}</div>
                    <div class="lesson-admin-notice-title">{{ __('lessons.lesson_removed_notice_heading') }}</div>
                </div>
            </div>
            <p class="lesson-admin-notice-copy">{{ __('lessons.recent_admin_notices_copy') }}</p>

            <div class="lesson-admin-notice-grid">
                @foreach($lessonDeletionNotifications as $notice)
                    <div class="lesson-admin-notice-item">
                        <div class="lesson-admin-notice-label">{{ __('lessons.lesson_removed_notice_copy', [
                            'title' => $notice->data['lesson_title'] ?? __('lessons.untitled_draft'),
                            'admin' => $notice->data['admin_name'] ?? __('dashboard.role_admin'),
                        ]) }}</div>
                        <div class="lesson-admin-notice-value">{!! nl2br(e($notice->data['reason'] ?? '')) !!}</div>
                        <div class="lesson-admin-notice-time">
                            {{ optional($notice->created_at)->format('Y-m-d H:i') }}
                        </div>
                    </div>
                @endforeach
            </div>
        </article>
    </section>
@endif
