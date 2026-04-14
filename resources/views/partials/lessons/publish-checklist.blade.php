@php
    $publishChecklistItems = [
        'title',
        'type',
        'subject',
        'duration',
        'media',
        'segments',
        'completion',
    ];
@endphp

<div class="publish-checklist" id="publishChecklistCard">
    <div class="publish-checklist-header">
        <div>
            <div class="publish-checklist-title">{{ __('lessons.publish_checklist') }}</div>
            <p class="publish-checklist-subtitle">{{ __('lessons.publish_checklist_hint') }}</p>
        </div>
        <div class="publish-checklist-summary" id="publishChecklistSummary">
            {{ __('lessons.publish_checklist_remaining', ['count' => count($publishChecklistItems)]) }}
        </div>
    </div>

    @error('publish_checklist')
        <p class="form-error publish-checklist-error">{{ $message }}</p>
    @enderror

    <div class="publish-checklist-items" id="publishChecklistItems">
        @foreach ($publishChecklistItems as $item)
            <div class="publish-checklist-item" data-checklist-item="{{ $item }}">
                <span class="publish-checklist-bullet" aria-hidden="true"></span>
                <span class="publish-checklist-label">{{ __('lessons.publish_checklist_' . $item) }}</span>
                <span class="publish-checklist-state">{{ __('lessons.publish_checklist_missing') }}</span>
            </div>
        @endforeach
    </div>
</div>
