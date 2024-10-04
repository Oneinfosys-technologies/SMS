<?php

return [
    'communication' => 'Communication',
    'config' => [
        'config' => 'Config',
    ],
    'types' => [
        'sms' => 'SMS',
        'email' => 'Email',
    ],
    'announcement' => [
        'announcement' => 'Announcement',
        'announcements' => 'Announcements',
        'module_title' => 'Manage all Announcements',
        'module_description' => 'List all Announcements',
        'props' => [
            'code_number' => 'Announcement #',
            'title' => 'Title',
            'description' => 'Description',
            'type' => 'Type',
            'is_public' => 'Is Public',
            'audience' => 'Audience',
            'published_at' => 'Published At',
        ],
        'config' => [
            'props' => [
                'number_prefix' => 'Announcement Number Prefix',
                'number_suffix' => 'Announcement Number Suffix',
                'number_digit' => 'Announcement Number Digit',
            ],
        ],
        'type' => [
            'type' => 'Announcement Type',
            'types' => 'Announcement Types',
            'module_title' => 'Manage all Announcement Types',
            'module_description' => 'List all Announcement Types',
            'props' => [
                'name' => 'Name',
                'description' => 'Description',
            ],
        ],
    ],
    'email' => [
        'email' => 'Email',
        'emails' => 'Emails',
        'module_title' => 'Manage all Emails',
        'module_description' => 'List all Emails',
        'no_recipient_found' => 'No recipient found.',
        'props' => [
            'audience' => 'Audience',
            'subject' => 'Subject',
            'content' => 'Content',
            'inclusion' => 'Inclusion',
            'exclusion' => 'Exclusion',
            'recipient' => 'Recipient',
        ],
    ],
];
