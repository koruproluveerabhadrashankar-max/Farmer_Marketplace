FARMDIRECT - FARMER MARKETPLACE MINI PROJECT
=============================================

REQUIREMENTS
- XAMPP with Apache, MySQL and PHP 8+
- A web browser
- VS Code (optional)

INSTALLATION (RECOMMENDED)
1. Extract Farmer_Marketplace.zip.
2. Copy the Farmer_Marketplace folder into:
   C:\xampp\htdocs\
3. Open XAMPP Control Panel and start Apache and MySQL.
4. Open this address:
   http://localhost/Farmer_Marketplace/setup.php
5. Wait for "Setup complete".
6. Click Open FarmDirect.
7. IMPORTANT: Delete setup.php after successful setup.

DEMO ADMIN LOGIN
Email: admin@farmdirect.local
Password: Admin@123
Change this demo password before using the site beyond a classroom demo.

CREATE TEST ACCOUNTS
- Use Join free to create a Farmer account.
- Add crop listings from the farmer dashboard.
- Open a private/incognito window or log out and create a Buyer account.
- Search for crops and open a crop detail page to see seller contact options.
- Admin can view users and manage listings from the Admin link.

DATABASE
The setup.php script creates the farmer_marketplace database and its users and crops tables.
Alternatively, import database.sql in phpMyAdmin, then use the app.
Database settings are in config.php:
DB_HOST = localhost
DB_NAME = farmer_marketplace
DB_USER = root
DB_PASS = ''
If your MySQL root account has a password, update DB_PASS.

FEATURES
- Role-based Farmer / Buyer / Admin accounts
- Secure password hashing and PHP sessions
- PDO prepared statements
- CSRF protection on POST forms
- Farmer crop create, read, update and delete
- Crop photo upload (JPG, PNG, WEBP; 3 MB max)
- Search by crop name and description
- Filter by location and maximum price
- Crop availability / sold status
- Farmer contact via phone and email
- Admin overview and listing moderation
- Responsive layout

PROJECT STRUCTURE
Farmer_Marketplace/
  assets/css/style.css
  assets/js/app.js
  assets/images/crop-placeholder.svg
  uploads/.htaccess
  config.php
  functions.php
  setup.php
  database.sql
  index.php
  register.php
  login.php
  logout.php
  dashboard.php
  crop_form.php
  crop.php
  delete_crop.php
  admin.php
  README.txt

CLASSROOM DEMONSTRATION FLOW
1. Open homepage.
2. Register a farmer account.
3. Log in as farmer and add a crop with quantity, unit, price, location and photo.
4. Log out and register/login as buyer.
5. Search the crop, view details and show farmer contact.
6. Log in as admin and show users, crop counts and moderation.

SECURITY / PROJECT SCOPE NOTES
This is a classroom mini-project starter, not a production marketplace.
It does not process online payments, verify identities, or provide an order/booking workflow.
Use HTTPS, stronger deployment configuration, backups, moderation and additional validation before any public deployment.
