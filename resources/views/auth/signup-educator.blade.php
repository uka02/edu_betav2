@include('auth.partials.signup-page', [
    'pageTitle' => __('auth.educator_create_account'),
    'formTitle' => __('auth.educator_create_your_account'),
    'formSubtitle' => __('auth.educator_free_forever'),
    'googleButtonLabel' => __('auth.educator_sign_up_with_google'),
    'signupRole' => 'educator',
    'panelTitle' => __('auth.educator_journey'),
    'panelSubtitle' => __('auth.educator_join_thousands'),
    'perkItems' => [
        [
            'badge' => 'LS',
            'icon_class' => 'pi-1',
            'title' => __('auth.publish_lessons'),
            'copy' => __('auth.publish_lessons_desc'),
        ],
        [
            'badge' => 'TR',
            'icon_class' => 'pi-2',
            'title' => __('auth.track_learner_progress'),
            'copy' => __('auth.track_learner_progress_desc'),
        ],
        [
            'badge' => 'PW',
            'icon_class' => 'pi-3',
            'title' => __('auth.manage_learning_paths'),
            'copy' => __('auth.manage_learning_paths_desc'),
        ],
        [
            'badge' => 'CF',
            'icon_class' => 'pi-4',
            'title' => __('auth.certification_ready_delivery'),
            'copy' => __('auth.certification_ready_delivery_desc'),
        ],
    ],
    'socialProofLead' => '320+ ' . __('auth.educators_building_courses'),
    'socialProofNote' => __('auth.educator_social_proof'),
])
