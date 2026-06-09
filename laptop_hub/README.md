# LaptopHub - E-Commerce Website

A responsive e-commerce website for selling laptops, built with HTML, CSS, JavaScript, PHP and MySQL.

## Features

- Responsive design (mobile, tablet, desktop)
- Product catalog with categories
- Shopping cart with localStorage (static) / database (PHP)
- Search functionality
- Category filtering
- User authentication (PHP version)
- Admin panel for product management (PHP version)
- Contact form
- Smooth animations and transitions

## Project Structure

```
E-Commerce Website/
├── images/                 # Product images
│   ├── Business Lap/
│   ├── Gaming lap/
│   ├── Professional lap/
│   └── Student lap/
├── includes/               # PHP backend files
│   ├── db.php
│   ├── functions.php
│   └── cart_handler.php
├── static/                 # Static version (for GitHub Pages)
│   ├── index.html
│   ├── products.html
│   ├── cart.html
│   ├── contact.html
│   ├── data.js
│   └── cart.js
├── index.php               # Homepage (PHP)
├── products.php            # Product listing (PHP)
├── cart.php                # Shopping cart (PHP)
├── contact.php             # Contact form (PHP)
├── login.php               # Login page (PHP)
├── register.php            # Registration (PHP)
├── admin.php               # Admin panel (PHP)
├── database.sql            # MySQL schema
├── style.css               # Stylesheet
├── script.js               # JavaScript
└── README.md               # This file
```

## Setup - Local (PHP + MySQL)

### Prerequisites
- [XAMPP](https://www.apachefriends.org/) (Apache + MySQL)

### Steps

1. **Install XAMPP** and start Apache + MySQL

2. **Import Database**
   - Open `http://localhost/phpmyadmin`
   - Create database: `laptop_hub`
   - Import `database.sql`

3. **Place Project**
   - Copy folder to `C:\xampp\htdocs\`

4. **Access Website**
   - Open: `http://localhost/E%20Commerce%20Website/`

### Admin Login
- Email: `admin@lapphub.com`
- Password: `admin123`

## Setup - GitHub Pages (Static Version)

GitHub Pages only supports static files. The `static/` folder contains a fully working version using JavaScript and localStorage.

### Steps

1. **Push to GitHub**
   ```bash
   git init
   git add .
   git commit -m "Initial commit"
   git remote add origin https://github.com/your-username/your-repo.git
   git push -u origin main
   ```

2. **Enable GitHub Pages**
   - Go to repository **Settings > Pages**
   - Select **main** branch and `/static` folder
   - Click **Save**

3. **Access Your Site**
   - URL: `https://your-username.github.io/your-repo/`

## Technologies

| Tier | Technology |
|------|------------|
| Frontend | HTML5, CSS3, JavaScript |
| Backend (PHP) | PHP 7.4+, PDO |
| Database (PHP) | MySQL 5.7+ |
| Cart (Static) | localStorage API |

## Screenshots

Add screenshots of your project here.
