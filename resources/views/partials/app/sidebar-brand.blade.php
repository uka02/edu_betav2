@php
    $brandHref = auth()->check() ? route('dashboard') : url('/');
@endphp

@once
    <style>
        .app-sidebar-brand {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 18px 16px 16px;
            text-decoration: none;
            color: inherit;
        }

        .app-sidebar-mark {
            width: 30px;
            height: 30px;
            border-radius: 7px;
            background: linear-gradient(135deg, var(--blue-dim, #1e6fc4), var(--blue, #3b9eff));
            display: grid;
            place-items: center;
            font-size: 11px;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.04em;
            flex-shrink: 0;
            box-shadow: 0 4px 12px var(--blue-glow, rgba(59,158,255,0.15));
        }

        .app-sidebar-name {
            font-size: 15px;
            font-weight: 800;
            letter-spacing: -0.04em;
            color: var(--tx, #e8f0f8);
        }

        .app-sidebar-name span {
            color: var(--blue, #3b9eff);
        }
    </style>
@endonce

<a
    href="{{ $brandHref }}"
    class="app-sidebar-brand {{ $wrapperClass ?? 'sb-brand' }}"
    style="text-decoration:none;color:inherit;"
    aria-label="EduDev"
>
    <div class="app-sidebar-mark {{ $markClass ?? 'sb-mark' }}">ED</div>
    <span class="app-sidebar-name {{ $nameClass ?? 'sb-name' }}">Edu<span>Dev</span></span>
</a>
