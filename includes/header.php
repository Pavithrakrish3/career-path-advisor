<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/* Check login */

if (!isset($_SESSION['user_id'])) {

    header("Location: login.php");
    exit();

}


/* Current page */

$current_page = basename($_SERVER['PHP_SELF']);


/* User details */

$user_name = $_SESSION['name'] ?? 'User';

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
        Career Path Advisor
    </title>


    <!-- Google Font -->

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
    >

    <link
        rel="preconnect"
        href="https://fonts.googleapis.com"
        crossorigin
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap"
        rel="stylesheet"
    >


    <!-- Main CSS -->

    <link
        rel="stylesheet"
        href="/career_path_advisor/css/style.css">
    >

</head>


<body>


<div class="app-layout">


    <!-- =====================================
         SIDEBAR
    ====================================== -->

    <aside class="sidebar">


        <!-- BRAND -->

        <div class="sidebar-brand">

            <img
                src="images/logo.png"
                alt="Career Path Advisor"
                class="sidebar-logo"
            >


            <div class="sidebar-brand-text">

                <h2>
                    CareerPath
                </h2>

                <span>
                    ADVISOR
                </span>

            </div>

        </div>



        <!-- USER -->

        <div class="sidebar-user">

            <div class="user-avatar">

                <?php

                echo strtoupper(
                    substr(
                        $user_name,
                        0,
                        1
                    )
                );

                ?>

            </div>


            <div class="user-info">

                <strong>

                    <?php

                    echo htmlspecialchars(
                        $user_name
                    );

                    ?>

                </strong>

                <small>
                    Student
                </small>

            </div>

        </div>



        <!-- =====================================
             NAVIGATION
        ====================================== -->

        <nav class="sidebar-nav">


            <!-- DASHBOARD -->

            <a
                href="dashboard.php"
                class="<?php
                    echo $current_page === 'dashboard.php'
                        ? 'active'
                        : '';
                ?>"
            >

                <span>🏠</span>

                <span>
                    Dashboard
                </span>

            </a>



            <!-- PROFILE -->

            <a
                href="profile.php"
                class="<?php
                    echo $current_page === 'profile.php'
                        ? 'active'
                        : '';
                ?>"
            >

                <span>👤</span>

                <span>
                    My Profile
                </span>

            </a>



            <!-- CAREER -->

            <a
                href="career.php"
                class="<?php
                    echo $current_page === 'career.php'
                        ? 'active'
                        : '';
                ?>"
            >

                <span>🎯</span>

                <span>
                    Career Recommendation
                </span>

            </a>



            <!-- SKILL GAP -->

            <a
                href="skill-gap.php"
                class="<?php
                    echo $current_page === 'skill-gap.php'
                        ? 'active'
                        : '';
                ?>"
            >

                <span>📊</span>

                <span>
                    Skill Gap
                </span>

            </a>



            <!-- COURSES -->

            <a
                href="courses.php"
                class="<?php
                    echo $current_page === 'courses.php'
                        ? 'active'
                        : '';
                ?>"
            >

                <span>📚</span>

                <span>
                    Courses
                </span>

            </a>



            <!-- CERTIFICATIONS -->

            <a
                href="certifications.php"
                class="<?php
                    echo $current_page === 'certifications.php'
                        ? 'active'
                        : '';
                ?>"
            >

                <span>🏆</span>

                <span>
                    Certifications
                </span>

            </a>



            <!-- INTERNSHIPS -->

            <a
                href="internships.php"
                class="<?php
                    echo $current_page === 'internships.php'
                        ? 'active'
                        : '';
                ?>"
            >

                <span>💼</span>

                <span>
                    Internships
                </span>

            </a>



            



            <!-- TRACKER -->

            <a
                href="tracker.php"
                class="<?php
                    echo $current_page === 'tracker.php'
                        ? 'active'
                        : '';
                ?>"
            >

                <span>📈</span>

                <span>
                    Learning Tracker
                </span>

            </a>



            <!-- RESUME -->

            <a
                href="resume.php"
                class="<?php
                    echo $current_page === 'resume.php'
                        ? 'active'
                        : '';
                ?>"
            >

                <span>📄</span>

                <span>
                    Resume Builder
                </span>

            </a>


        </nav>



        <!-- LOGOUT -->

        <div class="sidebar-bottom">

            <a
                href="logout.php"
                class="logout-link"
            >

                <span>
                    🚪
                </span>

                <span>
                    Logout
                </span>

            </a>

        </div>


    </aside>



    <!-- =====================================
         MAIN CONTENT
    ====================================== -->

    <main class="main-content">


        <!-- TOP BAR -->

        <header class="topbar">


            <div>

                <h1>

                    <?php

                    $page_titles = [

                        'dashboard.php' =>
                            'Dashboard',

                        'profile.php' =>
                            'My Profile',

                        'career.php' =>
                            'Career Recommendation',

                        'skill-gap.php' =>
                            'Skill Gap Analysis',

                        'courses.php' =>
                            'Recommended Courses',

                        'certifications.php' =>
                            'Certifications',

                        'internships.php' =>
                            'Internship Opportunities',

                        'roadmap.php' =>
                            'Learning Roadmap',

                        'tracker.php' =>
                            'Learning Tracker',

                        'resume.php' =>
                            'Resume Builder'

                    ];


                    echo htmlspecialchars(

                        $page_titles[$current_page]
                        ?? 'Career Path Advisor'

                    );

                    ?>

                </h1>


                <p>
                    Plan your career. Build your future.
                </p>

            </div>



            <!-- TOPBAR USER -->

            <div class="topbar-user">


                <div class="topbar-avatar">

                    <?php

                    echo strtoupper(
                        substr(
                            $user_name,
                            0,
                            1
                        )
                    );

                    ?>

                </div>


                <span>

                    <?php

                    echo htmlspecialchars(
                        $user_name
                    );

                    ?>

                </span>


            </div>


        </header>



        <!-- PAGE CONTENT START -->
