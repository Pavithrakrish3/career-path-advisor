<?php

session_start();

require_once __DIR__ . "/includes/db.php";

/* =========================================
   CHECK LOGIN
========================================= */

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];


/* =========================================
   DEFAULT VALUES
========================================= */

$name = "";
$email = "";

$degree = "";
$skills = "";
$interests = "";
$career_goal = "";

$phone = "";
$summary = "";
$education = "";
$experience = "";
$projects = "";

$message = "";
$message_type = "";


/* =========================================
   GET USER DETAILS
========================================= */

$stmt = $conn->prepare("
    SELECT name, email
    FROM users
    WHERE user_id = ?
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {

    $user = $result->fetch_assoc();

    $name = $user['name'];
    $email = $user['email'];
}

$stmt->close();


/* =========================================
   GET PROFILE DETAILS
========================================= */

$stmt = $conn->prepare("
    SELECT degree, skills, interests, career_goal
    FROM profiles
    WHERE user_id = ?
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {

    $profile = $result->fetch_assoc();

    $degree = $profile['degree'];
    $skills = $profile['skills'];
    $interests = $profile['interests'];
    $career_goal = $profile['career_goal'];
}

$stmt->close();


/* =========================================
   LOAD SAVED RESUME
========================================= */

$stmt = $conn->prepare("
    SELECT resume_data
    FROM resumes
    WHERE user_id = ?
    ORDER BY resume_id DESC
    LIMIT 1
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows > 0) {

    $saved_resume = $result->fetch_assoc();

    $resume_data = json_decode(
        $saved_resume['resume_data'],
        true
    );

    if (is_array($resume_data)) {

        $phone = $resume_data['phone'] ?? "";
        $summary = $resume_data['summary'] ?? "";
        $education = $resume_data['education'] ?? $degree;
        $experience = $resume_data['experience'] ?? "";
        $projects = $resume_data['projects'] ?? "";
    }
}

$stmt->close();


/* =========================================
   SAVE RESUME
========================================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $phone = trim($_POST['phone'] ?? "");

    $summary = trim($_POST['summary'] ?? "");

    $education = trim($_POST['education'] ?? "");

    $experience = trim($_POST['experience'] ?? "");

    $projects = trim($_POST['projects'] ?? "");


    /* Resume data */

    $resume_data_array = [

        "name" => $name,

        "email" => $email,

        "phone" => $phone,

        "summary" => $summary,

        "education" => $education,

        "skills" => $skills,

        "experience" => $experience,

        "projects" => $projects,

        "career_goal" => $career_goal,

        "interests" => $interests
    ];


    $resume_data = json_encode(
        $resume_data_array,
        JSON_UNESCAPED_UNICODE
    );


    /* Check existing resume */

    $check = $conn->prepare("
        SELECT resume_id
        FROM resumes
        WHERE user_id = ?
        LIMIT 1
    ");

    $check->bind_param("i", $user_id);

    $check->execute();

    $check_result = $check->get_result();


    if ($check_result->num_rows > 0) {

        /* UPDATE */

        $existing = $check_result->fetch_assoc();

        $resume_id = $existing['resume_id'];


        $update = $conn->prepare("
            UPDATE resumes
            SET resume_data = ?
            WHERE resume_id = ?
        ");

        $update->bind_param(
            "si",
            $resume_data,
            $resume_id
        );

        $update->execute();

        $update->close();

    } else {

        /* INSERT */

        $insert = $conn->prepare("
            INSERT INTO resumes
            (user_id, resume_data)
            VALUES (?, ?)
        ");

        $insert->bind_param(
            "is",
            $user_id,
            $resume_data
        );

        $insert->execute();

        $insert->close();
    }


    $check->close();


    $message = "Resume saved successfully!";

    $message_type = "success";
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

<title>Resume Builder | Career Path Advisor</title>


<!-- =========================================
     GOOGLE FONT
========================================= -->

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


<!-- =========================================
     MAIN CSS
========================================= -->

<link
    rel="stylesheet"
    href="css/style.css"
>


<style>

/* =========================================
   RESUME PAGE
========================================= */

.resume-page {

    min-height: 100vh;

    padding: 40px 20px;

    background: #f6f7fb;

    font-family: "Inter", sans-serif;
}


.resume-container {

    width: 100%;

    max-width: 1100px;

    margin: auto;
}


/* =========================================
   HEADER
========================================= */

.resume-header {
    position: relative;
    text-align: center;
    margin-bottom: 30px;
    padding: 0 150px;
}

.resume-dashboard-button {
    position: absolute;
    left: 0;
    top: 4px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    min-height: 40px;
    padding: 0 14px;
    border: 1px solid #e3e5ee;
    border-radius: 10px;
    background: #fff;
    color: #626578;
    font-size: 12px;
    font-weight: 700;
    text-decoration: none;
}
.resume-dashboard-button:hover { color: #5b5cf0; border-color: #5b5cf0; }

.ats-tip {
    margin: 0 0 24px;
    padding: 14px 16px;
    border: 1px solid #dfe3ff;
    border-radius: 10px;
    background: #f7f8ff;
    color: #50536b;
    font-size: 12px;
    line-height: 1.65;
}


.resume-header h1 {

    margin: 0 0 10px;

    color: #171827;

    font-size: 32px;

    font-weight: 800;
}


.resume-header p {

    margin: 0;

    color: #626578;

    font-size: 14px;
}


/* =========================================
   MAIN CARD
========================================= */

.resume-card {

    padding: 30px;

    background: #ffffff;

    border-radius: 18px;

    box-shadow:
        0 10px 35px rgba(30, 35, 70, 0.08);
}


/* =========================================
   MESSAGE
========================================= */

.resume-message {

    padding: 13px 16px;

    margin-bottom: 25px;

    border-radius: 10px;

    font-size: 13px;

    font-weight: 600;
}


.resume-message.success {

    color: #16804b;

    background: #ecfdf3;

    border: 1px solid #c8f1da;
}


.resume-message.error {

    color: #d13c3c;

    background: #fff1f1;

    border: 1px solid #ffd2d2;
}


/* =========================================
   FORM SECTION
========================================= */

.resume-section {

    margin-bottom: 24px;
}


.resume-section h3 {

    margin: 0 0 10px;

    color: #171827;

    font-size: 15px;

    font-weight: 700;
}


.resume-row {

    display: grid;

    grid-template-columns: 1fr 1fr;

    gap: 15px;
}


/* =========================================
   INPUT
========================================= */

.resume-input,
.resume-textarea {

    width: 100%;

    padding: 13px;

    border: 1px solid #e3e5ee;

    border-radius: 10px;

    outline: none;

    background: #fafbfe;

    color: #171827;

    font-family: inherit;

    font-size: 13px;

    box-sizing: border-box;

    transition: 0.25s ease;
}


.resume-input {

    height: 48px;
}


.resume-textarea {

    min-height: 100px;

    resize: vertical;

    line-height: 1.6;
}


.resume-input:focus,
.resume-textarea:focus {

    border-color: #5b5cf0;

    background: #ffffff;

    box-shadow:
        0 0 0 4px rgba(91,92,240,0.08);
}


.resume-input::placeholder,
.resume-textarea::placeholder {

    color: #a4a6b5;
}


/* =========================================
   BUTTONS
========================================= */

.resume-buttons {

    display: flex;

    gap: 12px;

    margin-top: 10px;
}


.resume-button {

    min-height: 50px;

    padding: 0 22px;

    border: none;

    border-radius: 11px;

    background: #5b5cf0;

    color: #ffffff;

    font-family: inherit;

    font-size: 13px;

    font-weight: 700;

    cursor: pointer;

    transition: 0.3s ease;
}


.resume-button:hover {

    background: #4849d9;

    transform: translateY(-2px);
}


.pdf-button {

    background: #171827;
}


.pdf-button:hover {

    background: #292b3d;
}


/* =========================================
   PREVIEW
========================================= */

.resume-preview-wrapper {

    margin-top: 40px;

    padding-top: 30px;

    border-top: 1px solid #e3e5ee;
}


.resume-preview-wrapper > h3 {

    margin-bottom: 20px;

    color: #171827;

    font-size: 19px;
}


/* =========================================
   ACTUAL RESUME
========================================= */

#resumePDF {

    width: 210mm;

    min-height: 297mm;

    margin: 0 auto;

    padding: 20mm;

    background: #ffffff;

    color: #171827;

    box-sizing: border-box;

    box-shadow:
        0 8px 30px rgba(0,0,0,0.10);

    font-family: Arial, sans-serif;
}


.pdf-name {

    margin-bottom: 5px;

    color: #171827;

    font-size: 32px;

    font-weight: 700;
}


.pdf-email {

    color: #555555;

    font-size: 13px;
}


.pdf-contact {

    margin-top: 5px;

    color: #555555;

    font-size: 13px;
}


.pdf-line {

    height: 2px;

    margin: 20px 0 25px;

    background: #5b5cf0;
}


.pdf-career {

    display: inline-block;

    margin-bottom: 25px;

    padding: 7px 12px;

    border-radius: 6px;

    background: #f0f0ff;

    color: #5b5cf0;

    font-size: 12px;

    font-weight: 700;
}


.pdf-section {

    margin-bottom: 22px;

    page-break-inside: avoid;
}


.pdf-section-title {

    margin-bottom: 7px;

    color: #5b5cf0;

    font-size: 14px;

    font-weight: 700;

    text-transform: uppercase;
}


.pdf-section-content {

    color: #333333;

    font-size: 13px;

    line-height: 1.7;

    white-space: pre-line;

    overflow-wrap: break-word;
}


/* =========================================
   PRINT / PDF
========================================= */

@media print {

    @page {

        size: A4 portrait;

        margin: 0;
    }


    html,
    body {

        width: 210mm;

        margin: 0;

        padding: 0;

        background: #ffffff !important;
    }


    /* Hide everything */

    body * {

        visibility: hidden !important;
    }


    /* Show ONLY resume */

    #resumePDF,
    #resumePDF * {

        visibility: visible !important;
    }


    /* Position resume */

    #resumePDF {

        position: absolute !important;

        left: 0 !important;

        top: 0 !important;

        width: 210mm !important;

        min-height: 297mm !important;

        margin: 0 !important;

        padding: 20mm !important;

        box-shadow: none !important;

        background: #ffffff !important;
    }


    .pdf-section {

        page-break-inside: avoid !important;
    }
}


/* =========================================
   RESPONSIVE
========================================= */

@media (max-width: 900px) {

    .resume-row {

        grid-template-columns: 1fr;
    }


    #resumePDF {

        width: 100%;

        min-height: auto;

        padding: 30px;

        box-shadow: none;
    }
}


@media (max-width: 600px) {

    .resume-page {

        padding: 25px 15px;
    }


    .resume-card {

        padding: 20px;
    }


    .resume-header h1 {

        font-size: 26px;
    }


    .resume-buttons {

        flex-direction: column;
    }


    .resume-button {

        width: 100%;
    }
}

</style>

</head>


<body>


<div class="resume-page">


<div class="resume-container">


<!-- =========================================
     PAGE HEADER
========================================= -->

<div class="resume-header">

    <a href="dashboard.php" class="resume-dashboard-button">← Dashboard</a>

    <h1>
        📄 Resume Builder
    </h1>

    <p>
        Create your professional resume using your
        Career Path Advisor profile.
    </p>

</div>


<!-- =========================================
     MAIN CARD
========================================= -->

<div class="resume-card">

<div class="ats-tip"><strong>ATS-friendly resume tips:</strong> Use standard section headings, include relevant job keywords naturally, list measurable results where possible, and keep experience/projects in simple text format. The downloaded resume uses a clean single-column layout for easier applicant-tracking-system reading.</div>

<?php if (!empty($message)): ?>

    <div class="resume-message <?php echo htmlspecialchars($message_type); ?>">

        ✓
        <?php echo htmlspecialchars($message); ?>

    </div>

<?php endif; ?>


<!-- =========================================
     RESUME FORM
========================================= -->

<form method="POST">


<!-- PERSONAL INFORMATION -->

<div class="resume-section">

    <h3>
        Personal Information
    </h3>


    <div class="resume-row">

        <input
            class="resume-input"
            type="text"
            value="<?php echo htmlspecialchars($name); ?>"
            readonly
        >


        <input
            class="resume-input"
            type="email"
            value="<?php echo htmlspecialchars($email); ?>"
            readonly
        >

    </div>

</div>


<!-- PHONE -->

<div class="resume-section">

    <h3>
        Phone Number
    </h3>

    <input
        class="resume-input"
        type="text"
        name="phone"
        value="<?php echo htmlspecialchars($phone); ?>"
        placeholder="Enter your phone number"
    >

</div>


<!-- SUMMARY -->

<div class="resume-section">

    <h3>
        Professional Summary
    </h3>

    <textarea
        class="resume-textarea"
        name="summary"
        placeholder="Example: MCA student with skills in Java, MySQL and web development. Interested in software development and building reliable applications."
    ><?php echo htmlspecialchars($summary); ?></textarea>

</div>


<!-- EDUCATION -->

<div class="resume-section">

    <h3>
        Education
    </h3>

    <textarea
        class="resume-textarea"
        name="education"
        placeholder="Example: MCA - Computer Applications"
    ><?php echo htmlspecialchars($education); ?></textarea>

</div>


<!-- SKILLS -->

<div class="resume-section">

    <h3>
        Skills
    </h3>

    <textarea
        class="resume-textarea"
        readonly
    ><?php echo htmlspecialchars($skills); ?></textarea>

</div>


<!-- EXPERIENCE -->

<div class="resume-section">

    <h3>
        Experience
    </h3>

    <textarea
        class="resume-textarea"
        name="experience"
        placeholder="Role | Company | Dates
- Describe your responsibility using action verbs.
- Add tools, skills, and measurable results where possible."
    ><?php echo htmlspecialchars($experience); ?></textarea>

</div>


<!-- PROJECTS -->

<div class="resume-section">

    <h3>
        Projects
    </h3>

    <textarea
        class="resume-textarea"
        name="projects"
        placeholder="Project Name | Technologies Used
- What you built and the problem it solves.
- Your contribution and outcome."
    ><?php echo htmlspecialchars($projects); ?></textarea>

</div>


<!-- BUTTONS -->

<div class="resume-buttons">

    <button
        type="submit"
        class="resume-button"
    >
        Save Resume
    </button>


    <button
        type="button"
        class="resume-button pdf-button"
        onclick="downloadResumePDF()"
    >
        📄 Download Resume as PDF
    </button>

</div>


</form>


<!-- =========================================
     RESUME PREVIEW
========================================= -->

<div class="resume-preview-wrapper">

    <h3>
        Resume Preview
    </h3>


    <!-- =====================================
         THIS IS THE PRINTABLE RESUME
    ====================================== -->

    <div id="resumePDF">


        <!-- NAME -->

        <div class="pdf-name">

            <?php echo htmlspecialchars($name); ?>

        </div>


        <!-- EMAIL -->

        <div class="pdf-email">

            <?php echo htmlspecialchars($email); ?>

        </div>


        <!-- PHONE -->

        <?php if (!empty($phone)): ?>

            <div class="pdf-contact">

                <?php echo htmlspecialchars($phone); ?>

            </div>

        <?php endif; ?>


        <!-- LINE -->

        <div class="pdf-line"></div>


        <!-- CAREER GOAL -->

        <?php if (!empty($career_goal)): ?>

            <div class="pdf-career">

                Career Goal:
                <?php echo htmlspecialchars($career_goal); ?>

            </div>

        <?php endif; ?>


        <!-- PROFESSIONAL SUMMARY -->

        <?php if (!empty($summary)): ?>

            <div class="pdf-section">

                <div class="pdf-section-title">

                    Professional Summary

                </div>

                <div class="pdf-section-content">

                    <?php echo htmlspecialchars($summary); ?>

                </div>

            </div>

        <?php endif; ?>


        <!-- EDUCATION -->

        <?php if (!empty($education)): ?>

            <div class="pdf-section">

                <div class="pdf-section-title">

                    Education

                </div>

                <div class="pdf-section-content">

                    <?php echo htmlspecialchars($education); ?>

                </div>

            </div>

        <?php endif; ?>


        <!-- SKILLS -->

        <?php if (!empty($skills)): ?>

            <div class="pdf-section">

                <div class="pdf-section-title">

                    Skills

                </div>

                <div class="pdf-section-content">

                    <?php echo htmlspecialchars($skills); ?>

                </div>

            </div>

        <?php endif; ?>


        <!-- EXPERIENCE -->

        <?php if (!empty($experience)): ?>

            <div class="pdf-section">

                <div class="pdf-section-title">

                    Experience

                </div>

                <div class="pdf-section-content">

                    <?php echo htmlspecialchars($experience); ?>

                </div>

            </div>

        <?php endif; ?>


        <!-- PROJECTS -->

        <?php if (!empty($projects)): ?>

            <div class="pdf-section">

                <div class="pdf-section-title">

                    Projects

                </div>

                <div class="pdf-section-content">

                    <?php echo htmlspecialchars($projects); ?>

                </div>

            </div>

        <?php endif; ?>


        <!-- INTERESTS -->

        <?php if (!empty($interests)): ?>

            <div class="pdf-section">

                <div class="pdf-section-title">

                    Interests

                </div>

                <div class="pdf-section-content">

                    <?php echo htmlspecialchars($interests); ?>

                </div>

            </div>

        <?php endif; ?>


    </div>

</div>


</div>

</div>

</div>


<!-- =========================================
     PRINT FUNCTION
========================================= -->

<script>

function downloadResumePDF() {

    const resume = document.getElementById("resumePDF");


    if (!resume) {

        alert("Resume preview not found.");

        return;
    }


    /*
       Browser print dialog opens.
       Select "Save to PDF".
    */

    window.print();

}

</script>


</body>

</html>
```
