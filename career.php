<?php

session_start();

require_once __DIR__ . "/includes/db.php";


/*
|--------------------------------------------------------------------------
| LOGIN CHECK
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["user_id"])) {

    header("Location: index.php");
    exit;

}


$user_id = (int) $_SESSION["user_id"];


/*
|--------------------------------------------------------------------------
| DEFAULT VALUES
|--------------------------------------------------------------------------
*/

$degree = "";
$skills_text = "";
$interests_text = "";
$career_goal = "";

$user_skills = [];
$user_interests = [];

$recommendations = [];
$top_career = null;
$top_career_id = 0;


/*
|--------------------------------------------------------------------------
| GET USER PROFILE
|--------------------------------------------------------------------------
|
| Correct table:
| profiles
|
| Columns:
| profile_id
| user_id
| degree
| skills
| interests
| career_goal
|
*/

$profile_stmt = $conn->prepare("
    SELECT
        degree,
        skills,
        interests,
        career_goal
    FROM profiles
    WHERE user_id = ?
    LIMIT 1
");


if (!$profile_stmt) {

    die(
        "Profile query error: "
        . htmlspecialchars($conn->error)
    );

}


$profile_stmt->bind_param(
    "i",
    $user_id
);

$profile_stmt->execute();

$profile_result =
    $profile_stmt->get_result();


if ($profile_result->num_rows > 0) {

    $profile =
        $profile_result->fetch_assoc();

    $degree =
        trim($profile["degree"] ?? "");

    $skills_text =
        trim($profile["skills"] ?? "");

    $interests_text =
        trim($profile["interests"] ?? "");

    $career_goal =
        trim($profile["career_goal"] ?? "");

}


$profile_stmt->close();


/*
|--------------------------------------------------------------------------
| CONVERT USER SKILLS INTO ARRAY
|--------------------------------------------------------------------------
|
| Example database value:
|
| Java, MySQL, HTML, CSS
|
*/

if ($skills_text !== "") {

    $user_skills = preg_split(
        '/\s*,\s*/',
        $skills_text
    );

}


/*
|--------------------------------------------------------------------------
| CLEAN USER SKILLS
|--------------------------------------------------------------------------
*/

$user_skills_clean = [];

foreach ($user_skills as $skill) {

    $skill = strtolower(
        trim($skill)
    );

    if ($skill !== "") {

        $user_skills_clean[] = $skill;

    }

}


/*
|--------------------------------------------------------------------------
| CONVERT USER INTERESTS INTO ARRAY
|--------------------------------------------------------------------------
*/

if ($interests_text !== "") {

    $user_interests = preg_split(
        '/\s*,\s*/',
        $interests_text
    );

}


/*
|--------------------------------------------------------------------------
| CLEAN USER INTERESTS
|--------------------------------------------------------------------------
*/

$user_interests_clean = [];

foreach ($user_interests as $interest) {

    $interest = strtolower(
        trim($interest)
    );

    if ($interest !== "") {

        $user_interests_clean[] =
            $interest;

    }

}


/*
|--------------------------------------------------------------------------
| GET ALL CAREERS
|--------------------------------------------------------------------------
|
| Expected careers columns:
|
| career_id
| career_name
| description
| required_skills
|
*/

$career_stmt = $conn->prepare("
    SELECT
        career_id,
        career_name,
        description,
        required_skills
    FROM careers
    ORDER BY career_id ASC
");


if (!$career_stmt) {

    die(
        "Career query error: "
        . htmlspecialchars($conn->error)
    );

}


$career_stmt->execute();

$career_result =
    $career_stmt->get_result();


/*
|--------------------------------------------------------------------------
| CAREER MATCHING
|--------------------------------------------------------------------------
*/

while (
    $career =
    $career_result->fetch_assoc()
) {


    $career_id =
        (int) $career["career_id"];


    $career_name =
        trim(
            $career["career_name"] ?? ""
        );


    $description =
        trim(
            $career["description"] ?? ""
        );


    $required_skills_text =
        trim(
            $career["required_skills"] ?? ""
        );


    /*
    |--------------------------------------------------------------------------
    | REQUIRED SKILLS
    |--------------------------------------------------------------------------
    */

    $required_skills = [];


    if (
        $required_skills_text !== ""
    ) {

        $required_skills =
            preg_split(
                '/\s*,\s*/',
                $required_skills_text
            );

    }


    /*
    |--------------------------------------------------------------------------
    | CLEAN REQUIRED SKILLS
    |--------------------------------------------------------------------------
    */

    $required_skills_clean = [];


    foreach (
        $required_skills
        as $required_skill
    ) {

        $required_skill =
            strtolower(
                trim($required_skill)
            );


        if (
            $required_skill !== ""
        ) {

            $required_skills_clean[] =
                $required_skill;

        }

    }


    /*
    |--------------------------------------------------------------------------
    | MATCH USER SKILLS
    |--------------------------------------------------------------------------
    */

    $matching_skills = [];


    foreach (
        $required_skills_clean
        as $required_skill
    ) {


        foreach (
            $user_skills_clean
            as $user_skill
        ) {


            /*
            | Exact or partial matching
            */

            if (
                $required_skill ===
                $user_skill
                ||
                strpos(
                    $user_skill,
                    $required_skill
                ) !== false
                ||
                strpos(
                    $required_skill,
                    $user_skill
                ) !== false
            ) {

                $matching_skills[] =
                    $required_skill;

                break;

            }

        }

    }


    /*
    |--------------------------------------------------------------------------
    | REMOVE DUPLICATES
    |--------------------------------------------------------------------------
    */

    $matching_skills =
        array_unique(
            $matching_skills
        );


    /*
    |--------------------------------------------------------------------------
    | SKILL MATCH PERCENTAGE
    |--------------------------------------------------------------------------
    */

    $skill_match_percentage = 0;


    if (
        count($required_skills_clean)
        > 0
    ) {

        $skill_match_percentage =
            (
                count($matching_skills)
                /
                count(
                    $required_skills_clean
                )
            )
            * 100;

    }


    /*
    |--------------------------------------------------------------------------
    | INTEREST MATCH
    |--------------------------------------------------------------------------
    */

    $interest_match = 0;


    $career_text =
        strtolower(
            $career_name
            . " "
            . $description
        );


    foreach (
        $user_interests_clean
        as $interest
    ) {

        if (
            $interest !== ""
            &&
            strpos(
                $career_text,
                $interest
            ) !== false
        ) {

            $interest_match += 10;

        }

    }


    /*
    |--------------------------------------------------------------------------
    | CAREER GOAL MATCH
    |--------------------------------------------------------------------------
    */

    $goal_match = 0;


    if (
        $career_goal !== ""
    ) {

        $goal =
            strtolower(
                trim($career_goal)
            );


        if (
            strpos(
                $career_text,
                $goal
            ) !== false
            ||
            strpos(
                $goal,
                strtolower($career_name)
            ) !== false
        ) {

            $goal_match = 15;

        }

    }


    /*
    |--------------------------------------------------------------------------
    | FINAL MATCH PERCENTAGE
    |--------------------------------------------------------------------------
    |
    | Skill match = 75%
    | Interest = 10%
    | Career goal = 15%
    |
    */

    $match_percentage =
        (
            $skill_match_percentage
            * 0.75
        )
        +
        $interest_match
        +
        $goal_match;


    /*
    |--------------------------------------------------------------------------
    | IF NO PROFILE SKILLS
    |--------------------------------------------------------------------------
    */

    if (
        count($user_skills_clean) === 0
        &&
        count($user_interests_clean) === 0
        &&
        $career_goal === ""
    ) {

        $match_percentage = 0;

    }


    /*
    |--------------------------------------------------------------------------
    | LIMIT TO 100
    |--------------------------------------------------------------------------
    */

    $match_percentage =
        min(
            100,
            max(
                0,
                round(
                    $match_percentage
                )
            )
        );


    /*
    |--------------------------------------------------------------------------
    | ADD RECOMMENDATION
    |--------------------------------------------------------------------------
    */

    $recommendations[] = [

        "career_id" =>
            $career_id,

        "career_name" =>
            $career_name,

        "description" =>
            $description,

        "required_skills" =>
            $required_skills,

        "matching_skills" =>
            array_values(
                $matching_skills
            ),

        "match_percentage" =>
            $match_percentage

    ];

}


$career_stmt->close();


/*
|--------------------------------------------------------------------------
| SORT CAREERS BY MATCH %
|--------------------------------------------------------------------------
*/

usort(
    $recommendations,
    function (
        $a,
        $b
    ) {

        return
            $b["match_percentage"]
            <=>
            $a["match_percentage"];

    }
);


/*
|--------------------------------------------------------------------------
| GET TOP CAREER
|--------------------------------------------------------------------------
*/

if (
    !empty($recommendations)
) {

    $top_career =
        $recommendations[0];


    /*
    |--------------------------------------------------------------------------
    | VERY IMPORTANT
    |--------------------------------------------------------------------------
    |
    | This is the actual career_id.
    |
    */

    $top_career_id =
        (int)
        $top_career["career_id"];

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
        Career Recommendations | Career Path Advisor
    </title>


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

        * {
            box-sizing: border-box;
        }


        body {

            margin: 0;

            background: #f6f7fb;

            color: #171827;

            font-family:
                "Inter",
                sans-serif;

        }


        .page {

            min-height: 100vh;

            padding: 40px 20px;

        }


        .container {

            width: 100%;

            max-width: 1100px;

            margin: auto;

        }


        /* HEADER */

        .career-header {

            display: flex;

            align-items: flex-start;

            justify-content: space-between;

            gap: 20px;

            margin-bottom: 25px;

        }


        .career-header h1 {

            margin: 0;

            font-size: 31px;

            font-weight: 800;

            letter-spacing: -1px;

        }


        .career-header p {

            margin: 8px 0 0;

            color: #686a79;

            font-size: 12px;

        }


        .back-button {

            padding: 11px 17px;

            border: 1px solid #e1e3ec;

            border-radius: 9px;

            background: #ffffff;

            color: #5e6070;

            text-decoration: none;

            font-size: 10px;

            font-weight: 700;

            white-space: nowrap;

        }


        .back-button:hover {

            color: #5b5cf0;

            border-color: #5b5cf0;

        }


        /* TOP CAREER */

        .top-career {

            margin-bottom: 22px;

            padding: 27px;

            border-radius: 18px;

            background:
                linear-gradient(
                    135deg,
                    #292b66,
                    #5b5cf0
                );

            color: white;

            box-shadow:
                0 18px 40px
                rgba(
                    91,
                    92,
                    240,
                    0.18
                );

        }


        .recommended-label {

            font-size: 9px;

            font-weight: 800;

            letter-spacing: 1.4px;

            opacity: 0.8;

        }


        .top-career-content {

            display: flex;

            align-items: center;

            justify-content: space-between;

            gap: 20px;

            margin-top: 15px;

        }


        .top-career h2 {

            margin: 0;

            font-size: 25px;

            font-weight: 800;

        }


        .top-career p {

            max-width: 700px;

            margin: 8px 0 0;

            color:
                rgba(
                    255,
                    255,
                    255,
                    0.72
                );

            font-size: 11px;

            line-height: 1.7;

        }


        .match-circle {

            width: 82px;

            height: 82px;

            min-width: 82px;

            display: flex;

            align-items: center;

            justify-content: center;

            flex-direction: column;

            border: 2px solid
                rgba(
                    255,
                    255,
                    255,
                    0.25
                );

            border-radius: 50%;

        }


        .match-circle strong {

            font-size: 18px;

        }


        .match-circle span {

            margin-top: 2px;

            font-size: 7px;

            letter-spacing: 1px;

            opacity: 0.7;

        }


        /* PROFILE SUMMARY */

        .profile-summary {

            display: grid;

            grid-template-columns:
                repeat(3, 1fr);

            gap: 14px;

            margin-bottom: 32px;

        }


        .summary-card {

            padding: 18px;

            border: 1px solid #e3e5ee;

            border-radius: 13px;

            background: #ffffff;

        }


        .summary-card span {

            display: block;

            color: #9092a2;

            font-size: 9px;

            font-weight: 600;

        }


        .summary-card strong {

            display: block;

            margin-top: 7px;

            color: #282a3b;

            font-size: 13px;

            font-weight: 800;

        }


        /* SECTION */

        .section-title {

            margin-bottom: 17px;

        }


        .section-title h2 {

            margin: 0;

            font-size: 20px;

            font-weight: 800;

        }


        .section-title p {

            margin: 5px 0 0;

            color: #77798a;

            font-size: 11px;

        }


        /* CAREER GRID */

        .career-grid {

            display: grid;

            grid-template-columns:
                repeat(2, 1fr);

            gap: 18px;

        }


        /* CAREER CARD */

        .career-card {

            padding: 22px;

            border: 1px solid #e3e5ee;

            border-radius: 15px;

            background: #ffffff;

            transition: 0.25s ease;

        }


        .career-card:hover {

            transform:
                translateY(-3px);

            box-shadow:
                0 14px 30px
                rgba(
                    20,
                    25,
                    60,
                    0.07
                );

        }


        .career-card-top {

            display: flex;

            align-items: center;

            justify-content: space-between;

        }


        .career-icon {

            width: 42px;

            height: 42px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 11px;

            background: #eeeeff;

            font-size: 19px;

        }


        .career-score {

            padding: 6px 9px;

            border-radius: 7px;

            background: #f0f0ff;

            color: #5b5cf0;

            font-size: 9px;

            font-weight: 800;

        }


        .career-card h3 {

            margin: 15px 0 7px;

            font-size: 17px;

            font-weight: 800;

        }


        .career-card-description {

            min-height: 42px;

            margin: 0;

            color: #686a79;

            font-size: 10px;

            line-height: 1.7;

        }


        .skills-title {

            margin-top: 17px;

            color: #303243;

            font-size: 9px;

            font-weight: 800;

        }


        .skill-tags {

            display: flex;

            flex-wrap: wrap;

            gap: 6px;

            margin-top: 8px;

        }


        .skill-tag {

            padding: 6px 8px;

            border-radius: 6px;

            background: #f5f5f9;

            color: #6a6c7c;

            font-size: 8px;

            font-weight: 600;

        }


        .skill-tag.matched {

            background: #e9f8ef;

            color: #208448;

        }


        /* MATCH BAR */

        .match-bar-container {

            margin-top: 17px;

            padding-top: 14px;

            border-top: 1px solid #eeeeF4;

        }


        .match-bar-header {

            display: flex;

            justify-content: space-between;

            margin-bottom: 7px;

            color: #77798a;

            font-size: 9px;

        }


        .match-bar-header strong {

            color: #303243;

        }


        .match-bar {

            width: 100%;

            height: 6px;

            overflow: hidden;

            border-radius: 10px;

            background: #eeeeF4;

        }


        .match-bar-fill {

            height: 100%;

            border-radius: 10px;

            background: #5b5cf0;

        }


        /* ROADMAP BUTTON */

        .profile-button {

            display: inline-flex;

            align-items: center;

            justify-content: center;

            margin-top: 25px;

            padding: 13px 20px;

            border-radius: 10px;

            background: #5b5cf0;

            color: #ffffff;

            text-decoration: none;

            font-size: 11px;

            font-weight: 800;

            transition: 0.25s ease;

        }


        .profile-button:hover {

            transform:
                translateY(-2px);

            box-shadow:
                0 10px 20px
                rgba(
                    91,
                    92,
                    240,
                    0.20
                );

        }


        /* EMPTY */

        .empty-message {

            padding: 50px;

            border: 1px solid #e3e5ee;

            border-radius: 15px;

            background: white;

            text-align: center;

            color: #686a79;

            font-size: 12px;

        }


        /* RESPONSIVE */

        @media (max-width: 750px) {

            .career-header {

                flex-direction: column;

            }


            .back-button {

                width: 100%;

                text-align: center;

            }


            .top-career-content {

                align-items: flex-start;

                flex-direction: column;

            }


            .profile-summary {

                grid-template-columns: 1fr;

            }


            .career-grid {

                grid-template-columns: 1fr;

            }

        }

    </style>

</head>


<body>


<div class="page">

<div class="container">


    <!-- HEADER -->

    <div class="career-header">

        <div>

            <h1>
                Career Recommendations
            </h1>

            <p>
                Personalized career suggestions
                based on your profile.
            </p>

        </div>


        <a
            href="dashboard.php"
            class="back-button"
        >
            ← Dashboard
        </a>

    </div>



    <?php if ($top_career): ?>


        <!-- TOP CAREER -->

        <div class="top-career">

            <span class="recommended-label">
                🎯 TOP RECOMMENDATION
            </span>


            <div class="top-career-content">

                <div>

                    <h2>

                        <?php

                        echo htmlspecialchars(
                            $top_career[
                                "career_name"
                            ]
                        );

                        ?>

                    </h2>


                    <p>

                        <?php

                        echo htmlspecialchars(
                            $top_career[
                                "description"
                            ]
                        );

                        ?>

                    </p>

                </div>


                <div class="match-circle">

                    <strong>

                        <?php

                        echo $top_career[
                            "match_percentage"
                        ];

                        ?>%

                    </strong>

                    <span>
                        MATCH
                    </span>

                </div>

            </div>

        </div>



        <!-- PROFILE SUMMARY -->

        <div class="profile-summary">


            <div class="summary-card">

                <span>
                    Degree
                </span>

                <strong>

                    <?php

                    echo htmlspecialchars(
                        $degree
                        ?: "Not Provided"
                    );

                    ?>

                </strong>

            </div>


            <div class="summary-card">

                <span>
                    Career Goal
                </span>

                <strong>

                    <?php

                    echo htmlspecialchars(
                        $career_goal
                        ?: "Not Provided"
                    );

                    ?>

                </strong>

            </div>


            <div class="summary-card">

                <span>
                    Skills Selected
                </span>

                <strong>

                    <?php

                    echo count(
                        $user_skills_clean
                    );

                    ?>

                    Skills

                </strong>

            </div>


        </div>



        <!-- RECOMMENDED CAREERS -->

        <div class="section-title">

            <h2>
                Recommended Careers
            </h2>

            <p>
                Explore careers ranked according
                to your profile match.
            </p>

        </div>



        <div class="career-grid">


            <?php foreach (
                $recommendations
                as $recommendation
            ): ?>


                <div class="career-card">


                    <div class="career-card-top">

                        <div class="career-icon">
                            💼
                        </div>


                        <div class="career-score">

                            <?php

                            echo $recommendation[
                                "match_percentage"
                            ];

                            ?>% Match

                        </div>

                    </div>



                    <h3>

                        <?php

                        echo htmlspecialchars(
                            $recommendation[
                                "career_name"
                            ]
                        );

                        ?>

                    </h3>



                    <p
                        class="career-card-description"
                    >

                        <?php

                        echo htmlspecialchars(
                            $recommendation[
                                "description"
                            ]
                        );

                        ?>

                    </p>



                    <div class="skills-title">

                        Required Skills

                    </div>


                    <div class="skill-tags">


                        <?php if (
                            !empty(
                                $recommendation[
                                    "required_skills"
                                ]
                            )
                        ): ?>


                            <?php foreach (
                                $recommendation[
                                    "required_skills"
                                ]
                                as $required_skill
                            ): ?>


                                <span
                                    class="skill-tag
                                    <?php

                                    echo in_array(
                                        strtolower(
                                            trim(
                                                $required_skill
                                            )
                                        ),
                                        $recommendation[
                                            "matching_skills"
                                        ]
                                    )
                                    ? "matched"
                                    : "";

                                    ?>"
                                >

                                    <?php

                                    echo htmlspecialchars(
                                        $required_skill
                                    );

                                    ?>

                                </span>


                            <?php endforeach; ?>


                        <?php else: ?>


                            <span class="skill-tag">
                                Skills not specified
                            </span>


                        <?php endif; ?>


                    </div>



                    <!-- MATCH BAR -->

                    <div
                        class="match-bar-container"
                    >

                        <div
                            class="match-bar-header"
                        >

                            <span>
                                Career Match
                            </span>

                            <strong>

                                <?php

                                echo $recommendation[
                                    "match_percentage"
                                ];

                                ?>%

                            </strong>

                        </div>


                        <div class="match-bar">

                            <div
                                class="match-bar-fill"
                                style="width:
                                <?php

                                echo min(
                                    100,
                                    max(
                                        0,
                                        $recommendation[
                                            "match_percentage"
                                        ]
                                    )
                                );

                                ?>%;"
                            ></div>

                        </div>

                    </div>


                </div>


            <?php endforeach; ?>


        </div>



        <!-- IMPORTANT ROADMAP BUTTON -->

        <a
            href="roadmap.php?career_id=<?php echo (int) $top_career_id; ?>"
            class="profile-button"
        >

            🗺️ View My Learning Roadmap

        </a>


        <br>


        <a
            href="profile.php"
            class="profile-button"
            style="
                background:#ffffff;
                color:#5b5cf0;
                border:1px solid #5b5cf0;
            "
        >

            ✏ Update My Profile

        </a>


    <?php else: ?>


        <div class="empty-message">

            No career recommendations are available yet.

            <br><br>

            Please update your profile with
            skills, interests and career goal.

        </div>


    <?php endif; ?>


</div>

</div>


</body>

</html>
```
