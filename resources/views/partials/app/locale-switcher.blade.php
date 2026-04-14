@php
    $variant = $variant ?? 'topbar';
    $locales = config('app.available_locales', []);
    $currentLocale = app()->getLocale();
    $isFloating = $variant === 'floating';
    $isSettings = $variant === 'settings';
    $formStyle = $isFloating
        ? 'position:fixed;top:16px;right:16px;z-index:120;display:inline-flex;align-items:center;padding:8px 12px;border-radius:999px;border:1px solid rgba(255,255,255,0.08);background:rgba(15,18,25,0.88);backdrop-filter:blur(10px);box-shadow:0 18px 40px rgba(0,0,0,0.28);'
        : ($isSettings
            ? 'display:inline-flex;align-items:center;min-width:120px;'
            : 'display:inline-flex;align-items:center;');
    $selectStyle = $isFloating
        ? 'min-width:96px;padding:0 18px 0 0;border:none;background:transparent;color:var(--text, #eef0f6);font:600 12px "Roboto", sans-serif;outline:none;cursor:pointer;appearance:none;-webkit-appearance:none;-moz-appearance:none;'
        : ($isSettings
            ? 'width:100%;min-width:120px;padding:7px 28px 7px 10px;border-radius:8px;border:1px solid var(--b1, rgba(255,255,255,0.12));background:var(--s1, var(--surface2, rgba(255,255,255,0.04)));color:var(--tx, var(--text, #eef0f6));font:500 12px "Roboto", sans-serif;outline:none;cursor:pointer;appearance:none;-webkit-appearance:none;-moz-appearance:none;'
            : 'min-width:96px;padding:6px 26px 6px 10px;border-radius:8px;border:1px solid var(--b1, rgba(255,255,255,0.12));background:var(--s1, var(--surface2, rgba(255,255,255,0.04)));color:var(--tx2, var(--text, #eef0f6));font:500 12px "Roboto", sans-serif;outline:none;cursor:pointer;appearance:none;-webkit-appearance:none;-moz-appearance:none;');
    $iconStyle = $isFloating
        ? 'position:absolute;right:0;top:50%;transform:translateY(-50%);pointer-events:none;color:var(--muted2, #8a92a0);font-size:10px;'
        : ($isSettings
            ? 'position:absolute;right:10px;top:50%;transform:translateY(-50%);pointer-events:none;color:var(--muted, #64748b);font-size:10px;'
            : 'position:absolute;right:10px;top:50%;transform:translateY(-50%);pointer-events:none;color:var(--muted, #64748b);font-size:10px;');
@endphp

@if(count($locales) > 1)
    <form method="POST" action="{{ route('locale.update') }}" style="{{ $formStyle }}">
        @csrf
        <div style="position:relative;display:inline-flex;align-items:center;">
            <select name="locale" onchange="this.form.submit()" aria-label="{{ __('messages.language') }}" style="{{ $selectStyle }}">
                @foreach($locales as $locale => $label)
                    <option value="{{ $locale }}" @selected($currentLocale === $locale)>{{ $label }}</option>
                @endforeach
            </select>
            <span aria-hidden="true" style="{{ $iconStyle }}">&#9662;</span>
        </div>
    </form>
@endif
