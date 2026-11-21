<?php

session_start();

require_once __DIR__ . "/includes/db.php";

/* =========================================
   LOGIN CHECK
   ========================================= */

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = intval($_SESSION['user_id']);

$user_name = $_SESSION['user_name'] ?? "User";


/* =========================================
   VARIABLES
   ========================================= */

$message = "";
$message_type = "";

$learning_items = [];

$total_courses = 0;
$in_progress_courses = 0;
$completed_courses = 0;
$average_progress = 0;


/* =========================================
   ADD LEARNING
   ========================================= */

if (isset($_POST['add_learning'])) {

    $course_name = trim($_POST['course_name'] ?? "");
    $progress = intval($_POST['progress'] ?? 0);

    if ($course_name === "") {

        $message = "Please enter a course name.";
        $message_type = "error";

    } else {

        if ($progress < 0) {
            $progress = 0;
        }

        if ($progress > 100) {
            $progress = 100;
        }


        /* Determine status */

        if ($progress == 0) {

            $status = "Not Started";

        } elseif ($progress >= 100) {

            $status = "Completed";

        } else {

            $status = "In Progress";

        }


        /* Insert into learning_tracker */

        $stmt = $conn->prepare("
            INSERT INTO learning_tracker
            (
                user_id,
                course_name,
                progress,
                status
            )
            VALUES (?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "isis",
            $user_id,
            $course_name,
            $progress,
            $status
        );


        if ($stmt->execute()) {

            $message = "Learning goal added successfully!";
            $message_type = "success";

        } else {

            $message = "Unable to add learning goal.";
            $message_type = "error";

        }

        $stmt->close();
    }
}


/* =========================================
   UPDATE LEARNING
   ========================================= */

if (isset($_POST['update_learning'])) {

    $tracker_id = intval($_POST['tracker_id'] ?? 0);
    $progress = intval($_POST['progress'] ?? 0);


    if ($progress < 0) {
        $progress = 0;
    }

    if ($progress > 100) {
        $progress = 100;
    }


    /* Determine status */

    if ($progress == 0) {

        $status = "Not Started";

    } elseif ($progress >= 100) {

        $status = "Completed";

    } else {

        $status = "In Progress";

    }


    if ($tracker_id > 0) {

        $stmt = $conn->prepare("
            UPDATE learning_tracker
            SET
                progress = ?,
                status = ?
            WHERE
                tracker_id = ?
                AND user_id = ?
        ");

        $stmt->bind_param(
            "isii",
            $progress,
            $status,
            $tracker_id,
            $user_id
        );


        if ($stmt->execute()) {

            $message = "Learning progress updated!";
            $message_type = "success";

        } else {

            $message = "Unable to update progress.";
            $message_type = "error";

        }

        $stmt->close();

    } else {

        $message = "Invalid learning item.";
        $message_type = "error";
    }
}


/* =========================================
   DELETE LEARNING
   ========================================= */

if (isset($_POST['delete_learning'])) {

    $tracker_id = intval($_POST['tracker_id'] ?? 0);


    if ($tracker_id > 0) {

        $stmt = $conn->prepare("
            DELETE FROM learning_tracker
            WHERE
                tracker_id = ?
                AND user_id = ?
        ");

        $stmt->bind_param(
            "ii",
            $tracker_id,
            $user_id
        );


        if ($stmt->execute()) {

            $message = "Learning item removed successfully.";
            $message_type = "success";

        } else {

            $message = "Unable to remove learning item.";
            $message_type = "error";

        }

        $stmt->close();

    } else {

        $message = "Invalid learning item.";
        $message_type = "error";
    }
}


/* =========================================
   FETCH LEARNING ITEMS
   ========================================= */

$stmt = $conn->prepare("
    SELECT
        tracker_id,
        course_name,
        progress,
        status
    FROM learning_tracker
    WHERE user_id = ?
    ORDER BY tracker_id DESC
");

$stmt->bind_param(
    "i",
    $user_id
);

$stmt->execute();

$result = $stmt->get_result();


while ($row = $result->fetch_assoc()) {

    $learning_items[] = $row;

}

$stmt->close();


/* =========================================
   CALCULATE STATISTICS
   ========================================= */

$total_courses = count($learning_items);

$total_progress = 0;

$in_progress_courses = 0;

$completed_courses = 0;


foreach ($learning_items as $item) {

    $progress_value = intval($item["progress"]);

    $total_progress += $progress_value;


    if ($item["status"] === "In Progress") {

        $in_progress_courses++;

    }


    if ($item["status"] === "Completed") {

        $completed_courses++;

    }
}


if ($total_courses > 0) {

    $average_progress = round(
        $total_progress / $total_courses
    );

} else {

    $average_progress = 0;
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
    Learning Tracker | Career Path Advisor
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


<!-- Existing CSS -->

<link
    rel="stylesheet"
    href="css/style.css"
>


<style>

/* =========================================
   TRACKER PAGE
   ========================================= */

* {
    box-sizing: border-box;
}

body {
    margin: 0;
    font-family: "Inter", sans-serif;
    background: #f6f7fb;
}


.tracker-page {

    min-height: 100vh;

    padding: 40px 20px;

    background: #f6f7fb;
}


.tracker-container {

    width: 100%;

    max-width: 1100px;

    margin: auto;
}


/* =========================================
   HEADER
   ========================================= */

.tracker-header {

    display: flex;

    align-items: flex-start;

    justify-content: space-between;

    gap: 20px;

    margin-bottom: 25px;
}


.tracker-label {

    color: #5b5cf0;

    font-size: 10px;

    font-weight: 800;

    letter-spacing: 1.8px;
}


.tracker-header h1 {

    margin: 8px 0 0;

    color: #171827;

    font-size: 31px;

    font-weight: 800;

    letter-spacing: -1px;
}


.tracker-header p {

    max-width: 650px;

    margin: 9px 0 0;

    color: #626578;

    font-size: 13px;

    line-height: 1.7;
}


.dashboard-button {

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

    white-space: nowrap;

    transition: 0.25s ease;
}


.dashboard-button:hover {

    color: #5b5cf0;

    border-color: #5b5cf0;
}


/* =========================================
   WELCOME BANNER
   ========================================= */

.tracker-banner {

    padding: 24px 27px;

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
        rgba(91,92,240,0.18);
}


.tracker-banner h2 {

    margin: 0;

    font-size: 21px;

    font-weight: 800;
}


.tracker-banner p {

    margin: 7px 0 0;

    color:
        rgba(255,255,255,0.70);

    font-size: 11px;

    line-height: 1.7;
}


/* =========================================
   STATISTICS
   ========================================= */

.tracker-stats {

    display: grid;

    grid-template-columns:
        repeat(4, 1fr);

    gap: 14px;

    margin-bottom: 22px;
}


.tracker-stat {

    padding: 18px;

    border: 1px solid #e3e5ee;

    border-radius: 14px;

    background: #ffffff;
}


.stat-icon {

    font-size: 19px;
}


.stat-number {

    display: block;

    margin-top: 9px;

    color: #171827;

    font-size: 23px;

    font-weight: 800;
}


.stat-label {

    display: block;

    margin-top: 3px;

    color: #9295a7;

    font-size: 10px;
}


/* =========================================
   ADD COURSE
   ========================================= */

.add-card {

    padding: 22px;

    margin-bottom: 22px;

    border: 1px solid #e3e5ee;

    border-radius: 15px;

    background: #ffffff;
}


.add-card h2 {

    margin: 0;

    color: #171827;

    font-size: 17px;

    font-weight: 800;
}


.add-card p {

    margin: 5px 0 0;

    color: #9295a7;

    font-size: 10px;
}


.add-form {

    display: grid;

    grid-template-columns:
        1fr 180px 150px;

    gap: 12px;

    margin-top: 16px;
}


.tracker-input {

    width: 100%;

    height: 48px;

    padding: 0 13px;

    border: 1px solid #e3e5ee;

    border-radius: 10px;

    outline: none;

    background: #fafbfe;

    color: #171827;

    font-family: inherit;

    font-size: 11px;
}


.tracker-input:focus {

    border-color: #5b5cf0;

    background: #ffffff;

    box-shadow:
        0 0 0 4px
        rgba(91,92,240,0.08);
}


.add-button {

    height: 48px;

    border: none;

    border-radius: 10px;

    background: #5b5cf0;

    color: #ffffff;

    font-family: inherit;

    font-size: 11px;

    font-weight: 700;

    cursor: pointer;

    transition: 0.25s ease;
}


.add-button:hover {

    background: #4849d9;

    transform: translateY(-1px);
}


/* =========================================
   MESSAGE
   ========================================= */

.tracker-message {

    padding: 12px 15px;

    margin-bottom: 18px;

    border-radius: 10px;

    font-size: 11px;
}


.tracker-message.success {

    color: #16804b;

    background: #ecfdf3;

    border: 1px solid #c8f1da;
}


.tracker-message.error {

    color: #d13c3c;

    background: #fff1f1;

    border: 1px solid #ffd2d2;
}


/* =========================================
   LEARNING GRID
   ========================================= */

.learning-grid {

    display: grid;

    grid-template-columns:
        repeat(2, 1fr);

    gap: 17px;
}


.learning-card {

    padding: 21px;

    border: 1px solid #e3e5ee;

    border-radius: 15px;

    background: #ffffff;

    transition: 0.3s ease;
}


.learning-card:hover {

    transform: translateY(-3px);

    box-shadow:
        0 13px 28px
        rgba(30,32,70,0.07);
}


.learning-top {

    display: flex;

    align-items: flex-start;

    justify-content: space-between;

    gap: 12px;
}


.course-icon {

    width: 43px;

    height: 43px;

    display: flex;

    align-items: center;

    justify-content: center;

    border-radius: 11px;

    background: #eeeeff;

    font-size: 18px;
}


.learning-card h2 {

    margin: 15px 0 0;

    color: #171827;

    font-size: 16px;

    font-weight: 800;
}


.status-badge {

    padding: 6px 9px;

    border-radius: 20px;

    font-size: 9px;

    font-weight: 700;
}


.status-not-started {

    color: #626578;

    background: #f0f1f5;
}


.status-in-progress {

    color: #5b5cf0;

    background: #eeeeff;
}


.status-completed {

    color: #16804b;

    background: #ecfdf3;
}


/* =========================================
   PROGRESS
   ========================================= */

.progress-header {

    display: flex;

    align-items: center;

    justify-content: space-between;

    margin-top: 17px;
}


.progress-label {

    color: #9295a7;

    font-size: 9px;

    font-weight: 600;
}


.progress-percent {

    color: #5b5cf0;

    font-size: 11px;

    font-weight: 800;
}


.progress-bar {

    width: 100%;

    height: 7px;

    margin-top: 8px;

    overflow: hidden;

    border-radius: 20px;

    background: #eeeeF5;
}


.progress-fill {

    height: 100%;

    border-radius: 20px;

    background: #5b5cf0;
}


/* =========================================
   UPDATE FORM
   ========================================= */

.update-form {

    display: flex;

    align-items: center;

    gap: 9px;

    margin-top: 17px;

    padding-top: 15px;

    border-top: 1px solid #eeeeF5;
}


.progress-input {

    width: 90px;

    height: 37px;

    padding: 0 10px;

    border: 1px solid #e3e5ee;

    border-radius: 8px;

    outline: none;

    font-family: inherit;

    font-size: 10px;
}


.update-button {

    height: 37px;

    padding: 0 13px;

    border: none;

    border-radius: 8px;

    background: #5b5cf0;

    color: #ffffff;

    font-family: inherit;

    font-size: 10px;

    font-weight: 700;

    cursor: pointer;
}


.delete-button {

    height: 37px;

    padding: 0 11px;

    border: 1px solid #ffd2d2;

    border-radius: 8px;

    background: #fff1f1;

    color: #d13c3c;

    font-family: inherit;

    font-size: 10px;

    font-weight: 600;

    cursor: pointer;
}


/* =========================================
   EMPTY STATE
   ========================================= */

.empty-tracker {

    padding: 50px 20px;

    border: 1px solid #e3e5ee;

    border-radius: 15px;

    background: #ffffff;

    text-align: center;
}


.empty-tracker-icon {

    font-size: 38px;
}


.empty-tracker h2 {

    margin: 11px 0 0;

    color: #171827;

    font-size: 19px;

    font-weight: 800;
}


.empty-tracker p {

    margin: 6px 0 0;

    color: #626578;

    font-size: 11px;
}


/* =========================================
   RESPONSIVE
   ========================================= */

@media (max-width: 850px) {

    .tracker-stats {

        grid-template-columns:
            repeat(2, 1fr);
    }


    .add-form {

        grid-template-columns:
            1fr 150px;
    }


    .add-button {

        grid-column: 1 / -1;
    }
}


@media (max-width: 700px) {

    .tracker-page {

        padding: 25px 15px;
    }


    .tracker-header {

        flex-direction: column;
    }


    .dashboard-button {

        width: 100%;
    }


    .learning-grid {

        grid-template-columns: 1fr;
    }
}


@media (max-width: 480px) {

    .tracker-stats {

        grid-template-columns:
            1fr 1fr;
    }


    .add-form {

        grid-template-columns: 1fr;
    }


    .add-button {

        grid-column: auto;
    }


    .update-form {

        flex-wrap: wrap;
    }
}

</style>

</head>


<body>


<div class="tracker-page">


<div class="tracker-container">


<!-- =====================================
     HEADER
     ===================================== -->

<div class="tracker-header">

    <div>

        <span class="tracker-label">
            LEARNING PROGRESS
        </span>

        <h1>
            Learning Tracker
        </h1>

        <p>
            Track your courses, monitor your progress
            and stay consistent with your learning goals.
        </p>

    </div>


    <a
        href="dashboard.php"
        class="dashboard-button"
    >
        ← Dashboard
    </a>

</div>


<!-- =====================================
     BANNER
     ===================================== -->

<div class="tracker-banner">

    <h2>
        Keep Learning,
        <?php echo htmlspecialchars($user_name); ?>
        🚀
    </h2>

    <p>
        Every completed course brings you one step
        closer to your career goal. Keep your progress
        updated and stay on track.
    </p>

</div>


<!-- =====================================
     STATISTICS
     ===================================== -->

<div class="tracker-stats">


    <div class="tracker-stat">

        <div class="stat-icon">
            📚
        </div>

        <span class="stat-number">
            <?php echo $total_courses; ?>
        </span>

        <span class="stat-label">
            Total Courses
        </span>

    </div>


    <div class="tracker-stat">

        <div class="stat-icon">
            🔄
        </div>

        <span class="stat-number">
            <?php echo $in_progress_courses; ?>
        </span>

        <span class="stat-label">
            In Progress
        </span>

    </div>


    <div class="tracker-stat">

        <div class="stat-icon">
            ✅
        </div>

        <span class="stat-number">
            <?php echo $completed_courses; ?>
        </span>

        <span class="stat-label">
            Completed
        </span>

    </div>


    <div class="tracker-stat">

        <div class="stat-icon">
            📈
        </div>

        <span class="stat-number">
            <?php echo $average_progress; ?>%
        </span>

        <span class="stat-label">
            Average Progress
        </span>

    </div>

</div>


<!-- =====================================
     MESSAGE
     ===================================== -->

<?php if (!empty($message)): ?>

<div class="tracker-message <?php echo $message_type; ?>">

    <?php echo htmlspecialchars($message); ?>

</div>

<?php endif; ?>


<!-- =====================================
     ADD LEARNING
     ===================================== -->

<div class="add-card">

    <h2>
        Add Learning Goal
    </h2>

    <p>
        Add a course or skill you are currently learning.
    </p>


    <form
        method="POST"
        class="add-form"
    >

        <input
            type="text"
            name="course_name"
            class="tracker-input"
            placeholder="Example: Java Programming"
            required
        >


        <input
            type="number"
            name="progress"
            class="tracker-input"
            placeholder="Progress %"
            min="0"
            max="100"
            value="0"
            required
        >


        <button
            type="submit"
            name="add_learning"
            class="add-button"
        >
            + Add Learning
        </button>

    </form>

</div>


<!-- =====================================
     LEARNING ITEMS
     ===================================== -->

<?php if (!empty($learning_items)): ?>


<div class="learning-grid">


<?php foreach ($learning_items as $index => $item): ?>


<?php

$status_class = "status-not-started";

if ($item["status"] === "In Progress") {

    $status_class = "status-in-progress";

} elseif ($item["status"] === "Completed") {

    $status_class = "status-completed";

}


$icons = [
    "📚",
    "💻",
    "🎯",
    "🧠",
    "🚀",
    "📖"
];


$icon = $icons[
    $index % count($icons)
];

?>


<div class="learning-card">


    <div class="learning-top">


        <div class="course-icon">

            <?php echo $icon; ?>

        </div>


        <span
            class="status-badge
            <?php echo $status_class; ?>"
        >

            <?php
            echo htmlspecialchars(
                $item["status"]
            );
            ?>

        </span>

    </div>


    <h2>

        <?php
        echo htmlspecialchars(
            $item["course_name"]
        );
        ?>

    </h2>


    <!-- PROGRESS HEADER -->

    <div class="progress-header">

        <span class="progress-label">
            LEARNING PROGRESS
        </span>

        <span class="progress-percent">

            <?php
            echo intval(
                $item["progress"]
            );
            ?>%

        </span>

    </div>


    <!-- PROGRESS BAR -->

    <div class="progress-bar">

        <div
            class="progress-fill"
            style="width: <?php echo intval($item["progress"]); ?>%;"
        ></div>

    </div>


    <!-- UPDATE -->

    <form
        method="POST"
        class="update-form"
    >


        <input
            type="hidden"
            name="tracker_id"
            value="<?php
                echo intval(
                    $item["tracker_id"]
                );
            ?>"
        >


        <input
            type="number"
            name="progress"
            class="progress-input"
            min="0"
            max="100"
            value="<?php
                echo intval(
                    $item["progress"]
                );
            ?>"
            required
        >


        <button
            type="submit"
            name="update_learning"
            class="update-button"
        >
            Update
        </button>


        <button
            type="submit"
            name="delete_learning"
            class="delete-button"
            onclick="return confirm('Remove this learning item?');"
        >
            Delete
        </button>


    </form>


</div>


<?php endforeach; ?>


</div>


<?php else: ?>


<!-- =================================
     EMPTY STATE
     ================================= -->

<div class="empty-tracker">

    <div class="empty-tracker-icon">
        📚
    </div>

    <h2>
        Start Tracking Your Learning
    </h2>

    <p>
        Add your first course or learning goal
        using the form above.
    </p>

</div>


<?php endif; ?>


</div>

</div>


</body>

</html>
```
