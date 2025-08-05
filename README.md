# Code Change Logger

A lightweight PHP library for tracking code changes in files and storing diffs in MySQL.

## Features
- Custom diff engine
- Database logging of changes
- Version history

## Installation
```bash
    composer require webcrafters/code-change-logger
``` 

##  Database setup
- 1: Import the schema
- 2: In your MySQL client or phpMyAdmin::
```bash
  SOURCE sql/schema.sql;
``` 

# Usage
```bash
require 'vendor/autoload.php';
$conn = new mysqli('localhost','user','pass','database');

use CodeChangeLogger\ChangeTracker;
$tracker = new ChangeTracker($conn);

// Track and save change:
$file = '/path/to/file.php';
$newContent = file_get_contents($file);
// ... modify $newContent ...
$tracker->trackCodeChange($file, $newContent, '/page/url', 'username');
```

# LICENSE
```text
Copyright (c) 2025 Matthias Gielen, Webcrafters
All rights reserved. Redistribution or modification of this software
is prohibited without explicit written permission of Webcrafters.