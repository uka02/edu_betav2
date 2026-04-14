<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    @include('partials.app.theme-boot')
    <title><?php echo e(__('home.meta_title')); ?> - EduDev</title>
    <meta name="description" content="<?php echo e(__('home.meta_description')); ?>">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700;900&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
        @vite('resources/css/home.css')
</head>
<body>
    <div class="bg-orb bg-orb-1"></div>
    <div class="bg-orb bg-orb-2"></div>
    <div class="bg-grid"></div>

    <?php
        $formattedUserCount = number_format($userCount);
        $formattedCourseCount = number_format($courseCount);
        $formattedCertificateCount = number_format($certificateCount);
        $formattedSatisfactionLevel = $satisfactionLevel !== null ? $satisfactionLevel . '%' : '--';
    ?>

    <header class="site-header">
        <div class="shell header-row">
            <a href="<?php echo e(route('home')); ?>" class="brand">
                <span class="brand-icon">ED</span>
                <span class="brand-copy">
                    <span class="brand-name">EduDev</span>
                    <span class="brand-tag"><?php echo e(__('home.brand_tag')); ?></span>
                </span>
            </a>

            <nav class="site-nav">
                <a href="#roles"><?php echo e(__('home.nav_roles')); ?></a>
                <a href="#stats"><?php echo e(__('home.nav_stats')); ?></a>
                <a href="#topics"><?php echo e(__('home.nav_topics')); ?></a>
                <a href="#start"><?php echo e(__('home.nav_start')); ?></a>
            </nav>

            <div class="header-actions">
                <a href="<?php echo e(route('login')); ?>" class="btn-inline"><?php echo e(__('auth.sign_in')); ?></a>
                <a href="<?php echo e(route('signup')); ?>" class="btn-inline accent"><?php echo e(__('auth.create_account')); ?></a>
            </div>
        </div>
    </header>

    <main class="shell">

        <section class="hero">
            <div class="hero-main">
                <div class="eyebrow"><?php echo e(__('home.eyebrow')); ?></div>
                <div class="hero-title">
                    <?php echo e(__('home.headline_top')); ?>

                </div>
                <p class="hero-sub"><?php echo e(__('home.lead')); ?></p>
                <p class="hero-copy"><?php echo e(__('home.description')); ?></p>

                <div class="hero-actions">
                    <a href="<?php echo e(route('signup')); ?>" class="btn-primary"><?php echo e(__('home.cta_primary')); ?></a>
                    <a href="<?php echo e(route('signup')); ?>" class="btn-secondary"><?php echo e(__('home.cta_secondary')); ?></a>
                </div>

                <div class="role-pills" id="roles">
                    <span class="role-pill"><?php echo e(__('home.role_learners')); ?></span>
                    <span class="role-pill"><?php echo e(__('home.role_educators')); ?></span>
                    <span class="role-pill"><?php echo e(__('home.role_builders')); ?></span>
                </div>
            </div>

            <aside class="hero-side">
                <div class="hero-choice-stack" aria-label="<?php echo e(__('auth.choose_account_type')); ?>">
                    <a href="<?php echo e(route('signup.learner')); ?>" class="hero-choice-card accent">
                        <div class="hero-choice-label">
                            <span><?php echo e(__('auth.i_am_learner')); ?></span>
                            <span class="hero-choice-arrow">→</span>
                        </div>
                        <div class="hero-choice-copy"><?php echo e(__('auth.i_am_learner_copy')); ?></div>
                    </a>

                    <a href="<?php echo e(route('signup.educator')); ?>" class="hero-choice-card">
                        <div class="hero-choice-label">
                            <span><?php echo e(__('auth.i_am_educator')); ?></span>
                            <span class="hero-choice-arrow">→</span>
                        </div>
                        <div class="hero-choice-copy"><?php echo e(__('auth.i_am_educator_copy')); ?></div>
                    </a>

                    <a href="<?php echo e(route('login')); ?>" class="hero-choice-card">
                        <div class="hero-choice-label">
                            <span><?php echo e(__('auth.i_already_have_account')); ?></span>
                            <span class="hero-choice-arrow">→</span>
                        </div>
                        <div class="hero-choice-copy"><?php echo e(__('auth.i_already_have_account_copy')); ?></div>
                    </a>
                </div>
            </aside>
        </section>

        <section class="section" id="stats">
            <div class="section-head">
                <div class="section-kicker"><?php echo e(__('home.live_metrics')); ?></div>
                <h2 class="section-title"><?php echo e(__('home.stats_title')); ?></h2>
                <p class="section-copy"><?php echo e(__('home.stats_copy')); ?></p>
            </div>

            <div class="stats-grid">
                <article data-metric="members">
                    <div class="stat-value" data-stat-value="<?php echo e($userCount); ?>"><?php echo e($formattedUserCount); ?></div>
                    <div class="stat-label"><?php echo e(__('home.metric_members')); ?></div>
                    <div class="stat-note"><?php echo e(__('home.metric_members_note')); ?></div>
                </article>

                <article data-metric="courses">
                    <div class="stat-value" data-stat-value="<?php echo e($courseCount); ?>"><?php echo e($formattedCourseCount); ?></div>
                    <div class="stat-label"><?php echo e(__('home.metric_courses')); ?></div>
                    <div class="stat-note"><?php echo e(__('home.metric_courses_note')); ?></div>
                </article>

                <article data-metric="certificates">
                    <div class="stat-value" data-stat-value="<?php echo e($certificateCount); ?>"><?php echo e($formattedCertificateCount); ?></div>
                    <div class="stat-label"><?php echo e(__('home.metric_certificates')); ?></div>
                    <div class="stat-note"><?php echo e(__('home.metric_certificates_note')); ?></div>
                </article>

                <article data-metric="satisfaction">
                    <div
                        class="stat-value"
                        <?php if($satisfactionLevel !== null): ?>
                            data-stat-value="<?php echo e($satisfactionLevel); ?>"
                            data-stat-suffix="%"
                        <?php endif; ?>
                    ><?php echo e($formattedSatisfactionLevel); ?></div>
                    <div class="stat-label"><?php echo e(__('home.metric_satisfaction')); ?></div>
                    <div class="stat-note">
                        <?php echo e($satisfactionLevel !== null ? __('home.metric_satisfaction_note') : __('home.metric_satisfaction_empty')); ?>

                    </div>
                </article>
            </div>
        </section>

        <section class="section" id="topics">
            <div class="section-head">
                <div class="section-kicker"><?php echo e(__('home.pathways_label')); ?></div>
                <h2 class="section-title"><?php echo e(__('home.topics_title')); ?></h2>
                <p class="section-copy"><?php echo e(__('home.topics_copy')); ?></p>
            </div>

            <div class="topics-grid">
                <article class="topic-card">
                    <div class="topic-badge badge-cyber">CY</div>
                    <div class="topic-title"><?php echo e(__('home.path_cyber_title')); ?></div>
                    <div class="topic-copy"><?php echo e(__('home.path_cyber_copy')); ?></div>
                    <div class="topic-meta"><?php echo e(__('home.path_cyber_meta')); ?></div>
                    <a href="<?php echo e(route('login')); ?>" class="topic-link"><?php echo e(__('home.topic_cta')); ?></a>
                </article>

                <article class="topic-card">
                    <div class="topic-badge badge-network">NW</div>
                    <div class="topic-title"><?php echo e(__('home.path_network_title')); ?></div>
                    <div class="topic-copy"><?php echo e(__('home.path_network_copy')); ?></div>
                    <div class="topic-meta"><?php echo e(__('home.path_network_meta')); ?></div>
                    <a href="<?php echo e(route('login')); ?>" class="topic-link"><?php echo e(__('home.topic_cta')); ?></a>
                </article>

                <article class="topic-card">
                    <div class="topic-badge badge-python">PY</div>
                    <div class="topic-title"><?php echo e(__('home.path_python_title')); ?></div>
                    <div class="topic-copy"><?php echo e(__('home.path_python_copy')); ?></div>
                    <div class="topic-meta"><?php echo e(__('home.path_python_meta')); ?></div>
                    <a href="<?php echo e(route('login')); ?>" class="topic-link"><?php echo e(__('home.topic_cta')); ?></a>
                </article>
            </div>
        </section>

        <section class="section" id="start">
            <div class="section-head">
                <div class="section-kicker"><?php echo e(__('home.journey_kicker')); ?></div>
                <h2 class="section-title"><?php echo e(__('home.journey_title')); ?></h2>
                <p class="section-copy"><?php echo e(__('home.journey_copy')); ?></p>
            </div>

            <div class="audience-grid">
                <article class="audience-card">
                    <div class="audience-label"><?php echo e(__('home.role_learners')); ?></div>
                    <div class="audience-title"><?php echo e(__('home.learners_card_title')); ?></div>
                    <div class="audience-copy"><?php echo e(__('home.learners_card_copy')); ?></div>

                    <ul class="audience-list">
                        <li><?php echo e(__('home.feature_one_title')); ?></li>
                        <li><?php echo e(__('home.feature_two_title')); ?></li>
                        <li><?php echo e(__('home.feature_three_title')); ?></li>
                    </ul>

                    <a href="<?php echo e(route('signup.learner')); ?>" class="btn-primary"><?php echo e(__('home.learners_card_cta')); ?></a>
                </article>

                <article class="audience-card">
                    <div class="audience-label"><?php echo e(__('home.role_educators')); ?></div>
                    <div class="audience-title"><?php echo e(__('home.educators_card_title')); ?></div>
                    <div class="audience-copy"><?php echo e(__('home.educators_card_copy')); ?></div>

                    <ul class="audience-list">
                        <li><?php echo e(__('home.strip_one_title')); ?></li>
                        <li><?php echo e(__('home.strip_two_title')); ?></li>
                        <li><?php echo e(__('home.strip_three_title')); ?></li>
                    </ul>

                    <a href="<?php echo e(route('signup.educator')); ?>" class="btn-secondary"><?php echo e(__('home.educators_card_cta')); ?></a>
                </article>
            </div>
        </section>

        <section class="section">
            <div class="final-card">
                <div>
                    <h2 class="final-title"><?php echo e(__('home.final_title')); ?></h2>
                    <p class="final-copy"><?php echo e(__('home.final_copy')); ?></p>
                </div>

                <div class="final-actions">
                    <a href="<?php echo e(route('signup')); ?>" class="btn-primary"><?php echo e(__('home.cta_primary')); ?></a>
                    <a href="<?php echo e(route('signup')); ?>" class="btn-secondary"><?php echo e(__('home.cta_secondary')); ?></a>
                </div>
            </div>
        </section>
    </main>

    <footer class="shell site-footer">
        <?php echo e(__('home.footer_copy')); ?>

        <a href="<?php echo e(route('login')); ?>"><?php echo e(__('auth.sign_in')); ?></a>
        /
        <a href="<?php echo e(route('signup')); ?>"><?php echo e(__('auth.create_account')); ?></a>
    </footer>

    <?php echo $__env->make('partials.app.settings-panel', ['showFloatingTrigger' => true], array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>

    <script>
        (() => {
            const formatter = new Intl.NumberFormat();

            document.querySelectorAll('[data-stat-value]').forEach((element, index) => {
                const target = Number(element.dataset.statValue || 0);
                const suffix = element.dataset.statSuffix || '';
                const duration = 900 + (index * 120);
                const startTime = performance.now();

                if (!Number.isFinite(target) || target <= 0) {
                    element.textContent = `${formatter.format(target)}${suffix}`;
                    return;
                }

                const tick = (now) => {
                    const progress = Math.min((now - startTime) / duration, 1);
                    const eased = 1 - Math.pow(1 - progress, 3);
                    const current = Math.round(target * eased);

                    element.textContent = `${formatter.format(current)}${suffix}`;

                    if (progress < 1) {
                        window.requestAnimationFrame(tick);
                    }
                };

                window.requestAnimationFrame(tick);
            });

        })();
    </script>
</body>
</html>
<?php /**PATH C:\Users\Someone\Desktop\uka\eduDev\resources\views/home.blade.php ENDPATH**/ ?>
