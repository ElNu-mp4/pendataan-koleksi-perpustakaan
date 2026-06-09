# Library Data Collection System - Use Case Diagram Documentation

## Overview
This document describes the complete Use Case Diagram for the **Library Data Collection System** (Sistem Pendataan Koleksi Perpustakaan) managed by Dinas Kearsipan dan Perpustakaan Jawa Tengah.

---

## System Scope

**System Name:** Library Data Collection System  
**Purpose:** Centralized platform for collecting, validating, and reporting library collection data across regional government libraries in Jawa Tengah  
**Built With:** PHP CodeIgniter 3, MySQL Database, jQuery/AJAX Frontend

---

## Actors

### 1. **Admin**
- **Role:** System administrator and supervisory staff
- **Responsibilities:**
  - Manage user accounts (create, edit, delete)
  - Configure survey status and periods
  - Manage survey questions
  - Monitor system-wide statistics
  - View and export all collected data
  - Manage rate limits and security configurations
  - Access all features including restricted admin functions
- **Access Level:** Full system access

### 2. **Operator/Staff**
- **Role:** Library staff or data entry personnel
- **Responsibilities:**
  - View and complete library data survey forms
  - Submit library collection data
  - View recap of submitted data
  - View statistical charts and reports
  - Export data for local use
  - Track submission history
- **Access Level:** Restricted to own submissions and public reports

### 3. **Guest/Viewer**
- **Role:** Public or read-only viewer (optional stakeholder)
- **Responsibilities:**
  - View publicly available statistics and charts
  - Access published reports
  - Monitor aggregate library metrics
- **Access Level:** Read-only access to statistics and recap pages

---

## Functional Modules

### 1. **Authentication Module**

| Use Case | Actor(s) | Purpose |
|----------|----------|---------|
| **Login** | All | Authenticate user identity and establish session |
| **Logout** | All | Terminate user session securely |
| **Manage Session** | Admin | Monitor and control active user sessions |

**Key Features:**
- XSS protection and security validation
- Session tracking for rate limiting
- IP address logging
- Session timeout management

---

### 2. **Library Data Entry Module**

| Use Case | Actor(s) | Purpose |
|----------|----------|---------|
| **View Survey Form** | Operator, Admin | Display dynamic form with all survey questions |
| **Input Library Data** | Operator, Admin | Allow users to fill in library collection data |
| **Validate Input Data** | System (automatic) | Verify data types, required fields, and constraints |
| **Check Rate Limit** | System (automatic) | Prevent spam submissions (1 per hour per session) |
| **Prevent Double Submission** | System (automatic) | Reject duplicate submissions within 30 seconds |
| **Submit Data** | Operator, Admin | Commit data to database with transaction support |
| **View Confirmation Page** | Operator, Admin | Display success message after submission |

**Validation Rules:**
- Required field checking
- Numeric range validation (min/max values)
- Text length constraints (max 1000 chars)
- File upload restrictions
- Dropdown/radio/checkbox option validation

**Security Features:**
- Honeypot spam detection
- Rate limiting per session (1 per hour)
- Rate limiting per IP (50 per hour)
- Double submission prevention (30-second window)
- Input sanitization and XSS prevention
- Database transaction support for consistency

---

### 3. **Recap & Statistics Module**

| Use Case | Actor(s) | Purpose |
|----------|----------|---------|
| **View Recap Page** | Operator, Admin, Guest | Display list of all submitted data entries |
| **Filter Data by Year** | Operator, Admin, Guest | Show only data from selected year (mandatory) |
| **Filter Data by Date Range** | Operator, Admin | Show data between specific dates (optional) |
| **Filter Data by Reference Number** | Operator, Admin | Search by library reference number (optional) |
| **Sort Data** | Operator, Admin | Order by date, reference number, or response count |
| **View Detail Data** | Operator, Admin | Show complete answers for specific submission |
| **View Chart Statistics** | Operator, Admin, Guest | Display visual statistics and KPI charts |
| **Export to Excel** | Operator, Admin | Download filtered data as CSV file (streaming) |
| **Check Export Rate Limit** | System (automatic) | Limit exports (3 per session, 20 per IP per hour) |

**Supported Charts:**
1. **Stat Cards** - Summary metrics (total reports, titles printed, titles digital, members)
2. **Collection Chart** - Current collection by type (printed vs. digital, titles vs. copies)
3. **Growth Chart** - Collection growth 2025 vs. 2026
4. **Visitor Chart** - On-site, online, and technology access visitors
5. **Utilization Chart** - Printed collection usage (read on-site vs. borrowed)
6. **Type Distribution Chart** - Distribution by library type

**Performance Features:**
- Data caching (5 minutes for recap, 1 hour for details)
- Pagination (10-100 records per page)
- Streaming export for unlimited records
- Optimized queries with proper indexing

---

### 4. **Reporting & Visualization Module**

| Use Case | Actor(s) | Purpose |
|----------|----------|---------|
| **Generate Stat Cards** | System | Create KPI summary cards |
| **Generate Collection Chart** | System | Build collection composition chart |
| **Generate Growth Chart** | System | Compare year-over-year growth |
| **Generate Visitor Chart** | System | Track visitor and access statistics |
| **Generate Utilization Chart** | System | Monitor collection usage metrics |
| **Generate Type Distribution Chart** | System | Show library type distribution |

**Technical Details:**
- Real-time calculation with SQL aggregation
- Automatic caching (5-minute TTL)
- Support for multiple filters (year, date range, reference number)
- Numeric-only validation for aggregation
- REGEXP-based data validation

---

### 5. **User Management Module**

| Use Case | Actor(s) | Purpose |
|----------|----------|---------|
| **View User List** | Admin | Display all registered system users |
| **Create User Account** | Admin | Register new operator or admin staff |
| **Edit User Account** | Admin | Modify user profile and permissions |
| **Delete User Account** | Admin | Remove user access from system |
| **Assign Roles** | Admin | Set user role (Admin or Operator) |

**User Types:**
- **Admin:** Full system access, user management, configuration
- **Operator:** Data entry and viewing own submissions
- **Guest:** Read-only access to public reports

---

### 6. **System Configuration Module**

| Use Case | Actor(s) | Purpose |
|----------|----------|---------|
| **Configure Survey Status** | Admin | Activate/deactivate survey (aktif/nonaktif) |
| **Set Active Year** | Admin | Define which year's data is being collected |
| **Manage Survey Questions** | Admin | Add, edit, enable/disable questions and options |
| **Set Survey Period** | Admin | Define survey start and end dates |
| **Configure Rate Limits** | Admin | Adjust submission and export throttling |

**Configuration Storage:**
- `config_pendataan` table for survey status
- `pertanyaan_koleksi` table for questions
- `opsi_pertanyaan_koleksi` table for question options
- Cache-based rate limit tracking

---

## Relationship Types

### **Include (<<include>>)** - Mandatory Sub-Functions
These use cases **always** execute when the parent use case is triggered:

```
- Input Library Data includes Validate Input Data
- Submit Data includes Validate Input Data
- Submit Data includes Check Rate Limit
- Submit Data includes Prevent Double Submission
- Submit Data includes View Confirmation Page
- View Recap Page includes Filter Data by Year
- View Recap Page includes Sort Data
- View Chart Statistics includes Generate Stat Cards
- View Chart Statistics includes Generate Collection Chart
- (and other chart generations)
- Export to Excel includes Check Export Rate Limit
```

### **Extend (<<extend>>)** - Optional/Conditional Behavior
These use cases **may** execute under certain conditions:

```
- View Recap Page extends Filter Data by Date Range (optional)
- View Recap Page extends Filter Data by Reference Number (optional)
- View Recap Page extends View Detail Data (conditional)
- View Recap Page extends Export to Excel (conditional)
```

---

## Security & Validation Framework

### **Input Security**
- XSS (Cross-Site Scripting) prevention via `xss_clean()`
- SQL Injection prevention via parameterized queries
- CSRF (Cross-Site Request Forgery) protection via session tokens
- File upload validation (size, type, storage location)

### **Rate Limiting**
| Limit Type | Threshold | Duration | Purpose |
|-----------|-----------|----------|---------|
| Session Submission | 1 per session | 1 hour | Prevent duplicate entries |
| IP Submission | 50 per IP | 1 hour | Prevent bulk spam |
| Double Submit | Within 30 seconds | 30 seconds | Prevent accidental duplicates |
| Session Export | 3 per session | 1 hour | Prevent data scraping |
| IP Export | 20 per IP | 1 hour | Prevent mass downloads |

### **Data Validation**
- Required field enforcement
- Numeric range validation (min/max)
- Text length constraints
- Dropdown option validation
- Regular expression validation
- Honeypot field detection

---

## Cache Strategy

| Cache Key | TTL | Purpose |
|-----------|-----|---------|
| `questions_v2` | 1 day | Survey questions with options |
| `years` | 1 hour | Available years list |
| `rekap_data_*` | 5 minutes | Recap page data by filters |
| `detail_*` | 1 hour | Individual submission details |
| `chart_stats_v2_*` | 5 minutes | Statistics and charts |
| `rate_limit_*` | 1 hour | Rate limit counters |
| `config` | 30 minutes | System configuration |

---

## Data Flow Diagram

```
┌─────────────────────────────────────────────────────────────┐
│                     OPERATOR/ADMIN                          │
└────────────────────┬────────────────────────────────────────┘
                     │
         ┌───────────┴───────────┐
         │                       │
    ┌────▼─────┐          ┌─────▼─────┐
    │   View   │          │  Input &  │
    │   Form   │          │  Submit   │
    └────┬─────┘          └─────┬─────┘
         │                       │
         │         ┌─────────────┴─────────────┐
         │         │                           │
    ┌────▼──────┐  │   ┌──────────────────┐    │
    │ Database  │  │   │ Validation &     │    │
    │ Questions │  │   │ Rate Limiting    │    │
    │ & Options │  │   └────────┬─────────┘    │
    └───────────┘  │            │              │
                   │   ┌────────▼────────┐    │
         ┌─────────┼───►Insert into DB◄──┘    │
         │         │   └────────┬────────┘    │
         │         │            │             │
    ┌────▼─────────┴────┐   ┌───▼──────┐     │
    │ Core Database     │   │Confirmation
    │ - identitas_input │   │Page       │     │
    │ - jawaban_koleksi │   └───┬──────┘     │
    └────┬──────────────┘       │             │
         │                       │             │
    ┌────▼──────────────────┐   │             │
    │   VIEW RECAP          │◄──┘             │
    │   - Filter by Year    │                │
    │   - Filter by Date    │                │
    │   - Filter by Number  │                │
    │   - Sort              │                │
    └────┬──────────────────┘                │
         │                                   │
    ┌────┴──────────────────────────────────┘
    │
    ├──────────────────────────────┐
    │                              │
┌───▼─────────┐          ┌────────▼─────┐
│ Statistics  │          │  Export CSV  │
│ & Charts    │          │  (Streaming) │
└─────────────┘          └──────────────┘
```

---

## Feature Highlights

### ✅ **Performance Optimizations**
- Multi-level caching strategy
- Database query optimization with GROUP BY, JOIN, and COUNT
- Streaming CSV export for unlimited records
- Pagination (10-100 records per page)
- Session-based rate limiting

### ✅ **Security Features**
- Honeypot spam detection
- Rate limiting per session and IP
- Double submission prevention
- Input sanitization (XSS, SQL injection)
- Session timeout
- CSRF token protection

### ✅ **Data Integrity**
- Database transaction support for atomic operations
- Validation at form and database levels
- Numeric-only regex validation for aggregations
- Consistent data types and constraints

### ✅ **User Experience**
- Dynamic form generation from database
- Real-time validation feedback
- Comprehensive filtering and sorting
- Multi-chart statistics dashboard
- Bulk export capability (up to 1000 items)
- Detail view for individual submissions

---

## Future Enhancement Opportunities

1. **Print Functionality** - Add "Print Report" use case for PDF generation
2. **Audit Logging** - Track user actions for compliance
3. **Email Notifications** - Alert admins of new submissions
4. **Advanced Analytics** - Trend analysis, anomaly detection
5. **API Integration** - RESTful API for third-party integrations
6. **Data Visualization** - More chart types (line graphs, heatmaps)
7. **Role-Based Access Control (RBAC)** - Fine-grained permissions
8. **Two-Factor Authentication** - Enhanced login security
9. **Data Backup & Recovery** - Automated backup procedures
10. **Mobile Responsive Design** - Full mobile support

---

## Related Diagrams

- **Activity Diagrams:** [activity-diagram.puml](activity-diagram.puml) - Detailed process flows
- **ERD Diagram:** [erd.puml](erd.puml) - Database schema and relationships
- **Sequence Diagrams:** [sequence-diagram.puml](sequence-diagram.puml) - Interaction sequences
- **System Flowchart:** [system-flowchart.puml](system-flowchart.puml) - High-level system flow
- **BPMN Diagram:** [bpmn-admin.puml](bpmn-admin.puml) - Business process notation

---

## Compliance & Standards

- **UML 2.x Standard:** Fully compliant use case notation
- **OWASP Security:** Input validation, rate limiting, XSS prevention
- **Accessibility:** Form labels, semantic HTML, ARIA support
- **Performance:** Query optimization, caching, streaming export

---

**Generated:** May 5, 2026  
**System:** Library Data Collection Platform (Dinas Kearsipan dan Perpustakaan Jawa Tengah)  
**Version:** 1.0

