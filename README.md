# Filipino Cookbook API

A REST API for the Filipino Cookbook project, built to run on XAMPP.

## Requirements

- [XAMPP](https://www.apachefriends.org/) (includes Apache and PHP)
- Git

## Installation

1. **Clone the repository** into your XAMPP `htdocs` folder:
   ```
   cd C:\xampp\htdocs
   git clone https://github.com/Burtzs/filipino-cookbook-api.git
   ```

2. **Start XAMPP**
   - Open the XAMPP Control Panel.
   - Start the **Apache** module (and **MySQL** if the API uses a database).

3. **Verify the folder**
   Make sure the project is located at:
   ```
   C:\xampp\htdocs\filipino-cookbook-api
   ```

## Running the API

Once Apache is running, access the API in your browser or via a tool like Postman at:

```
http://localhost/filipino-cookbook-api/
```

Adjust the path above if your entry point is inside a subfolder (e.g. `public/index.php`), for example:

```
http://localhost/filipino-cookbook-api/public/
```

## Configuration

If the API connects to a database:

1. Open **phpMyAdmin** at `http://localhost/phpmyadmin`.
2. Create the required database (check the project's config file for the expected database name).
3. Update any database credentials in the project's config file (e.g. `config.php` or `.env`) to match your local setup.

## Notes

- Make sure no other application is using port 80/443 before starting Apache.
- If you make changes, don't forget to commit and push:
  ```
  git add .
  git commit -m "your message"
  git push
  ```
