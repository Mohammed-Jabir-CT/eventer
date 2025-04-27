# Laravel Project Setup Guide


## Requirements

- PHP >= 8.0  
- Node.js >= 18.x (LTS recommended)  
- NPM >= 9.x  
- Composer  
- MySQL or any compatible database  

## Installation Steps

1. **Clone the Repository**  
   ```bash
   git clone <repository-url>
   cd <project-folder>
   ```

2. **Copy Environment File From '.env.example' to '.env'**

3. **Configure Database in `.env`**  
   Open the `.env` file and update the following lines with your database credentials:
   ```env
   DB_DATABASE=your_database_name
   DB_USERNAME=your_database_user
   DB_PASSWORD=your_database_password
   ```

4. **Install Dependencies**  
   ```bash
   composer install
   ```

5. **Generate App Encryption Key** 
   ```bash
   php artisan key:generate
   ```
   
6. **Run Migrations**  
   ```bash
   php artisan migrate
   ```

7. **Seed the Database**  
   This will create default users.
   ```bash
   php artisan db:seed
   ```
8. **Install Frontend Dependencies**  
   ```bash
   npm install
   ```

9. **Run the Frontend Vite Dev Server**  
   This will start the frontend on [http://localhost:5173](http://localhost:5173):
   ```bash
   npm run dev
   ```

10. **Start the Development Server**  
   ```bash
   php artisan serve
   ```
   The application will be available at: [http://localhost:8000](http://localhost:8000)

## Default Login Credentials

- **Email:** test@example.com  
- **Password:** password

## Quick Explanation

The application is built using Laravel Livewire with Volt components for dynamic user interaction, styled using Tailwind CSS for a clean and responsive design. I implemented event relationships to manage interactions between users and requisition items, ensuring that actions like claiming or uploading are properly handled. Authorization is controlled through Laravel policies, where only invited users are allowed to upload images and claim requisition items.
