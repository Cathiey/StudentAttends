<!DOCTYPE html>
<html lang="en">
<head>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Student Attends</title>
  <style>
    
  @import url("https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Oswald:wght@500&display=swap");

body {
  margin: 0;
  background: linear-gradient(to right, rgba(224, 248, 255, 0.3), rgba(144, 178, 216, 0.3), rgba(193, 227, 255, 0.3));
  color: white;
  font-family: "Inter", sans-serif;
  height: 100%; /* Ensure that the html and body take up the full viewport height */
  overflow-x: hidden; /* Prevent horizontal scrolling */
  display: flex;
  justify-content: center;
  align-items: center;
  text-align: center;
  animation: pageFlipIn 1s ease-out;
}

/* Page flip animation CSS */
@keyframes pageFlipIn {
    0% {
        transform: rotateY(-90deg);
        opacity: 0;
    }
    100% {
        transform: rotateY(0deg);
        opacity: 1;
    }
}

nav {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 20px 36px;
  background: linear-gradient(120deg, #122B40, #446CB3);
  z-index: 50;
}

nav .nav-links {
  display: flex;
  gap: 20px;
}

nav .nav-links a {
  color: white;
  text-decoration: none;
  font-size: 14px;
  text-transform: uppercase;
  transition: color 0.3s ease;
}

nav .nav-links a:hover {
  color: #E0F8FF;
}

nav svg {
  width: 20px;
  height: 20px;
}

nav .svg-container {
  width: 20px;
  height: 20px;
}

nav > div {
  display: inline-flex;
  align-items: center;
  text-transform: uppercase;
  font-size: 14px;
  color: #FFFFFFDD;
}

nav > div:first-child {
  gap: 10px;
}

nav > div:last-child {
  gap: 24px;
}

nav > div:last-child > .active {
  position: relative;
}

nav > div:last-child > .active:after {
  content: "";
  height: 3px;
  border-radius: 99px;
  background-color: #ecad29;
  position: absolute;
  bottom: -8px;
  left: 0;
  right: 0;
}

.main-content {
  display: flex;
  flex-direction: row;
  justify-content: center;
  align-items: center;
  gap: 20px;
  padding: 20px;
  width: 100%;
  max-width: 1200px; /* Prevents too wide on large screens */
  min-height: calc(100vh - 80px); /* Minimum height considering navigation height */
  text-align: center;
  flex-wrap: wrap; /* Allows cards to wrap to the next line on smaller screens */
  animation: fadeInSlideUp 1s ease-out; /* Adjust duration and easing as needed */
  animation: bounce-in 1s ease-in-out;
}

.main-content h1 {
  color: #122B40;
  font-family: "Oswald", sans-serif;
  font-size: 50px; /* Reduced font size */
  margin-bottom: 20px; /* Reduced margin */
  width: 100%;
  animation: bounce-in 1s ease-in-out;
}

@keyframes bounce-in {
  0% { transform: scale(0.8); opacity: 0; }
  60% { transform: scale(1.1); opacity: 1; }
  100% { transform: scale(1); }
}

.card {
  background: linear-gradient(120deg, #122B40, #446CB3);
  color: white;
  width: 220px; /* Reduced width */
  height: 150px; /* Reduced height */
  border-radius: 10px;
  box-shadow: 0 4px 8px rgba(0, 0, 0, 0.6);
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  cursor: pointer;
  text-align: center;
  transition: transform 0.3s, background 0.3s;
  padding: 15px; /* Reduced padding */
  box-sizing: border-box;
  animation: bounce-in 0.5s ease-in-out;
}

.card-icon {
  font-size: 36px; /* Reduced font size */
  margin-bottom: 8px; /* Reduced margin */
  color: white;
}

.card:hover {
  transform: scale(1.05);
  background: linear-gradient(to right, #E0F8FF, #90B2D8, #C1E3FF);
}

.card-title {
  font-size: 20px; /* Reduced font size */
  font-weight: 600;
  margin: 8px 0; /* Reduced margin */
}

.card-description {
  font-size: 14px; /* Reduced font size */
  margin-top: 8px; /* Reduced margin */
}

@keyframes float {
  0%, 100% {
    transform: translateY(0);
  }
  50% {
    transform: translateY(-20px);
  }
}

.splash {
  width: 100%; /* Full width on small screens */
  max-width: 500px; /* Reduced maximum width for splash image */
  height: auto; /* Maintain aspect ratio */
  margin: 20px auto; /* Center the splash section */
  animation: float 5s ease-in-out infinite;
}

.splash img {
  width: 100%; /* Ensure the image takes up full width of its container */
  height: auto; /* Maintain aspect ratio */
}

.splash-text {
  font-size: 16px; /* Reduced font size */
  color: #122B40;
  text-align: center; /* Center text */
  padding: 0 10px; /* Add padding for better readability */
}

@media (max-width: 768px) {
  nav {
    padding: 10px;
    flex-direction: column;
    align-items: center;
  }

  nav .nav-links {
    gap: 2px;
    margin-top: 10px;
  }

  nav .nav-links a {
    font-size: 14px; /* Increase font size for better touch targets */
  }

  .main-content {
    flex-direction: column; /* Stack items vertically on smaller screens */
    align-items: center; /* Center items horizontally */
    gap: 20px; /* Space between items */
    padding-bottom: 20px; /* Space for splash section */
  }

  .card {
    width: 80%; /* Slightly smaller width for better fit */
    max-width: 300px; /* Ensure cards don’t get too large */
    height: auto; /* Adjust height to content */
    padding: 10px; /* Reduced padding */
  }

  .card-title {
    font-size: 18px; /* Reduced font size */
  }

  .card-description {
    font-size: 12px; /* Reduced font size */
  }

  .splash {
    margin-top: 20px; /* Ensure splash image is below the cards */
    max-width: 90%; /* Ensure it fits the screen */
  }
}

@media (max-width: 480px) {
  .main-content h1 {
    font-size: 30px; /* Further reduced font size */
    margin-bottom: 15px; /* Further reduced margin */
  }

  .card {
    width: 90%; /* Slightly smaller width for better fit */
    max-width: 280px; /* Ensure cards don’t get too large */
    height: auto; /* Adjust height to content */
    padding: 10px; /* Reduced padding */
  }

  .card-title {
    font-size: 16px; /* Further reduced font size */
  }

  .card-description {
    font-size: 10px; /* Further reduced font size */
  }

  .splash {
    margin-top: 20px; /* Margin to ensure it goes under the cards */
    max-width: 90%; /* Ensure it fits the screen */
  }

  .splash-text {
    font-size: 12px; /* Further reduced font size */
    padding: 0 5px; /* Reduce padding for better fit */
  }

  .logo {
    max-width: 250px; /* Reduced max-width for logo */
  }
}

.logo {
  margin-top: 40px; 
  margin-bottom: 0px; /* Space between logo and form */
  max-width: 300px; 
  height: auto;
  animation: fadeInSlideUp 0.5s ease-out; /* Adjust duration and easing as needed */
}

  </style>
</head>
<body>

  <nav>
    <div>
      <div class="svg-container">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
        </svg>
      </div>
      <div>Student Attendance Management System</div>
    </div>
    <div class="nav-links">
      <a href="Splash.php">Home</a>
      <a href="Team.php">Team</a>
      <a href="Services.php">Services</a>
      <a href="AboutUs.php">About Us</a>
      <a href="Contact.php">Contact</a>
    
    <div class="account-icon">
      <i class="fas fa-user"></i>
    </div>
    </div>
  </nav>

  <div class="main-content">
    <h1><img src="UMPLogo.png" alt="Logo" class="logo"><br>Student Attends</h1>
    <div class="card" onclick="location.href='Student/StudentLogin.php';">
      <i class="fas fa-user-graduate card-icon"></i>
      <div class="card-title">Student</div>
      <div class="card-description">Take your attendance</div>
    </div>
    <div class="card" onclick="location.href='Lecturer/StaffLogin.php';">
      <i class="fas fa-chalkboard-teacher card-icon"></i>
      <div class="card-title">Lecturer</div>
      <div class="card-description">View and manage students</div>
    </div>
    <div class="card" onclick="location.href='Admin/AdminLogin.php';">
      <i class="fas fa-user-shield card-icon"></i>
      <div class="card-title">Admin</div>
      <div class="card-description">Administer the system</div>
    </div>
  </div>
  
  <div class="splash">
    <img src="splash.png" alt="splash">
    <div class="splash-text">"Welcome to Student Attends. Our platform is designed to simplify and enhance your attendance tracking experience. Whether you're a student, lecturer, or administrator, you'll find intuitive tools to streamline your daily tasks and keep everything organized. Dive in to experience effortless management and stay on top of your attendance needs."</div>
  </div>

  <!-- JavaScript -->
  <script>
    // No additional JavaScript needed for this static setup
  </script>
</body>
</html>
