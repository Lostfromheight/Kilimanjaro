# Claude Instructions — Kilimandjaro Project

## Read This First
Before making any changes read PROJECT_CONTEXT.md for full system context.
Root path: C:\xampp\htdocs\Cafe\Cafe\

---

## How To Override or Update These Instructions
Temporary override: "Ignore the [rule] in CLAUDE.md for this task only and [do X]"
Add a new rule: "Update CLAUDE.md — add this rule: [your rule]"
Reflect a new feature: "Read CLAUDE.md and PROJECT_CONTEXT.md, update both to reflect [feature], then implement it"

---

## Folder Structure
```
Cafe/
  Auth/       Login.php, LoginProcess.php, SignForm.php, CafeConnection.php, Logout.php
  Admin/      AdminInterface.php, AdminProcess.php, AdminUsers.php, AdminReports.php, AdminFeedback.php, AdminInterface.css
  Finance/    PurchaseHistory.php, Receipt.php
  Menu/       Menu.php, Drinks.php, Pastry.php, MenuProcess.php, CartProcess.php, TestMenuCss.css
  Kitchen/    Kitchen.php, KitchenMenu.php, KitchenFeedback.php, KitchenProcess.php, Kitchen.css
  Orders/     OrderProcess.php, OrderStatus.php, Checkout.php, Feedback.php
  Image/      all images
  connection.php   — root, shared by all folders
  Cafeteria.html   — root, landing page
  CLAUDE.md        — root
  PROJECT_CONTEXT.md — root
```

## Path Rule
Every file uses ../ to go up to root then into the target folder:
- ../connection.php
- ../Image/filename.jpg
- ../Auth/Login.php
- ../Menu/TestMenuCss.css
- ../Admin/AdminInterface.css
- ../Kitchen/Kitchen.css

---

## Code Style
- Use // for comments — never /---- title ----/
- No unnecessary blank lines between related code
- Variable names: $first_name not $FirstName
- Always use intval() for INT values in SQL
- Always use mysqli_real_escape_string() for strings in SQL
- No quotes around INT values in WHERE clauses
- No Bootstrap — custom CSS only
- No inline styles unless a single one-off value
- No emoji in buttons, links, nav tabs, or badges — plain text only

---

## Session Rules — Never Break
- Menu/MenuProcess.php calls session_start() — Menu.php, Drinks.php, Pastry.php, CartProcess.php must NOT
- Kitchen/Kitchen.php and KitchenMenu.php call session_start() — KitchenProcess.php must NOT
- Kitchen/KitchenFeedback.php calls its own session_start()
- Admin/AdminUsers.php calls session_start() — AdminProcess.php must NOT
- All other standalone pages call their own session_start() at the top

---

## Database Rules — Never Break
- All IDs are INT AUTO_INCREMENT starting from 1
- Display IDs padded: str_pad($id, 3, '0', STR_PAD_LEFT) shows as #001
- order.Status enum exactly: 'Pending','Paid','Preparing','Ready','Picked Up'
- menu.Category enum exactly: 'Lunch','Drinks','Pastry' — Drinks has an s
- menu_option.Quantity enum: 'Half','Full' only
- Always use intval() before using an ID in a query

---

## Business Logic — Never Change
- Full portion = base price + 50 Ksh. Half = base price
- Order flow: Pending → Paid → Preparing → Ready → Picked Up
- Chef only sees Status IN ('Paid','Preparing','Ready') — never Pending
- For Paid orders: show BOTH Preparing and Mark Ready buttons simultaneously — chef decides which to click
- For Preparing orders: show only Mark Ready button
- For Ready orders: show only Picked Up button
- Cancel button always visible on every order row
- Payment = JS prompt() for phone number → alert() confirms → order saved as Paid
- Feedback: Bug Report/Feature Request → Admin. Quality of Service/Food Complaint → Chef
- Customers must have at least one order before submitting feedback
- Chef accounts created by Admin only — not through SignForm.php
- Menu is public — no login required to browse
- Cart requires login — CartProcess.php redirects to ../Auth/Login.php if not logged in
- Menu items ordered by ItemID ASC in KitchenProcess.php

---

## What Not To Do
- Do not call session_start() more than once in a page load chain
- Do not use Bootstrap
- Do not add actors beyond Customer, Chef, Admin
- Do not integrate real M-Pesa API
- Do not change order status enum values or their sequence
- Do not use absolute Windows paths
- Do not wrap INT IDs in quotes in SQL queries
- Do not add a Cashier role or page
- Do not create new subfolders beyond the six modules
- Do not use emoji in interactive elements (buttons, links, nav, badges)

---

## Suggested Improvements When Polishing
- Add password_hash() and password_verify() for secure passwords
- Add htmlspecialchars() on all output to prevent XSS
- Disable Place Order button after first click to prevent duplicate orders
- Add Out of Stock overlay on menu cards to disable the + button visually
- Auto-refresh Kitchen.php every 30 seconds for new orders
- Show unread feedback count badge on sidebar and tab nav
- Add order count column to customer table in AdminUsers
