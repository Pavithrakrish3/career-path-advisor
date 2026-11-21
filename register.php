<?php

require_once __DIR__ . "/includes/db.php";

$message = "";
$message_type = "";

$name = "";
$email = "";


// ==========================================
// REGISTRATION PROCESSING
// ==========================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name = trim($_POST["name"] ?? "");
    $email = trim($_POST["email"] ?? "");

    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";


    // ======================================
    // VALIDATION
    // ======================================

    if (
        empty($name) ||
        empty($email) ||
        empty($password) ||
        empty($confirm_password)
    ) {

        $message = "Please fill in all fields.";
        $message_type = "error";

    }

    elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $message = "Please enter a valid email address.";
        $message_type = "error";

    }

    elseif (strlen($password) < 6) {

        $message = "Password must contain at least 6 characters.";
        $message_type = "error";

    }

    elseif ($password !== $confirm_password) {

        $message = "Passwords do not match.";
        $message_type = "error";

    }

    else {


        // ==================================
        // CHECK EXISTING EMAIL
        // ==================================

        $stmt = $conn->prepare(
            "SELECT email
             FROM users
             WHERE email = ?
             LIMIT 1"
        );


        if (!$stmt) {

            $message = "Database query error: " . $conn->error;
            $message_type = "error";

        } else {

            $stmt->bind_param("s", $email);

            $stmt->execute();

            $result = $stmt->get_result();


            // Email already exists

            if ($result->num_rows > 0) {

                $message =
                    "An account with this email already exists.";

                $message_type = "error";

                $stmt->close();

            }

            else {

                $stmt->close();


                // ==================================
                // HASH PASSWORD
                // ==================================

                $hashed_password = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );


                // ==================================
                // INSERT USER
                // ==================================

                $stmt = $conn->prepare(
                    "INSERT INTO users
                    (name, email, password)
                    VALUES (?, ?, ?)"
                );


                if (!$stmt) {

                    $message =
                        "Database query error: " . $conn->error;

                    $message_type = "error";

                }

                else {

                    $stmt->bind_param(
                        "sss",
                        $name,
                        $email,
                        $hashed_password
                    );


                    if ($stmt->execute()) {

                        $message =
                            "Account created successfully! You can now sign in.";

                        $message_type = "success";

                        $name = "";
                        $email = "";

                    }

                    else {

                        $message =
                            "Registration failed. Please try again.";

                        $message_type = "error";

                    }


                    $stmt->close();

                }

            }

        }

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

    <title>Create Account | Career Path Advisor</title>


    <!-- Google Font -->

    <link rel="preconnect" href="https://fonts.googleapis.com">

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

</head>


<body>


<!-- =========================================
     PAGE
     ========================================= -->

<div class="auth-page">


    <!-- =========================================
         CONTAINER
         ========================================= -->

    
<a href="index.php" class="auth-home-link">
    ← Back to Home
</a>

    <div class="auth-container">


        <!-- =====================================
             LEFT SIDE
             ===================================== -->

        <div class="auth-intro">


            <!-- BRAND -->

            <div class="brand">

                <img
                    src="images/logo.png"
                    alt="Career Path Advisor Logo"
                    class="website-logo"
                >

                <div class="brand-text">

                    <h2>CareerPath</h2>

                    <span>ADVISOR</span>

                </div>

            </div>


            <!-- INTRO CONTENT -->

            <div class="intro-content">

                <span class="welcome-badge">

                    🚀 Start Your Journey

                </span>


                <h1>

                    Discover the

                    <span>Career</span>

                    That's Right for You.

                </h1>


                <p>

                    Build your profile, explore career opportunities,
                    discover the right skills and create a personalized
                    learning path for your future.

                </p>


                <!-- FEATURE LIST -->

                <div class="feature-list">


                    <!-- FEATURE 1 -->

                    <div class="feature-item">

                        <div class="feature-icon">

                            🎯

                        </div>

                        <div>

                            <strong>

                                Personalized Career Paths

                            </strong>

                            <small>

                                Find careers that match your interests.

                            </small>

                        </div>

                    </div>


                    <!-- FEATURE 2 -->

                    <div class="feature-item">

                        <div class="feature-icon">

                            📚

                        </div>

                        <div>

                            <strong>

                                Smart Learning Roadmaps

                            </strong>

                            <small>

                                Know what skills you need to grow.

                            </small>

                        </div>

                    </div>


                    <!-- FEATURE 3 -->

                    <div class="feature-item">

                        <div class="feature-icon">

                            💼

                        </div>

                        <div>

                            <strong>

                                Career Opportunities

                            </strong>

                            <small>

                                Discover courses, internships and more.

                            </small>

                        </div>

                    </div>


                </div>

            </div>


            <!-- FOOTER -->

            <div class="intro-footer">

                <span>

                    © 2026 Career Path Advisor

                </span>

                <span>

                    Built for Students

                </span>

            </div>


        </div>


        <!-- =====================================
             RIGHT SIDE
             ===================================== -->

        <div class="auth-form-section">


            <div class="form-wrapper">


                <!-- MOBILE BRAND -->

                <div class="mobile-brand">

                    <img
                        src="images/logo.png"
                        alt="Career Path Advisor Logo"
                        class="mobile-logo"
                    >

                    <div class="mobile-brand-text">

                        <h2>CareerPath</h2>

                        <span>ADVISOR</span>

                    </div>

                </div>


                <!-- FORM HEADING -->

                <div class="form-heading">

                    <span class="form-label">

                        CREATE YOUR ACCOUNT

                    </span>


                    <h2>

                        Let's get started 👋

                    </h2>


                    <p>

                        Create your account and begin your career journey.

                    </p>

                </div>


                <!-- MESSAGE -->

                <?php if (!empty($message)): ?>

                    <div class="message <?php echo htmlspecialchars($message_type); ?>">

                        <span class="message-icon">

                            <?php

                            echo ($message_type === "success")
                                ? "✓"
                                : "!";

                            ?>

                        </span>


                        <span>

                            <?php

                            echo htmlspecialchars($message);

                            ?>

                        </span>

                    </div>

                <?php endif; ?>


                <!-- REGISTER FORM -->

                <form
                    method="POST"
                    action="register.php"
                >


                    <!-- FULL NAME -->

                    <div class="input-group">

                        <label for="name">

                            Full Name

                        </label>


                        <div class="input-wrapper">

                            <span class="input-icon">

                                👤

                            </span>


                            <input
                                type="text"
                                id="name"
                                name="name"
                                placeholder="Enter your full name"
                                value="<?php echo htmlspecialchars($name); ?>"
                                required
                            >

                        </div>

                    </div>


                    <!-- EMAIL -->

                    <div class="input-group">

                        <label for="email">

                            Email Address

                        </label>


                        <div class="input-wrapper">

                            <span class="input-icon">

                                ✉

                            </span>


                            <input
                                type="email"
                                id="email"
                                name="email"
                                placeholder="student@example.com"
                                value="<?php echo htmlspecialchars($email); ?>"
                                required
                            >

                        </div>

                    </div>


                    <!-- PASSWORD -->

                    <div class="input-group">

                        <label for="password">

                            Password

                        </label>


                        <div class="input-wrapper">

                            <span class="input-icon">

                                🔒

                            </span>


                            <input
                                type="password"
                                id="password"
                                name="password"
                                placeholder="Create a password"
                                required
                            >

                        </div>


                        <small class="input-hint">

                            Use at least 6 characters.

                        </small>

                    </div>


                    <!-- CONFIRM PASSWORD -->

                    <div class="input-group">

                        <label for="confirm_password">

                            Confirm Password

                        </label>


                        <div class="input-wrapper">

                            <span class="input-icon">

                                🔐

                            </span>


                            <input
                                type="password"
                                id="confirm_password"
                                name="confirm_password"
                                placeholder="Re-enter your password"
                                required
                            >

                        </div>

                    </div>


                    <!-- TERMS -->

                    <div class="terms">

                        <input
                            type="checkbox"
                            id="terms"
                            required
                        >


                        <label for="terms">

                            I agree to the

                            <a href="#">

                                Terms & Conditions

                            </a>

                        </label>

                    </div>


                    <!-- BUTTON -->

                    <button
                        type="submit"
                        class="auth-button"
                    >

                        Create My Account

                        <span>

                            →

                        </span>

                    </button>


                </form>


                <!-- LOGIN LINK -->

                <div class="login-link">

                    Already have an account?

                    <a href="login.php">

                        Sign In

                    </a>

                </div>


            </div>

        </div>


    </div>

</div>


</body>

</html>