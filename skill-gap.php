<?php

session_start();

require_once __DIR__ . "/includes/db.php";


// =========================================
// CHECK LOGIN
// =========================================

if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");
    exit();

}

$user_id = $_SESSION["user_id"];


// =========================================
// GET USER PROFILE
// =========================================

$profile_stmt = $conn->prepare(
    "SELECT degree, skills, interests, career_goal
     FROM profiles
     WHERE user_id = ?
     LIMIT 1"
);

$profile_stmt->bind_param("i", $user_id);

$profile_stmt->execute();

$profile_result = $profile_stmt->get_result();


// =========================================
// CHECK PROFILE
// =========================================

if ($profile_result->num_rows === 0) {

    header("Location: profile.php");
    exit();

}

$profile = $profile_result->fetch_assoc();

$profile_stmt->close();


// =========================================
// USER DATA
// =========================================

$degree = trim($profile["degree"] ?? "");

$user_skills = !empty($profile["skills"])
    ? array_map("trim", explode(",", $profile["skills"]))
    : [];

$career_goal = trim($profile["career_goal"] ?? "");


// =========================================
// CLEAN USER SKILLS
// =========================================

$user_skills_clean = [];

foreach ($user_skills as $skill) {

    $skill = strtolower(trim($skill));

    if ($skill !== "") {

        $user_skills_clean[] = $skill;

    }

}


// =========================================
// FIND CAREER
// =========================================
//
// First try career_goal.
// If career_goal doesn't exactly match,
// select the first career.
//

$career = null;


// Try career goal first

if (!empty($career_goal)) {

    $career_stmt = $conn->prepare(
        "SELECT
            career_id,
            career_name,
            description,
            required_skills
         FROM careers
         WHERE LOWER(career_name) = LOWER(?)
         LIMIT 1"
    );

    $career_stmt->bind_param("s", $career_goal);

    $career_stmt->execute();

    $career_result = $career_stmt->get_result();

    if ($career_result->num_rows > 0) {

        $career = $career_result->fetch_assoc();

    }

    $career_stmt->close();

}


// If career goal didn't match, use first career

if (!$career) {

    $career_result = $conn->query(
        "SELECT
            career_id,
            career_name,
            description,
            required_skills
         FROM careers
         ORDER BY career_id ASC
         LIMIT 1"
    );

    if ($career_result && $career_result->num_rows > 0) {

        $career = $career_result->fetch_assoc();

    }

}


// =========================================
// PREPARE CAREER SKILLS
// =========================================

$required_skills = [];

$skills_you_have = [];

$missing_skills = [];


if ($career) {

    $required_skills = !empty($career["required_skills"])
        ? array_map(
            "trim",
            explode(",", $career["required_skills"])
        )
        : [];


    foreach ($required_skills as $required_skill) {

        $required_clean =
            strtolower(trim($required_skill));


        if ($required_clean === "") {

            continue;

        }


        // Check whether user has this skill

        if (
            in_array(
                $required_clean,
                $user_skills_clean,
                true
            )
        ) {

            $skills_you_have[] =
                $required_skill;

        } else {

            $missing_skills[] =
                $required_skill;

        }

    }

}


// =========================================
// CALCULATE SKILL MATCH
// =========================================

$total_required =
    count($required_skills);

$total_have =
    count($skills_you_have);

$total_missing =
    count($missing_skills);


$skill_percentage = 0;


if ($total_required > 0) {

    $skill_percentage =
        round(
            ($total_have / $total_required) * 100
        );

}


// =========================================
// GAP PERCENTAGE
// =========================================

$gap_percentage =
    100 - $skill_percentage;


// =========================================
// MESSAGE
// =========================================

if ($skill_percentage >= 80) {

    $status_message =
        "Excellent! You already have most of the required skills.";

} elseif ($skill_percentage >= 50) {

    $status_message =
        "Good progress! A few additional skills will strengthen your career profile.";

} elseif ($skill_percentage > 0) {

    $status_message =
        "You have a good starting point. Focus on the missing skills below.";

} else {

    $status_message =
        "Start learning the required skills to prepare for this career.";

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

    <title>Skill Gap Analysis | Career Path Advisor</title>


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
        href="css/style.css"
    >


    <style>

        /* =========================================
           PAGE
           ========================================= */

        .skill-page {

            min-height: 100vh;

            background: #f6f7fb;

            padding: 40px 20px;

        }


        .skill-container {

            width: 100%;

            max-width: 1050px;

            margin: auto;

        }


        /* =========================================
           HEADER
           ========================================= */

        .skill-header {

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 20px;

            margin-bottom: 28px;

        }


        .skill-label {

            color: #5b5cf0;

            font-size: 10px;

            font-weight: 800;

            letter-spacing: 1.8px;

        }


        .skill-header h1 {

            margin-top: 7px;

            color: #171827;

            font-size: 30px;

            font-weight: 800;

            letter-spacing: -1px;

        }


        .skill-header p {

            margin-top: 7px;

            color: #626578;

            font-size: 13px;

        }


        .back-button {

            padding: 11px 17px;

            border: 1px solid #e3e5ee;

            border-radius: 10px;

            background: #ffffff;

            color: #626578;

            font-size: 11px;

            font-weight: 600;

            text-decoration: none;

            transition: 0.25s ease;

        }


        .back-button:hover {

            border-color: #5b5cf0;

            color: #5b5cf0;

        }


        /* =========================================
           CAREER BANNER
           ========================================= */

        .career-banner {

            position: relative;

            overflow: hidden;

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 25px;

            padding: 25px 28px;

            margin-bottom: 20px;

            border-radius: 17px;

            background:
                linear-gradient(
                    135deg,
                    #292b66,
                    #5b5cf0
                );

            color: #ffffff;

            box-shadow:
                0 15px 35px
                rgba(91,92,240,0.20);

        }


        .career-banner::after {

            content: "";

            position: absolute;

            width: 220px;

            height: 220px;

            right: -100px;

            top: -100px;

            border-radius: 50%;

            border:
                1px solid rgba(255,255,255,0.15);

        }


        .career-banner-content {

            position: relative;

            z-index: 2;

        }


        .career-banner-label {

            font-size: 9px;

            font-weight: 700;

            letter-spacing: 1.5px;

            color:
                rgba(255,255,255,0.65);

        }


        .career-banner h2 {

            margin-top: 7px;

            font-size: 25px;

            font-weight: 800;

            letter-spacing: -0.6px;

        }


        .career-banner p {

            max-width: 650px;

            margin-top: 7px;

            color:
                rgba(255,255,255,0.72);

            font-size: 11px;

            line-height: 1.6;

        }


        .degree-badge {

            position: relative;

            z-index: 2;

            padding: 9px 13px;

            border-radius: 20px;

            background:
                rgba(255,255,255,0.12);

            border:
                1px solid rgba(255,255,255,0.15);

            font-size: 10px;

            font-weight: 600;

            white-space: nowrap;

        }


        /* =========================================
           OVERVIEW
           ========================================= */

        .analysis-grid {

            display: grid;

            grid-template-columns:
                0.8fr 1.2fr;

            gap: 18px;

            margin-bottom: 20px;

        }


        .analysis-card {

            padding: 23px;

            border:
                1px solid #e3e5ee;

            border-radius: 15px;

            background: #ffffff;

        }


        .card-title {

            color: #171827;

            font-size: 14px;

            font-weight: 800;

        }


        .card-subtitle {

            margin-top: 5px;

            color: #9295a7;

            font-size: 10px;

        }


        /* =========================================
           SCORE
           ========================================= */

        .score-section {

            display: flex;

            align-items: center;

            gap: 20px;

            margin-top: 22px;

        }


        .score-circle {

            width: 110px;

            height: 110px;

            min-width: 110px;

            display: flex;

            flex-direction: column;

            align-items: center;

            justify-content: center;

            border-radius: 50%;

            border: 7px solid #eeeeff;

            background: #ffffff;

        }


        .score-circle strong {

            color: #5b5cf0;

            font-size: 28px;

            font-weight: 800;

        }


        .score-circle span {

            margin-top: 2px;

            color: #9295a7;

            font-size: 8px;

            font-weight: 700;

            letter-spacing: 0.8px;

        }


        .score-info h3 {

            font-size: 15px;

            font-weight: 800;

        }


        .score-info p {

            margin-top: 7px;

            color: #626578;

            font-size: 10px;

            line-height: 1.6;

        }


        /* =========================================
           PROGRESS
           ========================================= */

        .progress-area {

            margin-top: 22px;

        }


        .progress-header {

            display: flex;

            justify-content: space-between;

            margin-bottom: 7px;

            color: #626578;

            font-size: 10px;

            font-weight: 600;

        }


        .progress {

            height: 8px;

            overflow: hidden;

            border-radius: 10px;

            background: #eeeeF5;

        }


        .progress-fill {

            height: 100%;

            border-radius: 10px;

            background: #5b5cf0;

        }


        /* =========================================
           SKILL LIST
           ========================================= */

        .skill-list {

            display: grid;

            grid-template-columns:
                repeat(2, 1fr);

            gap: 10px;

            margin-top: 20px;

        }


        .skill-item {

            display: flex;

            align-items: center;

            gap: 10px;

            padding: 11px 12px;

            border-radius: 10px;

            background: #f8f8fc;

            border: 1px solid #eeeeF5;

        }


        .skill-status {

            width: 25px;

            height: 25px;

            min-width: 25px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 7px;

            font-size: 12px;

            font-weight: 800;

        }


        .skill-status.have {

            background: #ecfdf3;

            color: #16804b;

        }


        .skill-status.missing {

            background: #fff1f1;

            color: #d13c3c;

        }


        .skill-item span:last-child {

            color: #626578;

            font-size: 11px;

            font-weight: 600;

        }


        /* =========================================
           MISSING SKILLS
           ========================================= */

        .missing-section {

            margin-top: 20px;

        }


        .missing-grid {

            display: flex;

            flex-wrap: wrap;

            gap: 9px;

            margin-top: 17px;

        }


        .missing-tag {

            display: flex;

            align-items: center;

            gap: 6px;

            padding: 9px 12px;

            border-radius: 9px;

            background: #fff5f5;

            border: 1px solid #ffdcdc;

            color: #d13c3c;

            font-size: 10px;

            font-weight: 700;

        }


        /* =========================================
           ACTIONS
           ========================================= */

        .action-section {

            display: flex;

            gap: 10px;

            margin-top: 22px;

        }


        .action-button {

            display: inline-flex;

            align-items: center;

            justify-content: center;

            padding: 12px 17px;

            border-radius: 10px;

            background: #5b5cf0;

            color: #ffffff;

            font-size: 10px;

            font-weight: 700;

            text-decoration: none;

            transition: 0.25s ease;

        }


        .action-button:hover {

            background: #4849d9;

            transform: translateY(-2px);

        }


        .action-button.secondary {

            background: #ffffff;

            color: #626578;

            border: 1px solid #e3e5ee;

        }


        .action-button.secondary:hover {

            color: #5b5cf0;

            border-color: #5b5cf0;

            background: #ffffff;

        }


        /* =========================================
           NO DATA
           ========================================= */

        .no-data {

            padding: 40px 25px;

            border-radius: 15px;

            background: #ffffff;

            border: 1px solid #e3e5ee;

            text-align: center;

        }


        .no-data-icon {

            font-size: 35px;

        }


        .no-data h2 {

            margin-top: 12px;

            font-size: 19px;

            font-weight: 800;

        }


        .no-data p {

            margin-top: 7px;

            color: #626578;

            font-size: 11px;

        }


        /* =========================================
           RESPONSIVE
           ========================================= */

        @media (max-width: 750px) {

            .skill-page {

                padding: 25px 15px;

            }


            .skill-header {

                align-items: flex-start;

                flex-direction: column;

            }


            .skill-header h1 {

                font-size: 25px;

            }


            .career-banner {

                align-items: flex-start;

                flex-direction: column;

            }


            .analysis-grid {

                grid-template-columns: 1fr;

            }


            .skill-list {

                grid-template-columns: 1fr;

            }

        }


        @media (max-width: 450px) {

            .score-section {

                flex-direction: column;

                align-items: flex-start;

            }


            .action-section {

                flex-direction: column;

            }


            .action-button {

                width: 100%;

            }

        }

    </style>

</head>


<body>


<div class="skill-page">


    <div class="skill-container">


        <!-- =====================================
             HEADER
             ===================================== -->

        <div class="skill-header">


            <div>

                <span class="skill-label">

                    SKILL ANALYSIS

                </span>


                <h1>

                    Skill Gap Analysis

                </h1>


                <p>

                    Understand which skills you already have
                    and what you need to learn next.

                </p>

            </div>


            <a
                href="dashboard.php"
                class="back-button"
            >

                ← Dashboard

            </a>


        </div>



        <?php if ($career): ?>


            <!-- =================================
                 CAREER BANNER
                 ================================= -->

            <div class="career-banner">


                <div class="career-banner-content">


                    <span class="career-banner-label">

                        ANALYSIS FOR

                    </span>


                    <h2>

                        <?php

                        echo htmlspecialchars(
                            $career["career_name"]
                        );

                        ?>

                    </h2>


                    <p>

                        <?php

                        echo htmlspecialchars(
                            $career["description"]
                        );

                        ?>

                    </p>


                </div>


                <div class="degree-badge">

                    🎓

                    <?php

                    echo htmlspecialchars($degree);

                    ?>

                </div>


            </div>



            <!-- =================================
                 ANALYSIS GRID
                 ================================= -->

            <div class="analysis-grid">


                <!-- SCORE CARD -->

                <div class="analysis-card">


                    <div class="card-title">

                        Your Skill Score

                    </div>


                    <div class="card-subtitle">

                        Based on the required skills for this career.

                    </div>


                    <div class="score-section">


                        <div class="score-circle">


                            <strong>

                                <?php

                                echo $skill_percentage;

                                ?>%

                            </strong>


                            <span>

                                SKILL MATCH

                            </span>


                        </div>


                        <div class="score-info">


                            <h3>

                                <?php

                                echo $total_have;

                                ?>

                                of

                                <?php

                                echo $total_required;

                                ?>

                                skills

                            </h3>


                            <p>

                                <?php

                                echo htmlspecialchars(
                                    $status_message
                                );

                                ?>

                            </p>


                        </div>


                    </div>


                    <div class="progress-area">


                        <div class="progress-header">


                            <span>
                                Skill Progress
                            </span>


                            <strong>

                                <?php

                                echo $skill_percentage;

                                ?>%

                            </strong>


                        </div>


                        <div class="progress">


                            <div
                                class="progress-fill"
                                style="width:
                                <?php

                                echo $skill_percentage;

                                ?>%;"
                            ></div>


                        </div>


                    </div>


                </div>



                <!-- SKILLS CARD -->

                <div class="analysis-card">


                    <div class="card-title">

                        Skill Breakdown

                    </div>


                    <div class="card-subtitle">

                        Your skills compared with career requirements.

                    </div>


                    <div class="skill-list">


                        <?php foreach (
                            $skills_you_have
                            as $skill
                        ): ?>


                            <div class="skill-item">


                                <div class="skill-status have">

                                    ✓

                                </div>


                                <span>

                                    <?php

                                    echo htmlspecialchars($skill);

                                    ?>

                                </span>


                            </div>


                        <?php endforeach; ?>



                        <?php foreach (
                            $missing_skills
                            as $skill
                        ): ?>


                            <div class="skill-item">


                                <div class="skill-status missing">

                                    !

                                </div>


                                <span>

                                    <?php

                                    echo htmlspecialchars($skill);

                                    ?>

                                </span>


                            </div>


                        <?php endforeach; ?>


                    </div>


                </div>


            </div>



            <!-- =================================
                 MISSING SKILLS
                 ================================= -->

            <div class="analysis-card missing-section">


                <div class="card-title">

                    Skills You Need to Learn

                </div>


                <div class="card-subtitle">

                    These skills are required for your selected career
                    but are not currently listed in your profile.

                </div>


                <?php if (!empty($missing_skills)): ?>


                    <div class="missing-grid">


                        <?php foreach (
                            $missing_skills
                            as $skill
                        ): ?>


                            <div class="missing-tag">

                                ✦

                                <?php

                                echo htmlspecialchars($skill);

                                ?>

                            </div>


                        <?php endforeach; ?>


                    </div>


                <?php else: ?>


                    <div
                        style="
                            margin-top:18px;
                            color:#16804b;
                            font-size:11px;
                            font-weight:700;
                        "
                    >

                        ✓ Excellent! You have all the required
                        skills for this career.

                    </div>


                <?php endif; ?>


                <!-- ACTION BUTTONS -->

                <div class="action-section">


                    <a
                        href="profile.php"
                        class="action-button"
                    >

                        ✏ Update My Skills

                    </a>


                    <a
                        href="career.php"
                        class="action-button secondary"
                    >

                        ← Career Recommendations

                    </a>


                </div>


            </div>


        <?php else: ?>


            <!-- =================================
                 NO CAREER
                 ================================= -->

            <div class="no-data">


                <div class="no-data-icon">

                    🎯

                </div>


                <h2>

                    No Career Found

                </h2>


                <p>

                    Please add career data to the careers table
                    before using Skill Gap Analysis.

                </p>


                <div class="action-section"
                     style="justify-content:center;">

                    <a
                        href="profile.php"
                        class="action-button"
                    >

                        Complete Profile

                    </a>


                    <a
                        href="dashboard.php"
                        class="action-button secondary"
                    >

                        Dashboard

                    </a>

                </div>


            </div>


        <?php endif; ?>


    </div>


</div>


</body>

</html>
```
