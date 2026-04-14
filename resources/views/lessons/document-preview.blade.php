<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.app.theme-boot')
    <title>{{ $previewTitle }} - {{ $lesson->title }}</title>
    <style>
        :root {
            --bg: #09111c;
            --surface: #111b2b;
            --surface-2: #162235;
            --border: rgba(102, 153, 255, 0.18);
            --text: #e8eef8;
            --muted: #9cb0c7;
            --accent: #3b82f6;
        }

        [data-theme="light"] {
            --bg: #f4f7fb;
            --surface: #ffffff;
            --surface-2: #edf3fb;
            --border: rgba(37, 99, 235, 0.14);
            --text: #0f172a;
            --muted: #64748b;
            --accent: #2563eb;
        }

        * { box-sizing: border-box; }
        body {
            margin: 0;
            min-height: 100vh;
            background: var(--bg);
            color: var(--text);
            font-family: "Roboto", sans-serif;
        }

        .preview-shell {
            width: min(1200px, calc(100% - 32px));
            margin: 0 auto;
            padding: 24px 0 32px;
        }

        .preview-topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 18px;
            padding: 18px 20px;
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 18px;
        }

        .preview-copy {
            display: flex;
            flex-direction: column;
            gap: 6px;
            min-width: 0;
        }

        .preview-kicker {
            font-size: 12px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
            color: var(--accent);
        }

        .preview-title {
            font-size: clamp(22px, 4vw, 30px);
            font-weight: 800;
            line-height: 1.2;
        }

        .preview-meta {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            color: var(--muted);
            font-size: 13px;
        }

        .preview-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .preview-btn,
        .preview-btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 44px;
            padding: 0 16px;
            border-radius: 12px;
            text-decoration: none;
            font-weight: 700;
            transition: transform .16s ease, opacity .16s ease, background .16s ease;
        }

        .preview-btn {
            background: linear-gradient(135deg, #1d4ed8, var(--accent));
            color: #fff;
        }

        .preview-btn-secondary {
            background: var(--surface-2);
            border: 1px solid var(--border);
            color: var(--text);
        }

        .preview-btn:hover,
        .preview-btn-secondary:hover {
            transform: translateY(-1px);
            opacity: .95;
        }

        .preview-card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: 22px;
            overflow: hidden;
            min-height: calc(100vh - 180px);
        }

        .preview-frame {
            width: 100%;
            min-height: calc(100vh - 180px);
            border: 0;
            background: #fff;
        }

        .preview-empty {
            display: grid;
            place-items: center;
            min-height: calc(100vh - 180px);
            padding: 32px;
        }

        .preview-empty-card {
            max-width: 560px;
            text-align: center;
            background: var(--surface-2);
            border: 1px solid var(--border);
            border-radius: 20px;
            padding: 28px;
        }

        .preview-empty-card h2 {
            font-size: 24px;
            margin: 0 0 12px;
        }

        .preview-empty-card p {
            margin: 0;
            color: var(--muted);
            line-height: 1.7;
        }

        .preview-empty-actions {
            display: flex;
            justify-content: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 20px;
        }

        @media (max-width: 720px) {
            .preview-shell {
                width: min(100%, calc(100% - 20px));
                padding-top: 14px;
            }

            .preview-topbar {
                padding: 16px;
            }

            .preview-card,
            .preview-frame,
            .preview-empty {
                min-height: calc(100vh - 150px);
            }
        }
    </style>
</head>
<body>
    <script>
        if (localStorage.getItem('theme') === 'light') {
            document.documentElement.setAttribute('data-theme', 'light');
        }
    </script>

    <main class="preview-shell">
        <section class="preview-topbar">
            <div class="preview-copy">
                <div class="preview-kicker">{{ $previewTitle }}</div>
                <div class="preview-title">{{ $lesson->title }}</div>
                <div class="preview-meta">
                    <span>{{ $fileName }}</span>
                    @if($fileExtension !== '')
                        <span>{{ $fileExtension }}</span>
                    @endif
                    <span>{{ $fileSize }}</span>
                </div>
            </div>

            <div class="preview-actions">
                <a href="{{ route('lessons.show', $lesson) }}" class="preview-btn-secondary">{{ __('lessons.back_to_lesson') }}</a>
                <a href="{{ $downloadUrl }}" class="preview-btn-secondary">{{ $downloadLabel }}</a>
            </div>
        </section>

        <section class="preview-card">
            @if($canInlinePreview)
                <iframe src="{{ $streamUrl }}" class="preview-frame" title="{{ $previewTitle }}"></iframe>
            @else
                <div class="preview-empty">
                    <div class="preview-empty-card">
                        <h2>{{ __('lessons.preview_not_available') }}</h2>
                        <p>{{ __('lessons.preview_not_available_copy') }}</p>

                        <div class="preview-empty-actions">
                            <a href="{{ $streamUrl }}" target="_blank" rel="noopener noreferrer" class="preview-btn">{{ __('lessons.open_file') }}</a>
                            <a href="{{ $downloadUrl }}" class="preview-btn-secondary">{{ $downloadLabel }}</a>
                        </div>
                    </div>
                </div>
            @endif
        </section>
    </main>
</body>
</html>
