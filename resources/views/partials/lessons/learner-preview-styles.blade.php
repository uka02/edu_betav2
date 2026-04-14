        .header-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .learner-preview-open {
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .learner-preview-modal {
            position: fixed;
            inset: 0;
            background: rgba(4, 10, 18, 0.72);
            backdrop-filter: blur(10px);
            display: none;
            align-items: center;
            justify-content: center;
            padding: 22px;
            z-index: 200;
        }

        .learner-preview-modal.is-open {
            display: flex;
        }

        .learner-preview-shell {
            width: min(1380px, 100%);
            height: min(90vh, 920px);
            background: linear-gradient(180deg, color-mix(in srgb, var(--surface1) 96%, transparent), color-mix(in srgb, var(--surface2) 92%, transparent));
            border: 1px solid var(--border2);
            border-radius: 24px;
            box-shadow: 0 26px 70px rgba(4, 10, 18, 0.42);
            overflow: hidden;
            display: grid;
            grid-template-rows: auto minmax(0, 1fr);
        }

        .learner-preview-topbar {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            padding: 18px 22px;
            border-bottom: 1px solid var(--border);
            background: color-mix(in srgb, var(--surface) 92%, transparent);
        }

        .learner-preview-topbar-copy {
            min-width: 0;
        }

        .learner-preview-kicker {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 6px 11px;
            border-radius: 999px;
            background: var(--blue-soft);
            border: 1px solid rgba(59, 158, 255, 0.18);
            color: var(--blue);
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .1em;
            text-transform: uppercase;
        }

        .learner-preview-title {
            margin-top: 12px;
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -.04em;
            color: var(--text);
        }

        .learner-preview-subtitle {
            margin-top: 6px;
            max-width: 720px;
            color: var(--tx2);
            font-size: 13px;
            line-height: 1.6;
        }

        .learner-preview-topbar-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .learner-preview-status {
            padding: 7px 11px;
            border-radius: 999px;
            border: 1px solid var(--border);
            background: color-mix(in srgb, var(--surface2) 88%, transparent);
            color: var(--tx2);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .learner-preview-close {
            width: 38px;
            height: 38px;
            border-radius: 12px;
            border: 1px solid var(--border);
            background: var(--surface1);
            color: var(--tx2);
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: all .18s ease;
        }

        .learner-preview-close:hover {
            background: var(--surface2);
            color: var(--text);
            border-color: var(--border2);
        }

        .learner-preview-layout {
            display: grid;
            grid-template-columns: 300px minmax(0, 1fr);
            min-height: 0;
        }

        .learner-preview-sidebar {
            min-height: 0;
            overflow-y: auto;
            padding: 20px;
            border-right: 1px solid var(--border);
            background: color-mix(in srgb, var(--surface) 94%, transparent);
        }

        .learner-preview-stats {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 12px;
            margin-bottom: 18px;
        }

        .learner-preview-stat {
            padding: 14px 12px;
            border-radius: 14px;
            background: linear-gradient(180deg, color-mix(in srgb, var(--surface2) 96%, transparent), color-mix(in srgb, var(--surface3) 92%, transparent));
            border: 1px solid var(--border2);
            text-align: center;
        }

        .learner-preview-stat-value {
            font-size: 20px;
            font-weight: 800;
            color: var(--blue);
            line-height: 1;
        }

        .learner-preview-stat-label {
            margin-top: 6px;
            color: var(--muted);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .learner-preview-outline-card {
            padding: 16px;
            border-radius: 16px;
            border: 1px solid var(--border2);
            background: linear-gradient(180deg, color-mix(in srgb, var(--surface1) 96%, transparent), color-mix(in srgb, var(--surface2) 92%, transparent));
        }

        .learner-preview-outline-title {
            font-size: 11px;
            font-weight: 800;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: var(--muted);
            padding-bottom: 10px;
            border-bottom: 1px solid var(--border);
        }

        .learner-preview-outline-list {
            display: flex;
            flex-direction: column;
            gap: 8px;
            margin-top: 12px;
        }

        .learner-preview-outline-item {
            width: 100%;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 11px 12px;
            border-radius: 12px;
            border: 1px solid transparent;
            background: transparent;
            color: var(--tx2);
            cursor: pointer;
            text-align: left;
            transition: all .18s ease;
            font-family: "Roboto", sans-serif;
        }

        .learner-preview-outline-item:hover {
            background: color-mix(in srgb, var(--surface2) 86%, transparent);
            border-color: var(--border2);
            color: var(--text);
        }

        .learner-preview-outline-item.is-active {
            background: color-mix(in srgb, var(--blue-soft) 78%, var(--surface2));
            border-color: rgba(59, 158, 255, 0.22);
            color: var(--text);
        }

        .learner-preview-outline-icon {
            width: 30px;
            height: 30px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--border);
            background: color-mix(in srgb, var(--surface2) 88%, transparent);
            color: var(--blue);
            font-size: 12px;
            font-weight: 800;
            flex-shrink: 0;
        }

        .learner-preview-outline-item.is-active .learner-preview-outline-icon {
            background: linear-gradient(135deg, var(--accent), var(--purple));
            border-color: transparent;
            color: #fff;
        }

        .learner-preview-outline-copy {
            min-width: 0;
        }

        .learner-preview-outline-name {
            display: block;
            font-size: 13px;
            font-weight: 600;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .learner-preview-outline-meta {
            display: block;
            margin-top: 2px;
            font-size: 11px;
            color: var(--muted);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .learner-preview-content {
            min-height: 0;
            overflow-y: auto;
            padding: 24px;
        }

        .learner-preview-hero {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 18px;
            padding: 22px;
            border-radius: 20px;
            border: 1px solid var(--border2);
            background: linear-gradient(135deg, color-mix(in srgb, var(--surface1) 94%, transparent), color-mix(in srgb, var(--surface2) 90%, transparent));
            margin-bottom: 20px;
        }

        .learner-preview-hero-copy {
            min-width: 0;
        }

        .learner-preview-hero-title {
            font-size: 28px;
            font-weight: 800;
            letter-spacing: -.05em;
            line-height: 1.1;
            color: var(--text);
        }

        .learner-preview-hero-subtitle {
            margin-top: 8px;
            color: var(--tx2);
            font-size: 13px;
            line-height: 1.7;
        }

        .learner-preview-tag-row {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin-top: 14px;
        }

        .learner-preview-tag {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .04em;
            border: 1px solid var(--border);
            background: color-mix(in srgb, var(--surface2) 88%, transparent);
            color: var(--tx2);
            text-transform: uppercase;
        }

        .learner-preview-tag.is-blue {
            background: var(--blue-soft);
            color: var(--blue);
            border-color: rgba(59, 158, 255, 0.18);
        }

        .learner-preview-tag.is-green {
            background: var(--green-soft);
            color: var(--green);
            border-color: rgba(46, 204, 138, 0.18);
        }

        .learner-preview-tag.is-amber {
            background: var(--amber-soft);
            color: var(--amber);
            border-color: rgba(245, 166, 35, 0.18);
        }

        .learner-preview-progress {
            width: min(220px, 100%);
            flex-shrink: 0;
            padding: 14px 16px;
            border-radius: 16px;
            border: 1px solid var(--border2);
            background: color-mix(in srgb, var(--surface2) 92%, transparent);
        }

        .learner-preview-progress-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 8px;
        }

        .learner-preview-progress-label {
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .1em;
            text-transform: uppercase;
            color: var(--muted);
        }

        .learner-preview-progress-value {
            color: var(--blue);
            font-size: 14px;
            font-weight: 800;
        }

        .learner-preview-progress-track {
            height: 6px;
            border-radius: 999px;
            background: color-mix(in srgb, var(--surface3) 90%, transparent);
            overflow: hidden;
        }

        .learner-preview-progress-fill {
            height: 100%;
            width: 0;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--accent-hover), var(--accent));
        }

        .learner-preview-progress-note {
            margin-top: 8px;
            font-size: 11px;
            color: var(--muted);
        }

        .learner-preview-sections {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .learner-preview-section {
            display: none;
            padding: 24px;
            border-radius: 20px;
            border: 1px solid var(--border2);
            background: linear-gradient(180deg, color-mix(in srgb, var(--surface1) 96%, transparent), color-mix(in srgb, var(--surface2) 92%, transparent));
        }

        .learner-preview-section.is-active {
            display: block;
            animation: riseIn .18s ease;
        }

        .learner-preview-section-head {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 12px;
            padding-bottom: 16px;
            margin-bottom: 18px;
            border-bottom: 1px solid var(--border);
        }

        .learner-preview-section-title {
            font-size: 22px;
            font-weight: 800;
            letter-spacing: -.03em;
            color: var(--text);
        }

        .learner-preview-section-meta {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 7px 11px;
            border-radius: 999px;
            border: 1px solid var(--border);
            background: color-mix(in srgb, var(--surface2) 88%, transparent);
            color: var(--muted);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .06em;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .learner-preview-basic {
            display: grid;
            grid-template-columns: minmax(0, 1.3fr) minmax(280px, .85fr);
            gap: 22px;
        }

        .learner-preview-card {
            padding: 18px;
            border-radius: 16px;
            border: 1px solid var(--border2);
            background: color-mix(in srgb, var(--surface2) 90%, transparent);
        }

        .learner-preview-detail-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .learner-preview-detail {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            font-size: 13px;
        }

        .learner-preview-detail-label {
            color: var(--muted);
            font-weight: 600;
        }

        .learner-preview-detail-value {
            color: var(--text);
            font-weight: 600;
            text-align: right;
        }

        .learner-preview-media {
            width: 100%;
            overflow: hidden;
            border-radius: 18px;
            border: 1px solid var(--border2);
            background: color-mix(in srgb, var(--surface2) 92%, transparent);
        }

        .learner-preview-thumbnail {
            width: 100%;
            max-height: 360px;
            object-fit: cover;
            display: block;
        }

        .learner-preview-video-frame {
            position: relative;
            padding-bottom: 56.25%;
            height: 0;
        }

        .learner-preview-video-frame iframe {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            border: 0;
        }

        .learner-preview-media-actions {
            padding: 14px 16px;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: flex-end;
            background: color-mix(in srgb, var(--surface2) 92%, transparent);
        }

        .learner-preview-link {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 12px;
            border: 1px solid rgba(59, 158, 255, 0.18);
            background: var(--blue-soft);
            color: var(--blue);
            text-decoration: none;
            font-size: 12.5px;
            font-weight: 700;
            transition: all .18s ease;
        }

        .learner-preview-link:hover {
            transform: translateY(-1px);
            opacity: .95;
        }

        .learner-preview-link.is-static {
            cursor: default;
            text-decoration: none;
        }

        .learner-preview-empty {
            padding: 18px;
            border-radius: 14px;
            border: 1px dashed var(--border2);
            background: color-mix(in srgb, var(--surface2) 88%, transparent);
            color: var(--muted);
            font-size: 13px;
            line-height: 1.6;
        }

        .learner-preview-blocks {
            display: flex;
            flex-direction: column;
            gap: 18px;
        }

        .learner-preview-text {
            color: var(--text);
            font-size: 15px;
            line-height: 1.8;
            white-space: pre-wrap;
        }

        .learner-preview-subheading {
            color: var(--text);
            font-size: 20px;
            font-weight: 700;
            letter-spacing: -.02em;
            padding-bottom: 8px;
            border-bottom: 1px solid var(--border);
        }

        .learner-preview-image {
            width: 100%;
            display: block;
            border-radius: 16px;
            border: 1px solid var(--border2);
        }

        .learner-preview-caption {
            margin-top: 8px;
            color: var(--muted);
            font-size: 12.5px;
            font-style: italic;
        }

        .learner-preview-code {
            padding: 18px;
            border-radius: 16px;
            border: 1px solid var(--border2);
            background: color-mix(in srgb, var(--surface2) 92%, transparent);
            color: var(--text);
            font-family: Consolas, "Courier New", monospace;
            font-size: 13px;
            line-height: 1.7;
            overflow-x: auto;
            white-space: pre-wrap;
        }

        .learner-preview-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--border2), transparent);
        }

        .learner-preview-callout {
            padding: 16px 18px;
            border-radius: 16px;
            border-left: 4px solid;
            font-size: 14px;
            font-weight: 500;
            line-height: 1.7;
        }

        .learner-preview-callout.info {
            background: var(--blue-soft);
            border-left-color: var(--blue);
            color: var(--blue);
        }

        .learner-preview-callout.warning {
            background: var(--amber-soft);
            border-left-color: var(--amber);
            color: var(--amber);
        }

        .learner-preview-callout.success {
            background: var(--green-soft);
            border-left-color: var(--green);
            color: var(--green);
        }

        .learner-preview-callout.danger {
            background: var(--red-soft);
            border-left-color: var(--red);
            color: var(--red);
        }

        .learner-preview-quiz {
            padding: 20px;
            border-radius: 18px;
            border: 1px solid var(--border2);
            background: color-mix(in srgb, var(--surface2) 92%, transparent);
        }

        .learner-preview-quiz-question {
            padding-bottom: 12px;
            margin-bottom: 14px;
            border-bottom: 1px solid var(--border);
            color: var(--text);
            font-size: 16px;
            font-weight: 700;
        }

        .learner-preview-quiz-options {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .learner-preview-quiz-option {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 13px 14px;
            border-radius: 14px;
            border: 1px solid var(--border);
            background: color-mix(in srgb, var(--surface1) 92%, transparent);
        }

        .learner-preview-answer-letter {
            width: 28px;
            height: 28px;
            border-radius: 999px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border: 1px solid var(--border2);
            color: var(--blue);
            font-size: 11px;
            font-weight: 800;
            flex-shrink: 0;
        }

        .learner-preview-answer-text {
            min-width: 0;
            color: var(--text);
            font-size: 14px;
            line-height: 1.6;
        }

        .learner-preview-exam-box {
            padding: 20px;
            border-radius: 18px;
            border: 1px solid rgba(245, 166, 35, 0.22);
            background: color-mix(in srgb, var(--amber-soft) 65%, var(--surface2));
        }

        .learner-preview-exam-text {
            color: var(--tx);
            font-size: 14px;
            line-height: 1.7;
        }

        .learner-preview-exam-stats {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 12px;
            margin-top: 16px;
        }

        .learner-preview-exam-stat {
            padding: 14px 12px;
            border-radius: 14px;
            border: 1px solid rgba(245, 166, 35, 0.22);
            background: color-mix(in srgb, var(--surface1) 92%, transparent);
            text-align: center;
        }

        .learner-preview-exam-stat-label {
            color: var(--muted);
            font-size: 10px;
            font-weight: 800;
            letter-spacing: .1em;
            text-transform: uppercase;
        }

        .learner-preview-exam-stat-value {
            margin-top: 6px;
            color: var(--amber);
            font-size: 20px;
            font-weight: 800;
            line-height: 1;
        }

        .learner-preview-exam-btn {
            margin-top: 18px;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 16px;
            border-radius: 12px;
            border: 1px solid rgba(59, 158, 255, 0.18);
            background: linear-gradient(135deg, var(--accent-hover), var(--accent));
            color: #fff;
            font-size: 12.5px;
            font-weight: 700;
            cursor: default;
        }

        @media (max-width: 1100px) {
            .learner-preview-layout {
                grid-template-columns: 260px minmax(0, 1fr);
            }

            .learner-preview-basic {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 860px) {
            .learner-preview-modal {
                padding: 10px;
            }

            .learner-preview-shell {
                width: 100%;
                height: 100%;
                border-radius: 18px;
            }

            .learner-preview-layout {
                grid-template-columns: 1fr;
            }

            .learner-preview-sidebar {
                border-right: none;
                border-bottom: 1px solid var(--border);
                max-height: 280px;
            }

            .learner-preview-hero {
                flex-direction: column;
            }

            .learner-preview-progress {
                width: 100%;
            }
        }

        @media (max-width: 640px) {
            .header-actions {
                width: 100%;
                justify-content: flex-start;
            }

            .learner-preview-topbar {
                flex-direction: column;
            }

            .learner-preview-topbar-actions {
                width: 100%;
                justify-content: space-between;
            }

            .learner-preview-content {
                padding: 16px;
            }

            .learner-preview-hero,
            .learner-preview-section {
                padding: 18px;
            }

            .learner-preview-section-head {
                flex-direction: column;
                align-items: flex-start;
            }

            .learner-preview-exam-stats,
            .learner-preview-stats {
                grid-template-columns: 1fr 1fr;
            }
        }
