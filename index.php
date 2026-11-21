<?php

session_start();

/*
|--------------------------------------------------------------------------
| If user is already logged in
|--------------------------------------------------------------------------
*/

if (isset($_SESSION['user_id'])) {

    header("Location: dashboard.php");
    exit();

}

?>
<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Career Path Advisor | Find Your Future
    </title>


    <!-- Google Fonts -->

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        rel="preconnect"
        href="https://fonts.gstatic.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >


    <style>

        /* =====================================================
           RESET
        ===================================================== */

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }


        html {
            scroll-behavior: smooth;
        }


        body {

            font-family: "Inter", sans-serif;

            background: #f8f9ff;

            color: #171827;

            overflow-x: hidden;

        }


        a {
            text-decoration: none;
        }


        /* =====================================================
           NAVBAR
        ===================================================== */

        .navbar {

            position: fixed;

            top: 0;

            left: 0;

            width: 100%;

            z-index: 1000;

            background:
                rgba(255,255,255,0.92);

            backdrop-filter: blur(15px);

            border-bottom:
                1px solid #eeeeF5;

        }


        .nav-container {

            width: 90%;

            max-width: 1180px;

            margin: auto;

            height: 72px;

            display: flex;

            align-items: center;

            justify-content: space-between;

        }


        /* LOGO */

        .logo {

            display: flex;

            align-items: center;

            gap: 10px;

            color: #171827;

            font-size: 17px;

            font-weight: 800;

        }


        .logo-icon {
            width: 38px;
            height: 38px;
            flex: 0 0 38px;
            display: block;
            object-fit: contain;
            border-radius: 0;
        }


        .logo-name {
            color: #171827;
            white-space: nowrap;
        }


        /* NAV LINKS */

        .nav-links {

            display: flex;

            align-items: center;

            gap: 30px;

        }


        .nav-links a {

            color: #626578;

            font-size: 12px;

            font-weight: 600;

            transition: 0.25s;

        }


        .nav-links a:hover {

            color: #5b5cf0;

        }


        /* NAV BUTTONS */

        .nav-buttons {

            display: flex;

            align-items: center;

            gap: 10px;

        }


        .login-btn {

            padding: 10px 17px;

            border-radius: 9px;

            color: #5b5cf0;

            border: 1px solid #dedfff;

            font-size: 11px;

            font-weight: 700;

            transition: 0.25s;

        }


        .login-btn:hover {

            background: #f0f0ff;

        }


        .signup-btn {

            padding: 11px 18px;

            border-radius: 9px;

            background: #5b5cf0;

            color: white;

            font-size: 11px;

            font-weight: 700;

            box-shadow:
                0 8px 18px
                rgba(91,92,240,0.20);

            transition: 0.25s;

        }


        .signup-btn:hover {

            transform: translateY(-2px);

            box-shadow:
                0 12px 25px
                rgba(91,92,240,0.28);

        }


        /* =====================================================
           HERO
        ===================================================== */

        .hero {

            min-height: 700px;

            padding-top: 72px;

            position: relative;

            overflow: hidden;

            background:
                linear-gradient(
                    135deg,
                    #f8f9ff 0%,
                    #f2f2ff 50%,
                    #ffffff 100%
                );

        }


        .hero::before {

            content: "";

            position: absolute;

            width: 450px;

            height: 450px;

            border-radius: 50%;

            background:
                rgba(91,92,240,0.08);

            top: -170px;

            right: -100px;

            filter: blur(3px);

        }


        .hero::after {

            content: "";

            position: absolute;

            width: 300px;

            height: 300px;

            border-radius: 50%;

            background:
                rgba(123,97,255,0.06);

            bottom: -150px;

            left: -100px;

        }


        .hero-container {

            width: 90%;

            max-width: 1180px;

            margin: auto;

            min-height: 628px;

            display: grid;

            grid-template-columns:
                1.05fr 0.95fr;

            align-items: center;

            gap: 55px;

            position: relative;

            z-index: 2;

        }


        /* HERO TEXT */

        .hero-content {

            animation:
                fadeUp 0.8s ease;

        }


        @keyframes fadeUp {

            from {

                opacity: 0;

                transform:
                    translateY(25px);

            }

            to {

                opacity: 1;

                transform:
                    translateY(0);

            }

        }


        .hero-label {

            display: inline-flex;

            align-items: center;

            gap: 7px;

            padding: 7px 11px;

            border-radius: 20px;

            background: #eeeeff;

            color: #5b5cf0;

            font-size: 9px;

            font-weight: 800;

            letter-spacing: 0.8px;

        }


        .hero h1 {

            margin-top: 20px;

            max-width: 650px;

            font-size: 52px;

            line-height: 1.08;

            letter-spacing: -2.2px;

            font-weight: 800;

            color: #171827;

        }


        .hero h1 span {

            color: #5b5cf0;

        }


        .hero-description {

            max-width: 580px;

            margin-top: 19px;

            color: #626578;

            font-size: 14px;

            line-height: 1.8;

        }


        .hero-buttons {

            display: flex;

            align-items: center;

            gap: 12px;

            margin-top: 29px;

        }


        .primary-btn {

            display: inline-flex;

            align-items: center;

            justify-content: center;

            padding: 14px 22px;

            border-radius: 10px;

            background:
                linear-gradient(
                    135deg,
                    #5b5cf0,
                    #7068f5
                );

            color: white;

            font-size: 11px;

            font-weight: 700;

            box-shadow:
                0 12px 25px
                rgba(91,92,240,0.22);

            transition: 0.25s;

        }


        .primary-btn:hover {

            transform: translateY(-3px);

            box-shadow:
                0 17px 30px
                rgba(91,92,240,0.30);

        }


        .secondary-btn {

            display: inline-flex;

            align-items: center;

            justify-content: center;

            padding: 13px 21px;

            border-radius: 10px;

            background: white;

            color: #626578;

            border: 1px solid #e3e5ee;

            font-size: 11px;

            font-weight: 700;

            transition: 0.25s;

        }


        .secondary-btn:hover {

            color: #5b5cf0;

            border-color: #cfd0fa;

        }


        .hero-note {

            display: flex;

            align-items: center;

            gap: 8px;

            margin-top: 20px;

            color: #8a8d9e;

            font-size: 9px;

        }


        .hero-note span {

            color: #4caf76;

        }


        /* =====================================================
           HERO VISUAL
        ===================================================== */

        .hero-visual {

            position: relative;

            height: 470px;

            display: flex;

            align-items: center;

            justify-content: center;

        }


        .visual-main {

            width: 365px;

            padding: 22px;

            border-radius: 20px;

            background: white;

            border: 1px solid #e6e7f0;

            box-shadow:
                0 25px 60px
                rgba(31,33,70,0.12);

            transform:
                rotate(2deg);

            animation:
                floatCard 4s ease-in-out infinite;

        }


        @keyframes floatCard {

            0%, 100% {
                transform:
                    rotate(2deg)
                    translateY(0);
            }

            50% {
                transform:
                    rotate(2deg)
                    translateY(-9px);
            }

        }


        .visual-header {

            display: flex;

            align-items: center;

            justify-content: space-between;

            margin-bottom: 20px;

        }


        .visual-title {

            font-size: 13px;

            font-weight: 800;

        }


        .visual-dot {

            width: 8px;

            height: 8px;

            border-radius: 50%;

            background: #5b5cf0;

        }


        .profile-mini {

            display: flex;

            align-items: center;

            gap: 12px;

            padding: 13px;

            border-radius: 12px;

            background: #f7f7fb;

            margin-bottom: 14px;

        }


        .profile-avatar {

            width: 38px;

            height: 38px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 11px;

            background: #eeeeff;

            font-size: 17px;

        }


        .profile-mini strong {

            display: block;

            font-size: 11px;

        }


        .profile-mini small {

            color: #8b8e9e;

            font-size: 8px;

        }


        .career-result {

            padding: 15px;

            border-radius: 12px;

            border: 1px solid #eeeeF5;

            margin-bottom: 12px;

        }


        .result-top {

            display: flex;

            align-items: center;

            justify-content: space-between;

        }


        .result-top span {

            font-size: 8px;

            color: #5b5cf0;

            font-weight: 800;

        }


        .result-top strong {

            font-size: 10px;

            color: #4caf76;

        }


        .career-result h3 {

            margin-top: 8px;

            font-size: 14px;

        }


        .progress {

            height: 6px;

            margin-top: 11px;

            border-radius: 20px;

            background: #eeeeF5;

            overflow: hidden;

        }


        .progress div {

            width: 91%;

            height: 100%;

            border-radius: 20px;

            background:
                linear-gradient(
                    90deg,
                    #5b5cf0,
                    #8175ff
                );

        }


        .mini-steps {

            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 8px;

            margin-top: 13px;

        }


        .mini-step {

            padding: 10px 7px;

            border-radius: 9px;

            background: #f7f7fb;

            text-align: center;

            font-size: 8px;

            color: #626578;

        }


        .mini-step span {

            display: block;

            margin-bottom: 4px;

            font-size: 13px;

        }


        /* FLOATING CARDS */

        .floating-card {

            position: absolute;

            display: flex;

            align-items: center;

            gap: 9px;

            padding: 11px 13px;

            border-radius: 11px;

            background: white;

            border: 1px solid #e9e9f1;

            box-shadow:
                0 15px 35px
                rgba(31,33,70,0.10);

            font-size: 9px;

            font-weight: 700;

        }


        .floating-icon {

            width: 29px;

            height: 29px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 8px;

            background: #eeeeff;

        }


        .float-one {

            top: 65px;

            right: 10px;

            animation:
                floatOne 4s ease-in-out infinite;

        }


        .float-two {

            bottom: 70px;

            left: 5px;

            animation:
                floatTwo 4s ease-in-out infinite;

        }


        @keyframes floatOne {

            0%,100% {
                transform:
                    translateY(0);
            }

            50% {
                transform:
                    translateY(-8px);
            }

        }


        @keyframes floatTwo {

            0%,100% {
                transform:
                    translateY(0);
            }

            50% {
                transform:
                    translateY(8px);
            }

        }


        /* =====================================================
           SECTION COMMON
        ===================================================== */

        .section {

            width: 90%;

            max-width: 1180px;

            margin: auto;

            padding: 90px 0;

        }


        .section-heading {

            text-align: center;

            max-width: 650px;

            margin: auto;

        }


        .section-label {

            color: #5b5cf0;

            font-size: 9px;

            font-weight: 800;

            letter-spacing: 1.5px;

        }


        .section-heading h2 {

            margin-top: 9px;

            font-size: 30px;

            letter-spacing: -1px;

            font-weight: 800;

        }


        .section-heading p {

            margin-top: 10px;

            color: #77798b;

            font-size: 12px;

            line-height: 1.7;

        }


        /* =====================================================
           FEATURES
        ===================================================== */

        .features-section {

            background: white;

        }


        .features-grid {

            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 18px;

            margin-top: 45px;

        }


        .feature-card {

            padding: 25px;

            border-radius: 16px;

            background: #ffffff;

            border: 1px solid #e7e8f0;

            transition: 0.3s;

        }


        .feature-card:hover {

            transform: translateY(-6px);

            box-shadow:
                0 18px 35px
                rgba(31,33,70,0.08);

            border-color: #dadafa;

        }


        .feature-icon {

            width: 46px;

            height: 46px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 12px;

            background: #eeeeff;

            font-size: 20px;

        }


        .feature-card h3 {

            margin-top: 17px;

            font-size: 15px;

        }


        .feature-card p {

            margin-top: 8px;

            color: #77798b;

            font-size: 10px;

            line-height: 1.7;

        }


        /* =====================================================
           HOW IT WORKS
        ===================================================== */

        .steps-section {

            background: #f7f7fc;

        }


        .steps-grid {

            display: grid;

            grid-template-columns:
                repeat(4, 1fr);

            gap: 15px;

            margin-top: 45px;

        }


        .step-card {

            position: relative;

            padding: 22px;

            border-radius: 15px;

            background: white;

            border: 1px solid #e5e6ee;

        }


        .step-number {

            width: 31px;

            height: 31px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 9px;

            background: #5b5cf0;

            color: white;

            font-size: 10px;

            font-weight: 800;

        }


        .step-card h3 {

            margin-top: 15px;

            font-size: 13px;

        }


        .step-card p {

            margin-top: 7px;

            color: #77798b;

            font-size: 9px;

            line-height: 1.7;

        }


        /* =====================================================
           ABOUT
        ===================================================== */

        .about-section {

            background: white;

        }


        .about-grid {

            display: grid;

            grid-template-columns:
                1fr 1fr;

            gap: 70px;

            align-items: center;

        }


        .about-content .section-label {

            display: inline-block;

        }


        .about-content h2 {

            margin-top: 10px;

            font-size: 30px;

            line-height: 1.2;

            letter-spacing: -1px;

        }


        .about-content p {

            margin-top: 14px;

            color: #77798b;

            font-size: 11px;

            line-height: 1.8;

        }


        .about-list {

            margin-top: 20px;

            display: grid;

            gap: 11px;

        }


        .about-item {

            display: flex;

            align-items: center;

            gap: 10px;

            color: #626578;

            font-size: 10px;

            font-weight: 600;

        }


        .check {

            width: 24px;

            height: 24px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 7px;

            background: #eeeeff;

            color: #5b5cf0;

            font-size: 11px;

        }


        .about-visual {

            padding: 25px;

            border-radius: 20px;

            background:
                linear-gradient(
                    145deg,
                    #292b66,
                    #5b5cf0
                );

            color: white;

            min-height: 330px;

            display: flex;

            flex-direction: column;

            justify-content: center;

            box-shadow:
                0 20px 45px
                rgba(91,92,240,0.20);

        }


        .about-visual h3 {

            font-size: 22px;

            line-height: 1.3;

        }


        .about-visual p {

            margin-top: 10px;

            color:
                rgba(255,255,255,0.72);

            font-size: 10px;

            line-height: 1.7;

        }


        .about-stats {

            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 10px;

            margin-top: 25px;

        }


        .stat {

            padding: 13px 8px;

            border-radius: 10px;

            background:
                rgba(255,255,255,0.10);

            text-align: center;

        }


        .stat strong {

            display: block;

            font-size: 18px;

        }


        .stat span {

            color:
                rgba(255,255,255,0.65);

            font-size: 8px;

        }


        /* =====================================================
           CTA
        ===================================================== */

        .cta-section {

            width: 90%;

            max-width: 1180px;

            margin: 20px auto 80px;

            padding: 55px 35px;

            border-radius: 22px;

            text-align: center;

            background:
                linear-gradient(
                    135deg,
                    #292b66,
                    #5b5cf0
                );

            color: white;

            box-shadow:
                0 20px 50px
                rgba(91,92,240,0.18);

        }


        .cta-section h2 {

            font-size: 29px;

            letter-spacing: -0.8px;

        }


        .cta-section p {

            max-width: 580px;

            margin: 10px auto 22px;

            color:
                rgba(255,255,255,0.70);

            font-size: 11px;

            line-height: 1.7;

        }


        .cta-button {

            display: inline-flex;

            padding: 13px 22px;

            border-radius: 9px;

            background: white;

            color: #4d4fd8;

            font-size: 10px;

            font-weight: 800;

            transition: 0.25s;

        }


        .cta-button:hover {

            transform: translateY(-2px);

        }


        /* =====================================================
           FOOTER
        ===================================================== */

        footer {

            padding: 30px 20px;

            background: #15162c;

            color: white;

        }


        .footer-container {

            width: 90%;

            max-width: 1180px;

            margin: auto;

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 20px;

        }


        .footer-logo {

            font-size: 14px;

            font-weight: 800;

        }


        .footer-logo span {

            color: #8586ff;

        }


        .footer-text {

            color:
                rgba(255,255,255,0.50);

            font-size: 9px;

        }


        /* =====================================================
           MOBILE MENU
        ===================================================== */

        .menu-button {

            display: none;

            border: none;

            background: transparent;

            font-size: 22px;

            cursor: pointer;

        }


        /* =====================================================
           RESPONSIVE
        ===================================================== */

        @media (max-width: 950px) {

            .nav-links {

                display: none;

            }


            .hero-container {

                grid-template-columns: 1fr;

                padding: 55px 0;

            }


            .hero {

                min-height: auto;

            }


            .hero-content {

                text-align: center;

            }


            .hero h1,
            .hero-description {

                margin-left: auto;

                margin-right: auto;

            }


            .hero-buttons,
            .hero-note {

                justify-content: center;

            }


            .hero-visual {

                height: 430px;

            }


            .features-grid {

                grid-template-columns:
                    repeat(2, 1fr);

            }


            .steps-grid {

                grid-template-columns:
                    repeat(2, 1fr);

            }


            .about-grid {

                grid-template-columns: 1fr;

            }

        }


        @media (max-width: 600px) {

            .nav-buttons .signup-btn {

                display: none;

            }


            .hero h1 {

                font-size: 38px;

            }


            .hero-description {

                font-size: 12px;

            }


            .hero-buttons {

                flex-direction: column;

            }


            .primary-btn,
            .secondary-btn {

                width: 100%;

                max-width: 280px;

            }


            .hero-visual {

                transform: scale(0.85);

                margin-top: -25px;

            }


            .features-grid,
            .steps-grid {

                grid-template-columns: 1fr;

            }


            .section {

                padding: 65px 0;

            }


            .section-heading h2,
            .about-content h2 {

                font-size: 25px;

            }


            .cta-section {

                padding: 40px 20px;

                margin-bottom: 55px;

            }


            .cta-section h2 {

                font-size: 24px;

            }


            .footer-container {

                flex-direction: column;

                text-align: center;

            }

        }

    </style>

</head>


<body>


<!-- =====================================================
     NAVBAR
===================================================== -->

<nav class="navbar">

    <div class="nav-container">


        <a
            href="index.php"
            class="logo"
        >

            <img
                src="images/logo.png"
                alt="CareerPath Advisor logo"
                class="logo-icon"
            >

            <span class="logo-name">CareerPath Advisor</span>

        </a>


        <div class="nav-links">

            <a href="#home">
                Home
            </a>

            <a href="#features">
                Features
            </a>

            <a href="#how-it-works">
                How It Works
            </a>

            <a href="#about">
                About
            </a>

        </div>


        <div class="nav-buttons">

            <a
                href="login.php"
                class="login-btn"
            >
                Login
            </a>

            <a
                href="register.php"
                class="signup-btn"
            >
                Get Started
            </a>

        </div>

    </div>

</nav>



<!-- =====================================================
     HERO SECTION
===================================================== -->

<section
    class="hero"
    id="home"
>

    <div class="hero-container">


        <!-- HERO CONTENT -->

        <div class="hero-content">


            <span class="hero-label">

                ✨ PERSONALIZED CAREER GUIDANCE

            </span>


            <h1>

                Discover the
                <span>
                    Right Career
                </span>
                for Your Future.

            </h1>


            <p class="hero-description">

                Career Path Advisor helps you discover
                suitable career opportunities based on
                your education, skills, interests and
                career goals — all in one place.

            </p>


            <div class="hero-buttons">


                <a
                    href="register.php"
                    class="primary-btn"
                >

                    Start Your Journey
                    &nbsp; →

                </a>


                <a
                    href="#how-it-works"
                    class="secondary-btn"
                >

                    See How It Works

                </a>


            </div>


            <div class="hero-note">

                <span>✓</span>

                Personalized recommendations

                <span>✓</span>

                Learning resources

            </div>


        </div>



        <!-- HERO VISUAL -->

        <div class="hero-visual">


            <div class="floating-card float-one">

                <div class="floating-icon">
                    🎯
                </div>

                Career Match

                <strong>
                    91%
                </strong>

            </div>



            <div class="visual-main">


                <div class="visual-header">

                    <span class="visual-title">
                        Career Analysis
                    </span>

                    <span class="visual-dot"></span>

                </div>


                <div class="profile-mini">

                    <div class="profile-avatar">
                        👩‍💻
                    </div>

                    <div>

                        <strong>
                            Your Career Profile
                        </strong>

                        <small>
                            Skills • Interests • Goals
                        </small>

                    </div>

                </div>


                <div class="career-result">

                    <div class="result-top">

                        <span>
                            TOP RECOMMENDATION
                        </span>

                        <strong>
                            91% Match
                        </strong>

                    </div>


                    <h3>
                        Software Developer
                    </h3>


                    <div class="progress">

                        <div></div>

                    </div>

                </div>


                <div class="mini-steps">


                    <div class="mini-step">

                        <span>🎓</span>

                        Profile

                    </div>


                    <div class="mini-step">

                        <span>🎯</span>

                        Career

                    </div>


                    <div class="mini-step">

                        <span>🚀</span>

                        Growth

                    </div>


                </div>


            </div>



            <div class="floating-card float-two">

                <div class="floating-icon">
                    📚
                </div>

                Learning Roadmap

            </div>


        </div>


    </div>

</section>



<!-- =====================================================
     FEATURES
===================================================== -->

<section
    class="section features-section"
    id="features"
>


    <div class="section-heading">

        <span class="section-label">
            WHAT WE OFFER
        </span>

        <h2>
            Everything You Need to Plan Your Career
        </h2>

        <p>
            From discovering the right career to
            improving your skills, Career Path Advisor
            brings your career journey together.
        </p>

    </div>



    <div class="features-grid">


        <div class="feature-card">

            <div class="feature-icon">
                🎯
            </div>

            <h3>
                Career Recommendation
            </h3>

            <p>
                Get personalized career suggestions
                based on your skills, interests,
                education and goals.
            </p>

        </div>


        <div class="feature-card">

            <div class="feature-icon">
                📊
            </div>

            <h3>
                Skill Gap Analysis
            </h3>

            <p>
                Identify the skills you already have
                and discover what you need to improve
                for your chosen career.
            </p>

        </div>


        <div class="feature-card">

            <div class="feature-icon">
                📚
            </div>

            <h3>
                Courses & Certifications
            </h3>

            <p>
                Find relevant learning resources and
                certifications that support your career
                development.
            </p>

        </div>


        <div class="feature-card">

            <div class="feature-icon">
                💼
            </div>

            <h3>
                Internship Opportunities
            </h3>

            <p>
                Explore internship opportunities that
                can help you gain practical experience.
            </p>

        </div>


        <div class="feature-card">

            <div class="feature-icon">
                📈
            </div>

            <h3>
                Learning Tracker
            </h3>

            <p>
                Track your learning activities and
                monitor your progress toward your
                career goals.
            </p>

        </div>


        <div class="feature-card">

            <div class="feature-icon">
                👤
            </div>

            <h3>
                Personalized Profile
            </h3>

            <p>
                Manage your education, skills,
                interests and career goals from
                one profile.
            </p>

        </div>


    </div>

</section>



<!-- =====================================================
     HOW IT WORKS
===================================================== -->

<section
    class="section steps-section"
    id="how-it-works"
>


    <div class="section-heading">

        <span class="section-label">
            SIMPLE PROCESS
        </span>

        <h2>
            Your Career Journey in Four Steps
        </h2>

        <p>
            Start with your profile and move step by
            step toward a clearer career direction.
        </p>

    </div>



    <div class="steps-grid">


        <div class="step-card">

            <div class="step-number">
                01
            </div>

            <h3>
                Create Your Profile
            </h3>

            <p>
                Enter your degree, skills, interests
                and career goals.
            </p>

        </div>


        <div class="step-card">

            <div class="step-number">
                02
            </div>

            <h3>
                Discover Careers
            </h3>

            <p>
                The system analyzes your profile and
                recommends suitable career paths.
            </p>

        </div>


        <div class="step-card">

            <div class="step-number">
                03
            </div>

            <h3>
                Find Your Skill Gaps
            </h3>

            <p>
                Understand which skills you need to
                develop for your selected career.
            </p>

        </div>


        <div class="step-card">

            <div class="step-number">
                04
            </div>

            <h3>
                Start Learning
            </h3>

            <p>
                Explore courses, certifications,
                internships and track your progress.
            </p>

        </div>


    </div>

</section>



<!-- =====================================================
     ABOUT
===================================================== -->

<section
    class="section about-section"
    id="about"
>


    <div class="about-grid">


        <div class="about-content">


            <span class="section-label">
                ABOUT THE PLATFORM
            </span>


            <h2>
                One Place for Your
                Career Journey
            </h2>


            <p>

                Choosing a career can be challenging when
                information about skills, courses,
                certifications and opportunities is
                scattered across different platforms.

                <br><br>

                Career Path Advisor brings these resources
                together and provides a structured approach
                to help students make better-informed career
                decisions.

            </p>


            <div class="about-list">


                <div class="about-item">

                    <div class="check">
                        ✓
                    </div>

                    Personalized career suggestions

                </div>


                <div class="about-item">

                    <div class="check">
                        ✓
                    </div>

                    Career-focused skill analysis

                </div>


                <div class="about-item">

                    <div class="check">
                        ✓
                    </div>

                    Learning resources and certifications

                </div>


                <div class="about-item">

                    <div class="check">
                        ✓
                    </div>

                    Internship and career opportunities

                </div>


            </div>


        </div>



        <div class="about-visual">


            <h3>
                Turn Your Skills Into
                a Clear Career Direction.
            </h3>


            <p>

                Build your profile, discover suitable
                career options and take the next step
                with a personalized learning journey.

            </p>


            <div class="about-stats">


                <div class="stat">

                    <strong>
                        🎯
                    </strong>

                    <span>
                        Career Guidance
                    </span>

                </div>


                <div class="stat">

                    <strong>
                        📚
                    </strong>

                    <span>
                        Learning Resources
                    </span>

                </div>


                <div class="stat">

                    <strong>
                        🚀
                    </strong>

                    <span>
                        Career Growth
                    </span>

                </div>


            </div>


        </div>


    </div>

</section>



<!-- =====================================================
     CALL TO ACTION
===================================================== -->

<section class="cta-section">


    <h2>
        Ready to Find Your Career Path?
    </h2>


    <p>

        Create your profile and start exploring
        personalized career opportunities today.

    </p>


    <a
        href="register.php"
        class="cta-button"
    >

        Get Started for Free
        &nbsp; →

    </a>


</section>



<!-- =====================================================
     FOOTER
===================================================== -->

<footer>


    <div class="footer-container">


        <div class="footer-logo">

            🎯 Career<span> Path Advisor</span>

        </div>


        <div class="footer-text">

            Career Path Advisor ©
            <?php echo date("Y"); ?>.
            Personalized career guidance platform.

        </div>


    </div>


</footer>



</body>

</html>
