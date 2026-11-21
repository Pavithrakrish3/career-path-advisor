<?php

/* =========================================
   SESSION
========================================= */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/* =========================================
   DATABASE
========================================= */

require_once "includes/db.php";


/* =========================================
   LOGIN CHECK
========================================= */

if (!isset($_SESSION['user_id'])) {

    header("Location: login.php");
    exit();

}

$user_id = $_SESSION['user_id'];


/* =========================================
   GET USER NAME
========================================= */

$user_name = "Student";

$stmt = $conn->prepare("
    SELECT name
    FROM users
    WHERE user_id = ?
    LIMIT 1
");

$stmt->bind_param("i", $user_id);

$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {

    $user = $result->fetch_assoc();

    $user_name = $user['name'] ?? "Student";

}

$stmt->close();


/* =========================================
   GET COURSES
========================================= */

$courses = [];

$course_query = $conn->query("
    SELECT
        course_id,
        course_name,
        provider,
        description,
        course_link
    FROM courses
    ORDER BY course_id ASC
");


if ($course_query) {

    while ($course = $course_query->fetch_assoc()) {

        $courses[] = $course;

    }

}


/* =========================================
   HEADER
========================================= */

require_once "includes/header.php";

?>


<style>

/* =========================================
   COURSES PAGE
========================================= */

.courses-page {

    min-height: 100vh;

    padding: 40px 20px;

    background: #f6f7fb;

}


.courses-container {

    width: 100%;

    max-width: 1100px;

    margin: auto;

}


/* =========================================
   HEADER
========================================= */

.courses-header {

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 20px;

    margin-bottom: 28px;

}


.courses-label {

    color: #5b5cf0;

    font-size: 10px;

    font-weight: 800;

    letter-spacing: 1.8px;

}


.courses-header h1 {

    margin-top: 7px;

    color: #171827;

    font-size: 30px;

    font-weight: 800;

    letter-spacing: -1px;

}


.courses-header p {

    max-width: 650px;

    margin-top: 8px;

    color: #626578;

    font-size: 13px;

    line-height: 1.6;

}


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
   WELCOME BANNER
========================================= */

.course-banner {

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


.course-banner::after {

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


.course-banner h2 {

    position: relative;

    z-index: 2;

    font-size: 21px;

    font-weight: 800;

}


.course-banner p {

    position: relative;

    z-index: 2;

    margin-top: 7px;

    max-width: 650px;

    color:
        rgba(255,255,255,0.70);

    font-size: 11px;

    line-height: 1.6;

}


/* =========================================
   COURSE GRID
========================================= */

.course-grid {

    display: grid;

    grid-template-columns:
        repeat(2, 1fr);

    gap: 17px;

}


/* =========================================
   COURSE CARD
========================================= */

.course-card {

    position: relative;

    display: flex;

    flex-direction: column;

    padding: 22px;

    border: 1px solid #e3e5ee;

    border-radius: 15px;

    background: #ffffff;

    transition: 0.3s ease;

}


.course-card:hover {

    transform: translateY(-4px);

    border-color: #d7d8f8;

    box-shadow:
        0 14px 30px
        rgba(30,32,70,0.08);

}


/* =========================================
   COURSE TOP
========================================= */

.course-top {

    display: flex;

    align-items: flex-start;

    justify-content: space-between;

    gap: 15px;

}


.course-icon {

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


.course-number {

    color: #9295a7;

    font-size: 9px;

    font-weight: 700;

}


/* =========================================
   COURSE CONTENT
========================================= */

.course-card h2 {

    margin-top: 17px;

    color: #171827;

    font-size: 16px;

    font-weight: 800;

    line-height: 1.35;

}


.provider {

    display: inline-block;

    margin-top: 7px;

    color: #5b5cf0;

    font-size: 10px;

    font-weight: 700;

}


.course-description {

    margin-top: 12px;

    min-height: 55px;

    color: #626578;

    font-size: 11px;

    line-height: 1.65;

}


/* =========================================
   COURSE FOOTER
========================================= */

.course-footer {

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 12px;

    margin-top: 20px;

    padding-top: 15px;

    border-top: 1px solid #eeeeF5;

}


.course-type {

    color: #9295a7;

    font-size: 9px;

    font-weight: 600;

}


.course-button {

    display: inline-flex;

    align-items: center;

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


.course-button:hover {

    background: #4849d9;

    transform: translateY(-1px);

}


/* =========================================
   EMPTY STATE
========================================= */

.empty-courses {

    padding: 50px 20px;

    border: 1px solid #e3e5ee;

    border-radius: 15px;

    background: #ffffff;

    text-align: center;

}


.empty-icon {

    font-size: 38px;

}


.empty-courses h2 {

    margin-top: 12px;

    color: #171827;

    font-size: 19px;

    font-weight: 800;

}


.empty-courses p {

    margin-top: 7px;

    color: #626578;

    font-size: 11px;

}


/* =========================================
   RESPONSIVE
========================================= */

@media (max-width: 750px) {

    .courses-page {

        padding: 25px 15px;

    }


    .courses-header {

        align-items: flex-start;

        flex-direction: column;

    }


    .courses-header h1 {

        font-size: 25px;

    }


    .course-grid {

        grid-template-columns: 1fr;

    }

}


@media (max-width: 450px) {

    .course-banner {

        padding: 21px;

    }


    .course-card {

        padding: 18px;

    }


    .course-footer {

        align-items: flex-start;

        flex-direction: column;

    }


    .course-button {

        width: 100%;

        justify-content: center;

    }

}

</style>


<div class="courses-page">


<div class="courses-container">


    <!-- =====================================
         HEADER
    ====================================== -->

    <div class="courses-header">


        <div>

            <span class="courses-label">

                LEARNING RESOURCES

            </span>


            <h1>

                Recommended Courses

            </h1>


            <p>

                Explore courses that can help you build
                the technical and professional skills needed
                for your career journey.

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
    ====================================== -->

    <div class="course-banner">


        <h2>

            Hello,

            <?php

            echo htmlspecialchars(
                $user_name
            );

            ?> 👋

        </h2>


        <p>

            Continue learning and strengthen your skills
            with carefully selected learning resources.

        </p>


    </div>



    <!-- =====================================
         COURSE LIST
    ====================================== -->

    <?php if (!empty($courses)): ?>


        <div class="course-grid">


            <?php foreach (
                $courses
                as $index => $course
            ): ?>


                <div class="course-card">


                    <!-- COURSE TOP -->

                    <div class="course-top">


                        <div class="course-icon">

                            <?php

                            $icons = [

                                "📘",
                                "🐍",
                                "🗄️",
                                "🌐",
                                "⚡",
                                "🔧",
                                "💻",
                                "🧠"

                            ];

                            echo $icons[
                                $index %
                                count($icons)
                            ];

                            ?>

                        </div>


                        <span class="course-number">

                            COURSE

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



                    <!-- COURSE CONTENT -->

                    <h2>

                        <?php

                        echo htmlspecialchars(
                            $course["course_name"]
                        );

                        ?>

                    </h2>


                    <span class="provider">

                        By

                        <?php

                        echo htmlspecialchars(
                            $course["provider"]
                        );

                        ?>

                    </span>


                    <p class="course-description">

                        <?php

                        echo htmlspecialchars(
                            $course["description"]
                        );

                        ?>

                    </p>



                    <!-- COURSE FOOTER -->

                    <div class="course-footer">


                        <span class="course-type">

                            📚 Learning Resource

                        </span>


                        <?php if (
                            !empty(
                                $course["course_link"]
                            )
                        ): ?>


                            <a
                                href="<?php
                                    echo htmlspecialchars(
                                        $course["course_link"]
                                    );
                                ?>"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="course-button"
                            >

                                Start Learning →

                            </a>


                        <?php endif; ?>


                    </div>


                </div>


            <?php endforeach; ?>


        </div>


    <?php else: ?>


        <!-- EMPTY STATE -->

        <div class="empty-courses">


            <div class="empty-icon">

                📚

            </div>


            <h2>

                No Courses Available

            </h2>


            <p>

                Course recommendations will appear here
                once course data is added.

            </p>


        </div>


    <?php endif; ?>


</div>

</div>


<?php

require_once "includes/footer.php";

?>
```
