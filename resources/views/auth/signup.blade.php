@include('auth.partials.signup-page', [
    'pageTitle' => __('auth.create_account'),
    'formTitle' => __('auth.create_your_account'),
    'formSubtitle' => __('auth.free_forever'),
    'googleButtonLabel' => __('auth.sign_up_with_google'),
    'signupRole' => 'learner',
    'panelTitle' => __('auth.your_dev_journey'),
    'panelSubtitle' => __('auth.join_thousands'),
    'perkItems' => [
        [
            'badge' => 'PR',
            'icon_class' => 'pi-1',
            'title' => __('auth.hands_on_projects'),
            'copy' => __('auth.hands_on_desc'),
        ],
        [
            'badge' => 'AI',
            'icon_class' => 'pi-2',
            'title' => __('auth.ai_code_review'),
            'copy' => __('auth.ai_code_desc'),
        ],
        [
            'badge' => 'LP',
            'icon_class' => 'pi-3',
            'title' => __('auth.structured_paths'),
            'copy' => __('auth.structured_desc'),
        ],
        [
            'badge' => 'CT',
            'icon_class' => 'pi-4',
            'title' => __('auth.certificates_badges'),
            'copy' => __('auth.certificates_desc'),
        ],
    ],
    'socialProofLead' => '8,400+ ' . __('auth.developers_learning'),
    'socialProofNote' => __('auth.rated_product_hunt'),
])
