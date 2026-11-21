<?php

session_start();

require_once "includes/db.php";

/* Check login */

if (!isset($_SESSION['user_id'])) {

    header("Location: login.php");
    exit();

}


/* Get logged-in user details */

$user_id = $_SESSION['user_id'];

$user_name = $_SESSION['name'] ?? '';
$user_email = $_SESSION['email'] ?? '';


/* If name/email are not stored in session,
   get them from database */

if (empty($user_name) || empty($user_email)) {

    $stmt = $conn->prepare(
        "SELECT name, email FROM users WHERE user_id = ?"
    );

    $stmt->bind_param("i", $user_id);

    $stmt->execute();

    $result = $stmt->get_result();

    if ($result->num_rows === 1) {

        $user = $result->fetch_assoc();

        $user_name = $user['name'];
        $user_email = $user['email'];

        $_SESSION['name'] = $user_name;
        $_SESSION['email'] = $user_email;

    }

    $stmt->close();

}


/* Load common header */

require_once "includes/header.php";

?>


<!-- =========================================
     DASHBOARD CONTENT
========================================= -->

<div class="dashboard-content">


    <!-- WELCOME -->

    <section class="welcome">

        <span>
            DASHBOARD
        </span>

        <h1>

            Welcome,
            <?php echo htmlspecialchars($user_name); ?>! 👋

        </h1>

        <p>

            Start exploring your personalized
            career journey.

        </p>

    </section>



    <!-- =====================================
         FEATURE CARDS
    ====================================== -->

    <section class="cards">


        <!-- CAREER -->

        <a
            href="career.php"
            class="dashboard-card"
        >

            <div class="card-icon">
                🎯
            </div>

            <h3>
                Career Recommendation
            </h3>

            <p>
                Find career paths based on your
                skills, interests and goals.
            </p>

            <span class="card-link">
                Explore Career →
            </span>

        </a>



        



        <!-- COURSES -->

        <a
            href="courses.php"
            class="dashboard-card"
        >

            <div class="card-icon">
                📚
            </div>

            <h3>
                Courses & Certifications
            </h3>

            <p>
                Discover useful courses and
                certifications for your career.
            </p>

            <span class="card-link">
                Explore Learning →
            </span>

        </a>



        <!-- INTERNSHIPS -->

        <a
            href="internships.php"
            class="dashboard-card"
        >

            <div class="card-icon">
                💼
            </div>

            <h3>
                Internships
            </h3>

            <p>
                Explore internship and career
                opportunities.
            </p>

            <span class="card-link">
                Find Opportunities →
            </span>

        </a>


    </section>



    <!-- =====================================
         SECOND ROW
    ====================================== -->

    <section class="cards second-row">


        <!-- PROFILE -->

        <a
            href="profile.php"
            class="dashboard-card"
        >

            <div class="card-icon">
                👤
            </div>

            <h3>
                My Profile
            </h3>

            <p>
                Update your education, skills,
                interests and career goal.
            </p>

            <span class="card-link">
                View Profile →
            </span>

        </a>



        <!-- SKILL GAP -->

        <a
            href="skill-gap.php"
            class="dashboard-card"
        >

            <div class="card-icon">
                📊
            </div>

            <h3>
                Skill Gap Analysis
            </h3>

            <p>
                Identify the skills you need to
                improve for your selected career.
            </p>

            <span class="card-link">
                Check Skill Gap →
            </span>

        </a>



        <!-- CERTIFICATIONS -->

        <a
            href="certifications.php"
            class="dashboard-card"
        >

            <div class="card-icon">
                🏆
            </div>

            <h3>
                Certifications
            </h3>

            <p>
                Explore certifications that can
                strengthen your career profile.
            </p>

            <span class="card-link">
                View Certifications →
            </span>

        </a>



        <!-- TRACKER -->

        <a
            href="tracker.php"
            class="dashboard-card"
        >

            <div class="card-icon">
                📈
            </div>

            <h3>
                Learning Tracker
            </h3>

            <p>
                Track your course progress and
                learning activities.
            </p>

            <span class="card-link">
                Track Learning →
            </span>

        </a>


    </section>



    <!-- =====================================
         PROFILE SUMMARY
    ====================================== -->

    <section class="profile-summary">


        <div class="section-heading">

            <div>

                <span>
                    PROFILE
                </span>

                <h2>
                    Your Profile
                </h2>

            </div>


            <a href="profile.php">
                Edit Profile →
            </a>

        </div>



        <div class="profile-info">


            <!-- NAME -->

            <div class="info">

                <small>
                    Name
                </small>

                <strong>

                    <?php

                    echo htmlspecialchars(
                        $user_name
                    );

                    ?>

                </strong>

            </div>



            <!-- EMAIL -->

            <div class="info">

                <small>
                    Email
                </small>

                <strong>

                    <?php

                    echo htmlspecialchars(
                        $user_email
                    );

                    ?>

                </strong>

            </div>


        </div>


    </section>


</div>


<?php

/* Load common footer */

require_once "includes/footer.php";

?>

