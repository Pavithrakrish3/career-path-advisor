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


/*
|--------------------------------------------------------------------------
| GET CAREER ID
|--------------------------------------------------------------------------
|
| career.php sends:
|
| roadmap.php?career_id=1
|
*/

$career_id = 0;

if (
    isset($_GET["career_id"])
    &&
    is_numeric($_GET["career_id"])
) {

    $career_id = (int) $_GET["career_id"];

}


/*
|--------------------------------------------------------------------------
| CAREER ID CHECK
|--------------------------------------------------------------------------
*/

if ($career_id <= 0) {

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
            Roadmap | Career Path Advisor
        </title>

        <style>

            * {
                box-sizing: border-box;
            }

            body {

                margin: 0;

                min-height: 100vh;

                display: flex;

                align-items: center;

                justify-content: center;

                background: #f6f7fb;

                font-family: Arial, sans-serif;

            }

            .error-box {

                width: 90%;

                max-width: 550px;

                padding: 35px;

                border: 1px solid #e3e5ee;

                border-radius: 16px;

                background: #ffffff;

                text-align: center;

                box-shadow:
                    0 15px 35px
                    rgba(0,0,0,0.06);

            }

            .error-icon {

                font-size: 45px;

            }

            h2 {

                margin: 15px 0 8px;

                color: #252638;

            }

            p {

                color: #6b6d7c;

                font-size: 13px;

                line-height: 1.6;

            }

            a {

                display: inline-block;

                margin-top: 15px;

                padding: 11px 18px;

                border-radius: 9px;

                background: #5b5cf0;

                color: #ffffff;

                text-decoration: none;

                font-size: 12px;

                font-weight: 700;

            }

        </style>

    </head>

    <body>

        <div class="error-box">

            <div class="error-icon">
                ⚠️
            </div>

            <h2>
                Career ID is missing
            </h2>

            <p>
                Please open the roadmap from the
                Career Recommendations page.
            </p>

            <a href="career.php">
                ← Back to Career Recommendations
            </a>

        </div>

    </body>

    </html>

    <?php

    exit;

}


/*
|--------------------------------------------------------------------------
| GET CAREER DETAILS
|--------------------------------------------------------------------------
|
| careers:
|
| career_id
| career_name
| description
|
*/

$career_name = "";

$career_description = "";


$career_stmt = $conn->prepare("
    SELECT
        career_name,
        description
    FROM careers
    WHERE career_id = ?
    LIMIT 1
");


if (!$career_stmt) {

    die(
        "Database error: "
        . htmlspecialchars(
            $conn->error
        )
    );

}


$career_stmt->bind_param(
    "i",
    $career_id
);

$career_stmt->execute();

$career_result =
    $career_stmt->get_result();


if (
    !$career_result->num_rows
) {

    $career_stmt->close();

    die(
        "Career not found for Career ID: "
        . $career_id
    );

}


$career =
    $career_result->fetch_assoc();


$career_name =
    $career["career_name"];

$career_description =
    $career["description"];


$career_stmt->close();


/*
|--------------------------------------------------------------------------
| GET ROADMAP
|--------------------------------------------------------------------------
|
| roadmaps:
|
| roadmap_id
| career_id
| roadmap_title
| roadmap_content
|
*/

$roadmaps = [];


$roadmap_stmt = $conn->prepare("
    SELECT
        roadmap_id,
        career_id,
        roadmap_title,
        roadmap_content
    FROM roadmaps
    WHERE career_id = ?
    ORDER BY roadmap_id ASC
");


if (!$roadmap_stmt) {

    die(
        "Database error: "
        . htmlspecialchars(
            $conn->error
        )
    );

}


$roadmap_stmt->bind_param(
    "i",
    $career_id
);

$roadmap_stmt->execute();

$roadmap_result =
    $roadmap_stmt->get_result();


while (
    $row =
    $roadmap_result->fetch_assoc()
) {

    $roadmaps[] = $row;

}


$roadmap_stmt->close();

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
        <?php
        echo htmlspecialchars(
            $career_name
        );
        ?>
        Roadmap | Career Path Advisor
    </title>


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

            font-family:
                "Inter",
                sans-serif;

            background: #f6f7fb;

            color: #171827;

        }


        .page {

            min-height: 100vh;

            padding: 40px 20px;

        }


        .container {

            width: 100%;

            max-width: 1050px;

            margin: auto;

        }


        /* HEADER */

        .header {

            display: flex;

            align-items: flex-start;

            justify-content: space-between;

            gap: 20px;

            margin-bottom: 25px;

        }


        .label {

            color: #5b5cf0;

            font-size: 10px;

            font-weight: 800;

            letter-spacing: 1.5px;

        }


        h1 {

            margin: 7px 0 0;

            font-size: 31px;

            font-weight: 800;

            letter-spacing: -1px;

        }


        .header p {

            max-width: 700px;

            margin: 8px 0 0;

            color: #686a79;

            font-size: 12px;

            line-height: 1.7;

        }


        .back-button {

            display: inline-flex;

            align-items: center;

            justify-content: center;

            padding: 11px 16px;

            border: 1px solid #e2e4ed;

            border-radius: 9px;

            background: #ffffff;

            color: #5c5e6d;

            text-decoration: none;

            font-size: 10px;

            font-weight: 700;

            white-space: nowrap;

        }


        .back-button:hover {

            color: #5b5cf0;

            border-color: #5b5cf0;

        }


        /* CAREER BANNER */

        .career-banner {

            padding: 25px 28px;

            margin-bottom: 22px;

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
                rgba(
                    91,
                    92,
                    240,
                    0.18
                );

        }


        .career-banner h2 {

            margin: 0;

            font-size: 22px;

            font-weight: 800;

        }


        .career-banner p {

            margin: 7px 0 0;

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


        /* ROADMAP GRID */

        .roadmap-grid {

            display: grid;

            grid-template-columns:
                repeat(2, 1fr);

            gap: 18px;

        }


        /* CARD */

        .roadmap-card {

            padding: 23px;

            border: 1px solid #e3e5ee;

            border-radius: 16px;

            background: #ffffff;

            transition: 0.3s ease;

        }


        .roadmap-card:hover {

            transform: translateY(-4px);

            box-shadow:
                0 15px 30px
                rgba(
                    30,
                    32,
                    70,
                    0.08
                );

        }


        .card-top {

            display: flex;

            align-items: center;

            justify-content: space-between;

        }


        .roadmap-icon {

            width: 45px;

            height: 45px;

            display: flex;

            align-items: center;

            justify-content: center;

            border-radius: 12px;

            background: #eeeeff;

            font-size: 20px;

        }


        .roadmap-number {

            color: #989aaa;

            font-size: 9px;

            font-weight: 800;

            letter-spacing: 1px;

        }


        .roadmap-card h3 {

            margin: 16px 0 9px;

            color: #202132;

            font-size: 17px;

            line-height: 1.4;

            font-weight: 800;

        }


        .roadmap-content {

            margin: 0;

            color: #686a79;

            font-size: 11px;

            line-height: 1.8;

            white-space: pre-line;

        }


        /* STEPS */

        .steps {

            display: flex;

            flex-wrap: wrap;

            gap: 7px;

            margin-top: 16px;

            padding-top: 15px;

            border-top: 1px solid #eeeeF4;

        }


        .step {

            padding: 6px 9px;

            border-radius: 7px;

            background: #f5f5fa;

            color: #656777;

            font-size: 9px;

            font-weight: 600;

        }


        .step::before {

            content: "✓";

            margin-right: 5px;

            color: #5b5cf0;

            font-weight: 800;

        }


        /* EMPTY */

        .empty {

            padding: 50px 20px;

            border: 1px solid #e3e5ee;

            border-radius: 16px;

            background: #ffffff;

            text-align: center;

        }


        .empty-icon {

            font-size: 40px;

        }


        .empty h2 {

            margin: 12px 0 7px;

            font-size: 19px;

        }


        .empty p {

            margin: 0;

            color: #686a79;

            font-size: 11px;

        }


        /* RESPONSIVE */

        @media (max-width: 760px) {

            .page {

                padding: 25px 15px;

            }


            .header {

                flex-direction: column;

            }


            .back-button {

                width: 100%;

            }


            .roadmap-grid {

                grid-template-columns: 1fr;

            }

        }

    </style>

</head>


<body>


<div class="page">

<div class="container">


    <!-- HEADER -->

    <div class="header">

        <div>

            <span class="label">
                PERSONALIZED LEARNING
            </span>

            <h1>
                Learning Roadmap
            </h1>

            <p>
                A structured learning path based on
                your recommended career.
            </p>

        </div>


        <a
            href="career.php"
            class="back-button"
        >
            ← Career Recommendations
        </a>

    </div>



    <!-- CAREER -->

    <div class="career-banner">

        <h2>

            🎯
            <?php

            echo htmlspecialchars(
                $career_name
            );

            ?>

        </h2>


        <p>

            <?php

            echo htmlspecialchars(
                $career_description
            );

            ?>

        </p>

    </div>



    <?php if (!empty($roadmaps)): ?>


        <div class="roadmap-grid">


            <?php

            $icons = [
                "📚",
                "💻",
                "🧠",
                "🛠️",
                "🚀",
                "🎯"
            ];

            ?>


            <?php foreach (
                $roadmaps
                as $index => $roadmap
            ): ?>


                <?php

                /*
                |--------------------------------------------------------------------------
                | SPLIT CONTENT INTO STEPS
                |--------------------------------------------------------------------------
                */

                $steps = preg_split(
                    '/\s*(?:→|->|\|)\s*/u',
                    $roadmap[
                        "roadmap_content"
                    ]
                );

                ?>


                <div class="roadmap-card">


                    <div class="card-top">

                        <div class="roadmap-icon">

                            <?php

                            echo $icons[
                                $index %
                                count($icons)
                            ];

                            ?>

                        </div>


                        <span
                            class="roadmap-number"
                        >

                            STEP
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



                    <h3>

                        <?php

                        echo htmlspecialchars(
                            $roadmap[
                                "roadmap_title"
                            ]
                        );

                        ?>

                    </h3>



                    <p
                        class="roadmap-content"
                    >

                        <?php

                        echo htmlspecialchars(
                            $roadmap[
                                "roadmap_content"
                            ]
                        );

                        ?>

                    </p>



                    <?php if (
                        count($steps) > 1
                    ): ?>


                        <div class="steps">


                            <?php foreach (
                                $steps
                                as $step
                            ): ?>


                                <?php

                                $step =
                                    trim($step);

                                if (
                                    $step === ""
                                ) {

                                    continue;

                                }

                                ?>


                                <span
                                    class="step"
                                >

                                    <?php

                                    echo htmlspecialchars(
                                        $step
                                    );

                                    ?>

                                </span>


                            <?php endforeach; ?>


                        </div>


                    <?php endif; ?>


                </div>


            <?php endforeach; ?>


        </div>


    <?php else: ?>


        <div class="empty">

            <div class="empty-icon">
                🗺️
            </div>


            <h2>
                No Roadmap Available
            </h2>


            <p>

                No roadmap has been added for
                <?php

                echo htmlspecialchars(
                    $career_name
                );

                ?> yet.

            </p>

        </div>


    <?php endif; ?>


</div>

</div>


</body>

</html>
```
