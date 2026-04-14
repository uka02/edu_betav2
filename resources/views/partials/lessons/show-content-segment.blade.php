@php
    $segment = $segment ?? [];
    $segmentIndex = (int) ($segmentIndex ?? 0);
    $displayIndex = (int) ($displayIndex ?? ($segmentIndex + 1));
    $segmentId = (int) ($segment['id'] ?? $displayIndex);
    $sectionId = $sectionId ?? ('content-' . $segmentId);
    $isActive = (bool) ($isActive ?? false);
    $segmentBlocks = is_array($segment['blocks'] ?? null) ? $segment['blocks'] : [];
    $segmentName = trim((string) ($segment['custom_name'] ?? ''));
    $segmentTitle = $segmentName !== ''
        ? $segmentName
        : __('lessons.content_blocks') . ' ' . $displayIndex;
@endphp

<article class="segment-container{{ $isActive ? ' active' : '' }}" data-segment="{{ $sectionId }}" id="lesson-segment-{{ $sectionId }}">
    <div class="segment-panel-head">
        <h2 class="segment-header">{{ $segmentTitle }}</h2>
        <div class="segment-meta">{{ count($segmentBlocks) }} {{ __('lessons.blocks') }}</div>
    </div>

    <div class="segment-content">
        @foreach($segmentBlocks as $blockIndex => $block)
            @include('partials.lessons.show-content-block', [
                'block' => $block,
                'segmentId' => $segmentId,
                'blockId' => (int) ($block['id'] ?? ($blockIndex + 1)),
                'quizId' => $block['id'] ?? "segment-{$segmentIndex}-block-{$blockIndex}",
                'progressKey' => 'segment-' . $segmentId . '-block-' . (int) ($block['id'] ?? ($blockIndex + 1)),
            ])
        @endforeach
    </div>
</article>
