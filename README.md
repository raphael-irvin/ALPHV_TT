# ALPHV Technical Assesment (Web App)

## Project Architecture / Specification
This project fulfills the ALPHV assessment requirements by utilizing a strictly separated Front End and Back End architecture. 
- **Back End**: PHP Laravel (v12) serving a RESTful API.
- **Database**: MySQL handling data persistence.
- **Front End**: Pure HTML, CSS, and Vanilla JavaScript.
- **Real-Time Sync**: Implemented via a Short Polling script fetching new data every 3 seconds to ensure seamless User Portal updates without page refreshes.

## Initial Setup

### Prerequisites
To run this application locally, you will need:
1. **XAMPP** (or equivalent) running Apache and MySQL (Port 3306).
2. **PHP** (v8.2+) and **Composer** installed.

### Setup Instructions (Local)

#### 1. Database Setup
1. Open XAMPP and start **Apache** and **MySQL**.
2. Navigate to `http://localhost/phpmyadmin` in your browser.
3. Create a new, empty database named exactly: `alphv_db`

#### 2. Back End Setup (Laravel API)
1. Open your terminal and navigate to the `alphv-backend` folder.
2. Run the following command to install dependencies:
   `composer install`
3. Ensure the `.env` file has the correct database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=alphv_db
   DB_USERNAME=root
   DB_PASSWORD=

#### 3. Database Migration
Run `php artisan migrate` on `alphv-backend` folder.

#### 4. Backend Server Boot
Run `php artisan serve` on `alphv-backend` folder.
_IMPORTANT: This terminal shall remain opened for the web to function_

#### 5. Frontend Server Boot
Run `php artisan serve` on a **different terminal** within the `alphv-backend` folder.
_IMPORTANT: This terminal shall remain opened for the web to function_

## Automated Testing