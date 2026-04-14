@php
    $learnerNameLines = $learnerNameLines === [] ? [$learnerName] : $learnerNameLines;
    $lessonTitleLines = $lessonTitleLines === [] ? [$lessonTitle] : $lessonTitleLines;
    $nameStartY = 356;
    $lessonLabelY = $nameStartY + ((count($learnerNameLines) - 1) * 68) + 90;
    $lessonTitleStartY = $lessonLabelY + 72;
    $examTitleY = $lessonTitleStartY + ((count($lessonTitleLines) - 1) * 54) + 84;
    $metaTopY = $examTitleY + 92;
    $formattedScore = $score !== null
        ? rtrim(rtrim(number_format($score, 2, '.', ''), '0'), '.') . '%'
        : __('certificates.not_available');
@endphp
<svg xmlns="http://www.w3.org/2000/svg" width="1600" height="1131" viewBox="0 0 1600 1131" fill="none" role="img" aria-labelledby="certificateTitle certificateDesc">
    <title id="certificateTitle">{{ __('certificates.certificate_of_completion') }}</title>
    <desc id="certificateDesc">{{ $learnerName }} - {{ $lessonTitle }}</desc>
    <defs>
        <linearGradient id="certificateShell" x1="126" y1="84" x2="1480" y2="1048" gradientUnits="userSpaceOnUse">
            <stop stop-color="#FAF6EC"/>
            <stop offset="1" stop-color="#F3ECE0"/>
        </linearGradient>
        <linearGradient id="certificateBorder" x1="200" y1="120" x2="1400" y2="1020" gradientUnits="userSpaceOnUse">
            <stop stop-color="#0B3B64"/>
            <stop offset="0.52" stop-color="#2F6FA3"/>
            <stop offset="1" stop-color="#0F2740"/>
        </linearGradient>
        <radialGradient id="certificateGlow" cx="0" cy="0" r="1" gradientUnits="userSpaceOnUse" gradientTransform="translate(1320 186) rotate(137.793) scale(430.345 392.276)">
            <stop stop-color="#E7B960" stop-opacity=".32"/>
            <stop offset="1" stop-color="#E7B960" stop-opacity="0"/>
        </radialGradient>
    </defs>
    <style>
        .sans { font-family: "Segoe UI", "Noto Sans", Arial, sans-serif; }
        .serif { font-family: Georgia, "Times New Roman", serif; }
        .caps { letter-spacing: .28em; text-transform: uppercase; }
        .small { font-size: 26px; fill: #5A6D7E; }
        .metaLabel { font-size: 20px; fill: #6D7F8F; text-transform: uppercase; letter-spacing: .16em; }
        .metaValue { font-size: 34px; fill: #14314C; font-weight: 700; }
    </style>

    <rect width="1600" height="1131" fill="#E9EEF4"/>
    <rect x="58" y="58" width="1484" height="1015" rx="40" fill="url(#certificateShell)"/>
    <rect x="58" y="58" width="1484" height="1015" rx="40" stroke="url(#certificateBorder)" stroke-width="14"/>
    <rect x="92" y="92" width="1416" height="947" rx="28" stroke="#D6C7A9" stroke-width="2"/>
    <rect x="120" y="120" width="1360" height="891" rx="24" fill="url(#certificateGlow)"/>

    <circle cx="248" cy="214" r="88" fill="#0E3C62" fill-opacity=".08"/>
    <circle cx="248" cy="214" r="63" stroke="#D6A94F" stroke-width="6"/>
    <circle cx="248" cy="214" r="34" fill="#D6A94F"/>
    <path d="M235 212.5 246 224l21-27" stroke="#FAF6EC" stroke-width="10" stroke-linecap="round" stroke-linejoin="round"/>

    <text x="800" y="170" text-anchor="middle" class="sans caps" style="font-size:24px; fill:#0E3C62; font-weight:700;">
        EduDev
    </text>
    <text x="800" y="232" text-anchor="middle" class="serif" style="font-size:72px; fill:#14314C; font-weight:700;">
        {{ __('certificates.certificate_of_completion') }}
    </text>
    <text x="800" y="284" text-anchor="middle" class="sans small">
        {{ __('certificates.awarded_to') }}
    </text>

    <text x="800" y="{{ $nameStartY }}" text-anchor="middle" class="serif" style="font-size:68px; fill:#215E86; font-weight:700;">
        @foreach($learnerNameLines as $index => $line)
            <tspan x="800" dy="{{ $index === 0 ? 0 : 68 }}">{{ $line }}</tspan>
        @endforeach
    </text>

    <text x="800" y="{{ $lessonLabelY }}" text-anchor="middle" class="sans small">
        {{ __('certificates.for_lesson') }}
    </text>

    <text x="800" y="{{ $lessonTitleStartY }}" text-anchor="middle" class="sans" style="font-size:46px; fill:#14314C; font-weight:700;">
        @foreach($lessonTitleLines as $index => $line)
            <tspan x="800" dy="{{ $index === 0 ? 0 : 54 }}">{{ $line }}</tspan>
        @endforeach
    </text>

    <text x="800" y="{{ $examTitleY }}" text-anchor="middle" class="sans" style="font-size:30px; fill:#6D7F8F;">
        {{ $examTitle }}
    </text>

    <line x1="228" y1="{{ $metaTopY }}" x2="1372" y2="{{ $metaTopY }}" stroke="#D9C8A6" stroke-width="2"/>

    <text x="266" y="{{ $metaTopY + 58 }}" class="sans metaLabel">{{ __('certificates.awarded_to_label') }}</text>
    <text x="266" y="{{ $metaTopY + 104 }}" class="sans metaValue">{{ $learnerName }}</text>

    <text x="266" y="{{ $metaTopY + 174 }}" class="sans metaLabel">{{ __('certificates.issued_by') }}</text>
    <text x="266" y="{{ $metaTopY + 220 }}" class="sans metaValue">{{ $issuerName }}</text>

    <text x="936" y="{{ $metaTopY + 58 }}" class="sans metaLabel">{{ __('certificates.score') }}</text>
    <text x="936" y="{{ $metaTopY + 104 }}" class="sans metaValue">{{ $formattedScore }}</text>

    <text x="936" y="{{ $metaTopY + 174 }}" class="sans metaLabel">{{ __('certificates.issued_on') }}</text>
    <text x="936" y="{{ $metaTopY + 220 }}" class="sans metaValue">{{ optional($certificate->issued_at)->format('Y-m-d') }}</text>

    <line x1="228" y1="878" x2="624" y2="878" stroke="#14314C" stroke-width="2"/>
    <text x="228" y="920" class="sans metaValue" style="font-size:28px;">{{ $issuerName }}</text>
    <text x="228" y="954" class="sans small">{{ __('certificates.issued_by') }}</text>

    <line x1="986" y1="878" x2="1372" y2="878" stroke="#14314C" stroke-width="2"/>
    <text x="986" y="920" class="sans metaValue" style="font-size:28px;">{{ optional($certificate->issued_at)->format('Y-m-d') }}</text>
    <text x="986" y="954" class="sans small">{{ __('certificates.issued_on') }}</text>

    <rect x="1124" y="120" width="274" height="74" rx="18" fill="#0E3C62"/>
    <text x="1261" y="165" text-anchor="middle" class="sans" style="font-size:22px; fill:#FAF6EC; font-weight:700; letter-spacing:.08em;">
        {{ $certificate->certificate_code }}
    </text>
</svg>
