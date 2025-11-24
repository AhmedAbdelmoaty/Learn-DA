# Custom CMS - Bilingual Course Website

A fully functional, responsive Custom Content Management System built from scratch using PHP, PostgreSQL, and Bootstrap 5.

## Features

### Frontend (Public Website)
- **Bilingual Support**: Full English and Arabic language support with RTL layout
- **Responsive Design**: Bootstrap 5 responsive design for desktop, tablet, and mobile
- **Sections**:
  - Hero Section with background image and CTA button
  - About Course section
  - Key Benefits (icon-based feature cards)
  - Course Details with modules, duration, format, and fee
  - Testimonials carousel
  - FAQ accordion
  - Contact form with validation
  - Footer with social media links

### Backend (Admin Panel)
- **Secure Authentication**: Session-based login with password hashing
- **Content Management**:
  - Hero Section editor
  - About Course editor
  - Benefits manager (add/edit/delete)
  - Course Details editor
  - Testimonials manager
  - FAQ manager
  - Media upload system
  - Site settings (logo, colors, contact info, social links)
  - Admin user management
  - Contact messages viewer

## Installation

1. Run the installation script once:
   ```
   Navigate to: install.php
   ```

2. Default admin credentials (change after first login):
   - Email: admin@example.com
   - Password: 123456

3. The installation script will:
   - Create all necessary database tables
   - Insert default admin user
   - Populate sample content in both languages
   - Create a `.installed` file to prevent re-installation

## File Structure

```
/
├── admin/              # Admin panel pages
│   ├── includes/       # Admin header/footer
│   ├── dashboard.php
│   ├── login.php
│   ├── hero.php
│   ├── about.php
│   ├── benefits.php
│   ├── course_details.php
│   ├── testimonials.php
│   ├── faq.php
│   ├── media_upload.php
│   ├── site_settings.php
│   ├── manage_admins.php
│   └── messages.php
├── assets/
│   ├── css/
│   │   ├── style.css    # Main stylesheet
│   │   └── rtl.css      # RTL support for Arabic
│   ├── js/
│   │   └── main.js      # Frontend JavaScript
│   └── uploads/         # Uploaded media files
├── includes/
│   ├── db.php          # Database connection
│   └── auth.php        # Authentication functions
├── index.php           # Main public website
├── send_message.php    # Contact form handler
└── install.php         # One-time installation script
```

## Database Tables

- `admin_users` - Admin account management
- `hero_section` - Hero banner content
- `about_section` - About course content
- `benefits` - Key benefits/features
- `course_details` - Course information and modules
- `testimonials` - Student testimonials
- `faq` - Frequently asked questions
- `site_settings` - Site configuration
- `uploads` - Media file tracking
- `contact_messages` - Contact form submissions

## Technology Stack

- **Backend**: PHP 8.3 with PDO
- **Database**: PostgreSQL (via Replit)
- **Frontend**: Bootstrap 5, Font Awesome 6
- **Session Management**: PHP native sessions
- **Security**: Password hashing, prepared statements, session timeout

## Features Implemented

✅ Full bilingual support (English/Arabic)
✅ RTL layout for Arabic
✅ Responsive Bootstrap 5 design
✅ Database-driven content (no hardcoded text)
✅ Secure admin authentication
✅ Session timeout protection
✅ Media upload system
✅ CRUD operations for all content sections
✅ Contact form with database storage
✅ Multi-admin support
✅ Clean, modern UI inspired by IMP website

## Design Inspiration

The website design is inspired by the IMP (Institute of Management Professionals) website with:
- Clean, professional layout
- Rich maroon/burgundy color scheme (#a8324e)
- Modern card-based design
- Smooth animations and transitions
- Clear typography and spacing
- Mobile-first responsive approach

## Security Features

- Password hashing using PHP's `password_hash()`
- Prepared SQL statements (PDO)
- Session-based authentication
- Auto-logout after inactivity
- Protected admin routes
- Input validation and sanitization

## Language Support

The CMS supports complete bilingual content with:
- Separate database fields for English and Arabic content
- Language toggle in header and footer
- RTL CSS for Arabic layout
- URL parameter-based language switching (?lang=ar or ?lang=en)
