<?php
// index.php
require_once 'includes/config.php';
require_once 'templates/header.php';
?>

<style>
    .dashboard-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 40px 20px;
    }

    /* Hero Section Styling */
    .hero {
        text-align: center;
        padding: 60px 20px;
        background-color: #f8f9fa; /* Clean light background */
        margin-bottom: 20px;
        border-bottom: 1px solid #e9ecef;
    }

    .hero h1 {
        margin-bottom: 15px;
        font-size: 2.8rem;
        color: #2c3e50; /* Theme Dark Blue */
    }

    .hero p {
        font-size: 1.25rem;
        color: #555;
        margin-bottom: 30px;
    }

    /* --- THEME BUTTON (MATCHING LOGO) --- */
    .btn-primary {
        display: inline-block;
        background-color: #e67e22; /* ContentCraft Orange */
        color: #fff;
        padding: 14px 40px;
        font-size: 1.15rem;
        border-radius: 50px; /* Pill shape for modern look */
        text-decoration: none;
        font-weight: bold;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        box-shadow: 0 4px 6px rgba(230, 126, 34, 0.3); /* Subtle orange shadow */
    }

    .btn-primary:hover {
        background-color: #d35400; /* Darker Orange on Hover */
        transform: translateY(-2px); /* Slight lift effect */
        box-shadow: 0 6px 12px rgba(211, 84, 0, 0.4);
    }

    /* Warning Box */
    .quota-warning {
        background-color: #fff3cd;
        border: 1px solid #ffeeba;
        color: #856404;
        padding: 15px;
        border-radius: 6px;
        margin: 35px auto 0 auto;
        text-align: center;
        font-size: 0.95rem;
        max-width: 700px;
    }

    /* GRID SYSTEM */
    .tools-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr); /* 3 Columns Fixed */
        gap: 25px;
        margin-top: 20px;
    }

    /* Card Styling */
    .tool-card {
        background: #ffffff;
        border: 1px solid #e0e0e0;
        border-radius: 12px;
        padding: 30px 20px;
        text-align: center;
        text-decoration: none;
        color: #333;
        transition: all 0.3s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        box-shadow: 0 2px 5px rgba(0,0,0,0.02);
    }

    .tool-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
        border-color: #e67e22; /* Orange border on hover */
    }

    .tool-icon {
        font-size: 3rem;
        margin-bottom: 15px;
        display: block;
    }

    .tool-card h3 {
        margin: 10px 0;
        font-size: 1.25rem;
        font-weight: 700;
        color: #2c3e50;
    }

    .tool-card p {
        font-size: 0.95rem;
        color: #666;
        margin: 0;
        line-height: 1.5;
    }

    /* Responsive */
    @media (max-width: 992px) { .tools-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 600px) { .tools-grid { grid-template-columns: 1fr; } }
</style>

<section class="hero">
    <h1>Welcome to ContentCraft</h1>
    <p>Your ultimate assistant for teaching resources.</p>
    
    <a href="#tools" class="btn-primary">Start Creating</a>

    <div class="quota-warning">
        <strong>⚠️ Important Usage Note:</strong> <br>
        AI generation consumes API tokens. Please allow a gap of at least <strong>5 minutes</strong> between new requests to avoid hitting quota limits.
    </div>
</section>

<div class="dashboard-container" id="tools">
    
    <h2 style="text-align:center; margin-bottom: 30px; color: #333;">Quick Access Tools</h2>
    
    <div class="tools-grid">
        <a href="plans/lesson-plan.php" class="tool-card">
            <span class="tool-icon">📋</span>
            <h3>Lesson Plan</h3>
            <p>Standard ITI Format Plans</p>
        </a>

        <a href="plans/demonstration-plan.php" class="tool-card">
            <span class="tool-icon">🛠️</span>
            <h3>Demonstration Plan</h3>
            <p>Practical Guides & Safety</p>
        </a>

        <a href="materials/theory.php" class="tool-card">
            <span class="tool-icon">📖</span>
            <h3>Theory Notes</h3>
            <p>Detailed Reading Material</p>
        </a>

        <a href="tools/mcq-generator.php" class="tool-card">
            <span class="tool-icon">✅</span>
            <h3>MCQ Generator</h3>
            <p>Question Banks with Keys</p>
        </a>

        <a href="tools/descriptive-qs-generator.php" class="tool-card">
            <span class="tool-icon">📝</span>
            <h3>Descriptive Qs</h3>
            <p>Short & Long Questions</p>
        </a>

        <a href="plans/graded-exercise.php" class="tool-card">
            <span class="tool-icon">📊</span>
            <h3>Graded Exercise</h3>
            <p>Job Assignments & Criteria</p>
        </a>
    </div>

</div>

<?php require_once 'templates/footer.php'; ?>