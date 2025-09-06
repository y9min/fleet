# Fleet Management System

## Overview

This is a comprehensive Fleet Management System (FMS) built with Laravel and designed as a Progressive Web Application (PWA). The system provides complete fleet operations management including vehicle tracking, booking management, driver assignments, customer management, and real-time communication features. It supports both admin panel functionality and customer-facing frontend interfaces with multi-language support and modern web technologies.

## User Preferences

Preferred communication style: Simple, everyday language.

## System Architecture

### Frontend Architecture
- **PWA Implementation**: Full Progressive Web App support with service workers, web manifest, and offline capabilities
- **Multi-Interface Design**: Separate frontend for customers and admin panel for fleet operators
- **UI Framework**: Bootstrap-based responsive design with AdminLTE for admin interface
- **Real-time Features**: WebSocket integration using Pusher for live chat and notifications
- **Interactive Components**: jQuery-based dynamic forms, DataTables for data management, and Chart.js for analytics

### Backend Architecture
- **Framework**: Laravel 10.x with PHP 8.0+ as the core backend framework
- **Authentication**: Laravel Passport for API authentication and Laravel UI for web authentication
- **Permission System**: Spatie Laravel Permission for role-based access control
- **File Processing**: Excel import/export capabilities with Maatwebsite Excel package
- **PDF Generation**: DomPDF for generating reports and documents

### Data Management
- **Database**: Designed for relational database systems (MySQL/PostgreSQL compatible)
- **ORM**: Eloquent ORM for database interactions and relationships
- **Data Export**: Multiple export formats including Excel, PDF, and CSV
- **Backup System**: Spatie Laravel Backup for automated data backups

### Communication & Notifications
- **Push Notifications**: Firebase integration for mobile push notifications
- **Web Push**: Browser-based push notifications using Web Push protocol
- **Real-time Chat**: Pusher-powered chat system between drivers and administrators
- **Email Notifications**: Laravel notification system for email communications
- **Slack Integration**: Slack notification channel for team communications

### Booking & Scheduling System
- **Dynamic Pricing**: Configurable pricing models with quotation management
- **Driver Assignment**: Intelligent driver allocation based on availability and scheduling
- **Vehicle Management**: Comprehensive vehicle tracking and maintenance scheduling
- **Customer Portal**: Self-service booking interface for customers
- **Multi-language Support**: Internationalization with support for multiple languages

### Integration Capabilities
- **Payment Processing**: Stripe and Razorpay integration for payment handling
- **Google Services**: Google API integration for maps and location services
- **Third-party APIs**: Extensible architecture for additional service integrations

### Performance & Optimization
- **Caching Strategy**: Service worker caching for offline functionality
- **Asset Management**: Optimized CSS/JS bundling and minification
- **Database Optimization**: Query optimization with proper indexing strategies
- **Progressive Loading**: Lazy loading and progressive enhancement techniques

## External Dependencies

### Core Framework Dependencies
- **Laravel Framework 10.x**: Main application framework
- **Laravel Passport**: OAuth2 server implementation for API authentication
- **Laravel UI**: Frontend scaffolding and authentication views
- **Spatie Laravel Permission**: Role and permission management

### Database & ORM
- **Doctrine DBAL**: Database abstraction layer for schema operations
- **Laravel Legacy Factories**: Support for older factory patterns

### Frontend Libraries
- **jQuery 3.x**: Primary JavaScript library for DOM manipulation
- **Bootstrap 4/5**: CSS framework for responsive design
- **AdminLTE**: Admin dashboard template
- **DataTables**: Advanced table functionality with sorting and filtering
- **Chart.js**: Data visualization and reporting charts
- **Flatpickr**: Modern date/time picker component
- **Select2**: Enhanced select boxes with search functionality

### Communication Services
- **Pusher**: Real-time WebSocket communication for chat and live updates
- **Firebase**: Push notification service for mobile and web
- **Minishlink Web Push**: Web push notification implementation
- **Laravel Slack Notification Channel**: Slack integration for team notifications

### File Processing & Export
- **Maatwebsite Excel**: Excel file import/export functionality
- **DomPDF**: PDF generation for reports and documents
- **Spatie Laravel Backup**: Automated backup management

### Payment Integration
- **Stripe PHP SDK**: Credit card and online payment processing
- **Razorpay SDK**: Alternative payment gateway for diverse markets

### Development & Utility
- **Google API Client**: Integration with Google services (Maps, etc.)
- **Guzzle HTTP**: HTTP client for external API communications
- **Laravel Tinker**: Interactive shell for application debugging
- **Nunomaduro Collision**: Enhanced error reporting for development

### PWA & Performance
- **Service Workers**: Offline functionality and caching strategies
- **Web App Manifest**: PWA configuration for app-like experience
- **Push Notification APIs**: Browser-native notification support