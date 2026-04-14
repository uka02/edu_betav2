<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.app.theme-boot')
    <title><?php echo e(__('dashboard.dashboard')); ?> - EduDev</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        @vite('resources/css/dashboard.css')
</head>
<body>
<?php
    $dashboardUser = Auth::user();
    $isEducatorDashboard = $isEducatorDashboard ?? $dashboardUser->isEducator();
    $freeLessons = $freeLessons ?? ($certificateCount ?? 0);
    $featuredLessons = $featuredLessons ?? collect();
    $trendingLessons = $trendingLessons ?? collect();
    $userLessons = $userLessons ?? ($continueLearningLessons ?? collect());
    $hasLessonSearch = filled($lessonSearch ?? '');
    $learnerDiscoveryLessons = $hasLessonSearch ? $featuredLessons : $trendingLessons;
    $learnerDiscoveryTitle = $hasLessonSearch
        ? __('dashboard.search_results_for', ['term' => $lessonSearch])
        : __('dashboard.trending_lessons');
    $learnerDiscoveryEmptyTitle = $hasLessonSearch
        ? __('lessons.no_catalog_results')
        : __('dashboard.community_empty');
    $learnerDiscoveryEmptySub = $hasLessonSearch
        ? __('lessons.no_catalog_results_description')
        : __('lessons.explore_subtitle');
    $activityLessons = $isEducatorDashboard ? ($educatorActivityLessons ?? collect()) : $userLessons;
    $heroSubtitle = $isEducatorDashboard ? __('dashboard.ready_to_teach') : __('dashboard.ready_to_learn');
    $heroProgressLabel = $isEducatorDashboard ? __('dashboard.learner_completion') : __('dashboard.overall_progress');
    $highlightMetricCount = $isEducatorDashboard ? ($activeLearnersCount ?? 0) : ($dailyStreak ?? 0);
    $highlightMetricLabel = $isEducatorDashboard ? __('dashboard.active_learners') : __('dashboard.day_streak');
    $statThreeValue = $isEducatorDashboard ? ($issuedCertificatesCount ?? 0) : $freeLessons;
    $statThreeLabel = $isEducatorDashboard ? __('dashboard.certificates_issued') : __('dashboard.certificates');
    $statThreeChip = $isEducatorDashboard
        ? (($issuedCertificatesCount ?? 0) > 0 ? '+ ' . ($issuedCertificatesCount ?? 0) : __('dashboard.start'))
        : ($freeLessons > 0 ? '+ ' . $freeLessons : __('dashboard.earned'));
    $statFourValue = $isEducatorDashboard ? ($learnersReachedCount ?? 0) : ($totalLearningHoursDisplay ?? $totalLearningHours);
    $statFourLabel = $isEducatorDashboard ? __('dashboard.learners_reached') : __('dashboard.total_learning_time');
    $statFourChip = $isEducatorDashboard
        ? (($learnersReachedCount ?? 0) > 0 ? '+ ' . __('dashboard.active') : __('dashboard.start'))
        : ($totalLearningHours > 0 ? '+ ' . __('dashboard.active') : __('dashboard.start'));
    $activityTitle = $isEducatorDashboard ? __('dashboard.recent_learner_activity') : __('dashboard.continue_learning');
    $activityLinkLabel = $isEducatorDashboard ? __('dashboard.view_all_lessons') : __('dashboard.view_all_courses');
    $emptyActivityCopy = $isEducatorDashboard ? __('dashboard.activity_empty') : __('dashboard.continue_empty');
    $emptyActivityActionRoute = $isEducatorDashboard ? route('lessons.create') : route('lessons.index');
    $emptyActivityActionLabel = $isEducatorDashboard ? __('lessons.create_lesson') : __('dashboard.browse_courses');
?>

<div class="shell">

    <!-- SIDEBAR -->
    <aside class="sidebar">
        <?php echo $__env->make('partials.app.sidebar-brand', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

        <nav class="sb-nav">
            <?php echo $__env->make('partials.app.nav-links', [
                'activeKey' => 'dashboard',
                'showSettings' => true,
                'settingsId' => 'settingsBtn',
                'settingsGroupStyle' => 'margin-top:8px;',
            ], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </nav>

        <div class="sb-foot">
            <?php echo $__env->make('partials.app.user-summary', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
        </div>
    </aside>

    <!-- MAIN -->
    <div class="main-col">

        <header class="topbar">
            <span class="tb-title"><?php echo e($isEducatorDashboard ? __('dashboard.educator_dashboard') : __('dashboard.dashboard')); ?></span>
            <div class="tb-sep"></div>
            <div class="tb-right">
                <div class="tb-btn" role="button" tabindex="0" aria-label="<?php echo e(__('messages.notifications_soon')); ?>" data-tooltip="<?php echo e(__('messages.notifications_soon')); ?>">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 8A6 6 0 0 0 6 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                        <path d="M13.73 21a2 2 0 0 1-3.46 0"/>
                    </svg>
                    <div class="pip"></div>
                </div>
                <?php echo $__env->make('partials.app.logout-button', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
            </div>
        </header>

        <div class="content">

            <?php if(session('success')): ?>
                <div class="alert-ok">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            <!-- HERO -->
            <div class="hero">
                <div class="hero-l">
                    <div class="hero-date"><?php echo e(now()->translatedFormat('l, F j')); ?> - <?php echo e(now()->year); ?></div>
                    <div class="hero-title">
                        <?php echo e(now()->hour < 12 ? __('dashboard.good_morning') : (now()->hour < 18 ? __('dashboard.good_afternoon') : __('dashboard.good_evening'))); ?>,
                        <em><?php echo e(explode(' ', $dashboardUser->name)[0]); ?></em>
                    </div>
                    <div class="hero-sub"><?php echo e($heroSubtitle); ?></div>
                </div>
                <div class="hero-r">
                    <div class="pill-row">
                        <div class="pill pill-g">
                            <div class="dot-pulse"></div>
                            <?php echo e(__('dashboard.active_session')); ?>

                        </div>
                        <?php if($highlightMetricCount > 0): ?>
                            <div class="pill pill-a">
                                <?php if($isEducatorDashboard): ?>
                                    <?php echo e($highlightMetricCount); ?> <?php echo e($highlightMetricLabel); ?>

                                <?php else: ?>
                                <?php echo e($dailyStreak); ?> <?php echo e(__('dashboard.day_streak') ?? 'day streak'); ?>

                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="prog-box">
                        <div class="prog-head">
                            <span><?php echo e($heroProgressLabel); ?></span>
                            <span class="prog-pct"><?php echo e($progressPercentage); ?>%</span>
                        </div>
                        <div class="prog-track">
                            <div class="prog-fill" style="width:<?php echo e($progressPercentage); ?>%;"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- STATS -->
            <div class="stat-row">
                <div class="stat-card">
                    <div class="sc-top">
                        <div class="sc-ico ico-b">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                                <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                            </svg>
                        </div>
                        <span class="sc-chip <?php echo e($publishedLessons > 0 ? 'chip-g' : 'chip-b'); ?>">
                            <?php echo e($publishedLessons > 0 ? '+ ' . __('dashboard.active') : __('dashboard.start')); ?>

                        </span>
                    </div>
                    <div class="sc-val"><?php echo e($publishedLessons); ?></div>
                    <div class="sc-lbl"><?php echo e($isEducatorDashboard ? __('dashboard.published_lessons') : __('dashboard.courses_enrolled')); ?></div>
                </div>

                <div class="stat-card">
                    <div class="sc-top">
                        <div class="sc-ico ico-g">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polyline points="20 6 9 17 4 12"/>
                            </svg>
                        </div>
                        <span class="sc-chip <?php echo e($totalLessonsCreated > 0 ? 'chip-g' : 'chip-b'); ?>">
                            <?php echo e($totalLessonsCreated > 0 ? '+ ' . $totalLessonsCreated : __('dashboard.start')); ?>

                        </span>
                    </div>
                    <div class="sc-val"><?php echo e($totalLessonsCreated); ?></div>
                    <div class="sc-lbl"><?php echo e($isEducatorDashboard ? __('dashboard.lessons_created') : __('dashboard.lessons_completed')); ?></div>
                </div>

                <div class="stat-card">
                    <div class="sc-top">
                        <div class="sc-ico ico-a">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/>
                            </svg>
                        </div>
                        <span class="sc-chip <?php echo e($statThreeValue > 0 ? 'chip-g' : 'chip-b'); ?>">
                            <?php echo e($statThreeChip); ?>

                        </span>
                    </div>
                    <div class="sc-val"><?php echo e($statThreeValue); ?></div>
                    <div class="sc-lbl"><?php echo e($statThreeLabel); ?></div>
                </div>

                <div class="stat-card">
                    <div class="sc-top">
                        <div class="sc-ico ico-b">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>
                            </svg>
                        </div>
                        <span class="sc-chip <?php echo e((($isEducatorDashboard ? ($learnersReachedCount ?? 0) : $totalLearningHours) > 0) ? 'chip-g' : 'chip-b'); ?>">
                            <?php echo e($statFourChip); ?>

                        </span>
                    </div>
                    <div class="sc-val"><?php echo e($statFourValue); ?><?php if (! ($isEducatorDashboard)): ?><sup><?php echo e(__('dashboard.hours_short')); ?></sup><?php endif; ?></div>
                    <div class="sc-lbl"><?php echo e($statFourLabel); ?></div>
                </div>
            </div>

            <?php if($isEducatorDashboard): ?>
                <!-- YOUR LESSONS -->
                <div class="rise d2">
                    <div class="sec-h">
                        <div class="sec-ttl"><span class="dot dot-b"></span><?php echo e(__('dashboard.your_lessons')); ?></div>
                        <a href="<?php echo e(route('lessons.index')); ?>" class="sec-lnk"><?php echo e(__('lessons.see_all')); ?> -></a>
                    </div>

                    <?php if($featuredLessons->count() > 0): ?>
                        <div class="lesson-grid">
                            <?php $__currentLoopData = $featuredLessons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lesson): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a href="<?php echo e(route('lessons.show', $lesson)); ?>" class="lcard">
                                    <div class="lc-thumb">
                                        <?php if($lesson->thumbnail): ?>
                                            <img src="<?php echo e(Storage::url($lesson->thumbnail)); ?>" alt="<?php echo e($lesson->title); ?>">
                                        <?php else: ?>
                                            <span class="lc-ph"><?php echo e($lesson->type); ?></span>
                                        <?php endif; ?>
                                        <span class="lc-tag"><?php echo e(ucfirst($lesson->type)); ?></span>
                                    </div>
                                    <div class="lc-body">
                                        <div class="lc-meta">
                                            <span class="lc-auth"><?php echo e(__('lessons.published')); ?></span>
                                            <span class="lc-st <?php echo e($lesson->is_published ? 'st-pub' : 'st-drf'); ?>">
                                                <?php echo e($lesson->is_published ? __('lessons.published') : __('lessons.draft')); ?>

                                            </span>
                                        </div>
                                        <div class="lc-title"><?php echo e(Str::limit($lesson->title, 52)); ?></div>
                                        <div class="lc-foot">
                                            <span class="lc-dur">
                                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                                <?php echo e($lesson->duration_minutes ?? '0'); ?> <?php echo e(__('lessons.min') ?? 'min'); ?>

                                            </span>
                                            <span class="lc-price <?php echo e($lesson->is_free ? 'pr-free' : 'pr-paid'); ?>">
                                                <?php echo e($lesson->is_free ? __('lessons.free') : __('lessons.paid')); ?>

                                            </span>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <div class="empty">
                            <div class="empty-ico">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            </div>
                            <div class="empty-ttl"><?php echo e(__('lessons.no_lessons')); ?></div>
                            <div class="empty-sub"><?php echo e(__('lessons.create_first_lesson')); ?></div>
                            <a href="<?php echo e(route('lessons.create')); ?>" class="btn-cta">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                <?php echo e(__('lessons.create_lesson')); ?>

                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <!-- LEARNER DISCOVERY -->
                <div class="rise d2">
                    <div class="sec-h">
                        <div class="sec-ttl"><span class="dot dot-g"></span><?php echo e($learnerDiscoveryTitle); ?></div>
                        <div class="sec-actions">
                            <form method="GET" action="<?php echo e(route('dashboard')); ?>" class="lesson-search-form">
                                <label class="lesson-search-box" for="lessonDiscoverySearch">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <circle cx="11" cy="11" r="7"></circle>
                                        <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                                    </svg>
                                    <input
                                        id="lessonDiscoverySearch"
                                        class="lesson-search-input"
                                        type="search"
                                        name="lesson_search"
                                        value="<?php echo e($lessonSearch); ?>"
                                        placeholder="<?php echo e(__('dashboard.search_lessons_placeholder')); ?>"
                                        aria-label="<?php echo e(__('dashboard.search_lessons')); ?>"
                                    >
                                </label>
                                <button type="submit" class="lesson-search-btn"><?php echo e(__('dashboard.search_lessons')); ?></button>
                                <?php if($hasLessonSearch): ?>
                                    <a href="<?php echo e(route('dashboard')); ?>" class="lesson-search-clear"><?php echo e(__('dashboard.clear_search')); ?></a>
                                <?php endif; ?>
                            </form>
                            <a href="<?php echo e(route('lessons.index')); ?>" class="sec-lnk"><?php echo e(__('lessons.see_all')); ?> -></a>
                        </div>
                    </div>

                    <?php if($learnerDiscoveryLessons->count() > 0): ?>
                        <div class="lesson-grid">
                            <?php $__currentLoopData = $learnerDiscoveryLessons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lesson): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a href="<?php echo e(route('lessons.show', $lesson)); ?>" class="lcard">
                                    <div class="lc-thumb">
                                        <?php if($lesson->thumbnail): ?>
                                            <img src="<?php echo e(Storage::url($lesson->thumbnail)); ?>" alt="<?php echo e($lesson->title); ?>">
                                        <?php else: ?>
                                            <span class="lc-ph"><?php echo e($lesson->type); ?></span>
                                        <?php endif; ?>
                                        <span class="lc-tag"><?php echo e(ucfirst($lesson->type)); ?></span>
                                    </div>
                                    <div class="lc-body">
                                        <div class="lc-meta">
                                            <span class="lc-auth"><?php echo e($lesson->user->name ?? __('lessons.published')); ?></span>
                                        </div>
                                        <div class="lc-title"><?php echo e(Str::limit($lesson->title, 52)); ?></div>
                                        <div class="lc-foot">
                                            <span class="lc-dur">
                                                <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                                <?php echo e($lesson->duration_minutes ?? '0'); ?> <?php echo e(__('lessons.min') ?? 'min'); ?>

                                            </span>
                                            <span class="lc-price <?php echo e($lesson->is_free ? 'pr-free' : 'pr-paid'); ?>">
                                                <?php echo e($lesson->is_free ? __('lessons.free') : __('lessons.paid')); ?>

                                            </span>
                                        </div>
                                    </div>
                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php else: ?>
                        <div class="empty">
                            <div class="empty-ico">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                            </div>
                            <div class="empty-ttl"><?php echo e($learnerDiscoveryEmptyTitle); ?></div>
                            <div class="empty-sub"><?php echo e($learnerDiscoveryEmptySub); ?></div>
                            <a href="<?php echo e(route('lessons.index')); ?>" class="btn-cta">
                                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                <?php echo e(__('dashboard.browse_courses')); ?>

                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <!-- TRENDING LESSONS -->
            <?php if($isEducatorDashboard && $trendingLessons->count() > 0): ?>
            <div class="rise d3" style="margin-top:22px;">
                <div class="sec-h">
                    <div class="sec-ttl"><span class="dot dot-g"></span><?php echo e(__('dashboard.trending_lessons')); ?></div>
                    <a href="<?php echo e(route('lessons.index')); ?>" class="sec-lnk"><?php echo e(__('lessons.see_all')); ?> -></a>
                </div>
                <div class="lesson-grid">
                    <?php $__currentLoopData = $trendingLessons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lesson): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <a href="<?php echo e(route('lessons.show', $lesson)); ?>" class="lcard">
                            <div class="lc-thumb">
                                <?php if($lesson->thumbnail): ?>
                                    <img src="<?php echo e(Storage::url($lesson->thumbnail)); ?>" alt="<?php echo e($lesson->title); ?>">
                                <?php else: ?>
                                    <span class="lc-ph"><?php echo e($lesson->type); ?></span>
                                <?php endif; ?>
                                <span class="lc-tag"><?php echo e(ucfirst($lesson->type)); ?></span>
                            </div>
                            <div class="lc-body">
                                <div class="lc-meta">
                                    <span class="lc-auth"><?php echo e($lesson->user->name); ?></span>
                                </div>
                                <div class="lc-title"><?php echo e(Str::limit($lesson->title, 52)); ?></div>
                                <div class="lc-foot">
                                    <span class="lc-dur">
                                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                                        <?php echo e($lesson->duration_minutes ?? '0'); ?> <?php echo e(__('lessons.min') ?? 'min'); ?>

                                    </span>
                                    <span class="lc-price <?php echo e($lesson->is_free ? 'pr-free' : 'pr-paid'); ?>">
                                        <?php echo e($lesson->is_free ? __('lessons.free') : __('lessons.paid')); ?>

                                    </span>
                                </div>
                            </div>
                        </a>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- BOTTOM GRID -->
            <div class="bot-grid rise d4" style="margin-top:22px;">

                <div class="card">
                    <div class="card-h">
                        <span class="card-ttl"><?php echo e($activityTitle); ?></span>
                        <a href="<?php echo e(route('lessons.index')); ?>" class="card-lnk"><?php echo e($activityLinkLabel); ?></a>
                    </div>
                    <div class="c-list">
                        <?php if($activityLessons->count() > 0): ?>
                            <?php $__currentLoopData = $activityLessons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $lesson): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <a href="<?php echo e(route('lessons.show', $lesson)); ?>" class="c-row">
                                    <div class="cr-thumb">
                                        <?php if($lesson->thumbnail): ?>
                                            <img src="<?php echo e(Storage::url($lesson->thumbnail)); ?>" alt="<?php echo e($lesson->title); ?>">
                                        <?php else: ?>
                                            <span class="cr-txt"><?php echo e(strtoupper(substr($lesson->type, 0, 2))); ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <div class="cr-info">
                                        <div class="cr-bar"><div class="cr-fill" style="width:<?php echo e($isEducatorDashboard ? ($lesson->average_progress_percent ?? 0) : ($lesson->progress_percent ?? 0)); ?>%;"></div></div>
                                        <?php if($isEducatorDashboard): ?>
                                            <div class="cr-sub"><?php echo e($lesson->learner_count ?? 0); ?> <?php echo e(__("dashboard.learners")); ?> - <?php echo e($lesson->issued_certificate_count ?? 0); ?> <?php echo e(__("dashboard.certificates")); ?></div>
                                        <?php else: ?>
                                            <div class="cr-sub"><?php echo e($lesson->difficulty ? ucfirst($lesson->difficulty) : __("lessons.all_levels")); ?> - <?php echo e($lesson->duration_minutes ?? "0"); ?> <?php echo e(__("lessons.min") ?? "min"); ?></div>
                                        <?php endif; ?>
                                    </div>
                                    <span class="cr-tag <?php echo e($isEducatorDashboard ? (($lesson->learner_count ?? 0) > 0 ? 'pr-free' : 'pr-paid') : ($lesson->is_free ? 'pr-free' : 'pr-paid')); ?>">
                                        <?php if($isEducatorDashboard): ?>
                                            <?php echo e($lesson->average_progress_percent ?? 0); ?>% <?php echo e(__('dashboard.avg_progress')); ?>

                                        <?php else: ?>
                                            <?php echo e($lesson->is_free ? __('lessons.free') : __('lessons.paid')); ?>

                                        <?php endif; ?>
                                    </span>
                                </a>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <div style="padding:28px 16px;text-align:center;color:var(--muted);font-size:12.5px;">
                                <?php echo e($emptyActivityCopy); ?><br><br>
                                <a href="<?php echo e($emptyActivityActionRoute); ?>" class="btn-cta" style="font-size:12px;"><?php echo e($emptyActivityActionLabel); ?></a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <div style="display:flex;flex-direction:column;gap:12px;">
                    <div class="card">
                        <div class="pf-block">
                            <div class="pf-av">
                                <?php if($dashboardUser->avatar): ?>
                                    <img src="<?php echo e($dashboardUser->avatar); ?>" alt="avatar">
                                <?php else: ?>
                                    <?php echo e(strtoupper(substr($dashboardUser->name, 0, 1))); ?>

                                <?php endif; ?>
                            </div>
                            <div class="pf-name"><?php echo e($dashboardUser->name); ?></div>
                            <div class="pf-email"><?php echo e($dashboardUser->email); ?></div>
                            <div class="pf-chip <?php echo e($isEducatorDashboard ? 'pc-g' : 'pc-e'); ?>">
                                <?php echo e($isEducatorDashboard ? __('dashboard.role_educator') : __('dashboard.role_learner')); ?>

                            </div>
                            <div class="pf-chip <?php echo e($dashboardUser->google_id ? 'pc-g' : 'pc-e'); ?>">
                                <?php if($dashboardUser->google_id): ?>
                                    <svg width="10" height="10" viewBox="0 0 48 48">
                                        <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                                        <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                                        <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                                        <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.18 1.48-4.97 2.31-8.16 2.31-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                                    </svg>
                                    <?php echo e(__('dashboard.google_account')); ?>

                                <?php else: ?>
                                    <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                                    <?php echo e(__('dashboard.email_account')); ?>

                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="q-list">
                            <?php if($isEducatorDashboard): ?>
                                <a href="<?php echo e(route('lessons.create')); ?>" class="qb">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                                    <?php echo e(__('lessons.create_lesson')); ?>

                                </a>
                                <a href="<?php echo e(route('lessons.index')); ?>" class="qb">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/></svg>
                                    <?php echo e(__('dashboard.my_lessons')); ?>

                                </a>
                            <?php else: ?>
                                <a href="<?php echo e(route('lessons.index')); ?>" class="qb">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>
                                    <?php echo e(__('dashboard.browse_courses')); ?>

                                </a>
                                <a href="<?php echo e(route('certificates.index')); ?>" class="qb">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>
                                    <?php echo e(__('dashboard.view_certificates')); ?>

                                </a>
                            <?php endif; ?>
                            <button class="qb" id="settingsBtn2">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 0 1 0 14.14M4.93 4.93a10 10 0 0 0 0 14.14"/></svg>
                                <?php echo e(__('dashboard.settings')); ?>

                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div><!-- /content -->
    </div><!-- /main-col -->
</div><!-- /shell -->

<!-- FOOTER -->
<footer class="footer">
    <div class="ft-grid">
        <div class="ft-col">
            <h4><?php echo e(__('messages.platform')); ?></h4>
            <ul>
                <li><a href="<?php echo e(route('dashboard')); ?>"><?php echo e(__('messages.why_choose')); ?></a></li>
                <li><a href="<?php echo e(route('lessons.index')); ?>"><?php echo e(__('lessons.browse')); ?></a></li>
                <li><a href="#"><?php echo e(__('messages.pricing')); ?></a></li>
                <li><a href="#"><?php echo e(__('messages.community')); ?></a></li>
            </ul>
        </div>
        <div class="ft-col">
            <h4><?php echo e(__('messages.learn')); ?></h4>
            <ul>
                <li><a href="#"><?php echo e(__('messages.getting_started')); ?></a></li>
                <li><a href="#"><?php echo e(__('messages.best_practices')); ?></a></li>
                <li><a href="#"><?php echo e(__('messages.tutorials')); ?></a></li>
                <li><a href="#"><?php echo e(__('messages.documentation')); ?></a></li>
            </ul>
        </div>
        <div class="ft-col">
            <h4><?php echo e(__('messages.company')); ?></h4>
            <ul>
                <li><a href="#"><?php echo e(__('messages.about')); ?></a></li>
                <li><a href="#"><?php echo e(__('messages.blog')); ?></a></li>
                <li><a href="#"><?php echo e(__('messages.careers')); ?></a></li>
                <li><a href="#"><?php echo e(__('messages.contact')); ?></a></li>
            </ul>
        </div>
        <div class="ft-col">
            <h4><?php echo e(__('messages.legal')); ?></h4>
            <ul>
                <li><a href="#"><?php echo e(__('messages.privacy')); ?></a></li>
                <li><a href="#"><?php echo e(__('messages.terms')); ?></a></li>
                <li><a href="#"><?php echo e(__('messages.security')); ?></a></li>
                <li><a href="#"><?php echo e(__('messages.cookies')); ?></a></li>
            </ul>
        </div>
    </div>
    <div class="ft-bot">
        <span class="ft-copy">&copy; <?php echo e(date('Y')); ?> EduDev. <?php echo e(__('messages.all_rights_reserved')); ?></span>
        <div class="ft-socials">
            <a href="https://twitter.com" title="X">X</a>
            <a href="https://linkedin.com" title="LinkedIn">in</a>
            <a href="https://github.com" title="GitHub">GH</a>
        </div>
    </div>
</footer>

<?php echo $__env->make('partials.app.settings-panel', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

</body>
</html>
<?php /**PATH C:\Users\Someone\Desktop\uka\eduDev\resources\views/auth/dashboard.blade.php ENDPATH**/ ?>
