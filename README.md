# Online Bus Reservation System (OBRS)

An online bus reservation system built with PHP and MySQL. This project allows users to search for buses, book seats, and manage their reservations. It includes distinct portals for passengers, employees, and administrators, each with specific functionalities.

## Features

### Passenger
- **User Authentication:** Signup, login, and logout functionality.
- **Profile Management:** Update personal details, password, and profile picture.
- **Search & Book:** Search for available buses based on routes.
- **Booking:** Select and book seats, with support for different seat types.
- **View Bookings:** See a list of all personal bookings (active, expired, canceled).
- **Cancel Booking:** Cancel a previously made booking.
- **Print Ticket:** Generate and print a ticket for a confirmed booking.
- **Payment Integration:** A simple payment gateway for processing ticket fares.

### Employee
- **Dashboard:** An overview of system statistics and recent activities.
- **Passenger Management:** Add new passengers and manage existing ones.
- **Ticket Management:** View and confirm pending tickets, and see a list of all paid tickets.
- **Print Tickets:** Print tickets on behalf of passengers.
- **Profile Management:** Manage their own employee profile.

### Administrator
- **Full Employee Management:** Add, update, view, and manage employee accounts.
- **Bus Management:** Add new buses, update details, and manage the bus fleet.
- **Financial Overview:** View accounting and financial summaries.
- **Password Resets:** Approve or deny password reset requests from users.
- **System-wide Ticket Management:** View and manage all tickets within the system.

## Technology Stack

- **Backend:** PHP
- **Database:** MySQL
- **Frontend:** HTML, CSS, JavaScript
- **Libraries:** Bootstrap, jQuery, DataTables, SweetAlert2, and more.

## Setup and Installation

1.  After downloading, extract the project files and place the `OBRS-PHP` folder inside your web server's root directory (e.g., `htdocs` for XAMPP).
2.  Open your web browser and navigate to `http://localhost/phpmyadmin`.
3.  Create a new database named `obrsphp`.
4.  Click on the "Import" tab and select the `obrsphp.sql` file located in the `DATABASE FILE` directory.
5.  Once the database is set up, open the project in your browser by visiting `http://localhost/OBRS-PHP/`.

## Login Credentials

You can use the following default credentials to access the different portals:

### Admin
- **Email:** `login@admin.com`
- **Password:** `the.bhautikk`

### Employee
- **Username:** `johndoe`
- **Password:** `Employee@12345`

You can register as a new passenger through the signup page on the main website.
