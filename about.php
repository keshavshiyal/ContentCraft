<?php
// about.php
require_once 'includes/config.php';
require_once 'templates/header.php';
?>

<section class="hero" style="min-height: 300px; padding: 60px 20px;">
    <h1>About ContentCraft</h1>
    <p>Bridging the gap between traditional teaching and modern AI technology.</p>
</section>

<div class="about-container">

    <div class="mission-box">
        <h2>Our Mission</h2>
        <p style="font-size: 1.1rem; line-height: 1.8; color: #444;">
            Teaching is an art, but the administrative work behind it is often a burden.
            <strong>ContentCraft</strong> was built to assist educators by automating the creation of lesson plans,
            theory notes, and assessments. Our goal is to save you hours of paperwork so you can focus on
            what matters most: <strong>inspiring your students.</strong>
        </p>
    </div>

    <h2 style="text-align: center; margin-bottom: 30px; color: var(--primary-color);">What We Provide</h2>

    <div class="features-grid">
        <div class="feature-card">
            <div class="feature-icon"><i class="fas fa-chalkboard-teacher"></i></div>
            <h3>Lesson Planning</h3>
            <p>Generate structured, detailed lesson and demonstration plans instantly based on your topic.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon"><i class="fas fa-book-open"></i></div>
            <h3>Study Material</h3>
            <p>Create comprehensive reading material in Gujarati, Hindi, and English with one click.</p>
        </div>

        <div class="feature-card">
            <div class="feature-icon"><i class="fas fa-question-circle"></i></div>
            <h3>Assessment Tools</h3>
            <p>Automatically generate MCQs and descriptive questions to test student understanding.</p>
        </div>
    </div>

    <div class="developer-section">
        <h3>Powered by Advanced AI</h3>
        <p>
            ContentCraft utilizes the <strong>Google Gemini 2.5 Flash</strong> model to ensure accuracy,
            creativity, and deep understanding of educational contexts.
        </p>
        <br>

        <div style="margin-top: 20px; font-size: 1rem;">
            <p>
                Designed & Developed by
                <a href="https://github.com/keshavshiyal" target="_blank" style="color: var(--primary-color); font-weight: 700; text-decoration: none;">
                    <i class="fab fa-github"></i> Keshav Shiyal
                </a>
            </p>
            <!-- <p style="font-size: 0.85rem; color: #888; margin-top: 5px;">
                (Apk Factory)
            </p> -->
        </div>
    </div>

</div>

<?php require_once 'templates/footer.php'; ?>