# Kilimandjaro — Project Context

## Stack
PHP, MySQL, HTML/CSS, JavaScript, XAMPP, VS Code
Database: cafeteriareg
Students: Maneno Mabila Noah (221721), Metili Sean (147817)
Supervisor: Byron Chesoli — Strathmore Institute

---

## Database Tables

### registration (Customers)
UserID INT AUTO_INCREMENT PK | First_name VARCHAR(50) | Last_name VARCHAR(50) | Gender ENUM('M','F') | Password VARCHAR(255)

### chef
ChefID INT AUTO_INCREMENT PK | First_name VARCHAR(50) | Last_name VARCHAR(50) | Gender ENUM('M','F') | Password VARCHAR(255)

### admin
AdminID INT AUTO_INCREMENT PK | First_name VARCHAR(50) | Last_name VARCHAR(50) | Gender ENUM('M','F') | Password VARCHAR(255)
Default seeded: Maneno / Noah / M / Ambassador

### menu
ItemID INT AUTO_INCREMENT PK | Item VARCHAR(50) | Category ENUM('Lunch','Drinks','Pastry') | Price DECIMAL(8,2) | Quantity INT DEFAULT 0 | Status ENUM('In Stock','Out of Stock')

### order
OrderID INT AUTO_INCREMENT PK | UserID INT FK→registration | ChefID INT FK→chef | Status ENUM('Pending','Paid','Preparing','Ready','Picked Up') DEFAULT 'Pending' | Total INT | Time TIMESTAMP DEFAULT CURRENT_TIMESTAMP

### menu_option
OptionID INT AUTO_INCREMENT PK | OrderID INT FK→order | ItemID INT FK→menu | Quantity ENUM('Half','Full') | Price INT

### feedback
FeedbackID INT AUTO_INCREMENT PK | UserID INT FK→registration | Type VARCHAR(50) | Message TEXT | GoesTo ENUM('Admin','Chef') | Status ENUM('Unread','Read') DEFAULT 'Unread' | CreatedAt TIMESTAMP

---

## Sessions
$_SESSION['UserID']     — INT from database
$_SESSION['role']       — 'CU' = Customer, 'CH' = Chef, 'AD' = Admin
$_SESSION['First_name'] — user's first name
$_SESSION['cart']       — array of cart items (Customer only)

---

## File Purposes

### Auth/
Login.php          — login form with role dropdown, shows new user ID after registration
LoginProcess.php   — reads role, queries correct table, sets session, redirects
SignForm.php        — customer self-registration only, no manual ID entry
CafeConnection.php  — handles registration POST, validates inputs, blocks Chef self-reg
Logout.php          — destroys session, redirects to ../Auth/Login.php

### Menu/
MenuProcess.php    — calls session_start(), PUBLIC no role check, fetches $lunch_items $drink_items $pastry_items using mysqli_fetch_all()
CartProcess.php    — no session_start, handles add/remove/clear cart, redirects to ../Auth/Login.php if not logged in when adding
Menu.php           — Lunch tab, shows Login/Register buttons if guest, profile dropdown if logged in
Drinks.php         — Drinks tab, same structure as Menu.php
Pastry.php         — Pastry tab, same structure as Menu.php

### Orders/
Checkout.php       — cart review with Half/Full sizing, JS prompt() for phone payment, submits to OrderProcess.php
OrderProcess.php   — inserts order with Status='Paid', inserts menu_option rows, deducts stock, redirects to OrderStatus.php
OrderStatus.php    — customer tracks orders, progress bar Paid→Preparing→Ready→Picked Up, uses intval() on OrderID
Feedback.php       — customer submits feedback, requires at least one order, routes to Admin or Chef by type

### Finance/
PurchaseHistory.php — full order history table, status badge must appear inside Status td cell, links to Receipt per order
Receipt.php         — printable receipt, window.print() with @media print, verifies order belongs to customer

### Kitchen/
KitchenProcess.php  — no session_start, all kitchen handlers, fetches orders IN ('Paid','Preparing','Ready') only, menu items ordered by ItemID ASC
Kitchen.php         — session_start + KitchenProcess, orders table
                      Paid orders: show BOTH Preparing button (→Preparing) AND Mark Ready button (→Ready) simultaneously
                      Preparing orders: show only Mark Ready button
                      Ready orders: show only Picked Up button
                      Cancel button always visible
KitchenMenu.php     — session_start + KitchenProcess, menu CRUD, auto-sets Status from Quantity
KitchenFeedback.php — session_start, shows GoesTo='Chef' feedback, mark as read

### Admin/
AdminInterface.php  — redirects to AdminUsers.php
AdminProcess.php    — no session_start, all user CRUD handlers, redirects to AdminUsers.php
AdminUsers.php      — session_start + AdminProcess, manage Customers/Chefs/Admins
AdminReports.php    — standalone, summary cards + Chart.js bar chart + most ordered table
AdminFeedback.php   — shows GoesTo='Admin' feedback, mark as read

---

## Login Redirects
Customer → ../Menu/Menu.php
Chef     → ../Kitchen/Kitchen.php
Admin    → ../Admin/AdminUsers.php

## Role Checks
All protected pages redirect unauthorised users to: ../Auth/Login.php?error=no_access

## Key Decisions Made
- IDs start from 1 (INT AUTO_INCREMENT), displayed padded as #001 in UI
- No wallet or real payment — simulated via JS prompt() and alert()
- No emoji on any buttons, links, tabs, or badges
- Menu public browsing — login only required when adding to cart
- Chef sees both Preparing and Mark Ready buttons on Paid orders simultaneously
- Feedback requires at least one prior order from the customer
- Chef accounts created by Admin only