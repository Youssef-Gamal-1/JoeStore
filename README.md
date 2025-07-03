# 🛒 JoeStore – E-Commerce Web App

**JoeStore** is a simple yet functional e-commerce platform built using PHP and MySQL. It allows users to browse products, register and log in, manage their cart, and place orders. The system also includes an admin dashboard for managing products and users.

---

## 🔧 Features

- 🧑 User registration and login  
- 🛍️ Product browsing and categories  
- 🛒 Cart system with add/remove/update  
- 💳 Simulated checkout and payment  
- 🔐 Session-based access control  
- ⚙️ Admin dashboard (manage products & users)

---

## 🛠️ Tech Stack

- **Backend:** PHP (no framework)  
- **Frontend:** HTML, CSS, JavaScript, Bootstrap  
- **Database:** MySQL  
- **Web Server:** Apache (XAMPP or LAMP recommended)

---

## ⚙️ Installation & Setup

1. Clone the repository:
   ```bash
   git clone https://github.com/Youssef-Gamal-1/JoeStore.git
````

2. Move the project to your web server root directory:

   * XAMPP: `htdocs/`
   * Linux: `/var/www/html/`

3. Import the database:
   ⚠️ *Currently missing – schema will be reconstructed and added soon*

4. Update your database connection inside:

   ```
   init.php
   ```

5. Run the app in your browser:

   ```
   http://localhost/JoeStore/
   ```

---

## 📁 Project Structure

```bash
JoeStore/
├── admin/               # Admin panel
├── layout/              # Shared layout files (header, footer)
├── index.php            # Landing page
├── cart.php             # Shopping cart
├── register.php         # User registration
├── profile.php          # User profile
├── logout.php           # Logout functionality
├── init.php             # DB connection and core config
├── README.md            # Project documentation
└── ...
```

---

## 📌 Notes

> ⚠️ **Database schema is not currently included**
> I'm working on rebuilding and documenting the MySQL schema to make the project fully runnable.

---

## 🧪 Purpose

This project was built as part of my journey into full-stack development, and now serves as a base for understanding web app internals and testing them in my cybersecurity learning process.

---

## 📄 License

This project is for **educational and personal use** only.
Feel free to fork and explore, but please give credit if reused.

---
