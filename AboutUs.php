<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>

@import url("https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Oswald:wght@500&display=swap");
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: #f0f2f5;
            animation: pageFlipIn 0.7s ease-out;
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
        header {
            background: linear-gradient(120deg, #122B40, #446CB3);
            color: white;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            text-align: center;
        }
        header h1 {
            margin: 0;
            font-size: 16px;
            font-family: 'Arial Black', sans-serif;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        }
        .back-button {
            position: absolute;
            top: 12px;
            left: 20px;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            padding: 8px 16px;
            background: linear-gradient(to right, #E0F8FF, #90B2D8, #C1E3FF);
            transition: background-color 0.3s;
        }
        .back-button:hover {
           
            background: linear-gradient(120deg, #122B40, #446CB3);
        }
        
        .about-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .about-header h1 {
            color: #122B40;
            font-family: "Oswald", sans-serif;
            font-size: 48px;
            margin-bottom: 40px;
            width: 100%;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            animation: bounce-in 0.5s ease-in-out;
        }

        @keyframes bounce-in {
            0% { transform: scale(0.8); opacity: 0; }
            60% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(1); }
        }
        .about-content {
            line-height: 1.6;
        }
        .about-sections {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 20px;
            animation: bounce-in 1.5s ease-in-out;
        }

        @keyframes bounce-in {
            0% { transform: scale(0.8); opacity: 0; }
            60% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(1); }
        }
        .about-section {
            flex: 1;
            background: linear-gradient(to right, rgba(224, 248, 255, 0.3), rgba(144, 178, 216, 0.3), rgba(193, 227, 255, 0.3));
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            max-width: 350px;
            margin-left: 40px;
            margin-right: 40px;
        }
        .about-section h2 {
            font-size: 1.5em;
            color: #122B40;
            margin-bottom: 10px;
            font-family: 'Arial Black', sans-serif;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
        }
        .about-section h2 i {
            margin-right: 10px;
        }
        .about-section p {
            margin: 0 0 10px;
        }
        
        
        footer {
            text-align: center;
            padding: 15px 0;
            background: linear-gradient(120deg, #122B40, #446CB3);
            color: white;
            box-shadow: 0 -2px 5px rgba(0, 0, 0, 0.2);
        }

        footer p {
            margin: 0;
        }

        footer a {
            color: #E0F8FF;
            text-decoration: none;
            font-weight: bold;
        }
        @media (max-width: 768px) {
            .about-sections {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <header>
        &nbsp;<h1>About Student Attends</h1>
        <a class="back-button" onclick="history.back()">Back</a>
    </header>
    <main>
            <div class="about-header">
            <h1>Student Attends</h1>
            </div>
                <div class="about-sections">
                <div class="about-section">
                        <h2><i class="fas fa-clipboard-list"></i>Who are we</h2>
                        <p>Student Attends is a  student attendance management system  that streamlines the
                            tracking and reporting of student attendance with an intuitive interface designed for both
                            educators and administrators. By integrating real-time data, automated alerts, and comprehensive
                            analytics, our system simplifies the attendance process, enhances accuracy, and supports better
                            decision-making. Our goal is to empower educational institutions to focus more on teaching and
                            learning while we handle the complexities of attendance management.</p>
                    </div>
                    <div class="about-section">
                        <h2><i class="fas fa-bullseye"></i>Our Mission</h2>
                        <p>At Student Attends (SA), our mission is to revolutionize the way educational institutions manage student attendance. We aim to provide a robust, efficient, and user-friendly system that not only tracks attendance accurately but also enhances academic productivity and reduces administrative burdens.</p>
                    </div>
                    <div class="about-section">
                        <h2><i class="fas fa-eye"></i>Our Vision</h2>
                        <p>We envision a future where educational institutions can focus more on teaching and less on administrative tasks. By leveraging the power of technology, we strive to create an environment where students are more engaged, punctual, and accountable, contributing to a more productive academic experience for everyone involved.</p>
                    </div>
                </div>
                
               
    </main>
    <footer>
        <p>&copy; 2024 Student Attends. All rights reserved.</p>
        <p><a href="privacy.html">Privacy Policy</a> | <a href="terms.html">Terms of Service</a></p>
    </footer>
</body>
</html>