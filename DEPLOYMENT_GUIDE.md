# PetHaven Deployment Guide (PHP Hosting)

Since GitHub Pages doesn't support PHP, follow these steps to host your site on a platform like **InfinityFree**, **000webhost**, or any other PHP hosting provider.

## 1. Choose a PHP Host
- **InfinityFree** (Free, no ads)
- **000webhost** (Free, but limited)
- **Hostinger** (Paid, very reliable)

## 2. Set Up the Database
Most hosting providers use **phpMyAdmin**.
1. Log in to your hosting control panel (cPanel).
2. Go to **MySQL Databases** and create a new database (e.g., `pethaven_db`).
3. Create a **Database User** and assign it to the database with all privileges. **Save the username and password!**
4. Open **phpMyAdmin**, select your new database, and click **Import**.
5. Upload the [pethaven_db.sql](file:///e:/DIKSHA/xamp/htdocs/PET_ADOPTION/database/pethaven_db.sql) file located in your project's `database/` folder.

## 3. Update Database Connection
You need to tell the project how to connect to the *new* database.
1. Open [includes/db.php](file:///e:/DIKSHA/xamp/htdocs/PET_ADOPTION/includes/db.php).
2. Update the `$host`, `$db_name`, `$username`, and `$password` with the details from your hosting provider.
   - *Note: On many hosts, `$host` is `localhost`, but some use a specific address.*

```php
$host = 'your_remote_host'; // e.g., sql300.infinityfree.com
$db_name = 'your_database_name';
$username = 'your_database_user';
$password = 'your_database_password';
```

## 4. Upload Files
Use an FTP client (like **FileZilla**) or the hosting's **File Manager**.
1. Upload all files from your project folder to the `htdocs` or `public_html` folder on your server.
2. Make sure the `assets/images` folder is uploaded so your images show up!

## 5. Visit Your Site
Once uploaded, visit your domain (e.g., `yourname.infinityfreeapp.com`) and your PetHaven project should be live!
