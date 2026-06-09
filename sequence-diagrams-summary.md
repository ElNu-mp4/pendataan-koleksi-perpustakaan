# Sequence Diagrams Summary

This document summarizes all the PlantUML Sequence Diagrams generated for the Library Data Collection System based on the complete codebase analysis.

## Generated Sequence Diagrams

### Public/Frontend Flows

**SD-01 | Homepage View Flow** (`sd-01-homepage-view.puml`)
- **File**: `sd-01-homepage-view.puml`
- **Description**: Shows how visitors access the homepage and how the system loads and displays news, agenda, announcements, videos, and polling data
- **Key Components**: Main controller, Model_utama, multiple database queries with JOINs
- **Actors**: Visitor, Browser, Web Server, Router, Main Controller, Model, Database

**SD-02 | News Browsing Flow** (`sd-02-news-browsing.puml`)
- **File**: `sd-02-news-browsing.puml`
- **Description**: Covers news listing, search functionality, and detailed news viewing with read counters
- **Key Components**: Berita controller, pagination, search queries, read counter updates
- **Actors**: Visitor, Browser, Web Server, Router, Berita Controller, Model, Database

**SD-03 | Agenda Browsing Flow** (`sd-03-agenda-browsing.puml`)
- **File**: `sd-03-agenda-browsing.puml`
- **Description**: Shows agenda listing and detail viewing with read counters
- **Key Components**: Agenda controller, pagination, detail view with counter updates
- **Actors**: Visitor, Browser, Web Server, Router, Agenda Controller, Model, Database

**SD-04 | Albums Gallery Browsing Flow** (`sd-04-albums-gallery.puml`)
- **File**: `sd-04-albums-gallery.puml`
- **Description**: Covers photo gallery browsing, album details, and frame view for slideshows
- **Key Components**: Albums controller, gallery pagination, hit counters
- **Actors**: Visitor, Browser, Web Server, Router, Albums Controller, Model, Database

**SD-05 | Polling Voting Flow** (`sd-05-polling-voting.puml`)
- **File**: `sd-05-polling-voting.puml`
- **Description**: Shows poll display, voting process, and results viewing with session-based vote prevention
- **Key Components**: Polling controller, session management, vote validation
- **Actors**: Visitor, Browser, Web Server, Router, Polling Controller, Model, Database

### Admin/Backend Flows

**SD-06 | Admin Login Flow** (`sd-06-admin-login.puml`)
- **File**: `sd-06-admin-login.puml`
- **Description**: Covers administrator authentication with captcha validation and session management
- **Key Components**: Administrator controller, MD5 password hashing, session setup
- **Actors**: Administrator, Browser, Web Server, Router, Administrator Controller, Model, Database

**SD-07 | Password Reset Flow** (`sd-07-password-reset.puml`)
- **File**: `sd-07-password-reset.puml`
- **Description**: Shows forgot password process with email-based reset tokens and secure password updates
- **Key Components**: Administrator controller, email library, token generation and validation
- **Actors**: Administrator, Browser, Web Server, Router, Administrator Controller, Model, Database, Email Library

**SD-08 | Admin Dashboard Flow** (`sd-08-admin-dashboard.puml`)
- **File**: `sd-08-admin-dashboard.puml`
- **Description**: Displays admin dashboard loading with statistics and recent activities
- **Key Components**: Administrator controller, multiple count queries, recent data loading
- **Actors**: Administrator, Browser, Web Server, Router, Administrator Controller, Model, Database

### Library Data Management Flows

**SD-09 | Library Data Entry Flow** (`sd-09-library-data-entry.puml`)
- **File**: `sd-09-library-data-entry.puml`
- **Description**: Covers library data collection form submission with validation and AJAX processing
- **Key Components**: Pendataan controller, form validation, captcha verification, AJAX responses
- **Actors**: Librarian, Browser, Web Server, Router, Pendataan Controller, Model, Database

**SD-10 | Library Data Recap Flow** (`sd-10-library-data-recap.puml`)
- **File**: `sd-10-library-data-recap.puml`
- **Description**: Shows data recap viewing with pagination, filtering, and AJAX data loading
- **Key Components**: Pendataan controller, pagination, filtering, AJAX data retrieval
- **Actors**: Librarian, Browser, Web Server, Router, Pendataan Controller, Model, Database

**SD-11 | Library Statistics Flow** (`sd-11-library-statistics.puml`)
- **File**: `sd-11-library-statistics.puml`
- **Description**: Covers statistical chart generation with various demographic and temporal analyses
- **Key Components**: Pendataan controller, aggregation queries, chart data formatting, AJAX responses
- **Actors**: Librarian, Browser, Web Server, Router, Pendataan Controller, Model, Database

**SD-12 | Library Export Flow** (`sd-12-library-export.puml`)
- **File**: `sd-12-library-export.puml`
- **Description**: Shows data export functionality with filtering, field selection, and rate limiting
- **Key Components**: Pendataan controller, CSV generation, rate limiting, chunked downloads
- **Actors**: Librarian, Browser, Web Server, Router, Pendataan Controller, Model, Database

## Technical Architecture Overview

### Framework & Technologies
- **CodeIgniter 3 MVC Framework**: Controllers handle requests, Models manage data, Views render templates
- **MySQL Database**: Stores all application data with proper relationships
- **PHP**: Server-side processing with session management
- **jQuery/AJAX**: Dynamic frontend interactions
- **File-based Caching**: Performance optimization
- **Email Library**: Notification system
- **Pagination Library**: Data listing management
- **Upload Library**: File handling capabilities

### Security Features
- **Session Management**: User authentication and state management
- **Captcha Validation**: Prevents automated submissions
- **Input Sanitization**: XSS and injection protection
- **Rate Limiting**: Prevents export abuse
- **Password Hashing**: MD5 encryption for passwords
- **Access Control**: Admin-only sections

### Database Tables Involved
- `users` - User accounts and authentication
- `berita` - News articles
- `agenda` - Event schedules
- `album` - Photo galleries
- `gallery` - Individual gallery images
- `poling` - Polling questions and answers
- `pendataan` - Library data collection records
- `halamanstatis` - Static pages
- `kategori` - Content categories
- `menu` - Navigation menus

### Key Design Patterns
- **MVC Architecture**: Clear separation of concerns
- **Active Record Pattern**: Database operations through models
- **Template System**: Consistent UI rendering
- **AJAX Integration**: Dynamic content loading
- **Pagination Pattern**: Large dataset handling
- **Export Pattern**: Data download functionality

## Usage Instructions

1. **View Diagrams**: Open any `.puml` file in VS Code with PlantUML extension
2. **Generate Images**: Use PlantUML to export diagrams as PNG/PDF
3. **Modify Diagrams**: Edit PlantUML syntax directly in the files
4. **Integration**: These diagrams can be included in documentation or presentations

## Status & Validation

### Syntax Validation ✅
All PlantUML sequence diagrams have been validated and syntax errors have been corrected:
- **Issue 1**: Incorrect `deactivate Controller` statements were placed after message sends instead of proper activate/deactivate pairs
- **Issue 2**: Invalid `autonumber` directive causing parsing issues
- **Issue 3**: Title lines contained pipe symbols (`|`) which are invalid in PlantUML titles
- **Issue 4**: Multiline participant labels with `\n` escapes causing syntax errors
- **Issue 5**: Quoted participant labels causing parsing issues
- **Issue 6**: Invalid `left to right direction` directive for sequence diagrams
- **Issue 7**: Incorrect `deallocate` statements instead of `deactivate`

### Resolution Applied
- Removed all incorrect deactivate statements for Controller participants
- Removed autonumber directives
- Replaced pipe symbols in titles with hyphens
- Simplified participant labels to plain identifiers
- Removed invalid direction directive
- Corrected deallocate to deactivate statements

### File Status
- **Total Files**: 12 sequence diagram files
- **Syntax Status**: ✅ All files validated and corrected
- **Compilation Status**: ✅ All files compile successfully with PlantUML
- **Ready for Use**: ✅ All diagrams can be viewed and exported

## File Organization
All sequence diagram files are located in the project root directory with the naming convention:
- `sd-XX-description.puml`

Where XX is the sequence number and description summarizes the flow.