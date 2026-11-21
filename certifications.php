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
   GET CERTIFICATIONS
========================================= */

$certifications = [];

$cert_query = $conn->query("
    SELECT
        certification_id,
        certification_name,
        provider,
        description,
        certification_link
    FROM certifications
    ORDER BY certification_id ASC
");


if ($cert_query) {

    while ($cert = $cert_query->fetch_assoc()) {

        $certifications[] = $cert;

    }

}


/* =========================================
   HEADER
========================================= */

require_once "includes/header.php";

?>


<style>

/* =========================================
   CERTIFICATION PAGE
========================================= */

.cert-page {

    min-height: 100vh;

    padding: 40px 20px;

    background: #f6f7fb;

}


.cert-container {

    width: 100%;

    max-width: 1100px;

    margin: auto;

}


/* =========================================
   HEADER
========================================= */

.cert-header {

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 20px;

    margin-bottom: 28px;

}


.cert-label {

    color: #5b5cf0;

    font-size: 10px;

    font-weight: 800;

    letter-spacing: 1.8px;

}


.cert-header h1 {

    margin-top: 7px;

    color: #171827;

    font-size: 30px;

    font-weight: 800;

    letter-spacing: -1px;

}


.cert-header p {

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
   WELCOME BANNER
========================================= */

.cert-banner {

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


.cert-banner::after {

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


.cert-banner h2 {

    position: relative;

    z-index: 2;

    font-size: 21px;

    font-weight: 800;

}


.cert-banner p {

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
   CERTIFICATION GRID
========================================= */

.cert-grid {

    display: grid;

    grid-template-columns:
        repeat(2, 1fr);

    gap: 17px;

}


/* =========================================
   CERTIFICATION CARD
========================================= */

.cert-card {

    position: relative;

    display: flex;

    flex-direction: column;

    padding: 22px;

    border: 1px solid #e3e5ee;

    border-radius: 15px;

    background: #ffffff;

    transition: 0.3s ease;

}


.cert-card:hover {

    transform: translateY(-4px);

    border-color: #d7d8f8;

    box-shadow:
        0 14px 30px
        rgba(30,32,70,0.08);

}


/* =========================================
   CARD TOP
========================================= */

.cert-top {

    display: flex;

    align-items: flex-start;

    justify-content: space-between;

    gap: 15px;

}


.cert-icon {

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


.cert-number {

    color: #9295a7;

    font-size: 9px;

    font-weight: 700;

}


/* =========================================
   CERT CONTENT
========================================= */

.cert-card h2 {

    margin-top: 17px;

    color: #171827;

    font-size: 16px;

    font-weight: 800;

    line-height: 1.35;

}


.cert-provider {

    display: inline-block;

    margin-top: 7px;

    color: #5b5cf0;

    font-size: 10px;

    font-weight: 700;

}


.cert-description {

    min-height: 55px;

    margin-top: 12px;

    color: #626578;

    font-size: 11px;

    line-height: 1.65;

}


/* =========================================
   CARD FOOTER
========================================= */

.cert-footer {

    display: flex;

    align-items: center;

    justify-content: space-between;

    gap: 12px;

    margin-top: 20px;

    padding-top: 15px;

    border-top: 1px solid #eeeeF5;

}


.cert-type {

    color: #9295a7;

    font-size: 9px;

    font-weight: 600;

}


.cert-button {

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


.cert-button:hover {

    background: #4849d9;

    transform: translateY(-1px);

}


/* =========================================
   EMPTY STATE
========================================= */

.empty-cert {

    padding: 50px 20px;

    border: 1px solid #e3e5ee;

    border-radius: 15px;

    background: #ffffff;

    text-align: center;

}


.empty-icon {

    font-size: 38px;

}


.empty-cert h2 {

    margin-top: 12px;

    color: #171827;

    font-size: 19px;

    font-weight: 800;

}


.empty-cert p {

    margin-top: 7px;

    color: #626578;

    font-size: 11px;

}


/* =========================================
   RESPONSIVE
========================================= */

@media (max-width: 750px) {

    .cert-page {

        padding: 25px 15px;

    }


    .cert-header {

        align-items: flex-start;

        flex-direction: column;

    }


    .cert-header h1 {

        font-size: 25px;

    }


    .cert-grid {

        grid-template-columns: 1fr;

    }

}


@media (max-width: 450px) {

    .cert-banner {

        padding: 21px;

    }


    .cert-card {

        padding: 18px;

    }


    .cert-footer {

        align-items: flex-start;

        flex-direction: column;

    }


    .cert-button {

        width: 100%;

    }

}

</style>


<div class="cert-page">

<div class="cert-container">


    <!-- =====================================
         HEADER
    ====================================== -->

    <div class="cert-header">


        <div>

            <span class="cert-label">

                PROFESSIONAL DEVELOPMENT

            </span>


            <h1>

                Certifications

            </h1>


            <p>

                Earn industry-recognized certifications
                to validate your skills and strengthen
                your career profile.

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

    <div class="cert-banner">


        <h2>

            Build Your Professional Profile,

            <?php

            echo htmlspecialchars(
                $user_name
            );

            ?> 🏆

        </h2>


        <p>

            Certifications can help demonstrate your
            technical knowledge and improve your
            career opportunities.

        </p>


    </div>



    <!-- =====================================
         CERTIFICATION LIST
    ===================================== -->

    <?php if (!empty($certifications)): ?>


        <div class="cert-grid">


            <?php foreach (
                $certifications
                as $index => $cert
            ): ?>


                <div class="cert-card">


                    <!-- TOP -->

                    <div class="cert-top">


                        <div class="cert-icon">

                            <?php

                            $icons = [

                                "🏆",
                                "☁️",
                                "💻",
                                "🔐",
                                "🌐",
                                "🗄️",
                                "⚙️",
                                "🐍"

                            ];

                            echo $icons[
                                $index %
                                count($icons)
                            ];

                            ?>

                        </div>


                        <span class="cert-number">

                            CERTIFICATION

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



                    <!-- NAME -->

                    <h2>

                        <?php

                        echo htmlspecialchars(
                            $cert[
                                "certification_name"
                            ]
                        );

                        ?>

                    </h2>



                    <!-- PROVIDER -->

                    <span class="cert-provider">

                        By

                        <?php

                        echo htmlspecialchars(
                            $cert["provider"]
                        );

                        ?>

                    </span>



                    <!-- DESCRIPTION -->

                    <p class="cert-description">

                        <?php

                        echo htmlspecialchars(
                            $cert["description"]
                        );

                        ?>

                    </p>



                    <!-- FOOTER -->

                    <div class="cert-footer">


                        <span class="cert-type">

                            🏅 Industry Certification

                        </span>


                        <?php if (
                            !empty(
                                $cert[
                                    "certification_link"
                                ]
                            )
                        ): ?>


                            <a
                                href="<?php

                                    echo htmlspecialchars(
                                        $cert[
                                            "certification_link"
                                        ]
                                    );

                                ?>"
                                target="_blank"
                                rel="noopener noreferrer"
                                class="cert-button"
                            >

                                View Certification →

                            </a>


                        <?php endif; ?>


                    </div>


                </div>


            <?php endforeach; ?>


        </div>


    <?php else: ?>


        <!-- EMPTY STATE -->

        <div class="empty-cert">


            <div class="empty-icon">

                🏆

            </div>


            <h2>

                No Certifications Available

            </h2>


            <p>

                Certification recommendations will appear
                here once certification data is added.

            </p>


        </div>


    <?php endif; ?>


</div>

</div>


<?php

require_once "includes/footer.php";

?>
```
