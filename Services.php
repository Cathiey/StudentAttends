<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Services</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background: #f0f2f5;
            color: #333;
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
        @keyframes fadeInSlideUp {
            0% {
                opacity: 0;
                transform: translateY(20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        header {
            background: linear-gradient(120deg, #122B40, #446CB3);
            color: white;
            padding: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: relative;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        header h1 {
            margin: 0;
            font-size: 16px;
            font-family: 'Arial Black', sans-serif;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
            animation: bounce-in 0.5s ease-in-out;
        }

        @keyframes bounce-in {
            0% { transform: scale(0.8); opacity: 0; }
            60% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(1); }
        }

        .back-button {
    position: absolute;
    top: 12px;
    left: 20px;
    color: white;
    text-decoration: none;
    border-radius: 5px;
    padding: 8px 16px;
    background: linear-gradient(120deg, #122B40, #446CB3);
    transition: background-color 0.3s;
}
.back-button:hover {
    background: linear-gradient(to right, #E0F8FF, #90B2D8, #C1E3FF);
}

        main {
            padding: 40px 20px;
            max-width: 1200px;
            margin: auto;
        }

        .about-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .about-header h2 {
            font-size: 36px;
            color: #122B40;
            margin-bottom: 10px;
        }

        .about-header p {
            font-size: 18px;
            color: #122B40;
            line-height: 1.6;
            max-width: 800px;
            margin: 0 auto;
            animation: bounce-in 2s ease-in-out;
        }

        @keyframes bounce-in {
            0% { transform: scale(0.8); opacity: 0; }
            60% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(1); }
        }

        .about-section {
            background: linear-gradient(to right, rgba(224, 248, 255, 0.3), rgba(144, 178, 216, 0.3), rgba(193, 227, 255, 0.3));
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            margin-bottom: 40px;
            animation: bounce-in 1.5s ease-in-out;
        }

        @keyframes bounce-in {
            0% { transform: scale(0.8); opacity: 0; }
            60% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(1); }
        }

        .about-section h2 {
            font-size: 28px;
            color: #122B40;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .about-section h2 i {
            font-size: 32px;
            color: #122B40;
            margin-right: 10px;
        }

        .about-section ul {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .about-section ul li {
            font-size: 16px;
            color: #122B40;
            margin-bottom: 10px;
            position: relative;
            padding-left: 25px;
        }

        .about-section ul li::before {
            content: '\f0c8';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            color: #122B40;
            font-size: 18px;
        }

        footer {
            text-align: center;
            padding: 20px 0;
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
            .about-header p {
                font-size: 16px;
            }

            .about-section h2 {
                font-size: 24px;
            }

            .about-section ul li {
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <header>
    <a class="back-button" onclick="history.back()">Back</a>
        &nbsp;<h1>Services We Offer</h1>
    </header>
    <main>
        <div class="about-header">
            <h2>Discover Our Services</h2>
            <p>At Student Attends (SA), we offer a comprehensive suite of features to streamline the attendance management process, providing efficiency and accuracy to meet your institution's needs.</p>
        </div>
        <div class="about-section">
            <h2><i class="fas fa-cogs"></i> What We Offer</h2>
            <p>Our services are designed to simplify attendance management and enhance operational efficiency:</p>
            <ul>
                <li>Automated attendance tracking to eliminate manual errors.</li>
                <li>Real-time monitoring of student attendance and punctuality.</li>
                <li>Detailed reports and alerts for irregular attendance patterns.</li>
                <li>Support for enforcing institutional attendance policies.</li>
                <li>Easy integration with existing academic and administrative systems.</li>
            </ul>
        </div>
    </main>
    <footer>
        <p>&copy; 2024 Student Attends. All rights reserved.</p>
        <p><a href="privacy.html">Privacy Policy</a> | <a href="terms.html">Terms of Service</a></p>
    </footer>
</body>
</html>
