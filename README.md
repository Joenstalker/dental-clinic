# Dental Clinic Management System

A comprehensive Laravel-based dental clinic management system with appointment booking, patient management, and multi-role user access.

## 🚀 Features

### Admin Features

-   Dashboard with analytics
-   Manage dentists and assistants
-   View and manage patient records
-   Appointment scheduling and management
-   Audit logs tracking
-   Respond to patient concerns
-   System settings and profile management

### Patient Features

-   User registration with email verification
-   Book appointments with dentists
-   View available time slots and services
-   Submit concerns/queries
-   View appointment history
-   Profile management

### Dentist Features

-   View assigned appointments
-   Manage patient records
-   Schedule management

### Assistant Features

-   Approve/disapprove appointment requests
-   Create appointment sessions
-   Manage services and pricing
-   View pending patient accounts
-   Patient management

## 📋 Requirements

-   **PHP**: 8.0 or higher
-   **Composer**: Latest version
-   **MySQL**: 5.7 or higher
-   **Node.js & NPM**: (Optional, for frontend assets)
-   **Web Server**: Apache/Nginx

## 🔧 Installation

### 1. Clone the Repository

```bash
git clone <repository-url>
cd <project-folder>
```

### 2. Install Dependencies

```bash
composer install
```

### 3. Environment Configuration

Copy the example environment file:

```bash
cp .env.example .env
```

### 4. Configure Database

Edit `.env` file with your database credentials:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

### 5. Configure Mail Settings (Gmail)

**Important:** You must use Gmail App Password, not your regular password.

#### Generate Gmail App Password:

1. Go to: https://myaccount.google.com/apppasswords
2. Enable 2-Step Verification first (if not enabled)
3. Create app password: Select "Mail" → "Other (Custom name)"
4. Copy the 16-character password (no spaces)

#### Update .env file:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your_16_character_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

### 6. Generate Application Key

```bash
php artisan key:generate
```

### 7. Run Migrations & Seed Database

This will create all tables and insert default users:

```bash
php artisan migrate
```

### 8. Clear Cache

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### 9. Start Development Server

```bash
php artisan serve
```

Visit: `http://localhost:8000`

## 👤 Default User Accounts

After migration, these accounts are available:

| Role      | Email               | Password | Status |
| --------- | ------------------- | -------- | ------ |
| Admin     | admin@gmail.com     | admin    | Active |
| Patient   | patient@gmail.com   | admin    | Active |
| Patient 2 | patient2@gmail.com  | admin    | Active |
| Dentist   | dentist@gmail.com   | admin    | Active |
| Assistant | assistant@gmail.com | admin    | Active |

**Note:** All default passwords are `admin`. Change them after first login!

## 📁 Database Structure

### Main Tables

-   **users** - All system users (admin, patient, dentist, assistant)
-   **appointment_sessions** - Appointment slots created by assistants
-   **members** - Patient appointment bookings
-   **events** - Calendar events for appointments
-   **services** - Available dental services and pricing
-   **concern_boxes** - Patient inquiries and admin responses
-   **audits** - User activity tracking

### Default Services

-   Consultation - ₱500
-   Periapical Radiograph - ₱750

## 🔐 User Registration & Email Verification

1. New patients register via `/signup`
2. System sends verification email
3. User must verify email before accessing patient dashboard
4. Account status changes from `inactive` to `active` after verification

## 🛠️ Troubleshooting

### Email Sending Issues

**Error:** `Failed to authenticate on SMTP server`

**Solution:**

1. Ensure 2-Step Verification is enabled on Gmail
2. Generate a NEW App Password (old ones may be revoked)
3. Copy password exactly (remove spaces)
4. Update `.env` file
5. Clear cache: `php artisan config:clear`

### Database Connection Issues

**Error:** `SQLSTATE[HY000] [1045] Access denied`

**Solution:**

1. Verify database credentials in `.env`
2. Ensure MySQL service is running
3. Check database exists: `CREATE DATABASE your_database_name;`

### Permission Issues (Linux/Mac)

```bash
sudo chmod -R 775 storage bootstrap/cache
sudo chown -R www-data:www-data storage bootstrap/cache
```

## 🧪 Testing

### Test Email Configuration

```bash
php artisan tinker
```

Then run:

```php
Mail::raw('Test email from Dental Clinic', function($msg) {
    $msg->to('test@example.com')->subject('Test Email');
});
```

### Test Application

1. Register new patient account
2. Check email for verification link
3. Login with verified account
4. Book an appointment
5. Admin/Assistant approve appointment

## 📱 Accessing Different Dashboards

-   **Admin**: `/admin/dashboard`
-   **Patient**: `/patient/dashboard`
-   **Dentist**: `/dentist/dashboard`
-   **Assistant**: `/assistant/dashboard`

## 🔄 Deployment to Production

### 1. Set Production Environment

```env
APP_ENV=production
APP_DEBUG=false
```

### 2. Optimize Application

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer install --optimize-autoloader --no-dev
```

### 3. Set Proper Permissions

```bash
chmod -R 755 storage bootstrap/cache
```

### 4. Configure Web Server

Point document root to `/public` directory.

## 📞 Support

For issues or questions:

1. Check the troubleshooting section
2. Review error logs: `storage/logs/laravel.log`
3. Verify all environment variables in `.env`

## 🔒 Security Notes

-   Change all default passwords immediately
-   Keep `.env` file secure (never commit to git)
-   Use strong App Password for email
-   Regular database backups recommended
-   Update dependencies regularly: `composer update`

## 📝 License

This project is for educational/commercial use.

---

**Version**: 1.0  
**Last Updated**: 2024  
**Tech Stack**: Laravel 10, Blade, MySQL, Bootstrap
