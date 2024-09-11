<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team</title>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    margin: 0;
    padding: 0;
    background: #f0f2f5;
    color: #122B40;
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

.about-header {
    text-align: center;
    margin-bottom: 30px;
}

.about-content {
    line-height: 1.6;
    
}

.team-section {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
    margin-bottom: 10px;
    animation: bounce-in 1.5s ease-in-out;
        }

        @keyframes bounce-in {
            0% { transform: scale(0.8); opacity: 0; }
            60% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(1); }
        }
.team-member-container {
    text-align: center;
    max-width: 250px;
    background: linear-gradient(to right, rgba(224, 248, 255, 0.3), rgba(144, 178, 216, 0.3), rgba(193, 227, 255, 0.3));
    border-radius: 10px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    padding: 15px;
    animation: bounce-in 1.5s ease-in-out;
        }

        @keyframes bounce-in {
            0% { transform: scale(0.8); opacity: 0; }
            60% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(1); }
        }
        .team-member-container img {
    border-radius: 50%;
    width: 100%;
    height: auto;
    max-width: 150px;
    object-fit: cover;
    margin-bottom: 10px;
}
.team-member h3 {
    margin: 10px 0 5px;
    font-size: 18px;
    color: #333;
}
.team-member p {
    margin: 0;
    font-size: 14px;
    color: #122B40;
}
.team-member .bio {
    font-size: 14px;
    color: #555;
    margin-top: 10px;
}

.team-info {
    text-align: center;
    margin-bottom: 30px;
}

.team-info h2 {
    font-size: 2em;
    color: #122B40;
    margin-bottom: 15px;
}

.team-info p {
    font-size: 16px;
    color: #555;
    line-height: 1.6;
    animation: bounce-in 2s ease-in-out;
        }

        @keyframes bounce-in {
            0% { transform: scale(0.8); opacity: 0; }
            60% { transform: scale(1.1); opacity: 1; }
            100% { transform: scale(1); }
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
        /* Adjust size for Sinethemba's image */
.sinethemba-img {
    margin-top: 10px;
    width: 50px; /* Adjust the width as needed */
    height: 50px; /* Maintain aspect ratio */
}


    </style>
</head>
<body>
    <header>
        &nbsp;<h1>Team</h1>
        <a class="back-button" onclick="history.back()">Back</a>
    </header>
    <main>
        <div class="about-header">
            <!-- Possibly add content here -->
        </div>
        <div class="team-info">
            <h2><i class="fas fa-users"></i>Our Team</h2>
            <p>Our team comprises dedicated professionals with a passion for education and technology.
                <br> We work tirelessly to ensure that Student Attends (SA) meets the highest standards
                <br> of quality and reliability, providing educational institutions with a tool they can trust.</p>
        </div>
        <div class="team-section">
            <div class="team-member-container">
                <img src="cathiey.png" alt="Khanyisa Mathebula">
                <h3>Khanyisa Mathebula</h3>
                <p>Project Manager & Lead Developer</p>
                <p class="bio">Khanyisa is a seasoned developer with over 10 years of experience in project management and software development. She is passionate about leveraging technology to solve real-world problems.</p>
            </div>
            <div class="team-member-container">
                <img src="hlawu.png" alt="Hlawulekani Baloyi">
                <h3>Hlawulekani Baloyi</h3>
                <p>Front-End Developer</p>
                <p class="bio">Hlawulekani specializes in creating intuitive and visually appealing user interfaces. With a keen eye for design and user experience, he ensures that our applications are both functional and attractive.</p>
            </div>
            <div class="team-member-container">
                <img src="thembi.png" alt="Thembinkosi Emmanuel Mhlanga">
                <h3>Thembinkosi Emmanuel Mhlanga</h3>
                <p>Back-End Developer</p>
                <p class="bio">Thembinkosi excels in building robust and scalable server-side applications. His expertise in database management and backend systems is crucial for maintaining the performance of our platform.</p>
            </div>
            <div class="team-member-container">
    <img src="sine.png" alt="Sinethemba Lekhuleni" class="sinethemba-img">
    <h3>Sinethemba Lekhuleni</h3>
    <p>Database Administrator</p>
    <p class="bio">Sinethemba is responsible for ensuring the integrity and security of our databases. With extensive experience in data management, she plays a key role in keeping our data accurate and accessible.</p>
</div>

        </div>
        <div class="team-section">
            <div class="team-member-container">
                <img src="sandra.png" alt="Sandra Mathebula">
                <h3>Sandra Mathebula</h3>
                <p>UX/UI Designer</p>
                <p class="bio">Sandra is our creative mind, designing user experiences that are both functional and aesthetically pleasing. Her work ensures that our platform is easy to use and visually engaging.</p>
            </div>
            <div class="team-member-container">
                <img src="jobe.png" alt="Tapiwa Masuku">
                <h3>Tapiwa Masuku</h3>
                <p>Technical Support Engineer</p>
                <p class="bio">Tapiwa provides essential support and troubleshooting for our users. His problem-solving skills and technical knowledge ensure that any issues are addressed promptly.</p>
            </div>
            <div class="team-member-container">
                <img src="confi.png" alt="Mbedzi Thompho Confidence">
                <h3>Mbedzi Thompho Confidence</h3>
                <p>Technical Support Engineer</p>
                <p class="bio">Mbedzi assists with technical support and system maintenance. His dedication to customer service and technical expertise contributes to the overall reliability of our platform.</p>
            </div>
        </div>
    </main>
    <footer>
        <p>&copy; 2024 Student Attends. All rights reserved.</p>
        <p><a href="privacy.html">Privacy Policy</a> | <a href="terms.html">Terms of Service</a></p>
    </footer>
</body>
</html>
