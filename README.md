![Static Badge](https://img.shields.io/badge/DONE-%23009943?style=for-the-badge&logo=checkmarx&logoColor=%23FFF)

# 🗳️ ToreVote (Voting System)

This project aims to provide a secure and efficient platform for conducting **online elections**, specifically tailored for academic institutions or organizations. ToreVote ensures transparency, accessibility, and integrity of election results. The system is designed to be user-friendly for both administrators and voters, supporting real-time result monitoring and robust management of candidates, positions, and party lists.

> **📘 Project Title:** : ToreVote  
> **📌 Course:** Project : Application Development and Emerging Technologies  
> **🎓 Purpose:** : Final Project Compliance

A secure, responsive, and role-based **Online Voting System** developed in **PHP**, **MySQL**, **HTML/CSS**, and **Bootstrap 5**.
Built to facilitate student elections or similar voting scenarios with a user-friendly interface for both admins and voter.

---

## 🔐 Admin Login Credentials

- **Email:** `admin@votesys.com`
- **Password:** `admin123`
- **Database Clear Security Code:** `1234`

---

## 🧑‍🎓 Voter Login Credentials

| Email                | Password |
| -------------------- | -------- |
| 202312345@fit.edu.ph | 123      |
| 202357395@fit.edu.ph | 123      |
| 202386945@fit.edu.ph | 123      |
| 202376954@fit.edu.ph | 123      |

---

## 📁 File Directory Structure

```
VotingSys/                     # Root directory of the voting system
├── README.md                  # Documentation file explaining project details, setup, and usage
├── votingsysdb.sql            # SQL dump file used to set up the system's database
│
├── /screenshots/              # Contains screenshots of the system’s interfaces and features
│   ├── user_login_ss.png      # Sample of voter login page
│   ├── admin_login_ss.png     # Sample of admin login page
│   ├── candidateMain_dashboard_ss.png  # Admin dashboard showing candidate management
│   ├── admin_dashboard_ss.png # Main admin dashboard view
│   ├── position_ss.png        # Position maintenance page (admin side)
│   ├── voters_main_ss.png     # Voter maintenance dashboard (admin side)
│   ├── clr_db_ss.png          # Screenshot of clear-database function
│   ├── cast_ss.png            # Screenshot after a user casts their vote
│   └── vote_ss.png            # Voting page interface for voters
│
├── /img                       # Stores system images, logos, and icons
│   ├── logo.png               # Main system logo
│   └── ... (other image files for UI)
│
├── /pages/                    # Core PHP files that power the voting system
│   ├── add-candidates.php     # Admin page to add new candidates
│   ├── admin-logout.php       # Handles admin logout
│   ├── admin.php              # Admin login page
│   ├── candidates_maintenance.php # Candidate management page
│   ├── clear-db.php           # Clear database functionality with security code
│   ├── dashboard.php          # Main admin dashboard
│   ├── index.php              # Voter login page (system landing page)
│   ├── logout.php             # Handles voter logout
│   ├── partylist_maintenance.php # Admin page to manage party lists
│   ├── position_maintenance.php  # Admin page to manage election positions
│   ├── submit_vote.php        # Handles voter submissions to the database
│   ├── vote.php               # Voting page where users cast their votes
│   ├── voters_addition.php    # Admin tool to add voters manually
│   ├── voters_maintenance.php # Admin page to manage voter accounts
│   └── voters_modification.php # Admin page to update voter details
│
├── /database/                 # Database connection and config files
│   └── connect.php            # Establishes MySQL database connection
│
├── /styles/                   # Contains all CSS files for styling the system
│   ├── add_voter-style.css    # Styles for adding voters
│   ├── add-candidate.css      # Styles for candidate forms
│   ├── admin-style.css        # Styles for admin panel
│   ├── candidate_modification-style.css # Candidate modification page styles
│   ├── dashboard-style.css    # Styles for dashboard pages
│   ├── login-style.css        # Styles for login pages (admin & voter)
│   ├── partylist_maintenance.css # Styles for partylist maintenance
│   ├── position_maintenance.css  # Styles for position maintenance
│   ├── regis-style.css        # Styles for registration-related forms
│   ├── results-style.css      # Styles for results page
│   ├── vote-style.css         # Styles for voting page
│   ├── voters_maintenance-style.css # Styles for voters maintenance
│   └── voters_modification-style.css # Styles for modifying voter accounts
     
```

**Note:** The actual directory may contain additional files and folders depending on further development and deployment requirements.

---

- The Database file can be found on the File itself

## ✅ Features

### 🧑‍💻 Admin Side

- Admin authentication system
- Add/edit/delete candidates, party lists, and positions
- Upload candidate photos and descriptions
- View real-time election results per position and partylist
- Clear voting records with security code confirmation

### 🗳️ Voter Side

- User login system with session security
- View candidates grouped by position
- Cast votes using candidate cards
- Submit votes with confirmation modal
- Skip positions (optional voting)
- Blocks re-voting after submission

---

## 🛠️ Tech Stack

![Static Badge](https://img.shields.io/badge/PHP-%23777BB4?style=for-the-badge&logo=PHP&logoColor=FFF&logoSize=auto)
![Static Badge](https://img.shields.io/badge/phpMyAdmin-%236C78AF?style=for-the-badge&logo=phpmyadmin&logoColor=FFF&logoSize=auto)
![Static Badge](https://img.shields.io/badge/CSS-%23663399?style=for-the-badge&logo=css&logoColor=FFF&logoSize=auto)
![Static Badge](https://img.shields.io/badge/HTML-%23E34F26?style=for-the-badge&logo=html5&logoColor=FFF&logoSize=auto)
![Static Badge](https://img.shields.io/badge/Bootstrap-%237952B3?style=for-the-badge&logo=bootstrap&logoColor=FFF&logoSize=auto)

## 🛠️ Setup Instructions

1. **Clone the repository:**
   ```bash
   git clone https://github.com/cafyrei/VotingSys.git
   ```
2. **Import the Database to myphpadmin**
   ```bash
   import votingsysdb.sql
   ```
3. **Configure Database Connection**  
    ```bash
    database/connect.php
    ```
   ***Update Database***
   ```bash
    $host = "localhost";
    $user = "root";
    $pass = "";
    $db   = "torevote_db";
    ``` 
4. ***Start Local Server***
    - Place VotingSys in your htdocs folder (for XAMPP).
    - Start Apache and MySQL.
5. ***Access the System***
    - Go to:
    ```bash
    http://localhost/VotingSys/public/
    ```
    - Admin Login: 
    ```bash
    Email: admin@votesys.com
    Password: admin123
    ```
    - Sample Voter Login:
    ```bash
    Please do Navigate to section : Voter Login Credentials
    ```
6. ***Ready to Use***
    - Admins manage candidates, positions, and party lists.
    - Voters log in, cast votes, and view results.
---

## 🖼️ System Images

- Below are some previews of the system’s key features, including user and admin interfaces, dashboards, and voting functionalities.

<table>
        <tr>
            <td>
                <img src="/screenshots/user_login_ss.png" alt="user_login" width="400">
                <p align="center">User Login</p>
            </td>
            <td>
                <img src="/screenshots/admin_login_ss.png" alt="admin_login" width="400">
                <p align="center">Admin Login</p>                
            </td>
        </tr>
        <tr>
            <td>
                <img src="/screenshots/candidateMain_dashboard_ss.png" alt="dashboard" width="400">
                <p align="center">(Admin) Candidate Main Dashboard</p>                
            </td>
            <td>
                <img src="/screenshots/admin_dashboard_ss.png" alt="candidates" width="400">
                <p align="center">(Admin) Dashboard</p>                
            </td>
        </tr>
        <tr>
            <td>
                <img src="/screenshots/position_ss.png" alt="position" width="400">
                <p align="center">(Admin) Position Maintenance</p>
            </td>
            <td>
                <img src="/screenshots/voters_main_ss.png" alt="voters_main_ss" width="400">
                <p align="center">(Admin) Voters Maintenance</p>
            </td>
        </tr>
        <tr>
            <td>
                <img src="/screenshots/clr_db_ss.png" alt="clear_db" width="400">
                <p align="center">(Admin) Clear Database</p>
            </td>
            <td>
                <img src="/screenshots/cast_ss.png" alt="casted_vote" width="400">
                <p align="center">(User) Casted Votes</p>
            </td>
        </tr>
        <tr>
            <td align="center" colspan="2">
                <img src="/screenshots/vote_ss.png" alt="voting_page" width="400">
                <p align="center"> (User) Voting Page</p>
            </td>
        </tr>
</table>

---

## 🧑‍💻 Team Members

- **Backend Developer** : Rafhielle Allen Alcabaza
- **Backend Developer** : Sean Paul Nieves
- **UI/UX Developer** : Rovic Christopher Sarthou
- **Paper Documentation** : Breindelle Vincent Ayuso

---

> _"For Good Governance, Vote confidently"
