# 🩺 Medical Prescription Upload System

A full-stack web application to manage medical prescriptions, with a **Vue.js frontend** and a **Laravel API backend**. Users can upload prescriptions, while pharmacy can review them, provide quotations, and patients can accept or reject quotations.

---

## 📦 Tech Stack

**Frontend**
- Vue.js
- Tailwind CSS
- Axios for API integration

**Backend**
- Laravel
- PHP
- Laravel Passport (OAuth2 password grant)
- MySQL

---

## ⚙️ Project Setup

### Backend (Laravel)

1. **Extract the zip folder.**


2. **Install PHP dependencies:**

   ```bash
   cd backend
   composer install
   ```

3. **Create `.env` file and configure:**

   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   Edit .env file and update the database credentials.

4. **Run migrations and install Passport:**

   ```bash
    php artisan migrate
    php artisan passport:install
   ```

   Copy the password grant client credentials (client_id, client_secret) in .env file.

5. **Serve the Laravel app:**

   ```bash
   php artisan serve
   ```

   Laravel backend runs at `http://127.0.0.1:8000`

---

### Frontend (Vue.js)

1. **Navigate to the frontend directory:**

   ```bash
   cd frontend
   ```

2. **Install Node dependencies:**

   ```bash
   npm install
   ```

3. **Run the frontend:**

   ```bash
   npm run dev
   ```

   Vue app runs at `http://localhost:5173` or the port displayed in the terminal.

---

## 🔐 Login Credentials

* **As a customer**: `email : malki@gmail.com, password : 123456`
* **As a pharmacy**: `email : mediquixx@gmail.com, password : password123`


## ⏳ No. of hours spent

* **45 hours**
