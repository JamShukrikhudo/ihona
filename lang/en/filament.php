<?php

return [
    'nav_groups' => [
        'sales_lettings' => 'Sales & lettings',
        'people_relationships' => 'People & relationships',
        'property_management' => 'Property management',
        'marketing_portals' => 'Marketing & portals',
        'insights_tools' => 'Insights & tools',
        'instructions_media' => 'Instructions & media',
        'organisation' => 'Organisation',
        'property_configuration' => 'Property configuration',
        'platform_settings' => 'Platform settings',
        'integrations_api' => 'Integrations & API',
        'operations_diagnostics' => 'Operations & diagnostics',
        'account_support' => 'Account & support',
        'browse_discover' => 'Browse & discover',
        'my_activity' => 'My activity',
    ],

    'resources' => [
        'user' => ['singular' => 'User', 'plural' => 'Users'],
        'team' => ['singular' => 'Team', 'plural' => 'Teams'],
        'status_definition' => ['singular' => 'Status Definition', 'plural' => 'Status Definitions'],
        'branch' => ['singular' => 'Branch', 'plural' => 'Branches'],
        'territory' => ['singular' => 'Territory', 'plural' => 'Territories'],
        'agency' => ['singular' => 'Agency', 'plural' => 'Agencies'],
        'instruction' => ['singular' => 'Instruction', 'plural' => 'Instructions'],
        'letting' => ['singular' => 'Letting', 'plural' => 'Lettings'],
        'listing' => ['singular' => 'Listing', 'plural' => 'Listings'],
        'marketing_campaign' => ['singular' => 'Marketing Campaign', 'plural' => 'Marketing Campaigns'],
        'media_document' => ['singular' => 'Media Document', 'plural' => 'Media Documents'],
        'offer' => ['singular' => 'Offer', 'plural' => 'Offers'],
        'onthemarket_sync' => ['singular' => 'OnTheMarket Sync', 'plural' => 'OnTheMarket Syncs'],
        'portal_report' => ['singular' => 'Portal Report', 'plural' => 'Portal Reports'],
        'party' => ['singular' => 'Party', 'plural' => 'Parties'],
        'property_category' => ['singular' => 'Property Category', 'plural' => 'Property Categories'],
        'property_template' => ['singular' => 'Property Template', 'plural' => 'Property Templates'],
        'property' => ['singular' => 'Property', 'plural' => 'Properties'],
        'management_record' => ['singular' => 'Management Record', 'plural' => 'Management Records'],
        'rightmove_sync' => ['singular' => 'Rightmove Sync', 'plural' => 'Rightmove Syncs'],
        'sales_progression' => ['singular' => 'Sales Progression', 'plural' => 'Sales Progressions'],
        'viewing' => ['singular' => 'Viewing', 'plural' => 'Viewings'],
        'zoopla_sync' => ['singular' => 'Zoopla Sync', 'plural' => 'Zoopla Syncs'],
        'rental_application' => ['singular' => 'Rental Application', 'plural' => 'Rental Applications'],
        'news_article' => ['singular' => 'News Article', 'plural' => 'News Articles'],
        'inspection' => ['singular' => 'Inspection', 'plural' => 'Inspections'],
        'maintenance_request' => ['singular' => 'Maintenance Request', 'plural' => 'Maintenance Requests'],
        'vendor_quote' => ['singular' => 'Vendor Quote', 'plural' => 'Vendor Quotes'],
        'work_order' => ['singular' => 'Work Order', 'plural' => 'Work Orders'],
        'match_profile' => ['singular' => 'Match Profile', 'plural' => 'Match Profiles'],
        'valuation' => ['singular' => 'Valuation', 'plural' => 'Valuations'],
        'property_saved_search' => ['singular' => 'Saved Search', 'plural' => 'Saved Searches'],
        'activity_log' => ['singular' => 'Audit Entry', 'plural' => 'Audit Log'],
    ],

    'app_nav' => [
        'browse_properties' => 'Browse properties',
        'search_properties' => 'Search properties',
        'news_updates' => 'News & updates',
        'calculators' => 'Calculators',
        'saved_properties' => 'Saved properties',
        'contact_support' => 'Contact support',
        'profile' => 'Profile',
    ],

    'stub_page_note' => 'The Filament adapter is installed. Domain operations remain governed by the matching core module.',

    'admin_overview' => [
        'heading' => 'Workspace health',
        'team_members' => 'Team members',
        'properties' => 'Properties',
        'pending_applications' => 'Pending applications',
        'open_maintenance' => 'Open maintenance',
        'upcoming_viewings' => 'Upcoming viewings',
        'workspaces' => 'Workspaces',
    ],

    'property' => [
        'fields' => [
            'title' => 'Title',
            'status' => 'Status',
            'address' => 'Address',
            'branch_id' => 'Branch',
            'territory_id' => 'Territory',
            'description' => 'Description',
            'internal_notes' => 'Internal notes',
            'price' => 'Price',
            'currency' => 'Currency',
            'bedrooms' => 'Bedrooms',
            'bathrooms' => 'Bathrooms',
            'reception_rooms' => 'Reception rooms',
            'area_sqft' => 'Area, sqm',
            'year_built' => 'Year built',
            'property_category_id' => 'Category',
            'property_template_id' => 'Listing template',
            'postal_code' => 'Postal code',
            'country' => 'Country',
            'tenure' => 'Tenure',
            'council_tax_band' => 'Council tax band',
            'energy_rating' => 'Energy rating',
            'energy_score' => 'Energy score',
            'walkability_score' => 'Walkability score',
            'transit_score' => 'Transit score',
            'bike_score' => 'Bike score',
            'virtual_tour_url' => 'Virtual tour URL',
            'virtual_tour_provider' => 'Virtual tour provider',
            'live_tour_available' => 'Live tour available',
            'model_3d_url' => '3D model URL',
            'floor_plan_image' => 'Floor plan image',
            'is_featured' => 'Featured',
            'holographic_enabled' => 'Holographic tour enabled',
            'holographic_tour_url' => 'Holographic tour URL',
            'holographic_provider' => 'Holographic provider',
            'features' => 'Features',
            'insurance_policy_id' => 'Insurance policy ID',
            'insurance_coverage_amount' => 'Insurance coverage amount',
            'insurance_premium' => 'Insurance premium',
            'insurance_expiry_date' => 'Insurance expiry date',
            'deal_type' => 'Deal type',
        ],
        'deal_types' => [
            'sale' => 'Sale',
            'rent' => 'Rent',
        ],
        'statuses' => [
            'draft' => 'Draft',
            'available' => 'Available',
            'under_offer' => 'Under offer',
            'sold' => 'Sold',
            'let' => 'Let',
            'withdrawn' => 'Withdrawn',
            'For Sale' => 'For Sale',
            'For Rent' => 'For Rent',
            'to_let' => 'To let',
            'let_agreed' => 'Let agreed',
            'sold_stc' => 'Sold (subject to contract)',
            'sstc' => 'Sold (subject to contract)',
            'exchanged' => 'Exchanged',
            'archived' => 'Archived',
            'coming_soon' => 'Coming soon',
            'Rented' => 'Rented',
        ],
    ],

    'listing' => [
        'fields' => [
            'title' => 'Title',
            'status' => 'Status',
            'price' => 'Price',
            'created_at' => 'Created at',
            'available_from' => 'Available from',
        ],
        'statuses' => [
            'draft' => 'Draft',
            'ready' => 'Ready',
            'published' => 'Published',
            'suspended' => 'Suspended',
            'withdrawn' => 'Withdrawn',
        ],
    ],

    'valuation' => [
        'fields' => [
            'subject' => 'Subject',
            'status' => 'Status',
            'valued_amount' => 'Valued amount',
            'fee_amount' => 'Fee amount',
            'currency' => 'Currency',
            'comparable_data' => 'Comparable data',
            'recommendation' => 'Recommendation',
            'scheduled_at' => 'Scheduled at',
            'follow_up_at' => 'Follow-up date',
            'created_at' => 'Created at',
        ],
        'statuses' => [
            'draft' => 'Draft',
            'scheduled' => 'Scheduled',
            'completed' => 'Completed',
            'converted' => 'Converted',
            'cancelled' => 'Cancelled',
        ],
    ],

    'match_profile' => [
        'fields' => [
            'subject' => 'Subject',
            'score' => 'Match score',
            'party_id' => 'Party (ID)',
            'requirements' => 'Requirements',
            'affordability' => 'Affordability',
            'preferences' => 'Preferences',
            'alerts' => 'Alerts',
            'feedback' => 'Feedback',
            'exclusions' => 'Exclusions',
            'created_at' => 'Created at',
        ],
    ],

    'offer' => [
        'fields' => [
            'subject' => 'Subject',
            'amount' => 'Amount',
            'currency' => 'Currency',
            'terms' => 'Terms',
            'qualification' => 'Buyer qualification',
            'negotiation' => 'Negotiation',
            'proof' => 'Proof of funds',
            'conditions' => 'Conditions',
            'created_at' => 'Created at',
        ],
        'statuses' => [
            'draft' => 'Draft',
            'submitted' => 'Submitted',
            'countered' => 'Countered',
            'accepted' => 'Accepted',
            'rejected' => 'Rejected',
            'withdrawn' => 'Withdrawn',
        ],
    ],

    'media_document' => [
        'fields' => [
            'kind' => 'Kind',
            'path' => 'File path',
            'title' => 'Title',
            'sort_order' => 'Sort order',
            'retention_until' => 'Retention until',
            'created_at' => 'Created at',
        ],
        'kinds' => [
            'photo' => 'Photo',
            'floorplan' => 'Floorplan',
            'video' => 'Video',
            'certificate' => 'Certificate',
            'brochure' => 'Brochure',
            'document' => 'Document',
        ],
    ],

    'viewing' => [
        'fields' => [
            'subject' => 'Subject',
            'status' => 'Status',
            'starts_at' => 'Starts at',
            'ends_at' => 'Ends at',
            'access' => 'Access',
            'accompaniment' => 'Accompaniment',
            'reminders' => 'Reminders',
            'feedback' => 'Feedback',
            'created_at' => 'Created at',
        ],
        'statuses' => [
            'requested' => 'Requested',
            'confirmed' => 'Confirmed',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
            'no_show' => 'No show',
        ],
    ],

    'portal_report' => [
        'fields' => [
            'portal' => 'Portal',
            'report_type' => 'Report type',
            'property_id' => 'Property (ID)',
            'listing_id' => 'Listing (ID)',
            'status' => 'Status',
            'error' => 'Error',
            'generated_at' => 'Generated at',
        ],
    ],

    'marketing_campaign' => [
        'fields' => [
            'name' => 'Name',
            'channel' => 'Channel',
            'property_id' => 'Property (ID)',
            'listing_id' => 'Listing (ID)',
            'status' => 'Status',
            'notes' => 'Notes',
            'created_at' => 'Created at',
        ],
    ],

    'sales_progression' => [
        'fields' => [
            'subject' => 'Subject',
            'property_id' => 'Property (ID)',
            'offer_id' => 'Offer (ID)',
            'status' => 'Status',
            'notes' => 'Notes',
            'created_at' => 'Created at',
        ],
    ],

    'vendor_quote' => [
        'fields' => [
            'vendor_id' => 'Vendor (ID)',
            'property_id' => 'Property (ID)',
            'work_description' => 'Work description',
            'quote_amount' => 'Quote amount',
            'quote_date' => 'Quote date',
            'valid_until' => 'Valid until',
            'status' => 'Status',
        ],
        'statuses' => [
            'pending' => 'Pending',
            'accepted' => 'Accepted',
            'rejected' => 'Rejected',
            'expired' => 'Expired',
            'withdrawn' => 'Withdrawn',
        ],
    ],

    'work_order' => [
        'fields' => [
            'property_id' => 'Property (ID)',
            'vendor_id' => 'Vendor (ID)',
            'title' => 'Title',
            'description' => 'Description',
            'work_type' => 'Work type',
            'status' => 'Status',
            'scheduled_date' => 'Scheduled date',
        ],
        'statuses' => [
            'pending' => 'Pending',
            'approved' => 'Approved',
            'scheduled' => 'Scheduled',
            'in_progress' => 'In progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ],
    ],

    'party' => [
        'fields' => [
            'type' => 'Type',
            'name' => 'Name',
            'email' => 'Email',
            'phone' => 'Phone',
            'created_at' => 'Created at',
        ],
        'types' => [
            'applicant' => 'Applicant',
            'buyer' => 'Buyer',
            'vendor' => 'Vendor',
            'landlord' => 'Landlord',
            'tenant' => 'Tenant',
            'solicitor' => 'Solicitor',
            'contractor' => 'Contractor',
            'tourist' => 'Tourist',
            'guide' => 'Guide',
        ],
    ],

    'rental_application' => [
        'fields' => [
            'property_id' => 'Property (ID)',
            'party_id' => 'Party (ID)',
            'status' => 'Status',
            'employment_status' => 'Employment status',
            'annual_income' => 'Annual income',
            'desired_move_in_date' => 'Desired move-in date',
        ],
        'statuses' => [
            'draft' => 'Draft',
            'submitted' => 'Submitted',
            'under_review' => 'Under review',
            'approved' => 'Approved',
            'rejected' => 'Rejected',
        ],
    ],

    'maintenance_request' => [
        'fields' => [
            'property_id' => 'Property (ID)',
            'title' => 'Title',
            'description' => 'Description',
            'priority' => 'Priority',
            'status' => 'Status',
            'requested_date' => 'Requested date',
        ],
        'priorities' => [
            'low' => 'Low',
            'normal' => 'Normal',
            'high' => 'High',
            'urgent' => 'Urgent',
        ],
        'statuses' => [
            'pending' => 'Pending',
            'in_progress' => 'In progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ],
    ],

    'inspection' => [
        'fields' => [
            'property_id' => 'Property (ID)',
            'type' => 'Type',
            'status' => 'Status',
            'scheduled_at' => 'Scheduled at',
            'notes' => 'Notes',
        ],
        'types' => [
            'routine' => 'Routine',
            'check_in' => 'Check-in',
            'check_out' => 'Check-out',
            'mid_tenancy' => 'Mid-tenancy',
        ],
        'statuses' => [
            'scheduled' => 'Scheduled',
            'in_progress' => 'In progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ],
    ],

    'user_form' => [
        'fields' => [
            'name' => 'Name',
            'email' => 'Email',
            'password' => 'Password',
            'email_verified_at' => 'Email verified at',
            'roles' => 'Roles',
            'created_at' => 'Created at',
        ],
    ],

    'zoopla_sync' => [
        'fields' => [
            'listing_id' => 'Listing (ID)',
            'property_id' => 'Property (ID)',
            'external_id' => 'External ID',
            'status' => 'Status',
            'last_synced_at' => 'Last synced at',
        ],
    ],

    'rightmove_sync' => [
        'fields' => [
            'listing_id' => 'Listing (ID)',
            'property_id' => 'Property (ID)',
            'external_id' => 'External ID',
            'status' => 'Status',
            'last_synced_at' => 'Last synced at',
        ],
    ],

    'onthemarket_sync' => [
        'fields' => [
            'listing_id' => 'Listing (ID)',
            'property_id' => 'Property (ID)',
            'external_id' => 'External ID',
            'status' => 'Status',
            'last_synced_at' => 'Last synced at',
        ],
    ],

    'management_record' => [
        'fields' => [
            'subject' => 'Subject',
            'capability' => 'Capability',
            'status' => 'Status',
            'failure_reason' => 'Failure reason',
            'created_at' => 'Created at',
        ],
        'capabilities' => [
            'rent_schedule' => 'Rent schedule',
            'statements' => 'Statements',
            'inspections' => 'Inspections',
            'compliance' => 'Compliance',
            'maintenance' => 'Maintenance',
            'contractors' => 'Contractors',
            'owner_approvals' => 'Owner approvals',
        ],
        'statuses' => [
            'draft' => 'Draft',
            'in_progress' => 'In progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ],
    ],

    'news_article' => [
        'fields' => [
            'title' => 'Title',
            'slug' => 'Slug (URL)',
            'content' => 'Content',
            'published_at' => 'Published at',
            'is_featured' => 'Featured',
        ],
    ],

    'letting' => [
        'fields' => [
            'subject' => 'Subject',
            'capability' => 'Capability',
            'status' => 'Status',
            'failure_reason' => 'Failure reason',
            'created_at' => 'Created at',
        ],
        'capabilities' => [
            'applications' => 'Applications',
            'referencing' => 'Referencing',
            'deposits' => 'Deposits',
            'agreements' => 'Agreements',
            'move_in_out' => 'Move in/out',
            'renewals' => 'Renewals',
            'rent_changes' => 'Rent changes',
            'notices' => 'Notices',
        ],
        'statuses' => [
            'draft' => 'Draft',
            'in_progress' => 'In progress',
            'completed' => 'Completed',
            'cancelled' => 'Cancelled',
        ],
    ],

    'instruction' => [
        'fields' => [
            'subject' => 'Subject',
            'status' => 'Status',
            'approved_at' => 'Approved at',
            'withdrawn_at' => 'Withdrawn at',
            'created_at' => 'Created at',
        ],
        'statuses' => [
            'draft' => 'Draft',
            'pending_approval' => 'Pending approval',
            'approved' => 'Approved',
            'withdrawn' => 'Withdrawn',
            'rejected' => 'Rejected',
        ],
    ],

    'status_definition_form' => [
        'fields' => [
            'entity' => 'Entity',
            'key' => 'Key',
            'label' => 'Label',
            'active' => 'Active',
        ],
    ],

    'branch_form' => [
        'fields' => [
            'name' => 'Name',
            'code' => 'Code',
            'email' => 'Email',
            'phone' => 'Phone',
            'created_at' => 'Created at',
        ],
    ],

    'territory' => [
        'fields' => [
            'name' => 'Name',
            'code' => 'Code',
            'boundary' => 'Boundary (JSON)',
            'created_at' => 'Created at',
        ],
    ],

    'team_form' => [
        'fields' => [
            'name' => 'Name',
            'user_id' => 'Owner',
            'personal_team' => 'Personal team',
            'created_at' => 'Created at',
        ],
    ],

    'property_template' => [
        'fields' => [
            'name' => 'Name',
            'content' => 'Template content',
            'created_at' => 'Created at',
        ],
    ],

    'property_saved_search' => [
        'fields' => [
            'name' => 'Name',
            'criteria' => 'Search criteria',
            'user_id' => 'Saved by',
            'created_at' => 'Created at',
        ],
    ],

    'property_category' => [
        'fields' => [
            'name' => 'Name',
            'slug' => 'Slug (URL)',
            'created_at' => 'Created at',
        ],
    ],

    'agency_form' => [
        'fields' => [
            'name' => 'Name',
            'code' => 'Code',
            'active' => 'Active',
            'created_at' => 'Created at',
        ],
    ],

    'pages' => [
        'activity_comments' => 'Activity and Comments',
        'analytics_core' => 'Analytics Core',
        'analytics_google' => 'Analytics Google',
        'analytics_meta' => 'Analytics Meta',
        'api_access' => 'API Access',
        'application_core' => 'Application Core',
        'currency_context' => 'Currency Context',
        'developer_experience' => 'Developer Experience',
        'feature_flags' => 'Feature Flags',
        'files_media' => 'Files and Media',
        'import_export' => 'Import and Export',
        'integrations' => 'Integrations',
        'jetstream_bridge' => 'Jetstream Bridge',
        'localization' => 'Localization',
        'notifications' => 'Notifications',
        'observability' => 'Observability',
        'profiles' => 'Profiles',
        'scheduler_queues' => 'Scheduler and Queues',
        'search' => 'Search',
        'two_factor_authentication' => 'Two-Factor Authentication',
        'webhooks' => 'Webhooks',
        'foundation_operations' => 'Foundation Operations',
        'manage_site_settings' => 'Site Settings',
    ],

    'audit_log' => [
        'fields' => [
            'created_at' => 'Date',
            'event' => 'Event',
            'subject' => 'Subject',
            'causer' => 'Changed by',
            'tenant' => 'Team',
            'correlation_id' => 'Request ID',
            'changes' => 'Changes',
            'hash_chain' => 'Verification chain',
            'previous_hash' => 'Previous hash',
            'record_hash' => 'Record hash',
        ],
        'hash_chain_note' => 'Each record carries a hash of the one before it — retroactively altering any row in the log breaks the chain, and that break is visible.',
    ],
];
