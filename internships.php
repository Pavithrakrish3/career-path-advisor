<?php

session_start();

require_once "includes/db.php";


// =========================================
// CHECK LOGIN
// =========================================

if (!isset($_SESSION["user_id"])) {

    header("Location: login.php");
    exit();

}


// =========================================
// GET USER DETAILS
// =========================================

$user_id = $_SESSION["user_id"];

$user_name = "Student";

$user_sql = "
    SELECT name
    FROM users
    WHERE user_id = ?
";

$user_stmt = $conn->prepare($user_sql);

if ($user_stmt) {

    $user_stmt->bind_param("i", $user_id);

    $user_stmt->execute();

    $user_result = $user_stmt->get_result();

    if ($user_result->num_rows > 0) {

        $user = $user_result->fetch_assoc();

        $user_name = $user["name"];

    }

    $user_stmt->close();

}


// =========================================
// GET INTERNSHIPS
// =========================================

$internships = [];

$sql = "
    SELECT
        internship_id,
        internship_title,
        company_name,
        description,
        internship_link
    FROM internships
    ORDER BY internship_id ASC
";

$result = $conn->query($sql);

if ($result) {

    while ($row = $result->fetch_assoc()) {

        $internships[] = $row;

    }

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
    Internships | Career Path Advisor
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
    href="css/style.css"
>


<style>

/* =========================================
   INTERNSHIP PAGE
   ========================================= */

.internship-page {

    min-height: 100vh;

    padding: 40px 20px;

    background: #f6f7fb;

}


.internship-container {

    width: 100%;

    max-width: 1100px;

    margin: auto;

}


/* =========================================
   HEADER
   ========================================= */

.internship-header {

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 20px;

    margin-bottom: 28px;

}


.internship-label {

    color: #5b5cf0;

    font-size: 10px;

    font-weight: 800;

    letter-spacing: 1.8px;

}


.internship-header h1 {

    margin-top: 7px;

    color: #171827;

    font-size: 30px;

    font-weight: 800;

    letter-spacing: -1px;

}


.internship-header p {

    max-width: 650px;

    margin-top: 8px;

    color: #626578;

    font-size: 13px;

    line-height: 1.6;

}


/* =========================================
   BACK BUTTON
   ========================================= */

.back-button {

    display: inline-flex;

    align-items: center;

    justify-content: center;

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

    color: #5b5cf0;

    border-color: #5b5cf0;

}


/* =========================================
   BANNER
   ========================================= */

.internship-banner {

    position: relative;

    overflow: hidden;

    padding: 24px 28px;

    margin-bottom: 22px;

    border-radius: 16px;

    background:
        linear-gradient(
            135deg,
            #292b66 0%,
            #5b5cf0 100%
        );

    color: #ffffff;

    box-shadow:
        0 15px 35px
        rgba(91,92,240,0.18);

}


.internship-banner::after {

    content: "";

    position: absolute;

    width: 220px;

    height: 220px;

    right: -100px;

    top: -120px;

    border: 1px solid
        rgba(255,255,255,0.13);

    border-radius: 50%;

}


.internship-banner h2 {

    position: relative;

    z-index: 2;

    font-size: 21px;

    font-weight: 800;

}


.internship-banner p {

    position: relative;

    z-index: 2;

    max-width: 680px;

    margin-top: 7px;

    color:
        rgba(255,255,255,0.70);

    font-size: 11px;

    line-height: 1.6;

}


/* =========================================
   INTERNSHIP GRID
   ========================================= */

.internship-grid {

    display: grid;

    grid-template-columns:
        repeat(2, 1fr);

    gap: 17px;

}


/* =========================================
   CARD
   ========================================= */

.internship-card {

    position: relative;

    display: flex;

    flex-direction: column;

    padding: 22px;

    border: 1px solid #e3e5ee;

    border-radius: 15px;

    background: #ffffff;

    transition: 0.3s ease;

}


.internship-card:hover {

    transform: translateY(-4px);

    border-color: #d7d8f8;

    box-shadow:
        0 14px 30px
        rgba(30,32,70,0.08);

}


/* =========================================
   CARD TOP
   ========================================= */

.internship-top {

    display: flex;

    align-items: flex-start;

    justify-content: space-between;

    gap: 15px;

}


.internship-icon {

    width: 43px;

    height: 43px;

    min-width: 43px;

    display: flex;

    align-items: center;

    justify-content: center;

    border-radius: 11px;

    background: #eeeeff;

    font-size: 19px;

}


.internship-number {

    color: #9295a7;

    font-size: 9px;

    font-weight: 700;

}


/* =========================================
   CONTENT
   ========================================= */

.internship-card h2 {

    margin-top: 17px;

    color: #171827;

    font-size: 16px;

    font-weight: 800;

    line-height: 1.35;

}


.company-name {

    display: inline-block;

    margin-top: 7px;

    color: #5b5cf0;

    font-size: 10px;

    font-weight: 700;

}


.internship-description {

    min-height: 55px;

    margin-top: 12px;

    color: #626578;

    font-size: 11px;

    line-height: 1.65;

}


/* =========================================
   FOOTER
   ========================================= */

.internship-footer {

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 12px;

    margin-top: 20px;

    padding-top: 15px;

    border-top: 1px solid #eeeeF5;

}


.internship-type {

    color: #9295a7;

    font-size: 9px;

    font-weight: 600;

}


.internship-button {

    display: inline-flex;

    align-items: center;

    justify-content: center;

    gap: 7px;

    padding: 9px 13px;

    border-radius: 8px;

    background: #5b5cf0;

    color: #ffffff;

    font-size: 10px;

    font-weight: 700;

    text-decoration: none;

    transition: 0.25s ease;

}


.internship-button:hover {

    background: #4849d9;

    transform: translateY(-1px);

}


/* =========================================
   EMPTY STATE
   ========================================= */

.empty-internship {

    padding: 50px 20px;

    border: 1px solid #e3e5ee;

    border-radius: 15px;

    background: #ffffff;

    text-align: center;

}


.empty-icon {

    font-size: 38px;

}


.empty-internship h2 {

    margin-top: 12px;

    color: #171827;

    font-size: 19px;

    font-weight: 800;

}


.empty-internship p {

    margin-top: 7px;

    color: #626578;

    font-size: 11px;

}


/* =========================================
   RESPONSIVE
   ========================================= */

@media (max-width: 750px) {

    .internship-page {

        padding: 25px 15px;

    }


    .internship-header {

        align-items: flex-start;

        flex-direction: column;

    }


    .internship-header h1 {

        font-size: 25px;

    }


    .internship-grid {

        grid-template-columns: 1fr;

    }

}


@media (max-width: 450px) {

    .internship-banner {

        padding: 21px;

    }


    .internship-card {

        padding: 18px;

    }


    .internship-footer {

        align-items: flex-start;

        flex-direction: column;

    }


    .internship-button {

        width: 100%;

    }

}

</style>

</head>


<body>


<div class="internship-page">


<div class="internship-container">


<!-- =====================================
     HEADER
     ===================================== -->

<div class="internship-header">


    <div>

        <span class="internship-label">

            CAREER OPPORTUNITIES

        </span>


        <h1>

            Internships

        </h1>


        <p>

            Explore internship opportunities that
            can help you gain practical experience,
            develop professional skills and prepare
            for your future career.

        </p>

    </div>


    <a
        href="dashboard.php"
        class="back-button"
    >

        ← Dashboard

    </a>


</div>



<!-- =====================================
     BANNER
     ===================================== -->

<div class="internship-banner">


    <h2>

        Start Building Your Experience,

        <?php echo htmlspecialchars($user_name); ?>

        💼

    </h2>


    <p>

        Practical experience through internships
        can help you understand the industry and
        strengthen your career profile.

    </p>


</div>



<!-- =====================================
     INTERNSHIP LIST
     ===================================== -->

<?php if (!empty($internships)): ?>


<div class="internship-grid">


<?php foreach (
    $internships
    as $index => $internship
): ?>


<div class="internship-card">


    <!-- TOP -->

    <div class="internship-top">


        <div class="internship-icon">

            <?php

            $icons = [
                "💼",
                "💻",
                "🚀",
                "🌐",
                "☁️",
                "📊"
            ];

            echo $icons[
                $index % count($icons)
            ];

            ?>

        </div>


        <span class="internship-number">

            INTERNSHIP

            <?php

            echo str_pad(
                $index + 1,
                2,
                "0",
                STR_PAD_LEFT
            );

            ?>

        </span>


    </div>



    <!-- TITLE -->

    <h2>

        <?php

        echo htmlspecialchars(
            $internship["internship_title"]
        );

        ?>

    </h2>



    <!-- COMPANY -->

    <span class="company-name">

        <?php

        echo htmlspecialchars(
            $internship["company_name"]
        );

        ?>

    </span>



    <!-- DESCRIPTION -->

    <p class="internship-description">

        <?php

        echo htmlspecialchars(
            $internship["description"]
        );

        ?>

    </p>



    <!-- FOOTER -->

    <div class="internship-footer">


        <span class="internship-type">

            💼 Internship Opportunity

        </span>


        <?php if (
            !empty(
                $internship["internship_link"]
            )
        ): ?>


        <a
            href="<?php

                echo htmlspecialchars(
                    $internship["internship_link"]
                );

            ?>"
            target="_blank"
            rel="noopener noreferrer"
            class="internship-button"
        >

            View Opportunity →

        </a>


        <?php endif; ?>


    </div>


</div>


<?php endforeach; ?>


</div>


<?php else: ?>


<!-- =================================
     EMPTY STATE
     ================================= -->

<div class="empty-internship">


    <div class="empty-icon">

        💼

    </div>


    <h2>

        No Internships Available

    </h2>


    <p>

        Internship opportunities will appear
        here once internship data is added.

    </p>


</div>


<?php endif; ?>


</div>

</div>


</body>

</html>