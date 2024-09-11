<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
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
            position: relative;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
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
    background: linear-gradient(120deg, #122B40, #446CB3);
    transition: background-color 0.3s;
}
.back-button:hover {
    background: linear-gradient(to right, #E0F8FF, #90B2D8, #C1E3FF);
}

        .main {
            padding: 40px 20px;
            max-width: 800px;
            margin: auto;
        }

        .contact-info {
            text-align: center;
            margin-bottom: 40px;
        }

        .contact-info h2 {
            font-size: 32px;
            color: #122B40;
            margin-bottom: 15px;
            animation: bounce-in 0.5s ease-in-out;
        }

        @keyframes bounce-in {
            0% { transform: scale(0.8); opacity: 0; }
            60% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(1); }
        }

        .contact-info p {
            font-size: 18px;
            color: #555;
            line-height: 1.6;
            animation: bounce-in 2s ease-in-out;
        }

        @keyframes bounce-in {
            0% { transform: scale(0.8); opacity: 0; }
            60% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(1); }
        }

        .contact-info i {
            font-size: 24px;
            color: #122B40;
            margin-right: 10px;
        }

        .contact-details {
            display: flex;
            justify-content: space-between;
            gap: 20px;
            margin-bottom: 20px;
            animation: fadeInSlideUp 1s ease-out;
        }

        .contact-details div {
            background: linear-gradient(to right, rgba(224, 248, 255, 0.3), rgba(144, 178, 216, 0.3), rgba(193, 227, 255, 0.3));
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 400px;
            text-align: center;
            animation: bounce-in 1.5s ease-in-out;
        }

        @keyframes bounce-in {
            0% { transform: scale(0.8); opacity: 0; }
            60% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(1); }
        }

        .contact-details h3 {
            font-size: 20px;
            color: #122B40;
            margin-bottom: 10px;
        }

        .contact-details p {
            font-size: 16px;
            color: #555;

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
            .contact-details {
                flex-direction: column;
                align-items: center;
            }
        }
    </style>
</head>
<body>
    <header>
    <a class="back-button" onclick="history.back()">Back</a>
        &nbsp;<h1>Contact Us</h1>
    </header>

    <main class="main">
        <section class="contact-info">
            <h2><i class="fas fa-envelope"></i> Get in Touch</h2>
            <p>If you have any questions, feedback, or would like to learn more about how Student Attends (SA) can benefit your institution, please don't hesitate to get in touch with us. We are here to help!</p>
        </section>

        <section class="contact-details">
            <div>
                <h3><i class="fas fa-envelope"></i> Email</h3>
                <p><a href="mailto:support@studentattends.com">support@studentattends.com</a></p>
            </div>
            <div>
                <h3><i class="fas fa-phone"></i> Phone</h3>
                <p><a href="tel:+1234567890">(123) 456-7890</a></p>
            </div>
            <div>
                <h3><i class="fas fa-map-marker-alt"></i> Address</h3>
                <p>Cnr R40 and D725 Roads<br>University of Mpumalanga<br>Mbombela, 1200</p>
            </div>
        </section>
    </main>

    <footer>
        <p>&copy; 2024 Student Attends. All rights reserved.</p>
        <p><a href="privacy.html">Privacy Policy</a> | <a href="terms.html">Terms of Service</a></p>
    </footer>
</body>
</html>
