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

$user_name = $_SESSION["user_name"] ?? "";

$user_email = $_SESSION["user_email"] ?? "";


// =========================================
// VARIABLES
// =========================================

$message = "";

$message_type = "";

$degree = "";

$skills = [];

$interests = [];

$career_goal = "";


// =========================================
// LOAD EXISTING PROFILE
// =========================================

$stmt = $conn->prepare(
    "SELECT degree, skills, interests, career_goal
     FROM profiles
     WHERE user_id = ?
     LIMIT 1"
);

$stmt->bind_param("i", $user_id);

$stmt->execute();

$result = $stmt->get_result();


if ($result->num_rows === 1) {

    $profile = $result->fetch_assoc();


    $degree = $profile["degree"];

    $career_goal = $profile["career_goal"];


    if (!empty($profile["skills"])) {

        $skills = array_map(
            "trim",
            explode(",", $profile["skills"])
        );

    }


    if (!empty($profile["interests"])) {

        $interests = array_map(
            "trim",
            explode(",", $profile["interests"])
        );

    }

}


$stmt->close();


// =========================================
// SAVE / UPDATE PROFILE
// =========================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {


    $degree = trim($_POST["degree"] ?? "");

    $career_goal = trim($_POST["career_goal"] ?? "");


    $skills = $_POST["skills"] ?? [];

    $interests = $_POST["interests"] ?? [];


    // Convert arrays to comma-separated values

    $skills_string = implode(", ", $skills);

    $interests_string = implode(", ", $interests);


    // =====================================
    // VALIDATION
    // =====================================

    if (
        empty($degree) ||
        empty($skills) ||
        empty($interests) ||
        empty($career_goal)
    ) {

        $message = "Please complete all profile details.";

        $message_type = "error";

    } else {


        // =================================
        // CHECK EXISTING PROFILE
        // =================================

        $check = $conn->prepare(
            "SELECT profile_id
             FROM profiles
             WHERE user_id = ?
             LIMIT 1"
        );

        $check->bind_param("i", $user_id);

        $check->execute();

        $check_result = $check->get_result();


        // =================================
        // UPDATE EXISTING PROFILE
        // =================================

        if ($check_result->num_rows === 1) {


            $update = $conn->prepare(
                "UPDATE profiles
                 SET degree = ?,
                     skills = ?,
                     interests = ?,
                     career_goal = ?
                 WHERE user_id = ?"
            );


            $update->bind_param(
                "ssssi",
                $degree,
                $skills_string,
                $interests_string,
                $career_goal,
                $user_id
            );


            if ($update->execute()) {

                $message = "Profile updated successfully.";

                $message_type = "success";

            } else {

                $message = "Unable to update profile.";

                $message_type = "error";

            }


            $update->close();


        } else {


            // =================================
            // INSERT NEW PROFILE
            // =================================

            $insert = $conn->prepare(
                "INSERT INTO profiles
                (user_id, degree, skills, interests, career_goal)
                VALUES (?, ?, ?, ?, ?)"
            );


            $insert->bind_param(
                "issss",
                $user_id,
                $degree,
                $skills_string,
                $interests_string,
                $career_goal
            );


            if ($insert->execute()) {

                $message = "Profile saved successfully.";

                $message_type = "success";

            } else {

                $message = "Unable to save profile.";

                $message_type = "error";

            }


            $insert->close();

        }


        $check->close();

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

    <title>My Profile | Career Path Advisor</title>


    <!-- Google Font -->

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


    <!-- Main CSS -->

    <link
        rel="stylesheet"
        href="css/style.css"
    >


    <style>

        /* =========================================
           PROFILE PAGE
           ========================================= */

        .profile-page {

            min-height: 100vh;

            background: #f6f7fb;

            padding: 40px 20px;

        }


        .profile-container {

            width: 100%;

            max-width: 900px;

            margin: auto;

        }


        .profile-header {

            display: flex;

            align-items: center;

            justify-content: space-between;

            margin-bottom: 25px;

        }


        .profile-header h1 {

            font-size: 30px;

            font-weight: 800;

            letter-spacing: -0.8px;

        }


        .profile-header p {

            margin-top: 7px;

            color: #626578;

            font-size: 13px;

        }


        .back-button {

            padding: 11px 17px;

            border-radius: 10px;

            background: #ffffff;

            border: 1px solid #e3e5ee;

            color: #626578;

            text-decoration: none;

            font-size: 11px;

            font-weight: 600;

            transition: 0.25s ease;

        }


        .back-button:hover {

            border-color: #5b5cf0;

            color: #5b5cf0;

        }


        .profile-card {

            background: #ffffff;

            border: 1px solid #e3e5ee;

            border-radius: 18px;

            padding: 32px;

            box-shadow:
                0 12px 35px rgba(23,24,39,0.05);

        }


        .profile-user {

            display: flex;

            align-items: center;

            gap: 15px;

            padding-bottom: 25px;

            margin-bottom: 28px;

            border-bottom: 1px solid #eef0f5;

        }


        .profile-avatar {

            width: 52px;

            height: 52px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 14px;

            background: #eeeeff;

            color: #5b5cf0;

            font-size: 22px;

        }


        .profile-user h2 {

            font-size: 17px;

            font-weight: 700;

        }


        .profile-user p {

            margin-top: 4px;

            color: #9295a7;

            font-size: 11px;

        }


        .section-title {

            margin-bottom: 20px;

        }


        .section-title h3 {

            font-size: 17px;

            font-weight: 700;

        }


        .section-title p {

            margin-top: 5px;

            color: #9295a7;

            font-size: 11px;

        }


        .profile-grid {

            display: grid;

            grid-template-columns: 1fr 1fr;

            gap: 20px;

        }


        .profile-field {

            margin-bottom: 4px;

        }


        .profile-field.full {

            grid-column: 1 / -1;

        }


        .profile-field label {

            display: block;

            margin-bottom: 8px;

            color: #171827;

            font-size: 12px;

            font-weight: 600;

        }


        .profile-field select {

            width: 100%;

            height: 52px;

            padding: 0 14px;

            border: 1px solid #e3e5ee;

            border-radius: 11px;

            outline: none;

            background: #fafbfe;

            color: #171827;

            font-family: inherit;

            font-size: 13px;

            cursor: pointer;

            transition: 0.25s ease;

        }


        .profile-field select:focus {

            border-color: #5b5cf0;

            background: #ffffff;

            box-shadow:
                0 0 0 4px rgba(91,92,240,0.09);

        }


        /* =========================================
           CHECKBOX GROUP
           ========================================= */

        .checkbox-grid {

            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 10px;

        }


        .checkbox-item {

            position: relative;

        }


        .checkbox-item input {

            position: absolute;

            opacity: 0;

        }


        .checkbox-item label {

            display: flex;

            align-items: center;

            min-height: 48px;

            padding: 10px 12px;

            border: 1px solid #e3e5ee;

            border-radius: 10px;

            background: #fafbfe;

            color: #626578;

            font-size: 11px;

            font-weight: 500;

            cursor: pointer;

            transition: 0.25s ease;

        }


        .checkbox-item label:hover {

            border-color: #c9caf5;

        }


        .checkbox-item input:checked + label {

            border-color: #5b5cf0;

            background: #f0f0ff;

            color: #4849d9;

            font-weight: 700;

        }


        .save-section {

            display: flex;

            justify-content: flex-end;

            margin-top: 28px;

            padding-top: 25px;

            border-top: 1px solid #eef0f5;

        }


        .save-profile {

            min-width: 180px;

            height: 50px;

            border: none;

            border-radius: 11px;

            background: #5b5cf0;

            color: #ffffff;

            font-family: inherit;

            font-size: 12px;

            font-weight: 700;

            cursor: pointer;

            box-shadow:
                0 10px 25px rgba(91,92,240,0.22);

            transition: 0.3s ease;

        }


        .save-profile:hover {

            background: #4849d9;

            transform: translateY(-2px);

        }


        /* =========================================
           PROFILE MESSAGE
           ========================================= */

        .profile-message {

            display: flex;

            align-items: center;

            gap: 9px;

            padding: 12px 14px;

            margin-bottom: 22px;

            border-radius: 10px;

            font-size: 12px;

        }


        .profile-message.success {

            color: #16804b;

            background: #ecfdf3;

            border: 1px solid #c8f1da;

        }


        .profile-message.error {

            color: #d13c3c;

            background: #fff1f1;

            border: 1px solid #ffd2d2;

        }


        /* =========================================
           RESPONSIVE
           ========================================= */

        @media (max-width: 700px) {

            .profile-page {

                padding: 25px 15px;

            }


            .profile-header {

                align-items: flex-start;

                gap: 15px;

            }


            .profile-header h1 {

                font-size: 25px;

            }


            .profile-card {

                padding: 22px;

            }


            .profile-grid {

                grid-template-columns: 1fr;

            }


            .profile-field.full {

                grid-column: auto;

            }


            .checkbox-grid {

                grid-template-columns: 1fr 1fr;

            }

        }


        @media (max-width: 450px) {

            .profile-header {

                flex-direction: column;

            }


            .checkbox-grid {

                grid-template-columns: 1fr;

            }


            .save-section {

                justify-content: stretch;

            }


            .save-profile {

                width: 100%;

            }

        }

    </style>

</head>


<body>


<div class="profile-page">


    <div class="profile-container">


        <!-- =====================================
             HEADER
             ===================================== -->

        <div class="profile-header">


            <div>

                <h1>
                    Your Career Profile
                </h1>

                <p>
                    Tell us about yourself to get personalized career recommendations.
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
             PROFILE CARD
             ===================================== -->

        <div class="profile-card">


            <!-- USER -->

            <div class="profile-user">


                <div class="profile-avatar">

                    👤

                </div>


                <div>

                    <h2>

                        <?php
                        echo htmlspecialchars($user_name);
                        ?>

                    </h2>


                    <p>

                        <?php
                        echo htmlspecialchars($user_email);
                        ?>

                    </p>

                </div>


            </div>



            <!-- MESSAGE -->

            <?php if (!empty($message)): ?>

                <div class="profile-message <?php echo htmlspecialchars($message_type); ?>">


                    <strong>

                        <?php

                        echo ($message_type === "success")
                            ? "✓"
                            : "!";

                        ?>

                    </strong>


                    <span>

                        <?php

                        echo htmlspecialchars($message);

                        ?>

                    </span>


                </div>

            <?php endif; ?>



            <!-- FORM -->

            <form
                method="POST"
                action=""
            >


                <div class="section-title">

                    <h3>
                        Education & Career Preferences
                    </h3>

                    <p>
                        Select the options that best describe your career interests.
                    </p>

                </div>



                <div class="profile-grid">


                    <!-- DEGREE -->

                    <div class="profile-field">


                        <label for="degree">

                            Degree

                        </label>


                        <select
                            name="degree"
                            id="degree"
                            required
                        >

                            <option value="">
                                Select your degree
                            </option>


                            <option
                                value="BCA"
                                <?php
                                echo ($degree === "BCA")
                                    ? "selected"
                                    : "";
                                ?>
                            >
                                BCA
                            </option>


                            <option
                                value="B.Sc Computer Science"
                                <?php
                                echo ($degree === "B.Sc Computer Science")
                                    ? "selected"
                                    : "";
                                ?>
                            >
                                B.Sc Computer Science
                            </option>


                            <option
                                value="B.Tech / BE"
                                <?php
                                echo ($degree === "B.Tech / BE")
                                    ? "selected"
                                    : "";
                                ?>
                            >
                                B.Tech / BE
                            </option>


                            <option
                                value="MCA"
                                <?php
                                echo ($degree === "MCA")
                                    ? "selected"
                                    : "";
                                ?>
                            >
                                MCA
                            </option>


                            <option
                                value="M.Sc Computer Science"
                                <?php
                                echo ($degree === "M.Sc Computer Science")
                                    ? "selected"
                                    : "";
                                ?>
                            >
                                M.Sc Computer Science
                            </option>


                            <option
                                value="Other"
                                <?php
                                echo ($degree === "Other")
                                    ? "selected"
                                    : "";
                                ?>
                            >
                                Other
                            </option>


                        </select>

                    </div>



                    <!-- CAREER GOAL -->

                    <div class="profile-field">


                        <label for="career_goal">

                            Career Goal

                        </label>


                        <select
                            name="career_goal"
                            id="career_goal"
                            required
                        >

                            <option value="">
                                Select career goal
                            </option>


                            <option
                                value="Software Developer"
                                <?php
                                echo ($career_goal === "Software Developer")
                                    ? "selected"
                                    : "";
                                ?>
                            >
                                Software Developer
                            </option>


                            <option
                                value="Full Stack Developer"
                                <?php
                                echo ($career_goal === "Full Stack Developer")
                                    ? "selected"
                                    : "";
                                ?>
                            >
                                Full Stack Developer
                            </option>


                            <option
                                value="Data Analyst"
                                <?php
                                echo ($career_goal === "Data Analyst")
                                    ? "selected"
                                    : "";
                                ?>
                            >
                                Data Analyst
                            </option>


                            <option
                                value="Data Scientist"
                                <?php
                                echo ($career_goal === "Data Scientist")
                                    ? "selected"
                                    : "";
                                ?>
                            >
                                Data Scientist
                            </option>


                            <option
                                value="AI / ML Engineer"
                                <?php
                                echo ($career_goal === "AI / ML Engineer")
                                    ? "selected"
                                    : "";
                                ?>
                            >
                                AI / ML Engineer
                            </option>


                            <option
                                value="Cyber Security Analyst"
                                <?php
                                echo ($career_goal === "Cyber Security Analyst")
                                    ? "selected"
                                    : "";
                                ?>
                            >
                                Cyber Security Analyst
                            </option>


                            <option
                                value="Cloud Engineer"
                                <?php
                                echo ($career_goal === "Cloud Engineer")
                                    ? "selected"
                                    : "";
                                ?>
                            >
                                Cloud Engineer
                            </option>


                            <option
                                value="Database Administrator"
                                <?php
                                echo ($career_goal === "Database Administrator")
                                    ? "selected"
                                    : "";
                                ?>
                            >
                                Database Administrator
                            </option>


                        </select>

                    </div>



                    <!-- SKILLS -->

                    <div class="profile-field full">


                        <label>

                            Your Skills

                        </label>


                        <div class="checkbox-grid">


                            <?php

                            $skill_options = [

                                "Java",

                                "Python",

                                "C",

                                "C++",

                                "HTML",

                                "CSS",

                                "JavaScript",

                                "PHP",

                                "MySQL",

                                "Data Analytics",

                                "Machine Learning",

                                "Git"

                            ];


                            foreach ($skill_options as $skill):

                            ?>

                                <div class="checkbox-item">


                                    <input
                                        type="checkbox"
                                        name="skills[]"
                                        id="skill_<?php echo md5($skill); ?>"
                                        value="<?php echo htmlspecialchars($skill); ?>"
                                        <?php
                                        echo in_array($skill, $skills)
                                            ? "checked"
                                            : "";
                                        ?>
                                    >


                                    <label
                                        for="skill_<?php echo md5($skill); ?>"
                                    >

                                        <?php
                                        echo htmlspecialchars($skill);
                                        ?>

                                    </label>


                                </div>


                            <?php endforeach; ?>


                        </div>

                    </div>



                    <!-- INTERESTS -->

                    <div class="profile-field full">


                        <label>

                            Your Interests

                        </label>


                        <div class="checkbox-grid">


                            <?php

                            $interest_options = [

                                "Web Development",

                                "Software Development",

                                "Data Science",

                                "Artificial Intelligence",

                                "Machine Learning",

                                "Cyber Security",

                                "Cloud Computing",

                                "Database Management",

                                "UI / UX Design",

                                "Mobile App Development",

                                "Networking",

                                "DevOps"

                            ];


                            foreach ($interest_options as $interest):

                            ?>

                                <div class="checkbox-item">


                                    <input
                                        type="checkbox"
                                        name="interests[]"
                                        id="interest_<?php echo md5($interest); ?>"
                                        value="<?php echo htmlspecialchars($interest); ?>"
                                        <?php
                                        echo in_array($interest, $interests)
                                            ? "checked"
                                            : "";
                                        ?>
                                    >


                                    <label
                                        for="interest_<?php echo md5($interest); ?>"
                                    >

                                        <?php
                                        echo htmlspecialchars($interest);
                                        ?>

                                    </label>


                                </div>


                            <?php endforeach; ?>


                        </div>

                    </div>


                </div>



                <!-- SAVE -->

                <div class="save-section">


                    <button
                        type="submit"
                        class="save-profile"
                    >

                        Save Profile
                        &nbsp; →

                    </button>


                </div>


            </form>


        </div>


    </div>


</div>


</body>

</html>
```
