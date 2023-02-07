<?php

use App\Helpers\Helpers; 

// Firebase API Key
define('FIREBASE_API_KEY', Helpers::settings()->firebase_key ?? '');
