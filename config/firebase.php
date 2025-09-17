<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Firebase Credentials
    |--------------------------------------------------------------------------
    |
    | Set the path to the Firebase credentials JSON file. This file should be
    | downloaded from the Firebase Console as the service account key.
    |
    */
    'credentials' => env('FIREBASE_CREDENTIALS', storage_path('app/firebase-credentials.json')),

    /*
    |--------------------------------------------------------------------------
    | Firebase Project ID
    |--------------------------------------------------------------------------
    |
    | Optionally, you can manually set your Firebase Project ID here, but if
    | the project ID is included in the credentials JSON, it will be detected
    | automatically.
    |
    */
    'project_id' => env('FIREBASE_PROJECT_ID', 'mytree-65ec7'),
];
