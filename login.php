<?php

session_start();

require_once __DIR__ . "/includes/db.php";


// ================================
// VARIABLES
// ================================

$message = "";
$message_type = "";

$email = "";


// ================================
// LOGIN PROCESS
// ================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email = trim($_POST["email"] ?? "");
    $password = $_POST["password"] ?? "";


    // Validate fields

    if ($email === "" || $password === "") {

        $message = "Please enter your email and password.";
        $message_type = "error";

    } else {


        // Get user details

        $stmt = $conn->prepare(
            "SELECT user_id, name, email, password
             FROM users
             WHERE email = ?
             LIMIT 1"
        );


        $stmt->bind_param("s", $email);

        $stmt->execute();

        $result = $stmt->get_result();


        if ($result->num_rows === 1) {

            $user = $result->fetch_assoc();


            // Check password

            if (password_verify($password, $user["password"])) {


                // ================================
                // LOGIN SUCCESS
                // ================================

                $_SESSION["user_id"] = $user["user_id"];

                $_SESSION["user_name"] = $user["name"];

                $_SESSION["user_email"] = $user["email"];


                // Redirect to dashboard

                header("Location: dashboard.php");

                exit();


            } else {

                $message = "Invalid email or password.";

                $message_type = "error";

            }


        } else {

            $message = "Invalid email or password.";

            $message_type = "error";

        }


        $stmt->close();

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

    <title>Sign In | Career Path Advisor</title>


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


    <!-- CSS -->

    <link
        rel="stylesheet"
        href="css/style.css"
    >

</head>


<body>


<div class="auth-page">


  
<a href="index.php" class="auth-home-link">
    ← Back to Home
</a>

    <div class="auth-container">


        <!-- =================================
             LEFT SIDE
             ================================= -->

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


            <!-- INTRO -->

            <div class="intro-content">


                <span class="welcome-badge">

                    🚀 Welcome Back

                </span>


                <h1>

                    Continue Your

                    <span>Career</span>

                    Journey.

                </h1>


                <p>

                    Sign in to continue exploring personalized
                    career paths, learning roadmaps, skills,
                    courses and opportunities.

                </p>


                <!-- FEATURES -->

                <div class="feature-list">


                    <div class="feature-item">

                        <div class="feature-icon">
                            🎯
                        </div>

                        <div>

                            <strong>
                                Personalized Career Paths
                            </strong>

                            <small>
                                Explore careers that match your interests.
                            </small>

                        </div>

                    </div>


                    <div class="feature-item">

                        <div class="feature-icon">
                            📚
                        </div>

                        <div>

                            <strong>
                                Smart Learning Roadmaps
                            </strong>

                            <small>
                                Build the skills needed for your future.
                            </small>

                        </div>

                    </div>


                    <div class="feature-item">

                        <div class="feature-icon">
                            💼
                        </div>

                        <div>

                            <strong>
                                Career Opportunities
                            </strong>

                            <small>
                                Discover internships, courses and more.
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



        <!-- =================================
             RIGHT SIDE
             ================================= -->

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

                        WELCOME BACK

                    </span>


                    <h2>

                        Welcome back! 👋

                    </h2>


                    <p>

                        Sign in to continue your career journey.

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


                <!-- LOGIN FORM -->

                <form
                    method="POST"
                    action=""
                >


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
                                autocomplete="email"
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
                                placeholder="Enter your password"
                                autocomplete="current-password"
                                required
                            >

                        </div>

                    </div>



                    <!-- OPTIONS -->

                    <div class="login-options">


                        <label class="remember-me">

                            <input
                                type="checkbox"
                                name="remember"
                            >

                            <span>
                                Remember me
                            </span>

                        </label>


                        <a
                            href="#"
                            class="forgot-password"
                        >

                            Forgot Password?

                        </a>


                    </div>



                    <!-- BUTTON -->

                    <button
                        type="submit"
                        class="auth-button"
                    >

                        Sign In

                        <span>
                            →
                        </span>

                    </button>


                </form>



                <!-- REGISTER -->

                <div class="login-link">

                    Don't have an account?


                    <a href="register.php">

                        Create Account

                    </a>

                </div>


            </div>

        </div>


    </div>

</div>


</body>

</html>
